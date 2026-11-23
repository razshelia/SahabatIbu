<?php

namespace App\Support;

use App\Models\JenisSampah;
use App\Models\LogAktivitas;
use App\Models\PenarikanDana;
use App\Models\TransaksiSetoran;
use App\Models\User;

/**
 * Data nyata untuk ruangan 3D (window.GARASI di layout).
 * Alat-alat 3D menampilkan angka yang sama dengan isi halaman, dan tiap alat
 * bisa diketuk untuk pindah ke menu yang sesuai.
 */
class GarasiData
{
    /** Versi aman: kalau ada masalah query, halaman tetap tampil dengan 3D polos. */
    public static function safe(User $u): array
    {
        try {
            return self::for($u);
        } catch (\Throwable $e) {
            report($e);

            return ['role' => $u->isAdmin() ? 'admin' : 'nasabah', 'uid' => $u->id, 'nama' => $u->name,
                'fokus' => 'home', 'links' => self::links($u->isAdmin()), 'label' => self::label($u->isAdmin()),
                'harga' => [], 'sig' => null];
        }
    }

    public static function for(User $u): array
    {
        $admin = $u->isAdmin();
        $rute = request()->route()?->getName() ?? '';

        $d = [
            'role'  => $admin ? 'admin' : 'nasabah',
            'uid'   => $u->id,
            'nama'  => $u->name,
            'fokus' => self::fokus($rute, $admin),
            'links' => self::links($admin),
            'label' => self::label($admin),
            'harga' => self::harga(),
        ];

        $d += $admin ? self::dataAdmin() : self::dataNasabah($u);

        // Tanda perubahan data. JS membandingkannya dengan kunjungan sebelumnya
        // untuk memutar animasi (setoran baru, penarikan berubah, harga berubah).
        $d['sig'] = [
            'setoran' => $admin ? ($d['admin']['terbaru']['k'] ?? '') : ($d['nasabah']['setoran'][0]['k'] ?? ''),
            'tarik'   => $admin ? $d['admin']['sig_tarik'] : implode('|', array_map(fn ($t) => $t['id'].':'.$t['st'], $d['nasabah']['tarik'] ?? [])),
            'harga'   => md5(json_encode($d['harga'])),
        ];

        return $d;
    }

    /** Alat mana yang disorot untuk halaman ini. */
    private static function fokus(string $rute, bool $admin): string
    {
        if ($admin) {
            return match (true) {
                str_starts_with($rute, 'admin.nasabah.')   => 'scan',
                str_starts_with($rute, 'admin.katalog.')   => 'rak',
                str_starts_with($rute, 'admin.setoran.')   => 'timbang',
                str_starts_with($rute, 'admin.penarikan.') => 'capit',
                str_starts_with($rute, 'admin.laporan.')   => 'riwayat',
                str_starts_with($rute, 'admin.profile.')   => 'struk',
                default                                    => 'home',
            };
        }

        return match (true) {
            $rute === 'nasabah.dashboard'                => 'scan',
            str_starts_with($rute, 'nasabah.setoran.')   => 'riwayat',
            str_starts_with($rute, 'nasabah.penarikan.') => 'capit',
            str_starts_with($rute, 'nasabah.katalog.')   => 'rak',
            str_starts_with($rute, 'nasabah.profile.')   => 'struk',
            default                                      => 'home',
        };
    }

    /** Alat yang diketuk -> halaman tujuan. */
    private static function links(bool $admin): array
    {
        return $admin ? [
            'scan'    => route('admin.nasabah.index'),
            'timbang' => route('admin.setoran.index'),
            'struk'   => route('admin.profile.show'),
            'riwayat' => route('admin.laporan.index'),
            'rak'     => route('admin.katalog.index'),
            'capit'   => route('admin.penarikan.index'),
        ] : [
            'scan'    => route('nasabah.dashboard'),
            'timbang' => route('nasabah.setoran.index'),
            'struk'   => route('nasabah.profile.show'),
            'riwayat' => route('nasabah.setoran.index'),
            'rak'     => route('nasabah.katalog.index'),
            'capit'   => route('nasabah.penarikan.index'),
        ];
    }

    /** Tulisan di papan kecil di atas tiap alat (bahasa sehari-hari). */
    private static function label(bool $admin): array
    {
        return $admin ? [
            'scan' => 'Nasabah', 'timbang' => 'Setoran', 'struk' => 'Profil & Log',
            'riwayat' => 'Laporan', 'rak' => 'Katalog Harga', 'capit' => 'Penarikan',
        ] : [
            'scan' => 'Cek Saldo', 'timbang' => 'Setoran Saya', 'struk' => 'Profil Saya',
            'riwayat' => 'Buku Tabungan', 'rak' => 'Daftar Harga', 'capit' => 'Ambil Uang',
        ];
    }

    private static function harga(): array
    {
        $out = [];
        foreach (JenisSampah::where('aktif', true)->orderBy('id')->get() as $j) {
            $kini = $j->hargaBerlaku();
            if (! $kini) {
                continue;
            }
            $lalu = $j->harga()
                ->whereDate('berlaku_mulai', '<', $kini->berlaku_mulai->toDateString())
                ->orderByDesc('berlaku_mulai')->first();

            $out[] = [
                'n' => $j->nama,
                'h' => (float) $kini->harga_per_kg,
                'p' => $lalu ? (float) $lalu->harga_per_kg : null,
                't' => $kini->berlaku_mulai->format('d/m/Y'),
            ];
        }

        return $out;
    }

    private static function setoran(TransaksiSetoran $t): array
    {
        return [
            'k'    => $t->kode,
            't'    => $t->tanggal?->format('d/m/Y'),
            'v'    => (float) $t->total_nilai,
            'kg'   => (float) $t->total_berat,
            'who'  => $t->nasabah?->user?->name,
            'rows' => $t->detailSetoran->map(fn ($x) => [
                'j'  => $x->jenisSampah?->nama,
                'kg' => (float) $x->berat_kg,
                'v'  => (float) $x->subtotal,
            ])->values()->all(),
        ];
    }

    private static function dataNasabah(User $u): array
    {
        $n = $u->nasabah;
        if (! $n) {
            return ['nasabah' => ['nomor' => '', 'saldo' => 0, 'tahan' => 0, 'tersedia' => 0, 'jumlah' => 0, 'berat' => 0, 'setoran' => [], 'tarik' => []]];
        }

        $berhasil = $n->transaksiSetoran()->where('status', 'berhasil');

        return ['nasabah' => [
            'nomor'    => $n->nomor_nasabah,
            'saldo'    => (float) $n->saldo,
            'tahan'    => (float) $n->saldo_ditahan,
            'tersedia' => (float) $n->saldo_tersedia,
            'jumlah'   => (clone $berhasil)->count(),
            'berat'    => (float) (clone $berhasil)->sum('total_berat'),
            'setoran'  => (clone $berhasil)->with('detailSetoran.jenisSampah', 'nasabah.user')
                ->latest('tanggal')->limit(4)->get()->map(fn ($t) => self::setoran($t))->all(),
            'tarik'    => $n->penarikanDana()->latest('diajukan_pada')->limit(3)->get()->map(fn ($p) => [
                'id' => $p->kode,
                'v'  => (float) $p->nominal,
                'st' => $p->status,
                'metode' => $p->metode,
            ])->all(),
        ]];
    }

    private static function dataAdmin(): array
    {
        $hari = fn () => TransaksiSetoran::where('status', 'berhasil')->whereDate('tanggal', today());
        $bulan = fn () => TransaksiSetoran::where('status', 'berhasil')
            ->whereBetween('tanggal', [now()->startOfMonth(), now()->endOfMonth()]);

        $grafik = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->startOfMonth()->subMonths($i);
            $grafik[] = [
                'b' => $m->format('M'),
                'v' => (float) TransaksiSetoran::where('status', 'berhasil')
                    ->whereBetween('tanggal', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])
                    ->sum('total_nilai'),
            ];
        }

        $terbaru = TransaksiSetoran::where('status', 'berhasil')
            ->with('detailSetoran.jenisSampah', 'nasabah.user')->latest('tanggal')->first();

        $antrian = PenarikanDana::with('nasabah.user')->whereIn('status', ['menunggu', 'disetujui'])
            ->orderBy('diajukan_pada')->limit(3)->get()->map(fn ($p) => [
                'k' => $p->kode, 'who' => $p->nasabah?->user?->name, 'v' => (float) $p->nominal, 'st' => $p->status, 'metode' => $p->metode,
            ])->all();

        return ['admin' => [
            'pendaftar'      => User::where('role', 'nasabah')->where('status', 'menunggu')->count(),
            'aktif'          => User::where('role', 'nasabah')->where('status', 'aktif')->count(),
            'nonaktif'       => User::where('role', 'nasabah')->where('status', 'nonaktif')->count(),
            'tarik_menunggu' => PenarikanDana::where('status', 'menunggu')->count(),
            'tarik_siap'     => PenarikanDana::where('status', 'disetujui')->count(),
            'hari_n'         => $hari()->count(),
            'hari_kg'        => (float) $hari()->sum('total_berat'),
            'hari_rp'        => (float) $hari()->sum('total_nilai'),
            'bulan_n'        => $bulan()->count(),
            'bulan_kg'       => (float) $bulan()->sum('total_berat'),
            'bulan_rp'       => (float) $bulan()->sum('total_nilai'),
            'grafik'         => $grafik,
            'terbaru'        => $terbaru ? self::setoran($terbaru) : null,
            'antrian'        => $antrian,
            'log'            => LogAktivitas::with('user')->latest('created_at')->limit(7)->get()->map(fn ($l) => [
                'w' => $l->created_at?->format('d/m H:i'), 'a' => $l->aksi, 'u' => $l->user?->name,
            ])->all(),
            'sig_tarik'      => PenarikanDana::orderByDesc('updated_at')->limit(5)->get()
                ->map(fn ($p) => $p->kode.':'.$p->status)->implode('|'),
        ]];
    }
}