<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nasabah', function (Blueprint $table) {
            // BR-19: kategori nasabah. Data lama dianggap warga Taman Gading.
            $table->string('kategori', 10)->default('warga')->after('telepon');
            // Alamat terstruktur khusus warga; nasabah umum memakai kolom alamat bebas.
            $table->string('blok', 10)->nullable()->after('kategori');
            $table->string('nomor_rumah', 10)->nullable()->after('blok');
            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::table('nasabah', function (Blueprint $table) {
            $table->dropIndex(['kategori']);
            $table->dropColumn(['kategori', 'blok', 'nomor_rumah']);
        });
    }
};
