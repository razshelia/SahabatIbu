<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PendaftaranService;
use App\Support\AlamatNasabah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileCompletionController extends Controller
{
    public function __construct(
        protected PendaftaranService $pendaftaranService
    ) {}

    /**
     * Tampilkan formulir pelengkapan profil kontak.
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        // Jika profil sudah lengkap, arahkan sesuai status
        if ($user->nasabah && ! empty($user->nasabah->telepon) && ! empty($user->nasabah->alamat)) {
            if ($user->status === 'menunggu' || $user->status === 'ditolak') {
                return redirect()->route('account.status');
            }
            return redirect()->route('nasabah.dashboard');
        }

        return view('auth.complete-profile', compact('user'));
    }

    /**
     * Simpan data kontak nasabah.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'telepon' => ['required', 'string', 'max:20', 'regex:/^[0-9+]+$/'],
            ...AlamatNasabah::rules(),
            'persetujuan_privasi' => ['accepted'],
        ], [
            ...AlamatNasabah::pesan(),
            'telepon.regex' => 'Format nomor telepon hanya boleh berisi angka dan tanda +.',
            'persetujuan_privasi.accepted' => 'Anda wajib menyetujui Kebijakan Privasi dan pemrosesan data pribadi untuk melanjutkan.',
        ]);

        $this->pendaftaranService->lengkapiProfilGoogle(Auth::user(), $validated);

        return redirect()->route('account.status')
            ->with('success', 'Data profil Ibu sudah lengkap. Pengurus akan memeriksa pendaftaran sebelum akun diaktifkan.');
    }
}
