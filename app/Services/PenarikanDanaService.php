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
    public function ajukanPenarikan(
        Nasabah $nasabah,
        float $nominal,
        string $metode = 'tunai',
        ?string $namaBank = null,
        ?string $nomorRekening = null,
        ?string $namaPemilik = null
    ): PenarikanDana {
        if ($nominal <= 0) {
            throw new RuntimeException('Jumlah penarikan harus lebih dari 0.');
        }

        if ($metode === 'transfer') {
            if (empty(trim($namaBank ?? '')) || empty(trim($nomorRekening ?? '')) || empty(trim($namaPemilik ?? ''))) {
                throw new RuntimeException('Lengkapi bank atau e-wallet tujuan, nomor rekening, dan nama pemilik.');
            }
        } else {
            $metode = 'tunai';
            $namaBank = null;
            $nomorRekening = null;
            $namaPemilik = null;
        }

        $nominalFormatted = number_format($nominal, 2, '.', '');

        return DB::transaction(function () use ($nasabah, $nominalFormatted, $metode, $namaBank, $nomorRekening, $namaPemilik) {
            $lockedNasabah = Nasabah::where('id', $nasabah->id)->lockForUpdate()->firstOrFail();

            if ($lockedNasabah->user->status !== 'aktif') {
                throw new RuntimeException('Akun nasabah harus aktif sebelum dapat mengajukan penarikan.');
            }

            $batasMaksimalUmum = (float) config('bank_sampah.penarikan.batas_maksimal_umum', 100000);
            if (
                $lockedNasabah->kategori === 'umum'
                && $batasMaksimalUmum > 0
                && bccomp($nominalFormatted, number_format($batasMaksimalUmum, 2, '.', ''), 2) > 0
            ) {
                throw new RuntimeException(
                    'Ditolak: batas maksimal penarikan untuk nasabah umum adalah Rp '
                    . number_format($batasMaksimalUmum, 2, ',', '.') . '.'
                );
            }

            $saldoTersedia = bcsub((string) $lockedNasabah->saldo, (string) $lockedNasabah->saldo_ditahan, 2);

            if (bccomp($nominalFormatted, $saldoTersedia, 2) > 0) {
                throw new RuntimeException('Ditolak: saldo tidak mencukupi. Saldo tersedia saat ini Rp ' . number_format((float) $saldoTersedia, 2, ',', '.') . '.');
            }

            // Tahan saldo
            $lockedNasabah->saldo_ditahan = bcadd((string) $lockedNasabah->saldo_ditahan, $nominalFormatted, 2);
            $lockedNasabah->save();

            $penarikan = new PenarikanDana();
            $penarikan->forceFill([
                'kode' => self::generateKode(),
                'nasabah_id' => $lockedNasabah->id,
                'nominal' => $nominalFormatted,
                'metode' => $metode,
                'nama_bank' => $namaBank,
                'nomor_rekening' => $nomorRekening,
                'nama_pemilik' => $namaPemilik,
                'status' => 'menunggu',
                'diajukan_pada' => now(),
            ]);
            $penarikan->save();

            // Masking nomor rekening untuk log agar tidak menyimpan nomor rekening utuh (Privasi & Keamanan)
            $maskedRekening = null;
            if ($nomorRekening) {
                $len = strlen($nomorRekening);
                $maskedRekening = $len <= 4 ? '****' : str_repeat('*', $len - 4) . substr($nomorRekening, -4);
            }

            $keteranganLog = [
                'kode' => $penarikan->kode,
                'nominal' => $nominalFormatted,
                'nomor_nasabah' => $lockedNasabah->nomor_nasabah,
                'metode' => $metode,
            ];

            if ($metode === 'transfer') {
                $keteranganLog['nama_bank'] = $namaBank;
                $keteranganLog['nomor_rekening'] = $maskedRekening;
                $keteranganLog['nama_pemilik'] = $namaPemilik;
            }

            LogAktivitasService::catat(
                aksi: 'pengajuan_penarikan_dana',
                objekTipe: 'PenarikanDana',
                objekId: $penarikan->id,
                keterangan: $keteranganLog,
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
            throw new RuntimeException('Ibu hanya dapat membatalkan pengajuan milik sendiri.');
        }

        if ($penarikan->status !== 'menunggu') {
            throw new RuntimeException('Pengajuan hanya dapat dibatalkan saat statusnya Menunggu.');
        }

        DB::transaction(function () use ($penarikan, $nasabah) {
            $lockedNasabah = Nasabah::where('id', $nasabah->id)->lockForUpdate()->firstOrFail();

            // Kembalikan saldo ditahan
            $lockedNasabah->saldo_ditahan = bcsub((string) $lockedNasabah->saldo_ditahan, (string) $penarikan->nominal, 2);
            $lockedNasabah->save();

            $penarikan->forceFill([
                'status' => 'dibatalkan',
            ])->save();

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
            throw new RuntimeException('Pengajuan hanya dapat disetujui saat statusnya Menunggu.');
        }

        DB::transaction(function () use ($penarikan, $admin) {
            $penarikan->forceFill([
                'status' => 'disetujui',
                'diproses_oleh' => $admin->id,
                'diproses_pada' => now(),
            ])->save();

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
            throw new RuntimeException('Pengajuan hanya dapat ditolak saat statusnya Menunggu.');
        }

        if (empty(trim($alasan))) {
            throw new RuntimeException('Isi alasan penolakan penarikan.');
        }

        DB::transaction(function () use ($penarikan, $alasan, $admin) {
            $lockedNasabah = Nasabah::where('id', $penarikan->nasabah_id)->lockForUpdate()->firstOrFail();

            // Kembalikan saldo ditahan
            $lockedNasabah->saldo_ditahan = bcsub((string) $lockedNasabah->saldo_ditahan, (string) $penarikan->nominal, 2);
            $lockedNasabah->save();

            $penarikan->forceFill([
                'status' => 'ditolak',
                'alasan_penolakan' => $alasan,
                'diproses_oleh' => $admin->id,
                'diproses_pada' => now(),
            ])->save();

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
     * Admin konfirmasi pencairan dana tunai / transfer (F-30, BR-06).
     */
    public function cairkanPenarikan(PenarikanDana $penarikan, User $admin, ?string $catatanReferensi = null): void
    {
        if ($penarikan->status !== 'disetujui') {
            throw new RuntimeException('Penarikan hanya dapat dicairkan setelah disetujui.');
        }

        DB::transaction(function () use ($penarikan, $admin, $catatanReferensi) {
            $lockedNasabah = Nasabah::where('id', $penarikan->nasabah_id)->lockForUpdate()->firstOrFail();

            // Kurangi saldo utama dan saldo ditahan secara bersamaan
            $lockedNasabah->saldo = bcsub((string) $lockedNasabah->saldo, (string) $penarikan->nominal, 2);
            $lockedNasabah->saldo_ditahan = bcsub((string) $lockedNasabah->saldo_ditahan, (string) $penarikan->nominal, 2);
            $lockedNasabah->save();

            $updateData = [
                'status' => 'dicairkan',
                'dicairkan_pada' => now(),
            ];
            if ($catatanReferensi !== null && trim($catatanReferensi) !== '') {
                $updateData['catatan_referensi'] = trim($catatanReferensi);
            }

            $penarikan->forceFill($updateData)->save();

            $keteranganLog = [
                'kode' => $penarikan->kode,
                'nominal' => (string) $penarikan->nominal,
                'metode' => $penarikan->metode,
                'nomor_nasabah' => $lockedNasabah->nomor_nasabah,
                'dicairkan_oleh' => $admin->email,
            ];

            if ($penarikan->catatan_referensi) {
                $keteranganLog['catatan_referensi'] = $penarikan->catatan_referensi;
            }

            LogAktivitasService::catat(
                aksi: 'penarikan_dicairkan_admin',
                objekTipe: 'PenarikanDana',
                objekId: $penarikan->id,
                keterangan: $keteranganLog,
                user: $admin
            );
        });
    }
}
