<?php

namespace Database\Seeders;

use App\Models\Nasabah;
use App\Models\PenarikanDana;
use App\Models\TransaksiSetoran;
use App\Models\User;
use App\Services\PenarikanDanaService;
use Database\Seeders\Concerns\HanyaNonProduksi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Pengajuan penarikan dana dummy dengan semua status (hanya pengembangan/pengujian).
 *
 * Dibuat lewat PenarikanDanaService dengan tanggal mundur: saldo ditahan, saldo berkurang saat
 * dicairkan, dan log aktivitas mengikuti aturan aplikasi (BR-06, BR-07, BR-09). Nominal dihitung
 * dari saldo nasabah PADA tanggal pengajuan. Nasabah yang sudah punya penarikan dilewati.
 */
class PenarikanDemoSeeder extends Seeder
{
    use HanyaNonProduksi;

    private const ALASAN_TOLAK_KAS = 'Kas tunai pengurus belum tersedia hari ini (pengurus sedang di Surabaya nonton konser). Silakan ajukan lagi hari Senin.';
    private const ALASAN_TOLAK_REKENING = 'Nomor rekening/e-wallet tidak valid. Mohon cek lagi lalu ajukan ulang.';

    /**
     * email, hasil akhir, metode, bagian saldo yang ditarik (1 = semua), berapa hari lalu diajukan, rekening (bank, nomor)
     */
    private const RENCANA = [
        ['jungkook.bts@example.test',     'dicairkan', 'tunai',    0.50, 30, null],
        ['jungkook.bts@example.test',     'menunggu',  'transfer', 0.30, 1,  ['BCA', '8830214467']],
        ['jisoo.blackpink@example.test',  'dicairkan', 'transfer', 0.60, 21, ['BRI', '002301045678509']],
        ['jennie.blackpink@example.test', 'dicairkan', 'tunai',    0.40, 25, null],
        ['jennie.blackpink@example.test', 'disetujui', 'tunai',    0.30, 2,  null],
        ['lisa.blackpink@example.test',   'dicairkan', 'transfer', 0.50, 16, ['GoPay', '081230004455']],
        ['jimin.bts@example.test',        'dicairkan', 'tunai',    0.50, 18, null],
        ['jimin.bts@example.test',        'ditolak',   'tunai',    0.30, 6,  null],
        ['hoseok.bts@example.test',       'disetujui', 'transfer', 0.40, 1,  ['BNI', '0457812390']],
        ['minji.newjeans@example.test',   'menunggu',  'tunai',    0.35, 0,  null],
        ['hanni.newjeans@example.test',   'dibatalkan', 'tunai',   0.20, 4,  null],
        ['karina.aespa@example.test',     'dicairkan', 'tunai',    0.50, 12, null],
        ['winter.aespa@example.test',     'ditolak',   'transfer', 0.50, 3,  ['DANA', '08129998877']],
        ['rose.blackpink@example.test',   'menunggu',  'transfer', 0.30, 2,  ['Mandiri', '1400012345678']],
        ['seokjin.bts@example.test',      'dicairkan', 'tunai',    1.00, 14, null],   // dikosongkan -> nanti nonaktif, bisa dianonimkan
        ['yoongi.bts@example.test',       'dicairkan', 'tunai',    0.60, 15, null],   // masih ada sisa saldo
    ];

    public function run(): void
    {
        $this->tolakDiProduksi();

        $admin = $this->ambilAdmin();
        $layanan = app(PenarikanDanaService::class);
        $sekarang = Carbon::now();

        // Urutkan dari yang paling lama supaya saldo per tanggal masuk akal.
        $rencana = collect(self::RENCANA)->sortByDesc(fn ($r) => $r[4])->values();

        mt_srand(2612);   // menit pengajuan tetap sama setiap dijalankan

        $hitung = array_fill_keys(['menunggu', 'disetujui', 'dicairkan', 'ditolak', 'dibatalkan'], 0);
        $sudahPunya = [];

        try {
            foreach ($rencana as [$email, $hasil, $metode, $bagian, $hariLalu, $rekening]) {
                $nasabah = $this->cariNasabah($email);
                if (! $nasabah) {
                    $this->command?->warn("Lewati {$email}: nasabah belum ada.");
                    continue;
                }

                // Lewati bila nasabah ini sudah punya penarikan sebelum seeder ini dijalankan.
                $sudahPunya[$nasabah->id] ??= PenarikanDana::where('nasabah_id', $nasabah->id)->exists();
                if ($sudahPunya[$nasabah->id]) {
                    continue;
                }

                $diajukan = $sekarang->copy()->startOfDay()->subDays($hariLalu)->setTime(10, mt_rand(0, 59));
                if ($diajukan->greaterThan($sekarang->copy()->subMinutes(30))) {
                    $diajukan = $sekarang->copy()->subMinutes(30);
                }

                $nominal = $this->hitungNominal($nasabah, $diajukan, $bagian);
                if ($nominal === null) {
                    $this->command?->warn("Lewati {$email}: saldo pada tanggal itu terlalu kecil.");
                    continue;
                }

                Carbon::setTestNow($diajukan);
                $penarikan = $layanan->ajukanPenarikan(
                    $nasabah->fresh(),
                    $nominal,
                    $metode,
                    $rekening[0] ?? null,
                    $rekening[1] ?? null,
                    $metode === 'transfer' ? $nasabah->user->name : null,
                );

                $this->selesaikan($layanan, $penarikan, $nasabah, $admin, $hasil, $metode, $diajukan, $sekarang);
                $hitung[$hasil]++;
            }
        } finally {
            Carbon::setTestNow();
            mt_srand();
        }

        $this->command?->info('PenarikanDemoSeeder: ' . array_sum($hitung) . ' pengajuan dibuat ('
            . collect($hitung)->map(fn ($j, $s) => "{$j} {$s}")->implode(', ') . ').');
    }

    private function cariNasabah(string $email): ?Nasabah
    {
        $user = User::where('email', $email)->first();

        return $user ? Nasabah::where('user_id', $user->id)->first() : null;
    }

    /**
     * Saldo nasabah pada tanggal tertentu (setoran berhasil sampai tanggal itu dikurangi penarikan
     * yang masih berjalan atau sudah cair), lalu ambil sebagian. Pembulatan ke kelipatan Rp 5.000
     * (sama dengan tombol di form), kecuali "semua saldo" yang dipakai persis.
     */
    private function hitungNominal(Nasabah $nasabah, Carbon $pada, float $bagian): ?float
    {
        $masuk = (float) TransaksiSetoran::where('nasabah_id', $nasabah->id)
            ->where('status', 'berhasil')->where('tanggal', '<=', $pada)->sum('total_nilai');
        $keluar = (float) PenarikanDana::where('nasabah_id', $nasabah->id)
            ->whereIn('status', ['menunggu', 'disetujui', 'dicairkan'])->sum('nominal');
        $tersedia = $masuk - $keluar;

        if ($bagian >= 1.0) {
            return $tersedia >= 5000 ? round($tersedia, 2) : null;
        }

        $nominal = floor(($tersedia * $bagian) / 5000) * 5000;

        return $nominal >= 5000 ? (float) $nominal : null;
    }

    private function selesaikan(
        PenarikanDanaService $layanan,
        PenarikanDana $penarikan,
        Nasabah $nasabah,
        User $admin,
        string $hasil,
        string $metode,
        Carbon $diajukan,
        Carbon $sekarang,
    ): void {
        $paling = fn (Carbon $t) => $t->greaterThan($sekarang) ? $sekarang->copy()->subMinutes(5) : $t;

        match ($hasil) {
            'menunggu' => null,

            'dibatalkan' => $this->pada($paling($diajukan->copy()->addHours(3)), fn () => $layanan->batalkanOlehNasabah($penarikan->fresh(), $nasabah->fresh())),

            'ditolak' => $this->pada($paling($diajukan->copy()->addHours(5)), fn () => $layanan->tolakPenarikan(
                $penarikan->fresh(),
                $metode === 'transfer' ? self::ALASAN_TOLAK_REKENING : self::ALASAN_TOLAK_KAS,
                $admin,
            )),

            'disetujui' => $this->pada($paling($diajukan->copy()->addHours(4)), fn () => $layanan->setujuiPenarikan($penarikan->fresh(), $admin)),

            'dicairkan' => (function () use ($layanan, $penarikan, $admin, $metode, $diajukan, $paling) {
                $this->pada($paling($diajukan->copy()->addHours(4)), fn () => $layanan->setujuiPenarikan($penarikan->fresh(), $admin));
                $this->pada($paling($diajukan->copy()->addDay()->setTime(13, 20)), fn () => $layanan->cairkanPenarikan(
                    $penarikan->fresh(),
                    $admin,
                    $metode === 'transfer' ? 'Transfer ' . $penarikan->nama_bank . ', bukti #' . strtoupper(substr(md5($penarikan->kode), 0, 8)) : 'Diserahkan tunai di pos pengurus',
                ));
            })(),
        };
    }

    private function pada(Carbon $waktu, callable $aksi): void
    {
        Carbon::setTestNow($waktu);
        $aksi();
    }
}
