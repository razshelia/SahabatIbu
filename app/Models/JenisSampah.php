<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisSampah extends Model
{
    protected $table = 'jenis_sampah';

    protected $fillable = ['nama', 'satuan', 'aktif'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean'];
    }

    public function harga(): HasMany
    {
        return $this->hasMany(HargaSampah::class);
    }

    public function hargaSampah(): HasMany
    {
        return $this->hasMany(HargaSampah::class);
    }

    // Harga yang sedang berlaku = berlaku_mulai terbaru yang tidak melewati hari ini (BR-03)
    public function hargaBerlaku(): ?HargaSampah
    {
        return $this->harga()
            ->whereDate('berlaku_mulai', '<=', now()->toDateString())
            ->orderByDesc('berlaku_mulai')
            ->first();
    }
}
