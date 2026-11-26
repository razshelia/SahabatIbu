<?php

namespace Tests\Feature;

use App\Models\DetailSetoran;
use App\Models\HargaSampah;
use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\TransaksiSetoran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSetoranJenisFilterTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $admin = new User();
        $admin->forceFill([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('Password123'),
            'role' => 'admin',
            'status' => 'aktif',
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);
        $admin->save();

        return $admin;
    }

    private function createNasabah(string $name, string $nomor): User
    {
        $user = new User();
        $user->forceFill([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@example.test',
            'password' => bcrypt('Password123'),
            'role' => 'nasabah',
            'status' => 'aktif',
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);
        $user->save();

        $nasabah = new Nasabah();
        $nasabah->forceFill([
            'user_id' => $user->id,
            'nomor_nasabah' => $nomor,
            'telepon' => '08123456789',
            'kategori' => 'warga',
            'blok' => 'A',
            'nomor_rumah' => '1',
            'alamat' => 'Jl. Test',
            'saldo' => 0,
            'saldo_ditahan' => 0,
        ]);
        $nasabah->save();

        return $user;
    }

    private function createSetoran(User $admin, User $nasabahUser, string $kode, JenisSampah $jenis): TransaksiSetoran
    {
        $setoran = new TransaksiSetoran();
        $setoran->forceFill([
            'kode' => $kode,
            'nasabah_id' => $nasabahUser->nasabah->id,
            'admin_id' => $admin->id,
            'tanggal' => now(),
            'total_berat' => 10,
            'total_nilai' => 15000,
            'status' => 'berhasil',
        ]);
        $setoran->save();

        $detail = new DetailSetoran();
        $detail->forceFill([
            'transaksi_setoran_id' => $setoran->id,
            'jenis_sampah_id' => $jenis->id,
            'berat_kg' => 10,
            'harga_per_kg' => 1500,
            'subtotal' => 15000,
        ]);
        $detail->save();

        return $setoran;
    }

    public function test_admin_can_filter_setoran_by_waste_type(): void
    {
        $admin = $this->createAdmin();
        $nasabahKertas = $this->createNasabah('Nasabah Kertas', 'NSB-KERTAS');
        $nasabahPlastik = $this->createNasabah('Nasabah Plastik', 'NSB-PLASTIK');
        $kertas = JenisSampah::create(['nama' => 'Kertas', 'aktif' => true]);
        $plastik = JenisSampah::create(['nama' => 'Plastik', 'aktif' => true]);

        $setoranKertas = $this->createSetoran($admin, $nasabahKertas, 'ST-KERTAS', $kertas);
        $setoranPlastik = $this->createSetoran($admin, $nasabahPlastik, 'ST-PLASTIK', $plastik);

        $response = $this->actingAs($admin)->get(route('admin.setoran.index', [
            'jenis_sampah_id' => $kertas->id,
        ]));

        $response->assertOk();
        $response->assertSee('Nasabah Kertas');
        $response->assertViewHas('transaksiList', function ($list) use ($setoranKertas, $setoranPlastik) {
            $ids = $list->pluck('id')->all();

            return in_array($setoranKertas->id, $ids, true)
                && ! in_array($setoranPlastik->id, $ids, true);
        });
    }

    public function test_admin_can_save_setoran_with_other_waste_types_left_blank(): void
    {
        $admin = $this->createAdmin();
        $nasabah = $this->createNasabah('Nasabah Setoran', 'NSB-SETORAN');
        $kertas = JenisSampah::create(['nama' => 'Kertas', 'aktif' => true]);
        $plastik = JenisSampah::create(['nama' => 'Plastik', 'aktif' => true]);

        foreach ([$kertas, $plastik] as $jenis) {
            HargaSampah::create([
                'jenis_sampah_id' => $jenis->id,
                'harga_per_kg' => 1500,
                'berlaku_mulai' => now()->toDateString(),
                'dibuat_oleh' => $admin->id,
            ]);
        }

        $response = $this->actingAs($admin)->post(route('admin.setoran.store'), [
            'nasabah_id' => $nasabah->nasabah->id,
            'items' => [
                ['jenis_sampah_id' => $kertas->id, 'berat_kg' => '2.5'],
                ['jenis_sampah_id' => $plastik->id, 'berat_kg' => ''],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('transaksi_setoran', 1);
        $this->assertDatabaseCount('detail_setoran', 1);
        $this->assertDatabaseHas('detail_setoran', [
            'jenis_sampah_id' => $kertas->id,
            'berat_kg' => '2.50',
        ]);
        $this->assertSame('3750.00', $nasabah->nasabah->fresh()->saldo);
    }

    public function test_admin_cannot_save_setoran_when_every_waste_type_is_blank(): void
    {
        $admin = $this->createAdmin();
        $nasabah = $this->createNasabah('Nasabah Kosong', 'NSB-KOSONG');
        $kertas = JenisSampah::create(['nama' => 'Kertas', 'aktif' => true]);
        $plastik = JenisSampah::create(['nama' => 'Plastik', 'aktif' => true]);

        $response = $this->actingAs($admin)->post(route('admin.setoran.store'), [
            'nasabah_id' => $nasabah->nasabah->id,
            'items' => [
                ['jenis_sampah_id' => $kertas->id, 'berat_kg' => ''],
                ['jenis_sampah_id' => $plastik->id, 'berat_kg' => ''],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('transaksi_setoran', 0);
    }
}
