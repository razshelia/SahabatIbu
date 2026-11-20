<?php

namespace App\Services;

use App\Models\DetailSetoran;
use App\Models\PenarikanDana;
use App\Models\TransaksiSetoran;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanService
{
    /**
     * Dapatkan rekapitulasi ringkasan periode.
     */
    public function getRekapPeriode(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();

        $totalSetoran = TransaksiSetoran::where('status', 'berhasil')
            ->whereBetween('tanggal', [$start, $end])
            ->sum('total_nilai');

        $totalBerat = TransaksiSetoran::where('status', 'berhasil')
            ->whereBetween('tanggal', [$start, $end])
            ->sum('total_berat');

        $jumlahTransaksiSetoran = TransaksiSetoran::where('status', 'berhasil')
            ->whereBetween('tanggal', [$start, $end])
            ->count();

        $totalPenarikanDicairkan = PenarikanDana::where('status', 'dicairkan')
            ->whereBetween('dicairkan_pada', [$start, $end])
            ->sum('nominal');

        $jumlahPenarikanDicairkan = PenarikanDana::where('status', 'dicairkan')
            ->whereBetween('dicairkan_pada', [$start, $end])
            ->count();

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_setoran' => $totalSetoran,
            'total_berat' => $totalBerat,
            'jumlah_setoran' => $jumlahTransaksiSetoran,
            'total_penarikan' => $totalPenarikanDicairkan,
            'jumlah_penarikan' => $jumlahPenarikanDicairkan,
            'saldo_bersih' => bcsub((string) $totalSetoran, (string) $totalPenarikanDicairkan, 2),
        ];
    }

    /**
     * Dapatkan rekap per jenis sampah.
     */
    public function getRekapPerJenisSampah(?string $startDate = null, ?string $endDate = null): Collection
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();

        return DetailSetoran::select(
            'jenis_sampah.nama as jenis_nama',
            'jenis_sampah.satuan as jenis_satuan',
            DB::raw('SUM(detail_setoran.berat_kg) as total_berat'),
            DB::raw('SUM(detail_setoran.subtotal) as total_nilai')
        )
            ->join('transaksi_setoran', 'detail_setoran.transaksi_setoran_id', '=', 'transaksi_setoran.id')
            ->join('jenis_sampah', 'detail_setoran.jenis_sampah_id', '=', 'jenis_sampah.id')
            ->where('transaksi_setoran.status', 'berhasil')
            ->whereBetween('transaksi_setoran.tanggal', [$start, $end])
            ->groupBy('jenis_sampah.id', 'jenis_sampah.nama', 'jenis_sampah.satuan')
            ->get();
    }

    /**
     * Dapatkan daftar transaksi setoran untuk diekspor ke CSV (F-33).
     * Kolom: Nomor Nasabah, Nama, Tanggal, Kode Transaksi, Total Nilai.
     * Mencegah CSV Formula Injection (BR-33).
     */
    public function exportSetoranCsv(?string $startDate = null, ?string $endDate = null): string
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();

        $transaksi = TransaksiSetoran::with('nasabah.user')
            ->where('status', 'berhasil')
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal', 'desc')
            ->get();

        $output = fopen('php://temp', 'r+');

        // Header CSV
        fputcsv($output, ['Nomor Nasabah', 'Nama Nasabah', 'Kode Transaksi', 'Tanggal', 'Total Nilai (Rp)']);

        foreach ($transaksi as $t) {
            $nomorNasabah = self::sanitizeCsvFormula($t->nasabah->nomor_nasabah);
            $namaNasabah = self::sanitizeCsvFormula($t->nasabah->user->name);
            $kode = self::sanitizeCsvFormula($t->kode);
            $tanggal = Carbon::parse($t->tanggal)->format('d/m/Y H:i');
            $nilai = (string) $t->total_nilai;

            fputcsv($output, [$nomorNasabah, $namaNasabah, $kode, $tanggal, $nilai]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv ?: '';
    }

    /**
     * Dapatkan daftar penarikan dana dicairkan untuk diekspor ke CSV (F-33).
     */
    public function exportPenarikanCsv(?string $startDate = null, ?string $endDate = null): string
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();

        $penarikan = PenarikanDana::with('nasabah.user')
            ->where('status', 'dicairkan')
            ->whereBetween('dicairkan_pada', [$start, $end])
            ->orderBy('dicairkan_pada', 'desc')
            ->get();

        $output = fopen('php://temp', 'r+');

        fputcsv($output, ['Nomor Nasabah', 'Nama Nasabah', 'Kode Penarikan', 'Tanggal Dicairkan', 'Nominal (Rp)']);

        foreach ($penarikan as $p) {
            $nomorNasabah = self::sanitizeCsvFormula($p->nasabah->nomor_nasabah);
            $namaNasabah = self::sanitizeCsvFormula($p->nasabah->user->name);
            $kode = self::sanitizeCsvFormula($p->kode);
            $tanggal = Carbon::parse($p->dicairkan_pada)->format('d/m/Y H:i');
            $nominal = (string) $p->nominal;

            fputcsv($output, [$nomorNasabah, $namaNasabah, $kode, $tanggal, $nominal]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv ?: '';
    }

    /**
     * Menetralkan karakter formula berisiko (=, +, -, @) untuk mencegah CSV injection.
     */
    public static function sanitizeCsvFormula(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $dangerousChars = ['=', '+', '-', '@', "\t", "\r"];
        if (in_array($value[0], $dangerousChars, true)) {
            return "'" . $value;
        }

        return $value;
    }
}
