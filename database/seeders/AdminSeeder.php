<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $nama     = (string) config('bank_sampah.admin.nama');
        $email    = (string) config('bank_sampah.admin.email');
        $password = (string) config('bank_sampah.admin.password');
        $reset    = (bool) config('bank_sampah.admin.reset_password');

        if ($email === '' || $password === '') {
            throw new RuntimeException('Isi ADMIN_EMAIL dan ADMIN_PASSWORD di berkas .env sebelum menjalankan seeder '
                . '(jalankan `php artisan config:clear` jika baru mengubah .env).');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("ADMIN_EMAIL \"{$email}\" bukan alamat email yang valid.");
        }

        // Sama dengan kebijakan kata sandi aplikasi: minimal 8 karakter, kombinasi huruf dan angka.
        if (strlen($password) < 8 || ! preg_match('/[A-Za-z]/', $password) || ! preg_match('/\d/', $password)) {
            throw new RuntimeException('ADMIN_PASSWORD minimal 8 karakter dan harus kombinasi huruf dan angka.');
        }

        $admin = User::where('email', $email)->first();

        if ($admin && $admin->role !== 'admin') {
            // Jangan diam-diam menaikkan akun nasabah menjadi admin hanya karena emailnya sama.
            throw new RuntimeException("Email {$email} sudah dipakai akun {$admin->role}. Gunakan email lain untuk ADMIN_EMAIL.");
        }

        if ($admin && ! $reset) {
            // Seeder boleh dijalankan ulang (mis. saat deploy) tanpa menimpa kata sandi yang sudah diganti admin.
            $this->command?->info("Admin {$email} sudah ada, tidak diubah.");

            return;
        }

        // Admin hanya dibuat lewat seeder (BR-14). forceFill dipakai karena role/status
        // sengaja tidak ada di $fillable.
        $admin ??= new User();

        if (! $admin->exists) {
            $admin->name = $nama;
        }

        $admin->forceFill([
            'email'                => $email,
            'password'             => $password,           // di-hash otomatis oleh cast 'hashed'
            'role'                 => 'admin',
            'status'               => 'aktif',
            'must_change_password' => true,                // wajib ganti saat login pertama
            'email_verified_at'    => $admin->email_verified_at ?? now(),
        ])->save();

        $this->command?->info($reset ? "Kata sandi admin {$email} direset." : "Admin {$email} dibuat.");
    }
}
