<?php

namespace App\Services;

use App\Models\Nasabah;
use App\Models\PenarikanDana;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PenarikanDanaService
{
    /**
     * Generate kode penarikan unik. Format: TRK-YYYYMMDD-XXXX.
     */
    public static function generateKode(): string
    {
        $datePrefix = 'TRK-' . now()->format('Ymd') . '-';
        $countToday = PenarikanDana::where('kode', 'like', $datePrefix . '%')->count() + 1;

        do {
            $kode = $datePrefix . sprintf('%04d', $countToday);
            $exists = PenarikanDana::where('kode', $kode)->exists();
            if ($exists) {
                $countToday++;
            }
        } while ($exists);

        return $kode;
    }

    /**
     * Nasabah mengajukan penarikan dana (F-25, F-26, BR-06, BR-07).
     */
    public function ajukanPenarikan(Nasabah $nasabah, float $nominal): PenarikanDana
    {
        if ($nominal <= 0) {
            throw new RuntimeException('Nominal penarikan harus lebih besar dari 0.');
        }

        $nominalFormatted = number_format($nominal, 2, '.', '');

        return DB::transaction(function () use ($nasabah, $nominalFormatted) {
            $lockedNasabah = Nasabah::where('id', $nasabah->id)->lockForUpdate()->firstOrFail();

            if ($lockedNasabah->user->status !== 'aktif') {
                throw new RuntimeException('Akun nasabah harus aktif untuk mengajukan penarikan dana.');
            }

            $saldoTersedia = bcsub((string) $lockedNasabah->saldo, (string) $lockedNasabah->saldo_ditahan, 2);

            if (bccomp($nominalFormatted, $saldoTersedia, 2) > 0) {
                throw new RuntimeException('Nominal penarikan (Rp ' . number_format((float) $nominalFormatted, 2, ',', '.') . ') melebihi saldo tersedia (Rp ' . number_format((float) $saldoTersedia, 2, ',', '.') . ').');
            }

            // Tahan saldo
            $lockedNasabah->saldo_ditahan = bcadd((string) $lockedNasabah->saldo_ditahan, $nominalFormatted, 2);
            $lockedNasabah->save();

            $penarikan = PenarikanDana::create([
                'kode' => self::generateKode(),
                'nasabah_id' => $lockedNasabah->id,
                'nominal' => $nominalFormatted,
                'status' => 'menunggu',
                'diajukan_pada' => now(),
            ]);

            LogAktivitasService::catat(
                aksi: 'pengajuan_penarikan_dana',
                objekTipe: 'PenarikanDana',
                objekId: $penarikan->id,
                keterangan: [
                    'kode' => $penarikan->kode,
                    'nominal' => $nominalFormatted,
                    'nomor_nasabah' => $lockedNasabah->nomor_nasabah,
                ],
                user: $lockedNasabah->user
            );

            return $penarikan;
        });
    }

    /**
     * Nasabah membatalkan pengajuan yang masih berstatus 'menunggu' (F-27, BR-09).
     */
    public function batalkanOlehNasabah(PenarikanDana $penarikan, Nasabah $nasabah): void
    {
        if ($penarikan->nasabah_id !== $nasabah->id) {
            throw new RuntimeException('Anda hanya dapat membatalkan pengajuan milik sendiri.');
        }

        if ($penarikan->status !== 'menunggu') {
            throw new RuntimeException('Hanya pengajuan penarikan berstatus Menunggu yang dapat dibatalkan.');
        }

        DB::transaction(function () use ($penarikan, $nasabah) {
            $lockedNasabah = Nasabah::where('id', $nasabah->id)->lockForUpdate()->firstOrFail();

            // Kembalikan saldo ditahan
            $lockedNasabah->saldo_ditahan = bcsub((string) $lockedNasabah->saldo_ditahan, (string) $penarikan->nominal, 2);
            $lockedNasabah->save();

            $penarikan->update([
                'status' => 'dibatalkan',
            ]);

            LogAktivitasService::catat(
                aksi: 'penarikan_dibatalkan_nasabah',
                objekTipe: 'PenarikanDana',
                objekId: $penarikan->id,
                keterangan: [
                    'kode' => $penarikan->kode,
                    'nominal' => (string) $penarikan->nominal,
                ],
                user: $nasabah->user
            );
        });
    }

    /**
     * Admin menyetujui penarikan dana (F-29).
     */
    public function setujuiPenarikan(PenarikanDana $penarikan, User $admin): void
    {
        if ($penarikan->status !== 'menunggu') {
            throw new RuntimeException('Hanya pengajuan penarikan berstatus Menunggu yang dapat disetujui.');
        }

        DB::transaction(function () use ($penarikan, $admin) {
            $penarikan->update([
                'status' => 'disetujui',
                'diproses_oleh' => $admin->id,
                'diproses_pada' => now(),
            ]);

            LogAktivitasService::catat(
                aksi: 'penarikan_disetujui_admin',
                objekTipe: 'PenarikanDana',
                objekId: $penarikan->id,
                keterangan: [
                    'kode' => $penarikan->kode,
                    'diproses_oleh' => $admin->email,
                ],
                user: $admin
            );
        });
    }

    /**
     * Admin menolak penarikan dana dengan alasan wajib (F-29, BR-09).
     */
    public function tolakPenarikan(PenarikanDana $penarikan, string $alasan, User $admin): void
    {
        if ($penarikan->status !== 'menunggu') {
            throw new RuntimeException('Hanya pengajuan penarikan berstatus Menunggu yang dapat ditolak.');
        }

        if (empty(trim($alasan))) {
            throw new RuntimeException('Alasan penolakan penarikan wajib diisi.');
        }

        DB::transaction(function () use ($penarikan, $alasan, $admin) {
            $lockedNasabah = Nasabah::where('id', $penarikan->nasabah_id)->lockForUpdate()->firstOrFail();

            // Kembalikan saldo ditahan
            $lockedNasabah->saldo_ditahan = bcsub((string) $lockedNasabah->saldo_ditahan, (string) $penarikan->nominal, 2);
            $lockedNasabah->save();

            $penarikan->update([
                'status' => 'ditolak',
                'alasan_penolakan' => $alasan,
                'diproses_oleh' => $admin->id,
                'diproses_pada' => now(),
            ]);

            LogAktivitasService::catat(
                aksi: 'penarikan_ditolak_admin',
                objekTipe: 'PenarikanDana',
                objekId: $penarikan->id,
                keterangan: [
                    'kode' => $penarikan->kode,
                    'alasan' => $alasan,
                    'diproses_oleh' => $admin->email,
                ],
                user: $admin
            );
        });
    }

    /**
     * Admin konfirmasi pencairan dana tunai setelah uang diserahkan (F-30, BR-06).
     */
    public function cairkanPenarikan(PenarikanDana $penarikan, User $admin): void
    {
        if ($penarikan->status !== 'disetujui') {
            throw new RuntimeException('Hanya penarikan berstatus Disetujui yang dapat dicairkan.');
        }

        DB::transaction(function () use ($penarikan, $admin) {
            $lockedNasabah = Nasabah::where('id', $penarikan->nasabah_id)->lockForUpdate()->firstOrFail();

            // Kurangi saldo utama dan saldo ditahan secara bersamaan
            $lockedNasabah->saldo = bcsub((string) $lockedNasabah->saldo, (string) $penarikan->nominal, 2);
            $lockedNasabah->saldo_ditahan = bcsub((string) $lockedNasabah->saldo_ditahan, (string) $penarikan->nominal, 2);
            $lockedNasabah->save();

            $penarikan->update([
                'status' => 'dicairkan',
                'dicairkan_pada' => now(),
            ]);

            LogAktivitasService::catat(
                aksi: 'penarikan_dicairkan_admin',
                objekTipe: 'PenarikanDana',
                objekId: $penarikan->id,
                keterangan: [
                    'kode' => $penarikan->kode,
                    'nominal' => (string) $penarikan->nominal,
                    'nomor_nasabah' => $lockedNasabah->nomor_nasabah,
                    'dicairkan_oleh' => $admin->email,
                ],
                user: $admin
            );
        });
    }
}
