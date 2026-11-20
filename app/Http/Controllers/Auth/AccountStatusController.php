<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountStatusController extends Controller
{
    /**
     * Tampilkan halaman status pendaftaran nasabah (F-37, F-40).
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->role === 'admin' || $user->status === 'aktif') {
            return $user->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('nasabah.dashboard');
        }

        return view('auth.status-pendaftaran', compact('user'));
    }
}
