<?php

namespace Tests\Feature;

use App\Models\HargaSampah;
use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NasabahKatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_katalog_menampilkan_harga_yang_berlaku(): void
    {
        $admin = new User;
        $admin->forceFill([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'Password123',
            'role' => 'admin',
            'status' => 'aktif',
        ])->save();

        $nasabah = new User;
        $nasabah->forceFill([
            'name' => 'Nasabah',
            'email' => 'nasabah@example.test',
            'password' => 'Password123',
            'role' => 'nasabah',
            'status' => 'aktif',
        ])->save();
        Nasabah::create([
            'user_id' => $nasabah->id,
            'nomor_nasabah' => 'NSB-0001',
            'telepon' => '08123456789',
            'alamat' => 'Jl. Mawar No. 1',
        ]);

        $jenis = JenisSampah::create(['nama' => 'Kertas']);
        HargaSampah::create([
            'jenis_sampah_id' => $jenis->id,
            'harga_per_kg' => 1500,
            'berlaku_mulai' => now()->toDateString(),
            'dibuat_oleh' => $admin->id,
        ]);

        $this->actingAs($nasabah)
            ->get(route('nasabah.katalog.index'))
            ->assertOk()
            ->assertSee('Rp 1.500/kg')
            ->assertDontSee('Belum ditetapkan');
    }
}
