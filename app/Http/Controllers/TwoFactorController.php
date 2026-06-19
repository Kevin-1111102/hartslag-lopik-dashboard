<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    // Schakel 2FA in en genereer een QR-code
    public function enable(Request $request)
    {
        $user = $request->user();
        
        // Genereer een geldige Base32 geheime sleutel (vereist voor Authenticator apps)
        $secret = $this->generateBase32Secret(); 
        
        // Sla direct op via het model om het object in het geheugen direct te synchroniseren
        $user->two_factor_secret = $secret;
        $user->save();

        // Maak de URL voor de Authenticator App
        $company = config('app.name', 'Laravel');
        $challegeUrl = "otpauth://totp/" . rawurlencode($company) . ":" . rawurlencode($user->email) . "?secret={$secret}&issuer=" . rawurlencode($company);

        // Genereer de QR-code als SVG string
        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($challegeUrl);

        // We sturen ZOWEL de sessie als een geforceerde herlaad terug om Blade direct te triggeren
        return back()->with(['qrCode' => $qrCode, 'secret' => $secret, 'status' => '2FA setup gestart.']);
    }

    // Bevestig de 2FA code van de profielpagina
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required']);
        $user = $request->user();

        // Haal eventuele spaties uit de invoer (bijv "558 674" -> "558674")
        $code = str_replace(' ', '', $request->code);

        if ($this->verifyCode($user->two_factor_secret, $code)) {
            $user->two_factor_enabled = true;
            $user->save();
            
            return back()->with('status', '2FA succesvol ingeschakeld!');
        }

        return back()->withErrors(['code' => 'Ongeldige verificatiecode.']);
    }

    // Schakel 2FA volledig uit
    public function disable(Request $request)
    {
        $user = $request->user();
        $user->two_factor_secret = null;
        $user->two_factor_enabled = false;
        $user->save();

        return back()->with('status', '2FA is uitgeschakeld.');
    }

    // De login-check nadat het wachtwoord klopt
    public function loginCheck(Request $request)
    {
        $request->validate(['code' => 'required']);
        $user = auth()->user();

        // Haal eventuele spaties uit de invoer
        $code = str_replace(' ', '', $request->code);

        if ($this->verifyCode($user->two_factor_secret, $code)) {
            $request->session()->put('2fa_verified', true);
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['code' => 'Ongeldige code.']);
    }

    // Tijdgebaseerde TOTP Verificatie met Base32 ondersteuning
    private function verifyCode($secret, $code)
    {
        if (empty($secret)) {
            return false;
        }

        $timeSlice = floor(time() / 30);
        $secretKey = $this->base32Decode($secret);
        
        // Controleer de huidige minuut, 30 seconden terug en 30 seconden vooruit (marge voor tijdverschil)
        for ($i = -1; $i <= 1; $i++) {
            $timeInput = pack('N*', 0) . pack('N*', $timeSlice + $i);
            $hash = hash_hmac('sha1', $timeInput, $secretKey, true);
            $offset = ord(substr($hash, -1)) & 0x0F;
            
            $hashPart = substr($hash, $offset, 4);
            $value = unpack('Nvalue', $hashPart); // Benoem de array index als 'value' voor stabiliteit
            $value = $value['value'] & 0x7FFFFFFF;
            
            $calculatedCode = str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);
            
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }
        return false;
    }

    // Helper om een willekeurige Base32 string te genereren
    private function generateBase32Secret($length = 16)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    // Helper om Base32 te decoderen naar bytes voor HMAC-SHA1
    private function base32Decode($base32)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $map = array_flip(str_split($chars));
        $base32 = strtoupper($base32);
        $binary = '';
        
        foreach (str_split($base32) as $char) {
            if (!isset($map[$char])) continue;
            $binary .= str_pad(decbin($map[$char]), 5, '0', STR_PAD_LEFT);
        }
        
        $bytes = '';
        foreach (str_split($binary, 8) as $chunk) {
            if (strlen($chunk) < 8) break;
            $bytes .= chr(bindec($chunk));
        }
        return $bytes;
    }
}
