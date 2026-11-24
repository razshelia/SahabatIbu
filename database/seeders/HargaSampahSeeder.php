<?php

namespace Database\Seeders;

use App\Models\HargaSampah;
use App\Models\JenisSampah;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class HargaSampahSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->orderBy('id')->first()
            ?? throw new RuntimeException('Admin belum ada. Jalankan AdminSeeder dulu.');

        // CONTOH harga awal. Ganti dengan harga yang ditetapkan pengurus Bank Sampah Sahabat Ibu.
        $harga = ['Kertas' => 1500, 'Botol Plastik' => 3000, 'Logam' => 5000];

        foreach ($harga as $nama => $nilai) {
            $jenis = JenisSampah::where('nama', $nama)->firstOrFail();

            // Harga yang berlaku = berlaku_mulai terbaru. Jika jenis ini sudah punya riwayat harga
            // (misalnya ditetapkan pengurus), JANGAN menambah harga contoh: baris baru dengan tanggal
            // lebih baru akan diam-diam menimpa harga asli dan mengubah nilai setoran.
            if (HargaSampah::where('jenis_sampah_id', $jenis->id)->exists()) {
                continue;
            }

            HargaSampah::create([
                'jenis_sampah_id' => $jenis->id,
                'harga_per_kg'    => $nilai,
                'berlaku_mulai'   => now()->toDateString(),
                'dibuat_oleh'     => $admin->id,
            ]);
        }
    }
}
