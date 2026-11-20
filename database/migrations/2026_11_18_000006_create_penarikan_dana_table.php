<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penarikan_dana', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 25)->unique();
            $table->foreignId('nasabah_id')->constrained('nasabah')->restrictOnDelete();
            $table->decimal('nominal', 14, 2);
            $table->enum('status', ['menunggu', 'disetujui', 'dicairkan', 'ditolak', 'dibatalkan'])->default('menunggu');
            $table->dateTime('diajukan_pada');
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('diproses_pada')->nullable();
            $table->string('alasan_penolakan')->nullable();
            $table->dateTime('dicairkan_pada')->nullable();
            $table->timestamps();

            $table->index(['nasabah_id', 'status']);
            $table->index('status');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE penarikan_dana ADD CONSTRAINT chk_penarikan_nominal CHECK (nominal > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('penarikan_dana');
    }
};
