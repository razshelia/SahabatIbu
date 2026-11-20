<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    /**
     * Tampilkan profil admin dan log aktivitas (F-44).
     */
    public function show(Request $request): View
    {
        $admin = Auth::user();

        $search = $request->query('q');

        $query = LogAktivitas::with('user')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('aksi', 'like', "%{$search}%")
                    ->orWhere('objek_tipe', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.profile.show', compact('admin', 'logs', 'search'));
    }
}
