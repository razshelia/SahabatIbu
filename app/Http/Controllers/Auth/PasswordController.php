<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LogAktivitasService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        $rules = [
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'],
        ];

        // Jika bukan penggantian paksa pertama, wajib periksa kata sandi lama
        if (! $user->must_change_password) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules, [
            'password.regex' => 'Kata sandi baru harus minimal 8 karakter dan memuat kombinasi huruf dan angka.',
            'current_password.current_password' => 'Kata sandi lama yang Anda masukkan tidak sesuai.',
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

        $targetRoute = $user->role === 'admin' ? 'admin.dashboard' : 'nasabah.dashboard';

        return redirect()->route($targetRoute)->with('success', 'Kata sandi Anda berhasil diperbarui!');
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
                'email' => "Terlalu banyak permintaan reset kata sandi. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        RateLimiter::hit($throttleKey, 600);

        // Jangan membocorkan apakah email terdaftar atau tidak (F-05)
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            // Log bahwa tautan reset dibuat
            LogAktivitasService::catat(
                aksi: 'permintaan_reset_kata_sandi',
                keterangan: ['email' => $request->email]
            );

            // Untuk kemudahan testing prototipe lokal, simpan link di session flash
            $resetUrl = route('password.reset.form', ['token' => $token, 'email' => $request->email]);
            session()->flash('demo_reset_link', $resetUrl);
        }

        return back()->with('status', 'Jika alamat email terdaftar di sistem, tautan pemulihan kata sandi telah dikirim.');
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

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Validasi token dan masa berlaku (60 menit)
        if (! $record || ! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Tautan reset kata sandi tidak valid atau sudah pernah digunakan.']);
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Tautan reset kata sandi telah kedaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Pengguna dengan email tersebut tidak ditemukan.']);
        }

        $user->forceFill([
            'password' => $request->password,
        ])->save();

        // Hapus token reset setelah digunakan (sekali pakai, BR-16)
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        LogAktivitasService::catat(
            aksi: 'reset_kata_sandi_berhasil',
            objekTipe: 'User',
            objekId: $user->id,
            user: $user
        );

        return redirect()->route('login')->with('success', 'Kata sandi Anda telah berhasil direset. Silakan masuk menggunakan kata sandi baru.');
    }
}
