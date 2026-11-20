<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailSetoran extends Model
{
    protected $table = 'detail_setoran';

    protected $fillable = ['transaksi_setoran_id', 'jenis_sampah_id', 'berat_kg', 'harga_per_kg', 'subtotal'];

    protected function casts(): array
    {
        return [
            'berat_kg'     => 'decimal:2',
            'harga_per_kg' => 'decimal:2',
            'subtotal'     => 'decimal:2',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiSetoran::class, 'transaksi_setoran_id');
    }

    public function transaksiSetoran(): BelongsTo
    {
        return $this->belongsTo(TransaksiSetoran::class, 'transaksi_setoran_id');
    }

    public function jenisSampah(): BelongsTo
    {
        return $this->belongsTo(JenisSampah::class);
    }
}
