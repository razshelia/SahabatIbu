<?php

namespace Database\Seeders\Concerns;

use App\Models\User;
use RuntimeException;

/**
 * Dipakai seeder data demo. Akun demo berkata sandi yang diketahui umum, jadi tidak boleh
 * pernah masuk ke production.
 */
trait HanyaNonProduksi
{
    protected function tolakDiProduksi(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('Seeder data demo tidak boleh dijalankan di production (APP_ENV=production).');
        }
    }

    protected function ambilAdmin(): User
    {
        return User::where('role', 'admin')->orderBy('id')->first()
            ?? throw new RuntimeException('Admin belum ada. Isi ADMIN_EMAIL/ADMIN_PASSWORD di .env lalu jalankan `php artisan db:seed` '
                . '(atau langsung `php artisan db:seed --class=DemoSeeder`).');
    }
}
