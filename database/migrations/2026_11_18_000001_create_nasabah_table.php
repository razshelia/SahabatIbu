<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nasabah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->string('nomor_nasabah', 20)->unique();
            $table->string('telepon', 20);
            $table->string('alamat');
            $table->decimal('saldo', 14, 2)->default(0);           // setoran berhasil - penarikan dicairkan
            $table->decimal('saldo_ditahan', 14, 2)->default(0);   // saldo tersedia = saldo - saldo_ditahan
            $table->timestamps();
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE nasabah ADD CONSTRAINT chk_nasabah_saldo
                CHECK (saldo >= 0 AND saldo_ditahan >= 0 AND saldo_ditahan <= saldo)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('nasabah');
    }
};
