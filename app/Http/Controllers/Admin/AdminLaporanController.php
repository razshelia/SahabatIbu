<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenarikanDana;
use App\Models\TransaksiSetoran;
use App\Services\LaporanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminLaporanController extends Controller
{
    public function __construct(
        protected LaporanService $laporanService
    ) {}

    /**
     * Tampilkan halaman rekap laporan operasional (F-31, F-32).
     */
    public function index(Request $request): View
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $rekap = $this->laporanService->getRekapPeriode($startDate, $endDate);
        $rekapJenis = $this->laporanService->getRekapPerJenisSampah($startDate, $endDate);

        $setoranList = TransaksiSetoran::with('nasabah.user')
            ->where('status', 'berhasil')
            ->whereBetween('tanggal', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->orderBy('tanggal', 'desc')
            ->paginate(10, ['*'], 'setoran_page');

        $penarikanList = PenarikanDana::with('nasabah.user')
            ->where('status', 'dicairkan')
            ->whereBetween('dicairkan_pada', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->orderBy('dicairkan_pada', 'desc')
            ->paginate(10, ['*'], 'penarikan_page');

        return view('admin.laporan.index', compact('rekap', 'rekapJenis', 'setoranList', 'penarikanList', 'startDate', 'endDate'));
    }

    /**
     * Ekspor CSV daftar setoran sampah (F-33).
     */
    public function exportSetoran(Request $request): Response
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $csv = $this->laporanService->exportSetoranCsv($startDate, $endDate);
        $fileName = 'laporan-setoran-' . now()->format('Ymd-His') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Ekspor CSV daftar penarikan dana dicairkan (F-33).
     */
    public function exportPenarikan(Request $request): Response
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $csv = $this->laporanService->exportPenarikanCsv($startDate, $endDate);
        $fileName = 'laporan-penarikan-' . now()->format('Ymd-His') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Halaman cetak ramah browser (PDF via browser print, F-33).
     */
    public function cetak(Request $request): View
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $rekap = $this->laporanService->getRekapPeriode($startDate, $endDate);
        $rekapJenis = $this->laporanService->getRekapPerJenisSampah($startDate, $endDate);

        $setoranList = TransaksiSetoran::with('nasabah.user')
            ->where('status', 'berhasil')
            ->whereBetween('tanggal', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->orderBy('tanggal', 'desc')
            ->get();

        $penarikanList = PenarikanDana::with('nasabah.user')
            ->where('status', 'dicairkan')
            ->whereBetween('dicairkan_pada', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()])
            ->orderBy('dicairkan_pada', 'desc')
            ->get();

        return view('admin.laporan.cetak', compact('rekap', 'rekapJenis', 'setoranList', 'penarikanList', 'startDate', 'endDate'));
    }
}
