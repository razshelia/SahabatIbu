<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nasabah extends Model
{
    protected $table = 'nasabah';

    // saldo dan saldo_ditahan sengaja tidak fillable: hanya diubah oleh service (BR-06, NF-09).
    protected $fillable = ['user_id', 'nomor_nasabah', 'telepon', 'kategori', 'blok', 'nomor_rumah', 'alamat'];

    protected function casts(): array
    {
        return ['saldo' => 'decimal:2', 'saldo_ditahan' => 'decimal:2'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaksiSetoran(): HasMany
    {
        return $this->hasMany(TransaksiSetoran::class);
    }

    public function setoran(): HasMany
    {
        return $this->hasMany(TransaksiSetoran::class);
    }

    public function penarikanDana(): HasMany
    {
        return $this->hasMany(PenarikanDana::class);
    }

    public function penarikan(): HasMany
    {
        return $this->hasMany(PenarikanDana::class);
    }

    // Saldo tersedia = saldo - saldo_ditahan (dipakai oleh BR-07 dan BR-17)
    public function getSaldoTersediaAttribute(): string
    {
        return bcsub((string) $this->saldo, (string) $this->saldo_ditahan, 2);
    }
}
