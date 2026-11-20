<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HargaSampah extends Model
{
    protected $table = 'harga_sampah';

    protected $fillable = ['jenis_sampah_id', 'harga_per_kg', 'berlaku_mulai', 'dibuat_oleh'];

    protected function casts(): array
    {
        return ['harga_per_kg' => 'decimal:2', 'berlaku_mulai' => 'date'];
    }

    public function jenisSampah(): BelongsTo
    {
        return $this->belongsTo(JenisSampah::class);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
