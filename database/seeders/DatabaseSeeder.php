<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting: admin dulu karena harga_sampah.dibuat_oleh mengacu ke users.
        $this->call([
            AdminSeeder::class,
            JenisSampahSeeder::class,
            HargaSampahSeeder::class,
        ]);
    }
}
