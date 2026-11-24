<?php

namespace Database\Seeders;

use App\Models\LogAktivitas;
use App\Models\Nasabah;
use App\Models\PenarikanDana;
use App\Models\TransaksiSetoran;
use App\Models\User;
use App\Services\PendaftaranService;
use Database\Seeders\Concerns\HanyaNonProduksi;
use Database\Seeders\Support\DemoRoster;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Data dummy lengkap bertema K-pop untuk pengembangan dan pengujian.
 *
 *   php artisan migrate:fresh --seed                    (admin + jenis + harga awal)
 *   php artisan db:seed --class=DemoSeeder              (isi data dummy)
 *
 * TIDAK dipanggil dari DatabaseSeeder dan menolak berjalan di production. Aman dijalankan ulang
 * (data yang sudah ada dilewati). Semua akun nasabah demo berkata sandi DemoRoster::PASSWORD.
 */
class DemoSeeder extends Seeder
{
    use HanyaNonProduksi;

    public function run(): void
    {
        $this->tolakDiProduksi();

        $this->call([
            AdminSeeder::class,          // aman dijalankan ulang: tidak menimpa admin yang sudah ada
            JenisSampahSeeder::class,
            HargaSampahSeeder::class,
            NasabahDemoSeeder::class,
            SetoranDemoSeeder::class,
            PenarikanDemoSeeder::class,
        ]);

        $this->nonaktifkanNasabahBerhenti();
        $this->ringkasan();
    }

    /** Nasabah "nonaktif" baru dinonaktifkan setelah riwayat setoran dan penarikannya terbentuk. */
    private function nonaktifkanNasabahBerhenti(): void
    {
        $admin = $this->ambilAdmin();
        $service = app(PendaftaranService::class);

        try {
            foreach (DemoRoster::nasabah() as $n) {
                if ($n['status'] !== 'nonaktif') {
                    continue;
                }

                $user = User::where('email', $n['email'])->first();
                if ($user && $user->status === 'aktif') {
                    Carbon::setTestNow(Carbon::now()->startOfDay()->subDays(3)->setTime(15, 0));
                    $service->toggleStatusAktif($user, $admin);
                }
            }
        } finally {
            Carbon::setTestNow();
        }
    }

    private function ringkasan(): void
    {
        if (! $this->command) {
            return;
        }

        $status = User::where('role', 'nasabah')->selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');
        $tarik = PenarikanDana::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');

        $this->command->newLine();
        $this->command->info('Data demo siap.');
        $this->command->line('  Nasabah   : ' . Nasabah::count() . ' (' . $status->map(fn ($n, $s) => "{$n} {$s}")->implode(', ') . ')');
        $this->command->line('  Setoran   : ' . TransaksiSetoran::where('status', 'berhasil')->count() . ' berhasil, '
            . TransaksiSetoran::where('status', 'dibatalkan')->count() . ' dibatalkan');
        $this->command->line('  Penarikan : ' . PenarikanDana::count() . ' (' . $tarik->map(fn ($n, $s) => "{$n} {$s}")->implode(', ') . ')');
        $this->command->line('  Log       : ' . LogAktivitas::count() . ' catatan aktivitas');
        $this->command->newLine();
        $this->command->line('  Login nasabah contoh : jungkook.bts@example.test  /  ' . DemoRoster::PASSWORD);
        $this->command->line('  Nasabah menunggu     : hyein.newjeans@example.test, ningning.aespa@example.test, sana.twice@example.test, chaeyoung.twice@example.test');
        $this->command->line('  Nonaktif (saldo 0)   : seokjin.bts@example.test  -> bisa diuji anonimisasi');
    }
}
