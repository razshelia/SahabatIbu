<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LogAktivitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectUserBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login (F-01, F-06).
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        // Pembatasan 5 percobaan login per menit (F-06, Lampiran D)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            LogAktivitasService::catat(
                aksi: 'login_gagal',
                keterangan: [
                    'email' => $request->input('email'),
                    'detik' => $seconds,
                    'dibatasi' => true,
                ]
            );

            return back()
                ->with('error', "Terlalu banyak percobaan masuk yang gagal. Coba lagi dalam {$seconds} detik.")
                ->onlyInput('email');
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            LogAktivitasService::catat(
                aksi: 'login_berhasil',
                objekTipe: 'User',
                objekId: $user->id,
                user: $user
            );

            return $this->redirectUserBasedOnRole($user);
        }

        RateLimiter::hit($throttleKey, 60);

        LogAktivitasService::catat(
            aksi: 'login_gagal',
            keterangan: ['email' => $request->input('email')]
        );

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Proses keluar sistem (F-02).
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            LogAktivitasService::catat(
                aksi: 'logout',
                objekTipe: 'User',
                objekId: $user->id,
                user: $user
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Ibu sudah keluar dari akun.');
    }

    /**
     * Pengalihan halaman sesuai peran dan status (F-03).
     */
    protected function redirectUserBasedOnRole($user): RedirectResponse
    {
        if ($user->must_change_password) {
            return redirect()->route('password.change.form');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Nasabah
        if (! $user->nasabah || empty($user->nasabah->telepon) || empty($user->nasabah->alamat)) {
            return redirect()->route('profile.complete.form');
        }

        if ($user->status === 'menunggu' || $user->status === 'ditolak') {
            return redirect()->route('account.status');
        }

        return redirect()->route('nasabah.dashboard');
    }
}
