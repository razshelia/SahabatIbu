<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\PenarikanDana;
use App\Services\PenarikanDanaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Throwable;

class NasabahPenarikanController extends Controller
{
    public function __construct(
        protected PenarikanDanaService $penarikanService
    ) {}

    /**
     * Tampilkan formulir pengajuan dan daftar riwayat penarikan dana (F-28).
     */
    public function index(): View
    {
        $nasabah = Auth::user()->nasabah;

        $penarikanList = $nasabah->penarikanDana()
            ->orderBy('diajukan_pada', 'desc')
            ->paginate(15);

        return view('nasabah.penarikan.index', compact('nasabah', 'penarikanList'));
    }

    /**
     * Proses pengajuan penarikan dana baru (F-25, F-26).
     */
    public function store(Request $request): RedirectResponse
    {
        $nasabah = Auth::user()->nasabah;

        $throttleKey = 'tarik_dana|' . Auth::id();
        if (RateLimiter::tooManyAttempts($throttleKey, 30)) {
            return back()->with('error', 'Terlalu banyak permintaan penarikan. Silakan tunggu sebentar.');
        }

        RateLimiter::hit($throttleKey, 60);

        $request->validate([
            'nominal' => ['required', 'numeric', 'gt:0'],
        ], [
            'nominal.gt' => 'Nominal penarikan harus lebih besar dari 0.',
        ]);

        try {
            $penarikan = $this->penarikanService->ajukanPenarikan($nasabah, (float) $request->input('nominal'));

            return back()->with('success', "Pengajuan penarikan sebesar Rp " . number_format((float) $penarikan->nominal, 0, ',', '.') . " berhasil diajukan dengan kode {$penarikan->kode}. Saldo Anda telah ditahan sementara.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Nasabah membatalkan pengajuan penarikan berstatus 'menunggu' (F-27, BR-09).
     */
    public function batalkan(PenarikanDana $penarikan): RedirectResponse
    {
        $nasabah = Auth::user()->nasabah;

        try {
            $this->penarikanService->batalkanOlehNasabah($penarikan, $nasabah);
            return back()->with('success', "Pengajuan penarikan {$penarikan->kode} telah berhasil dibatalkan. Saldo ditahan telah dikembalikan.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
