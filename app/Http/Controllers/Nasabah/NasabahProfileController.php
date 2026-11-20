<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Services\LogAktivitasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NasabahProfileController extends Controller
{
    /**
     * Tampilkan profil nasabah (F-11).
     */
    public function show(): View
    {
        $user = Auth::user();
        $nasabah = $user->nasabah;

        return view('nasabah.profile.show', compact('user', 'nasabah'));
    }

    /**
     * Perbarui data kontak nasabah (F-11).
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'telepon' => ['required', 'string', 'max:20', 'regex:/^[0-9+]+$/'],
            'alamat' => ['required', 'string', 'max:255'],
        ], [
            'telepon.regex' => 'Nomor telepon hanya boleh memuat angka dan tanda +.',
        ]);

        $nasabah = Auth::user()->nasabah;
        $nasabah->update($validated);

        LogAktivitasService::catat(
            aksi: 'nasabah_update_profil',
            objekTipe: 'Nasabah',
            objekId: $nasabah->id,
            keterangan: ['nomor_nasabah' => $nasabah->nomor_nasabah],
            user: Auth::user()
        );

        return back()->with('success', 'Data profil kontak Anda berhasil diperbarui.');
    }
}
