<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NasabahDashboardController extends Controller
{
    /**
     * Dashboard Nasabah (F-22).
     */
    public function index(): View
    {
        $user = Auth::user();
        $nasabah = $user->nasabah;

        // Metrik setoran nasabah
        $totalBeratSetoran = $nasabah->transaksiSetoran()
            ->where('status', 'berhasil')
            ->sum('total_berat');

        $totalNilaiSetoran = $nasabah->transaksiSetoran()
            ->where('status', 'berhasil')
            ->sum('total_nilai');

        $jumlahTransaksiSetoran = $nasabah->transaksiSetoran()
            ->where('status', 'berhasil')
            ->count();

        // 5 Setoran terbaru
        $setoranTerbaru = $nasabah->transaksiSetoran()
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        // Penarikan aktif (menunggu atau disetujui)
        $penarikanAktif = $nasabah->penarikanDana()
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->orderBy('diajukan_pada', 'desc')
            ->get();

        return view('nasabah.dashboard', compact(
            'user',
            'nasabah',
            'totalBeratSetoran',
            'totalNilaiSetoran',
            'jumlahTransaksiSetoran',
            'setoranTerbaru',
            'penarikanAktif'
        ));
    }
}
