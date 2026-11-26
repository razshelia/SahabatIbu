<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// MENGGANTIKAN app/Models/User.php bawaan Laravel.
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    // Hanya kolom aman yang boleh diisi massal. role, status, must_change_password,
    // verified_by, dan verified_at TIDAK boleh diisi dari input pengguna (BR-12, BR-14).
    protected $fillable = ['name', 'email', 'password', 'google_id'];

    protected $hidden = ['password', 'remember_token', 'google_id'];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'verified_at'          => 'datetime',
            'password'             => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    public function nasabah(): HasOne
    {
        return $this->hasOne(Nasabah::class);
    }

    public function diverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class);
    }

    public function setoranDiproses(): HasMany
    {
        return $this->hasMany(TransaksiSetoran::class, 'admin_id');
    }

    public function penarikanDiproses(): HasMany
    {
        return $this->hasMany(PenarikanDana::class, 'diproses_oleh');
    }
}
