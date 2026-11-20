<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'nasabah') {
            $nasabah = $user->nasabah;

            // Jika belum punya data nasabah atau kontak belum lengkap
            if (! $nasabah || empty($nasabah->telepon) || empty($nasabah->alamat)) {
                if (! $request->routeIs('profile.complete.*') && ! $request->routeIs('logout')) {
                    return redirect()->route('profile.complete.form')
                        ->with('info', 'Silakan lengkapi nomor telepon dan alamat Anda untuk melanjutkan.');
                }
            }
        }

        return $next($request);
    }
}
