<?php

namespace Database\Seeders;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Database\Seeder;

// Data uji untuk pengembangan. TIDAK dipanggil dari DatabaseSeeder.
// Jalankan manual: php artisan db:seed --class=NasabahDemoSeeder
class NasabahDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $data = [
            ['Siti Aminah',     'siti@example.test',   'aktif'],
            ['Dewi Lestari',    'dewi@example.test',   'aktif'],
            ['Rina Wulandari',  'rina@example.test',   'menunggu'],
            ['Ani Rahayu',      'ani@example.test',    'aktif'],
            ['Budi Santoso',    'budi@example.test',   'aktif'],
            ['Putri Maharani',  'putri@example.test',  'menunggu'],
            ['Joko Prasetyo',   'joko@example.test',   'aktif'],
            ['Sri Wahyuni',     'sri@example.test',    'aktif'],
            ['Agus Setiawan',   'agus@example.test',   'menunggu'],
            ['Maya Anggraini',  'maya@example.test',   'aktif'],
        ];

        foreach ($data as $i => [$nama, $email, $status]) {
            // Lewati kalau email sudah ada, supaya seeder aman dijalankan ulang
            if (User::where('email', $email)->exists()) {
                continue;
            }

            $blok  = chr(65 + $i % 5);   // A-E
            $nomor = 10 + $i;

            $user = new User();
            $user->forceFill([
                'name'              => $nama,
                'email'             => $email,
                'password'          => 'Password123!',
                'role'              => 'nasabah',
                'status'            => $status,
                'email_verified_at' => now(),
                'verified_by'       => $status === 'aktif' ? $admin->id : null,
                'verified_at'       => $status === 'aktif' ? now() : null,
            ])->save();

            Nasabah::create([
                'user_id'       => $user->id,
                'nomor_nasabah' => sprintf('NSB-%04d', $i + 1),
                'telepon'       => sprintf('08120000%04d', $i + 1),
                'kategori'      => 'warga',
                'blok'          => $blok,
                'nomor_rumah'   => (string) $nomor,
                'alamat'        => "Blok {$blok} No. {$nomor}, Perumahan Taman Gading",
            ]);
        }
    }
}