<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailSetoran;
use App\Models\Nasabah;
use App\Models\PenarikanDana;
use App\Models\TransaksiSetoran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard statistik dan metrik Admin (F-24).
     */
    public function index(): View
    {
        $totalNasabahAktif = Nasabah::whereHas('user', fn ($q) => $q->where('status', 'aktif'))->count();
        $totalPendaftarMenunggu = User::where('role', 'nasabah')->where('status', 'menunggu')->count();
        $totalPenarikanMenunggu = PenarikanDana::where('status', 'menunggu')->count();

        $totalSaldoTersimpan = Nasabah::sum('saldo');
        $totalSaldoDitahan = Nasabah::sum('saldo_ditahan');

        $totalSetoranSukses = TransaksiSetoran::where('status', 'berhasil')->sum('total_nilai');
        $totalBeratSukses = TransaksiSetoran::where('status', 'berhasil')->sum('total_berat');

        // Rekap sampah terkumpul per jenis
        $rekapSampah = DetailSetoran::select(
            'jenis_sampah.nama',
            'jenis_sampah.satuan',
            DB::raw('SUM(detail_setoran.berat_kg) as total_berat'),
            DB::raw('SUM(detail_setoran.subtotal) as total_nilai')
        )
            ->join('transaksi_setoran', 'detail_setoran.transaksi_setoran_id', '=', 'transaksi_setoran.id')
            ->join('jenis_sampah', 'detail_setoran.jenis_sampah_id', '=', 'jenis_sampah.id')
            ->where('transaksi_setoran.status', 'berhasil')
            ->groupBy('jenis_sampah.id', 'jenis_sampah.nama', 'jenis_sampah.satuan')
            ->get();

        // 5 Setoran terbaru
        $setoranTerbaru = TransaksiSetoran::with(['nasabah.user', 'admin'])
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        // 5 Penarikan terbaru
        $penarikanTerbaru = PenarikanDana::with(['nasabah.user'])
            ->orderBy('diajukan_pada', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalNasabahAktif',
            'totalPendaftarMenunggu',
            'totalPenarikanMenunggu',
            'totalSaldoTersimpan',
            'totalSaldoDitahan',
            'totalSetoranSukses',
            'totalBeratSukses',
            'rekapSampah',
            'setoranTerbaru',
            'penarikanTerbaru'
        ));
    }
}
