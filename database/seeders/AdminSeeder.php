<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            throw new \RuntimeException('Isi ADMIN_EMAIL dan ADMIN_PASSWORD di berkas .env sebelum menjalankan seeder.');
        }

        // Admin hanya dibuat lewat seeder (BR-14). forceFill dipakai karena role/status
        // sengaja tidak ada di $fillable.
        $admin = User::where('email', $email)->first() ?? new User();
        $admin->forceFill([
            'name'                 => env('ADMIN_NAME', 'Admin Bank Sampah'),
            'email'                => $email,
            'password'             => $password,          // di-hash otomatis oleh cast 'hashed'
            'role'                 => 'admin',
            'status'               => 'aktif',
            'must_change_password' => true,                // wajib ganti saat login pertama
            'email_verified_at'    => now(),
        ])->save();
    }
}
