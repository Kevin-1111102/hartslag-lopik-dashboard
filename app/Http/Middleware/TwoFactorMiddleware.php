<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
   
    public function handle($request, \Closure $next)
{
    $user = $request->user();

    // Als de gebruiker 2FA aan heeft staan, maar nog niet succesvol de code heeft ingevoerd tijdens deze sessie
    if ($user && $user->two_factor_enabled && !$request->session()->has('2fa_verified')) {
        // Voorkom oneindige loops op de login-pagina zelf
        if (!$request->is('2fa/*')) {
            return redirect()->route('2fa.login');
        }
    }

    return $next($request);
}
}


