<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LogAktivitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and authenticate the user.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        $throttleKey = 'google_oauth|' . Request::ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            return redirect()->route('login')->with('error', 'Terlalu banyak permintaan otentikasi Google. Silakan tunggu sebentar.');
        }

        RateLimiter::hit($throttleKey, 60);

        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->getId())->first()
                ?? User::where('email', $googleUser->getEmail())->first();

            $isNew = false;
            if (! $user) {
                $user = new User();
                $user->forceFill([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                    'email' => $googleUser->getEmail(),
                    'role' => 'nasabah',
                    'status' => 'menunggu',
                    'email_verified_at' => now(),
                ]);
                $isNew = true;
            }

            $user->google_id = $googleUser->getId();
            $user->save();

            Auth::login($user);

            LogAktivitasService::catat(
                aksi: $isNew ? 'registrasi_google' : 'login_google',
                objekTipe: 'User',
                objekId: $user->id,
                user: $user
            );

            // Redirect sesuai peran & kelengkapan data
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // Jika belum punya data nasabah / profil kontak belum lengkap
            if (! $user->nasabah || empty($user->nasabah->telepon) || empty($user->nasabah->alamat)) {
                return redirect()->route('profile.complete.form');
            }

            if ($user->status === 'menunggu' || $user->status === 'ditolak') {
                return redirect()->route('account.status');
            }

            return redirect()->route('nasabah.dashboard');
        } catch (Throwable $e) {
            Log::error('Google login error: ' . $e->getMessage());

            return redirect()->route('login')->with('error', 'Gagal login menggunakan akun Google. Silakan coba kembali.');
        }
    }
}
