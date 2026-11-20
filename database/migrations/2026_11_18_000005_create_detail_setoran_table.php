<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_setoran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_setoran_id')->constrained('transaksi_setoran')->restrictOnDelete();
            $table->foreignId('jenis_sampah_id')->constrained('jenis_sampah')->restrictOnDelete();
            $table->decimal('berat_kg', 10, 2);
            $table->decimal('harga_per_kg', 12, 2);   // salinan harga saat transaksi (BR-04)
            $table->decimal('subtotal', 14, 2);
            $table->timestamps();

            $table->unique(['transaksi_setoran_id', 'jenis_sampah_id']);
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE detail_setoran ADD CONSTRAINT chk_detail_berat CHECK (berat_kg > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_setoran');
    }
};
