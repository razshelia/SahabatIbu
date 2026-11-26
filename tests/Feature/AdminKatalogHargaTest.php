<?php

namespace Tests\Feature;

use App\Models\HargaSampah;
use App\Models\JenisSampah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKatalogHargaTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = new User;
        $admin->forceFill([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'Password123',
            'role' => 'admin',
            'status' => 'aktif',
            'email_verified_at' => now(),
            'must_change_password' => false,
        ])->save();

        return $admin;
    }

    public function test_admin_can_update_price_for_same_type_and_start_date(): void
    {
        $admin = $this->admin();
        $jenis = JenisSampah::create(['nama' => 'Kertas']);
        $tanggal = '2026-11-25';
        $harga = HargaSampah::create([
            'jenis_sampah_id' => $jenis->id,
            'harga_per_kg' => 1500,
            'berlaku_mulai' => $tanggal,
            'dibuat_oleh' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.katalog.harga.store'), [
                'jenis_sampah_id' => $jenis->id,
                'harga_per_kg' => 3000,
                'berlaku_mulai' => $tanggal,
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Harga pada tanggal tersebut sudah diperbarui.');

        $this->assertDatabaseCount('harga_sampah', 1);
        $this->assertDatabaseHas('harga_sampah', [
            'id' => $harga->id,
            'jenis_sampah_id' => $jenis->id,
            'harga_per_kg' => 3000,
            'dibuat_oleh' => $admin->id,
        ]);
        $this->assertDatabaseHas('log_aktivitas', [
            'aksi' => 'ubah_harga_sampah',
            'objek_tipe' => 'HargaSampah',
            'objek_id' => $harga->id,
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_add_price_when_no_price_exists_for_start_date(): void
    {
        $admin = $this->admin();
        $jenis = JenisSampah::create(['nama' => 'Plastik']);
        $tanggal = '2026-11-25';

        $this->actingAs($admin)
            ->post(route('admin.katalog.harga.store'), [
                'jenis_sampah_id' => $jenis->id,
                'harga_per_kg' => 2500,
                'berlaku_mulai' => $tanggal,
            ])
            ->assertRedirect()
            ->assertSessionHas('success', "Harga baru sudah disimpan dan berlaku mulai {$tanggal}.");

        $this->assertDatabaseCount('harga_sampah', 1);
        $this->assertDatabaseHas('harga_sampah', [
            'jenis_sampah_id' => $jenis->id,
            'harga_per_kg' => 2500,
            'dibuat_oleh' => $admin->id,
        ]);
        $this->assertDatabaseHas('log_aktivitas', [
            'aksi' => 'tambah_harga_sampah',
            'objek_tipe' => 'HargaSampah',
            'user_id' => $admin->id,
        ]);
    }
}
