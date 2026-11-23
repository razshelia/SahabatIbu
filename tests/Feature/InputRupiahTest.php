<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InputRupiahTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = new User();
        $u->forceFill(['name' => 'Admin', 'email' => 'a@example.test', 'password' => 'Password123', 'role' => 'admin', 'status' => 'aktif', 'email_verified_at' => now(), 'must_change_password' => false])->save();

        return $u;
    }

    private function nasabah(): User
    {
        $u = new User();
        $u->forceFill(['name' => 'Siti', 'email' => 's@example.test', 'password' => 'Password123', 'role' => 'nasabah', 'status' => 'aktif', 'email_verified_at' => now()])->save();
        Nasabah::create(['user_id' => $u->id, 'nomor_nasabah' => 'NSB-0001', 'telepon' => '0812', 'alamat' => 'Blok A No. 1, Perumahan Taman Gading', 'saldo' => 100000, 'saldo_ditahan' => 0]);

        return $u;
    }

    public function test_halaman_harga_memuat_pintasan_nol(): void
    {
        $this->actingAs($this->admin())->get(route('admin.katalog.index'))
            ->assertOk()->assertSee('data-rupiah', false)->assertSee('data-aksi="nol3"', false);
    }

    public function test_halaman_penarikan_memuat_pintasan_dan_batas_saldo(): void
    {
        if (! function_exists('bcsub')) {
            $this->markTestSkipped('Ekstensi bcmath tidak aktif di lingkungan ini.');
        }

        $this->actingAs($this->nasabah())->get(route('nasabah.penarikan.index'))
            ->assertOk()->assertSee('data-max="100000"', false)->assertSee('Semua saldo');
    }

    public function test_nominal_minus_atau_nol_ditolak_server(): void
    {
        $u = $this->nasabah();
        foreach (['-5000', '0'] as $v) {
            $this->actingAs($u)->post(route('nasabah.penarikan.store'), ['nominal' => $v])->assertSessionHasErrors('nominal');
        }
    }

    public function test_harga_minus_ditolak_server(): void
    {
        $this->actingAs($this->admin())->post(route('admin.katalog.harga.store'), ['jenis_sampah_id' => 1, 'harga_per_kg' => '-500', 'berlaku_mulai' => now()->toDateString()])
            ->assertSessionHasErrors('harga_per_kg');
    }
}
