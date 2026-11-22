<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiSetoran extends Model
{
    protected $table = 'transaksi_setoran';

    // status dan kolom pembatalan diubah lewat service, bukan input langsung (BR-10, BR-17).
    protected $fillable = ['kode', 'nasabah_id', 'admin_id', 'tanggal', 'total_berat', 'total_nilai'];

    protected function casts(): array
    {
        return [
            'tanggal'         => 'datetime',
            'dibatalkan_pada' => 'datetime',
            'total_berat'     => 'decimal:2',
            'total_nilai'     => 'decimal:2',
        ];
    }

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function dibatalkanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibatalkan_oleh');
    }

    public function detailSetoran(): HasMany
    {
        return $this->hasMany(DetailSetoran::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailSetoran::class);
    }

    public function getNomorSetoranAttribute(): ?string
    {
        return $this->kode;
    }
}
