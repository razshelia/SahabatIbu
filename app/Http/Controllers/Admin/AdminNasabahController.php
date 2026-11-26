<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\User;
use App\Services\LogAktivitasService;
use App\Services\PendaftaranService;
use App\Support\AlamatNasabah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class AdminNasabahController extends Controller
{
    public function __construct(
        protected PendaftaranService $pendaftaranService
    ) {}

    /**
     * Tampilkan daftar nasabah dengan pencarian & filter status (F-08).
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $status = $request->query('status');

        $query = Nasabah::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_nasabah', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($status) {
            $query->whereHas('user', fn ($uq) => $uq->where('status', $status));
        }

        $nasabahList = $query->latest()->paginate(15)->withQueryString();

        return view('admin.nasabah.index', compact('nasabahList', 'search', 'status'));
    }

    /**
     * Tampilkan daftar pendaftar yang menunggu verifikasi (F-38).
     */
    public function pendaftar(): View
    {
        $pendaftarList = User::with('nasabah')
            ->where('role', 'nasabah')
            ->latest()
            ->paginate(15);

        return view('admin.nasabah.pendaftar', compact('pendaftarList'));
    }

    /**
     * Proses verifikasi pendaftar nasabah (F-38).
     */
    public function verifikasi(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'aksi' => ['required', 'in:setujui,tolak'],
            'alasan' => ['nullable', 'string', 'max:255', 'required_if:aksi,tolak'],
        ]);

        $setujui = $request->input('aksi') === 'setujui';
        $alasan = $request->input('alasan');

        try {
            $this->pendaftaranService->verifikasiPendaftar($user, $setujui, $alasan, Auth::user());
            $pesan = $setujui ? "Pendaftaran {$user->name} disetujui. Akunnya sekarang aktif." : "Pendaftaran {$user->name} belum disetujui.";
            return back()->with('success', $pesan);
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan formulir tambah nasabah langsung oleh Admin (F-07).
     */
    public function create(): View
    {
        return view('admin.nasabah.create');
    }

    /**
     * Simpan nasabah baru yang ditambahkan langsung oleh Admin (F-07).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'telepon' => ['required', 'string', 'max:20', 'regex:/^[0-9+]+$/'],
            ...AlamatNasabah::rules(),
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'],
        ], [
            ...AlamatNasabah::pesan(),
            'telepon.regex' => 'Nomor telepon hanya boleh memuat angka dan tanda +.',
            'password.regex' => 'Kata sandi harus minimal 8 karakter dan memuat huruf serta angka.',
        ]);

        try {
            $user = $this->pendaftaranService->tambahNasabahLangsung($validated, Auth::user());
            return redirect()->route('admin.nasabah.index')->with('success', "Akun nasabah {$user->name} sudah dibuat dan aktif.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan formulir ubah data nasabah (F-09).
     */
    public function edit(Nasabah $nasabah): View
    {
        return view('admin.nasabah.edit', compact('nasabah'));
    }

    /**
     * Perbarui data nasabah (F-09).
     */
    public function update(Request $request, Nasabah $nasabah): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'telepon' => ['required', 'string', 'max:20', 'regex:/^[0-9+]+$/'],
            ...AlamatNasabah::rules(),
        ], AlamatNasabah::pesan());

        $alamatLama = $nasabah->only(['kategori', 'blok', 'nomor_rumah', 'alamat']);
        $alamatBaru = AlamatNasabah::normalisasi($validated);

        $nasabah->user->update(['name' => $validated['name']]);
        $nasabah->update(['telepon' => $validated['telepon'], ...$alamatBaru]);

        LogAktivitasService::catat(
            aksi: 'admin_ubah_nasabah',
            objekTipe: 'Nasabah',
            objekId: $nasabah->id,
            // Teks alamat tidak ditulis ke log agar tidak tersisa setelah anonimisasi (BR-18).
            keterangan: [
                'nomor_nasabah' => $nasabah->nomor_nasabah,
                'kategori_lama' => $alamatLama['kategori'],
                'kategori_baru' => $alamatBaru['kategori'],
                'alamat_berubah' => $alamatLama['alamat'] !== $alamatBaru['alamat'],
            ],
            user: Auth::user()
        );

        return redirect()->route('admin.nasabah.index')->with('success', "Data nasabah {$nasabah->user->name} sudah diperbarui.");
    }

    /**
     * Aktifkan atau nonaktifkan nasabah (F-10).
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        try {
            $this->pendaftaranService->toggleStatusAktif($user, Auth::user());
            $statusText = $user->fresh()->status === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
            return back()->with('success', "Akun {$user->name} sudah {$statusText}.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Anonimisasi akun nasabah nonaktif (BR-18, F-43).
     */
    public function anonimkan(Request $request, Nasabah $nasabah): RedirectResponse
    {
        $request->validate([
            'konfirmasi' => ['required', 'in:ANONIMKAN'],
        ], [
            'konfirmasi.in' => 'Ketik kata ANONIMKAN untuk mengonfirmasi tindakan ini.',
        ]);

        try {
            $this->pendaftaranService->anonimisasi($nasabah, Auth::user());
            return redirect()->route('admin.nasabah.index')->with('success', "Data nasabah nomor {$nasabah->nomor_nasabah} sudah dianonimkan. Tindakan ini tidak dapat dibatalkan.");
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
