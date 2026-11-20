<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    // Tabel hanya punya created_at.
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'aksi', 'objek_tipe', 'objek_id', 'keterangan', 'ip_address', 'user_agent'];

    protected function casts(): array
    {
        return ['keterangan' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
