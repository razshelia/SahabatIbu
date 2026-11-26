<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LogAktivitasService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Tampilkan formulir ubah kata sandi (untuk pengguna login / admin awal).
     */
    public function showChangeForm(): View
    {
        return view('auth.change-password', [
            'isMustChange' => Auth::user()->must_change_password,
        ]);
    }

    /**
     * Proses ubah kata sandi pengguna (F-01, F-04).
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $mustChangePassword = $user->must_change_password;

        $rules = [
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'],
        ];

        // Jika bukan penggantian paksa pertama, wajib periksa kata sandi lama
        if (! $mustChangePassword) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules, [
            'password.regex' => 'Kata sandi baru harus terdiri dari minimal 8 karakter, dengan huruf dan angka.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
        ]);

        $user->forceFill([
            'password' => $request->password, // automatically hashed by cast 'hashed'
            'must_change_password' => false,
        ])->save();

        LogAktivitasService::catat(
            aksi: 'ganti_kata_sandi',
            objekTipe: 'User',
            objekId: $user->id,
            user: $user
        );

        if ($user->role === 'admin') {
            $targetRoute = 'admin.dashboard';
        } elseif ($mustChangePassword) {
            $targetRoute = 'nasabah.dashboard';
        } else {
            $targetRoute = 'nasabah.profile.show';
        }

        $redirect = redirect()->route($targetRoute);
        $message = 'Kata sandi berhasil diperbarui.';

        if ($targetRoute === 'nasabah.profile.show') {
            return $redirect->with('password_change_success', $message);
        }

        return $redirect->with('success', $message);
    }

    /**
     * Tampilkan formulir lupa kata sandi (F-05).
     */
    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim tautan reset kata sandi ke email (F-05, Lampiran D).
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $throttleKey = 'reset_pass|' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak permintaan tautan reset. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        RateLimiter::hit($throttleKey, 600);

        // Keep the response identical whether or not the email is registered.
        $user = User::where('email', $request->email)->first();
        $status = Password::sendResetLink($request->only('email'));

        if ($user && $status === Password::RESET_LINK_SENT) {
            LogAktivitasService::catat(
                aksi: 'permintaan_reset_kata_sandi',
                keterangan: ['email' => $request->email]
            );
        }

        return back()->with('status', 'Jika email terdaftar, tautan untuk mengatur ulang kata sandi akan dikirim.');
    }

    /**
     * Tampilkan formulir reset kata sandi baru.
     */
    public function showResetForm(Request $request, string $token): View|RedirectResponse
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Proses reset kata sandi sekali pakai (F-05).
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'],
        ], [
            'password.regex' => 'Kata sandi harus minimal 8 karakter dan memuat kombinasi huruf dan angka.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                LogAktivitasService::catat(
                    aksi: 'reset_kata_sandi_berhasil',
                    objekTipe: 'User',
                    objekId: $user->id,
                    user: $user
                );
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors([
                'email' => 'Tautan reset tidak valid, sudah digunakan, atau sudah kedaluwarsa. Minta tautan baru.',
            ]);
        }

        return redirect()->route('login')->with('success', 'Kata sandi berhasil diatur ulang. Silakan masuk dengan kata sandi baru.');
    }
}
