<?php

namespace App\Services;

use App\Models\DetailSetoran;
use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\TransaksiSetoran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TransaksiSetoranService
{
    /**
     * Generate kode setoran unik. Format: STR-YYYYMMDD-XXXX.
     */
    public static function generateKode(): string
    {
        $datePrefix = 'STR-' . now()->format('Ymd') . '-';
        $countToday = TransaksiSetoran::where('kode', 'like', $datePrefix . '%')->count() + 1;

        do {
            $kode = $datePrefix . sprintf('%04d', $countToday);
            $exists = TransaksiSetoran::where('kode', $kode)->exists();
            if ($exists) {
                $countToday++;
            }
        } while ($exists);

        return $kode;
    }

    /**
     * Simpan transaksi setoran sampah baru (F-16, F-17, F-18, F-19).
     *
     * @param int $nasabahId
     * @param array $items Array of ['jenis_sampah_id' => int, 'berat_kg' => float]
     * @param User $admin
     * @return TransaksiSetoran
     */
    public function simpanSetoran(int $nasabahId, array $items, User $admin): TransaksiSetoran
    {
        if (empty($items)) {
            throw new RuntimeException('Pilih setidaknya satu jenis sampah untuk dicatat.');
        }

        return DB::transaction(function () use ($nasabahId, $items, $admin) {
            // Lock record nasabah untuk integritas saldo
            $nasabah = Nasabah::where('id', $nasabahId)->lockForUpdate()->firstOrFail();

            if ($nasabah->user->status !== 'aktif') {
                throw new RuntimeException('Setoran hanya dapat dicatat untuk nasabah aktif.');
            }

            $totalBerat = '0';
            $totalNilai = '0';
            $detailList = [];

            foreach ($items as $item) {
                $jenisId = (int) $item['jenis_sampah_id'];
                $berat = (float) $item['berat_kg'];

                if ($berat <= 0) {
                    throw new RuntimeException('Berat sampah harus lebih dari 0.');
                }

                // Bulatkan ke 2 desimal
                $beratFormatted = number_format($berat, 2, '.', '');

                $jenisSampah = JenisSampah::findOrFail($jenisId);
                if (! $jenisSampah->aktif) {
                    throw new RuntimeException("Jenis sampah {$jenisSampah->nama} tidak aktif.");
                }

                // Ambil harga yang berlaku saat ini (BR-03)
                $hargaBerlaku = $jenisSampah->hargaBerlaku();
                if (! $hargaBerlaku) {
                    throw new RuntimeException("Harga untuk {$jenisSampah->nama} belum tersedia. Tetapkan harga sebelum mencatat setoran.");
                }

                $hargaPerKg = (string) $hargaBerlaku->harga_per_kg;
                $subtotal = bcmul($beratFormatted, $hargaPerKg, 2);

                $totalBerat = bcadd($totalBerat, $beratFormatted, 2);
                $totalNilai = bcadd($totalNilai, $subtotal, 2);

                $detailList[] = [
                    'jenis_sampah_id' => $jenisId,
                    'berat_kg' => $beratFormatted,
                    'harga_per_kg' => $hargaPerKg,
                    'subtotal' => $subtotal,
                ];
            }

            $transaksi = TransaksiSetoran::create([
                'kode' => self::generateKode(),
                'nasabah_id' => $nasabah->id,
                'admin_id' => $admin->id,
                'tanggal' => now(),
                'total_berat' => $totalBerat,
                'total_nilai' => $totalNilai,
                'status' => 'berhasil',
            ]);

            foreach ($detailList as $detail) {
                DetailSetoran::create(array_merge($detail, [
                    'transaksi_setoran_id' => $transaksi->id,
                ]));
            }

            // Tambah saldo nasabah secara atomik (BR-06)
            $nasabah->saldo = bcadd((string) $nasabah->saldo, $totalNilai, 2);
            $nasabah->save();

            LogAktivitasService::catat(
                aksi: 'transaksi_setoran_berhasil',
                objekTipe: 'TransaksiSetoran',
                objekId: $transaksi->id,
                keterangan: [
                    'kode' => $transaksi->kode,
                    'nomor_nasabah' => $nasabah->nomor_nasabah,
                    'total_nilai' => $totalNilai,
                    'total_berat' => $totalBerat,
                ],
                user: $admin
            );

            return $transaksi;
        });
    }

    /**
     * Batalkan transaksi setoran oleh Admin (BR-10, BR-17, F-20).
     */
    public function batalkanSetoran(TransaksiSetoran $transaksi, string $alasan, User $admin): void
    {
        if ($transaksi->status !== 'berhasil') {
            throw new RuntimeException('Hanya setoran yang berstatus Berhasil yang dapat dibatalkan.');
        }

        if (empty(trim($alasan))) {
            throw new RuntimeException('Isi alasan pembatalan setoran.');
        }

        DB::transaction(function () use ($transaksi, $alasan, $admin) {
            $nasabah = Nasabah::where('id', $transaksi->nasabah_id)->lockForUpdate()->firstOrFail();

            // Aturan BR-17: Pembatalan setoran ditolak jika saldo tersedia < total nilai transaksi
            $saldoTersedia = bcsub((string) $nasabah->saldo, (string) $nasabah->saldo_ditahan, 2);
            if (bccomp($saldoTersedia, (string) $transaksi->total_nilai, 2) < 0) {
                throw new RuntimeException('Setoran tidak dapat dibatalkan karena saldo tersedia (Rp ' . number_format((float) $saldoTersedia, 2, ',', '.') . ') kurang dari nilai setoran (Rp ' . number_format((float) $transaksi->total_nilai, 2, ',', '.') . ').');
            }

            // Kurangi saldo nasabah
            $nasabah->saldo = bcsub((string) $nasabah->saldo, (string) $transaksi->total_nilai, 2);
            $nasabah->save();

            $transaksi->update([
                'status' => 'dibatalkan',
                'alasan_pembatalan' => $alasan,
                'dibatalkan_oleh' => $admin->id,
                'dibatalkan_pada' => now(),
            ]);

            LogAktivitasService::catat(
                aksi: 'transaksi_setoran_dibatalkan',
                objekTipe: 'TransaksiSetoran',
                objekId: $transaksi->id,
                keterangan: [
                    'kode' => $transaksi->kode,
                    'nomor_nasabah' => $nasabah->nomor_nasabah,
                    'total_nilai' => (string) $transaksi->total_nilai,
                    'alasan' => $alasan,
                    'dibatalkan_oleh' => $admin->email,
                ],
                user: $admin
            );
        });
    }
}
