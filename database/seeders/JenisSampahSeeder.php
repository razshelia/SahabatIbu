<?php

namespace Database\Seeders;

use App\Models\JenisSampah;
use Illuminate\Database\Seeder;

class JenisSampahSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Kertas', 'Botol Plastik', 'Logam'] as $nama) {
            JenisSampah::firstOrCreate(['nama' => $nama], ['satuan' => 'kg', 'aktif' => true]);
        }
    }
}
