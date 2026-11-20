<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_setoran', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 25)->unique();
            $table->foreignId('nasabah_id')->constrained('nasabah')->restrictOnDelete();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('tanggal');
            $table->decimal('total_berat', 10, 2);
            $table->decimal('total_nilai', 14, 2);
            $table->enum('status', ['berhasil', 'dibatalkan'])->default('berhasil');
            $table->string('alasan_pembatalan')->nullable();
            $table->foreignId('dibatalkan_oleh')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('dibatalkan_pada')->nullable();
            $table->timestamps();

            $table->index(['nasabah_id', 'tanggal']);
            $table->index('tanggal');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE transaksi_setoran ADD CONSTRAINT chk_setoran_total
                CHECK (total_berat > 0 AND total_nilai >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_setoran');
    }
};
