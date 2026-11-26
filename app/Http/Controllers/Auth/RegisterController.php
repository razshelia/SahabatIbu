<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PendaftaranService;
use App\Support\AlamatNasabah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        protected PendaftaranService $pendaftaranService
    ) {}

    /**
     * Tampilkan halaman registrasi.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('auth.register');
    }

    /**
     * Proses pendaftaran mandiri nasabah baru (F-34, F-42).
     */
    public function register(Request $request): RedirectResponse
    {
        $throttleKey = 'register|' . $request->ip();

        // Batas 10 registrasi per jam per IP (Lampiran D)
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak permintaan pendaftaran. Coba lagi dalam {$seconds} detik.",
            ])->withInput();
        }

        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => Str::lower(trim($email))]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'telepon' => ['required', 'string', 'max:20', 'regex:/^[0-9+]+$/'],
            ...AlamatNasabah::rules(),
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'],
            'persetujuan_privasi' => ['accepted'],
        ], [
            ...AlamatNasabah::pesan(),
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'telepon.regex' => 'Format nomor telepon hanya boleh berisi angka dan tanda +.',
            'password.regex' => 'Kata sandi harus terdiri dari minimal 8 karakter dan memuat kombinasi huruf dan angka.',
            'persetujuan_privasi.accepted' => 'Anda wajib menyetujui Kebijakan Privasi dan pemrosesan data pribadi untuk melanjutkan.',
        ]);

        RateLimiter::hit($throttleKey, 3600);

        $user = $this->pendaftaranService->registerMandiri($validated);

        Auth::login($user);

        return redirect()->route('account.status')
            ->with('success', 'Pendaftaran Ibu sudah dikirim. Pengurus akan memeriksa data sebelum akun diaktifkan.');
    }
}
