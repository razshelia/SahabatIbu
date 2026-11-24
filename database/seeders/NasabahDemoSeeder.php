<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\PendaftaranService;
use Database\Seeders\Concerns\HanyaNonProduksi;
use Database\Seeders\Support\DemoRoster;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Nasabah dummy bertema K-pop (lihat DemoRoster). Hanya untuk pengembangan/pengujian.
 * TIDAK dipanggil dari DatabaseSeeder. Jalankan manual:
 *   php artisan db:seed --class=NasabahDemoSeeder     (hanya akun nasabah)
 *   php artisan db:seed --class=DemoSeeder            (lengkap: nasabah + setoran + penarikan)
 *
 * Akun dibuat lewat PendaftaranService (alur yang sama dengan aplikasi) dengan tanggal mundur,
 * jadi nomor nasabah, alamat, dan log aktivitas konsisten. Aman dijalankan ulang: email yang
 * sudah ada dilewati. Keadaan "nonaktif" diterapkan DemoSeeder setelah riwayat transaksi dibuat.
 */
class NasabahDemoSeeder extends Seeder
{
    use HanyaNonProduksi;

    public function run(): void
    {
        $this->tolakDiProduksi();

        $admin = $this->ambilAdmin();
        $service = app(PendaftaranService::class);
        $dibuat = 0;

        try {
            foreach (DemoRoster::nasabah() as $n) {
                if (User::where('email', $n['email'])->exists()) {
                    continue;   // aman dijalankan ulang
                }

                // Satu transaksi basis data per nasabah: kalau gagal di tengah, tidak ada akun setengah jadi.
                DB::transaction(function () use ($n, $admin, $service) {
                    $daftar = Carbon::now()->startOfDay()->subDays($n['hari_daftar'])->setTime(9, 15);
                    Carbon::setTestNow($daftar);

                    $data = [
                        'name'        => $n['nama'],
                        'email'       => $n['email'],
                        'password'    => DemoRoster::PASSWORD,
                        'telepon'     => $n['telepon'],
                        'kategori'    => $n['kategori'],
                        'blok'        => $n['blok'],
                        'nomor_rumah' => $n['nomor_rumah'],
                        'alamat'      => $n['alamat'],
                    ];

                    if ($n['cara'] === 'langsung' && $n['status'] === 'aktif') {
                        $service->tambahNasabahLangsung($data, $admin);

                        return;
                    }

                    $user = $service->registerMandiri($data);

                    if ($n['status'] === 'menunggu') {
                        return;
                    }

                    Carbon::setTestNow($daftar->copy()->addDay()->setTime(10, 30));
                    $service->verifikasiPendaftar($user, $n['status'] !== 'ditolak', $n['alasan'], $admin);
                });

                $dibuat++;
            }
        } finally {
            Carbon::setTestNow();
        }

        $this->command?->info("NasabahDemoSeeder: {$dibuat} nasabah dummy dibuat (kata sandi semua akun: " . DemoRoster::PASSWORD . ').');
    }
}
