<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    /**
     * Enable 2FA and generate a QR code for the authenticator app.
     */
    public function enable(Request $request)
    {
        $user = $request->user();

        // Generate a valid Base32 secret key required by authenticator apps
        $secret = $this->generateBase32Secret();

        // Store the secret immediately and sync the user object
        $user->two_factor_secret = $secret;
        $user->save();

        // Create the OTPAuth URL used by authenticator applications
        $company = config('app.name', 'Laravel');
        $challegeUrl = "otpauth://totp/" . rawurlencode($company) . ":" . rawurlencode($user->email) . "?secret={$secret}&issuer=" . rawurlencode($company);

        // Generate the QR code as an SVG string
        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($challegeUrl);

        // Return the QR code and secret through the session
        return back()->with([
            'qrCode' => $qrCode,
            'secret' => $secret,
            'status' => '2FA setup started.'
        ]);
    }

    /**
     * Verify the code entered during the 2FA setup process.
     */
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required']);
        $user = $request->user();

        // Remove spaces from the entered code (e.g. "123 456" → "123456")
        $code = str_replace(' ', '', $request->code);

        if ($this->verifyCode($user->two_factor_secret, $code)) {
            $user->two_factor_enabled = true;
            $user->save();

            return back()->with('status', '2FA successfully enabled.');
        }

        return back()->withErrors(['code' => 'Invalid verification code.']);
    }

    /**
     * Disable 2FA for the current user.
     */
    public function disable(Request $request)
    {
        $user = $request->user();

        // Remove the secret and disable 2FA
        $user->two_factor_secret = null;
        $user->two_factor_enabled = false;
        $user->save();

        return back()->with('status', '2FA has been disabled.');
    }

    /**
     * Validate the 2FA code during login.
     */
    public function loginCheck(Request $request)
    {
        $request->validate(['code' => 'required']);
        $user = auth()->user();

        // Remove spaces from the entered code
        $code = str_replace(' ', '', $request->code);

        if ($this->verifyCode($user->two_factor_secret, $code)) {
            // Mark the session as 2FA verified
            $request->session()->put('2fa_verified', true);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['code' => 'Invalid code.']);
    }

    /**
     * Verify a TOTP code using the stored secret.
     * Accepts codes from the previous, current, and next 30-second time window.
     */
    private function verifyCode($secret, $code)
    {
        if (empty($secret)) {
            return false;
        }

        $timeSlice = floor(time() / 30);
        $secretKey = $this->base32Decode($secret);

        // Allow a small time drift of ±30 seconds
        for ($i = -1; $i <= 1; $i++) {
            $timeInput = pack('N*', 0) . pack('N*', $timeSlice + $i);
            $hash = hash_hmac('sha1', $timeInput, $secretKey, true);

            $offset = ord(substr($hash, -1)) & 0x0F;
            $hashPart = substr($hash, $offset, 4);

            $value = unpack('Nvalue', $hashPart);
            $value = $value['value'] & 0x7FFFFFFF;

            $calculatedCode = str_pad(
                $value % 1000000,
                6,
                '0',
                STR_PAD_LEFT
            );

            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a random Base32 secret key.
     */
    private function generateBase32Secret($length = 16)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }

        return $secret;
    }

    /**
     * Decode a Base32 encoded string into binary data.
     */
    private function base32Decode($base32)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $map = array_flip(str_split($chars));

        $base32 = strtoupper($base32);
        $binary = '';

        foreach (str_split($base32) as $char) {
            if (!isset($map[$char])) {
                continue;
            }

            $binary .= str_pad(
                decbin($map[$char]),
                5,
                '0',
                STR_PAD_LEFT
            );
        }

        $bytes = '';

        foreach (str_split($binary, 8) as $chunk) {
            if (strlen($chunk) < 8) {
                break;
            }

            $bytes .= chr(bindec($chunk));
        }

        return $bytes;
    }
}