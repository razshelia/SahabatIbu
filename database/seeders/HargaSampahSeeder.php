<?php

namespace Database\Seeders;

use App\Models\HargaSampah;
use App\Models\JenisSampah;
use App\Models\User;
use Illuminate\Database\Seeder;

class HargaSampahSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        // CONTOH harga. Ganti dengan harga yang ditetapkan pengurus Bank Sampah Sahabat Ibu.
        $harga = ['Kertas' => 1500, 'Botol Plastik' => 3000, 'Logam' => 5000];

        foreach ($harga as $nama => $nilai) {
            $jenis = JenisSampah::where('nama', $nama)->firstOrFail();

            HargaSampah::firstOrCreate(
                ['jenis_sampah_id' => $jenis->id, 'berlaku_mulai' => now()->toDateString()],
                ['harga_per_kg' => $nilai, 'dibuat_oleh' => $admin->id]
            );
        }
    }
}
