<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\TransaksiSetoran;
use App\Services\TransaksiSetoranService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class AdminSetoranController extends Controller
{
    public function __construct(
        protected TransaksiSetoranService $setoranService
    ) {}

    /**
     * Tampilkan daftar transaksi setoran dengan filter (F-21).
     */
    public function index(Request $request): View
    {
        $nasabahId = $request->query('nasabah_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $status = $request->query('status');

        $query = TransaksiSetoran::with(['nasabah.user', 'admin']);

        if ($nasabahId) {
            $query->where('nasabah_id', $nasabahId);
        }

        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $transaksiList = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();
        $nasabahList = Nasabah::with('user')->whereHas('user', fn ($q) => $q->where('status', 'aktif'))->get();

        return view('admin.setoran.index', compact('transaksiList', 'nasabahList', 'nasabahId', 'startDate', 'endDate', 'status'));
    }

    /**
     * Tampilkan formulir input setoran sampah (F-16).
     */
    public function create(): View
    {
        $nasabahList = Nasabah::with('user')
            ->whereHas('user', fn ($q) => $q->where('status', 'aktif'))
            ->get();

        $jenisList = JenisSampah::where('aktif', true)->get();
        foreach ($jenisList as $jenis) {
            $jenis->harga_saat_ini = $jenis->hargaBerlaku()?->harga_per_kg ?? 0;
        }

        return view('admin.setoran.create', compact('nasabahList', 'jenisList'));
    }

    /**
     * Simpan transaksi setoran sampah baru (F-17, F-18, F-19).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nasabah_id' => ['required', 'exists:nasabah,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.jenis_sampah_id' => ['required', 'exists:jenis_sampah,id'],
            'items.*.berat_kg' => ['required', 'numeric', 'gt:0'],
        ], [
            'items.min' => 'Transaksi setoran harus memuat minimal 1 jenis sampah.',
            'items.*.berat_kg.gt' => 'Berat sampah harus lebih besar dari 0.',
        ]);

        try {
            $transaksi = $this->setoranService->simpanSetoran(
                (int) $request->input('nasabah_id'),
                $request->input('items'),
                Auth::user()
            );

            return redirect()->route('admin.setoran.show', $transaksi->id)
                ->with('success', "Setoran {$transaksi->kode} sudah dicatat. Saldo nasabah bertambah.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan detail transaksi setoran dan rincian item.
     */
    public function show(TransaksiSetoran $setoran): View
    {
        $setoran->load(['nasabah.user', 'admin', 'detail.jenisSampah', 'dibatalkanOleh']);

        return view('admin.setoran.show', compact('setoran'));
    }

    /**
     * Batalkan transaksi setoran (F-20, BR-10, BR-17).
     */
    public function batalkan(Request $request, TransaksiSetoran $setoran): RedirectResponse
    {
        $request->validate([
            'alasan_pembatalan' => ['required', 'string', 'max:255'],
        ], [
            'alasan_pembatalan.required' => 'Alasan pembatalan transaksi setoran wajib diisi.',
        ]);

        try {
            $this->setoranService->batalkanSetoran(
                $setoran,
                $request->input('alasan_pembatalan'),
                Auth::user()
            );

            return back()->with('success', "Setoran {$setoran->kode} dibatalkan. Saldo nasabah sudah disesuaikan.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
