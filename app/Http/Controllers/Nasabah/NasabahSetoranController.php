<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\TransaksiSetoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NasabahSetoranController extends Controller
{
    /**
     * Tampilkan riwayat setoran nasabah dengan filter periode (F-23, BR-11).
     */
    public function index(Request $request): View
    {
        $nasabah = Auth::user()->nasabah;

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = $nasabah->transaksiSetoran();

        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        $transaksiList = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();

        return view('nasabah.setoran.index', compact('transaksiList', 'startDate', 'endDate'));
    }

    /**
     * Detail transaksi setoran nasabah (BR-11: hanya data miliknya sendiri).
     */
    public function show(TransaksiSetoran $setoran): View
    {
        $nasabah = Auth::user()->nasabah;

        if ($setoran->nasabah_id !== $nasabah->id) {
            abort(403, 'Akses ditolak. Anda hanya dapat melihat transaksi milik Anda sendiri.');
        }

        $setoran->load(['detailSetoran.jenisSampah']);

        return view('nasabah.setoran.show', compact('setoran'));
    }
}
