<?php

namespace App\Services;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogAktivitasService
{
    /**
     * Catat log aktivitas ke database.
     *
     * @param string $aksi
     * @param string|null $objekTipe
     * @param int|null $objekId
     * @param array|string|null $keterangan
     * @param User|null $user
     * @return LogAktivitas
     */
    public static function catat(
        string $aksi,
        ?string $objekTipe = null,
        ?int $objekId = null,
        array|string|null $keterangan = null,
        ?User $user = null
    ): LogAktivitas {
        $currentUser = $user ?? Auth::user();

        // Hindari logging sensitive keys
        if (is_array($keterangan)) {
            unset($keterangan['password'], $keterangan['password_confirmation'], $keterangan['_token']);
        }

        return LogAktivitas::create([
            'user_id' => $currentUser?->id,
            'aksi' => $aksi,
            'objek_tipe' => $objekTipe,
            'objek_id' => $objekId,
            'keterangan' => $keterangan,
            'ip_address' => Request::ip(),
            'user_agent' => substr(Request::userAgent() ?? '', 0, 500),
            'created_at' => now(),
        ]);
    }
}
