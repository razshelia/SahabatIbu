<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harga_sampah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_sampah_id')->constrained('jenis_sampah')->restrictOnDelete();
            $table->decimal('harga_per_kg', 12, 2);
            $table->date('berlaku_mulai');
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['jenis_sampah_id', 'berlaku_mulai']);
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE harga_sampah ADD CONSTRAINT chk_harga_positif CHECK (harga_per_kg > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('harga_sampah');
    }
};
