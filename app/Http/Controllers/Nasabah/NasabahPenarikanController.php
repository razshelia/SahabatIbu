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
            return back()->with('error', 'Terlalu banyak pengajuan penarikan. Coba lagi sebentar.');
        }

        RateLimiter::hit($throttleKey, 60);

        $request->validate([
            'nominal' => ['required', 'numeric', 'gt:0'],
            'metode' => ['required', 'in:tunai,transfer'],
            'nama_bank' => ['required_if:metode,transfer', 'nullable', 'string', 'max:100'],
            'nomor_rekening' => ['required_if:metode,transfer', 'nullable', 'string', 'max:50'],
            'nama_pemilik' => ['required_if:metode,transfer', 'nullable', 'string', 'max:100'],
        ], [
            'nominal.gt' => 'Jumlah penarikan harus lebih dari 0.',
            'metode.in' => 'Pilih cara menerima dana: tunai atau transfer.',
            'nama_bank.required_if' => 'Isi nama bank atau e-wallet tujuan transfer.',
            'nomor_rekening.required_if' => 'Isi nomor rekening atau nomor e-wallet tujuan transfer.',
            'nama_pemilik.required_if' => 'Isi nama pemilik rekening atau akun e-wallet.',
        ]);

        try {
            $penarikan = $this->penarikanService->ajukanPenarikan(
                $nasabah,
                (float) $request->input('nominal'),
                $request->input('metode', 'tunai'),
                $request->input('nama_bank'),
                $request->input('nomor_rekening'),
                $request->input('nama_pemilik')
            );

            $pesanMetode = $penarikan->metode === 'transfer'
                ? "melalui transfer ke {$penarikan->nama_bank} ({$penarikan->nomor_rekening_masked})"
                : "secara tunai";

            return back()->with('success', "Pengajuan Rp " . number_format((float) $penarikan->nominal, 0, ',', '.') . " {$pesanMetode} sudah dikirim dengan kode {$penarikan->kode}. Saldo ditahan sementara sampai pengajuan diproses.");
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
            return back()->with('success', "Pengajuan {$penarikan->kode} dibatalkan. Dana yang ditahan sudah kembali ke saldo tersedia.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
