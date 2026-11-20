<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Admin selalu diizinkan
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Cek status akun nasabah (BR-13)
        if ($user->status === 'menunggu' || $user->status === 'ditolak') {
            if (! $request->routeIs('account.status') && ! $request->routeIs('logout')) {
                return redirect()->route('account.status');
            }
        } elseif ($user->status === 'nonaktif') {
            abort(403, 'Akun Anda telah dinonaktifkan oleh pengurus. Silakan hubungi admin bank sampah.');
        }

        return $next($request);
    }
}
