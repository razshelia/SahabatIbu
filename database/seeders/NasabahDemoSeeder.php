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
            ['Siti Aminah', 'siti@example.test', 'aktif'],
            ['Dewi Lestari', 'dewi@example.test', 'aktif'],
            ['Rina Wulandari', 'rina@example.test', 'menunggu'],
        ];

        foreach ($data as $i => [$nama, $email, $status]) {
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
                'telepon'       => '0812000000' . ($i + 1),
                'alamat'        => 'Perumahan Taman Gading, Jember',
            ]);
        }
    }
}
