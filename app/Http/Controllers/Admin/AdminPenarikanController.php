<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenarikanDana;
use App\Services\PenarikanDanaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class AdminPenarikanController extends Controller
{
    public function __construct(
        protected PenarikanDanaService $penarikanService
    ) {}

    /**
     * Tampilkan daftar pengajuan penarikan dana dengan filter status.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = PenarikanDana::with(['nasabah.user', 'pemroses']);

        if ($status) {
            $query->where('status', $status);
        }

        $penarikanList = $query->orderBy('diajukan_pada', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.penarikan.index', compact('penarikanList', 'status'));
    }

    /**
     * Admin menyetujui pengajuan penarikan dana (F-29).
     */
    public function setujui(PenarikanDana $penarikan): RedirectResponse
    {
        try {
            $this->penarikanService->setujuiPenarikan($penarikan, Auth::user());
            return back()->with('success', "Pengajuan penarikan {$penarikan->kode} telah disetujui. Siapkan uang tunai untuk diserahkan ke nasabah.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin menolak pengajuan penarikan dana dengan alasan wajib (F-29).
     */
    public function tolak(Request $request, PenarikanDana $penarikan): RedirectResponse
    {
        $request->validate([
            'alasan_penolakan' => ['required', 'string', 'max:255'],
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan penarikan dana wajib diisi.',
        ]);

        try {
            $this->penarikanService->tolakPenarikan(
                $penarikan,
                $request->input('alasan_penolakan'),
                Auth::user()
            );

            return back()->with('success', "Pengajuan penarikan {$penarikan->kode} berhasil ditolak dan saldo ditahan telah dikembalikan ke nasabah.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin konfirmasi pencairan dana tunai (F-30).
     */
    public function cairkan(PenarikanDana $penarikan): RedirectResponse
    {
        try {
            $this->penarikanService->cairkanPenarikan($penarikan, Auth::user());
            return back()->with('success', "Pencairan dana {$penarikan->kode} berhasil dikonfirmasi! Saldo nasabah telah dipotong secara permanen.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
