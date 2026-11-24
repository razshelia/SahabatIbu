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
            $pesan = $penarikan->metode === 'transfer'
                ? "Pengajuan {$penarikan->kode} disetujui. Lanjutkan transfer ke rekening tujuan."
                : "Pengajuan {$penarikan->kode} disetujui. Siapkan uang tunai untuk diserahkan kepada nasabah.";
            return back()->with('success', $pesan);
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

            return back()->with('success', "Pengajuan {$penarikan->kode} ditolak. Dana yang ditahan sudah kembali ke saldo nasabah.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin konfirmasi pencairan dana tunai / transfer (F-30).
     */
    public function cairkan(Request $request, PenarikanDana $penarikan): RedirectResponse
    {
        $request->validate([
            'catatan_referensi' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $this->penarikanService->cairkanPenarikan(
                $penarikan,
                Auth::user(),
                $request->input('catatan_referensi')
            );

            $pesan = $penarikan->metode === 'transfer'
                ? "Transfer untuk pengajuan {$penarikan->kode} sudah dikonfirmasi. Saldo nasabah sudah diperbarui."
                : "Penarikan tunai {$penarikan->kode} sudah dikonfirmasi. Saldo nasabah sudah diperbarui.";

            return back()->with('success', $pesan);
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
