<?php

namespace Database\Seeders;

use App\Models\HargaSampah;
use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\TransaksiSetoran;
use App\Models\User;
use App\Services\TransaksiSetoranService;
use Database\Seeders\Concerns\HanyaNonProduksi;
use Database\Seeders\Support\DemoRoster;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * Jenis sampah tambahan, riwayat harga, dan riwayat setoran untuk nasabah demo (hanya pengembangan).
 *
 * Setoran dibuat lewat TransaksiSetoranService dengan tanggal mundur, sehingga harga yang dipakai
 * adalah harga yang berlaku PADA tanggal itu (riwayat harga naik/turun ikut terbentuk), saldo
 * bertambah dengan benar, dan log aktivitas tercatat. Beberapa setoran sengaja dibatalkan (BR-10).
 * Hasilnya sama setiap dijalankan (angka acak memakai benih tetap). Nasabah yang sudah punya
 * setoran dilewati, jadi aman dijalankan ulang.
 */
class SetoranDemoSeeder extends Seeder
{
    use HanyaNonProduksi;

    private const BENIH = 2611;

    /** Setoran ke-n milik nasabah (email) yang dibatalkan sebentar setelah dicatat. */
    private const DIBATALKAN = [
        'jisoo.blackpink@example.test'     => 2,
        'hanni.newjeans@example.test'      => 3,
        'karina.aespa@example.test'        => 2,
        'jungkook.bts@example.test'        => 4,
    ];

    /** Nasabah yang punya setoran hari ini (agar "setoran hari ini" di dashboard admin terisi). */
    private const SETORAN_HARI_INI = [
        'jungkook.bts@example.test',
        'jisoo.blackpink@example.test',
        'jimin.bts@example.test',
    ];

    public function run(): void
    {
        $this->tolakDiProduksi();

        $this->call([JenisSampahSeeder::class, HargaSampahSeeder::class]);

        $admin = $this->ambilAdmin();
        $jenis = $this->siapkanJenisDanHarga($admin);
        $layanan = app(TransaksiSetoranService::class);

        $peristiwa = $this->susunPeristiwa();
        if ($peristiwa === []) {
            $this->command?->warn('SetoranDemoSeeder: tidak ada nasabah demo yang perlu diisi (jalankan NasabahDemoSeeder dulu, atau data sudah ada).');

            return;
        }

        $dibuat = 0;
        $dibatalkan = 0;

        try {
            foreach ($peristiwa as $p) {
                Carbon::setTestNow($p['waktu']);

                $items = [];
                foreach ($p['items'] as $nama => $berat) {
                    $items[] = ['jenis_sampah_id' => $jenis[$nama]->id, 'berat_kg' => $berat];
                }

                $transaksi = $layanan->simpanSetoran($p['nasabah_id'], $items, $admin);
                $dibuat++;

                if ($p['batal'] !== null) {
                    Carbon::setTestNow($p['waktu']->copy()->addMinutes(35));
                    $layanan->batalkanSetoran($transaksi, $p['batal'], $admin);
                    $dibatalkan++;
                }
            }
        } finally {
            Carbon::setTestNow();
        }

        $this->command?->info("SetoranDemoSeeder: {$dibuat} setoran dibuat ({$dibatalkan} di antaranya dibatalkan).");
    }

    /**
     * Pastikan jenis demo ada dan seluruh jenis punya riwayat harga sejak sebelum setoran tertua.
     *
     * @return array<string, JenisSampah> nama => jenis (hanya yang aktif dan boleh disetor)
     */
    private function siapkanJenisDanHarga(User $admin): array
    {
        $riwayat = DemoRoster::RIWAYAT_JENIS_BAWAAN;

        foreach (DemoRoster::JENIS_TAMBAHAN as $nama => [$aktif, $harga]) {
            JenisSampah::firstOrCreate(['nama' => $nama], ['satuan' => 'kg', 'aktif' => $aktif]);
            $riwayat[$nama] = $harga;
        }

        foreach ($riwayat as $nama => $daftarHarga) {
            $jenis = JenisSampah::where('nama', $nama)->firstOrFail();

            foreach ($daftarHarga as $hariLalu => $harga) {
                $tanggal = Carbon::now()->subDays($hariLalu)->toDateString();

                // whereDate (bukan firstOrCreate dengan tanggal polos): di SQLite kolom tersimpan
                // sebagai "Y-m-d 00:00:00" sehingga pencarian "Y-m-d" tidak menemukannya dan
                // menabrak unique(jenis_sampah_id, berlaku_mulai) saat seeder dijalankan ulang.
                $sudah = HargaSampah::where('jenis_sampah_id', $jenis->id)->whereDate('berlaku_mulai', $tanggal)->exists();
                if (! $sudah) {
                    HargaSampah::create([
                        'jenis_sampah_id' => $jenis->id,
                        'harga_per_kg'    => $harga,
                        'berlaku_mulai'   => $tanggal,
                        'dibuat_oleh'     => $admin->id,
                    ]);
                }
            }
        }

        return JenisSampah::where('aktif', true)->whereIn('nama', array_keys(DemoRoster::KISARAN_BERAT))
            ->get()->keyBy('nama')->all();
    }

    /**
     * Susun semua setoran (tanggal, nasabah, isi) lalu urutkan dari yang paling lama.
     *
     * @return list<array{waktu:Carbon,nasabah_id:int,items:array<string,float>,batal:?string}>
     */
    private function susunPeristiwa(): array
    {
        mt_srand(self::BENIH);

        $nama = array_keys(DemoRoster::KISARAN_BERAT);
        $sekarang = Carbon::now();
        $acara = [];
        $alasan = DemoRoster::ALASAN_BATAL_SETORAN;
        $noAlasan = 0;

        foreach (DemoRoster::nasabah() as $n) {
            if ($n['setoran'] < 1) {
                continue;
            }

            $user = User::where('email', $n['email'])->first();
            $nasabah = $user ? Nasabah::where('user_id', $user->id)->first() : null;

            if (! $nasabah) {
                $this->command?->warn("Lewati {$n['email']}: belum ada (jalankan NasabahDemoSeeder).");
                continue;
            }

            if (TransaksiSetoran::where('nasabah_id', $nasabah->id)->exists()) {
                continue;   // sudah punya riwayat, jangan digandakan
            }

            // Tanggal: setoran pertama 55-68 hari lalu (setelah akun diverifikasi), sisanya acak.
            // Nasabah yang akan nonaktif berhenti menyetor sejak 20 hari lalu (sebelum penarikan terakhir mereka).
            $terbaru = $n['berhenti'] ? 20 : 0;
            $hari = [mt_rand(55, 68)];
            for ($i = 1; $i < $n['setoran']; $i++) {
                $hari[] = mt_rand($terbaru, 55);
            }
            if (in_array($n['email'], self::SETORAN_HARI_INI, true)) {
                $hari[count($hari) - 1] = 0;
            }
            rsort($hari);   // dari yang paling lama

            foreach ($hari as $urutan => $hariLalu) {
                $waktu = $sekarang->copy()->startOfDay()->subDays($hariLalu)->setTime(mt_rand(8, 16), mt_rand(0, 59));
                if ($waktu->greaterThan($sekarang->copy()->subMinutes(45))) {
                    $waktu = $sekarang->copy()->subMinutes(45 + mt_rand(0, 60));   // jangan di masa depan
                }

                $batal = null;
                if ((self::DIBATALKAN[$n['email']] ?? null) === $urutan + 1) {
                    $batal = $alasan[$noAlasan++ % count($alasan)];
                }

                $acara[] = [
                    'waktu'      => $waktu,
                    'nasabah_id' => $nasabah->id,
                    'items'      => $this->isiSetoran($nama),
                    'batal'      => $batal,
                ];
            }
        }

        mt_srand();   // kembalikan ke acak biasa

        usort($acara, fn ($a, $b) => $a['waktu']->getTimestamp() <=> $b['waktu']->getTimestamp());

        return $acara;
    }

    /** @return array<string, float> nama jenis => berat kg (kelipatan 0,25) */
    private function isiSetoran(array $nama): array
    {
        $r = mt_rand(1, 100);
        $banyak = $r <= 30 ? 1 : ($r <= 65 ? 2 : ($r <= 90 ? 3 : 4));

        $pilihan = $nama;
        shuffle($pilihan);

        $items = [];
        foreach (array_slice($pilihan, 0, $banyak) as $jenis) {
            [$min, $max] = DemoRoster::KISARAN_BERAT[$jenis];
            $berat = round((mt_rand((int) ($min * 4), (int) ($max * 4))) / 4, 2);
            $items[$jenis] = max(0.25, $berat);
        }

        return $items;
    }
}
