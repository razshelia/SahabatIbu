<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            if (! $request->routeIs('password.change.*') && ! $request->routeIs('logout')) {
                return redirect()->route('password.change.form')
                    ->with('warning', 'Demi keamanan, Anda wajib mengubah kata sandi pada login pertama sebelum melanjutkan.');
            }
        }

        return $next($request);
    }
}
