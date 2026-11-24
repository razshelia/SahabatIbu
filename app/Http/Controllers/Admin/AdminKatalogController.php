<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HargaSampah;
use App\Models\JenisSampah;
use App\Services\KatalogHargaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class AdminKatalogController extends Controller
{
    public function __construct(
        protected KatalogHargaService $katalogService
    ) {}

    /**
     * Tampilkan katalog jenis sampah dan harga berlaku (F-12, F-14).
     */
    public function index(): View
    {
        $jenisList = JenisSampah::with(['harga' => function ($q) {
            $q->orderBy('berlaku_mulai', 'desc');
        }])->get();

        foreach ($jenisList as $jenis) {
            $jenis->setRelation('harga_berlaku', $jenis->hargaBerlaku());
        }

        $riwayatHargaTerbaru = HargaSampah::with(['jenisSampah', 'pembuat'])
            ->orderBy('berlaku_mulai', 'desc')
            ->take(15)
            ->get();

        return view('admin.katalog.index', compact('jenisList', 'riwayatHargaTerbaru'));
    }

    /**
     * Tambah jenis sampah baru (F-12).
     */
    public function storeJenis(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'satuan' => ['required', 'string', 'max:10'],
        ]);

        try {
            $this->katalogService->simpanJenisSampah($validated, null, Auth::user());
            return back()->with('success', "Jenis sampah {$validated['nama']} sudah ditambahkan.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Ubah jenis sampah (F-12).
     */
    public function updateJenis(Request $request, JenisSampah $jenis): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'satuan' => ['required', 'string', 'max:10'],
            'aktif' => ['nullable', 'boolean'],
        ]);

        try {
            $validated['aktif'] = $request->has('aktif');
            $this->katalogService->simpanJenisSampah($validated, $jenis, Auth::user());
            return back()->with('success', "Jenis sampah {$jenis->nama} sudah diperbarui.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Tambah harga baru harian untuk jenis sampah (F-13).
     */
    public function storeHarga(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_sampah_id' => ['required', 'exists:jenis_sampah,id'],
            'harga_per_kg' => ['required', 'numeric', 'gt:0'],
            'berlaku_mulai' => ['required', 'date'],
        ], [
            'harga_per_kg.gt' => 'Harga per satuan harus lebih besar dari 0.',
        ]);

        try {
            $this->katalogService->tambahHarga(
                (int) $validated['jenis_sampah_id'],
                (float) $validated['harga_per_kg'],
                $validated['berlaku_mulai'],
                Auth::user()
            );

            return back()->with('success', 'Harga baru sudah disimpan dan berlaku mulai ' . $validated['berlaku_mulai'] . '.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
