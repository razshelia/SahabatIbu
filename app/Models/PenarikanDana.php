<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenarikanDana extends Model
{
    protected $table = 'penarikan_dana';

    // status dan kolom pemrosesan diubah lewat service (BR-08, BR-09).
    protected $fillable = ['kode', 'nasabah_id', 'nominal', 'diajukan_pada'];

    protected function casts(): array
    {
        return [
            'nominal'        => 'decimal:2',
            'diajukan_pada'  => 'datetime',
            'diproses_pada'  => 'datetime',
            'dicairkan_pada' => 'datetime',
        ];
    }

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function pemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function getNomorPenarikanAttribute(): ?string
    {
        return $this->kode;
    }
}
