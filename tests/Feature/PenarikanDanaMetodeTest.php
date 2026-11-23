<?php

namespace Tests\Feature;

use App\Models\LogAktivitas;
use App\Models\Nasabah;
use App\Models\PenarikanDana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenarikanDanaMetodeTest extends TestCase
{
    use RefreshDatabase;

    private function createNasabah(float $saldo = 100000): User
    {
        $u = new User();
        $u->forceFill([
            'name' => 'Nasabah Test',
            'email' => 'nasabah@example.test',
            'password' => bcrypt('Password123'),
            'role' => 'nasabah',
            'status' => 'aktif',
            'email_verified_at' => now(),
        ])->save();

        $n = new Nasabah();
        $n->forceFill([
            'user_id' => $u->id,
            'nomor_nasabah' => 'NSB-TEST-001',
            'telepon' => '08123456789',
            'kategori' => 'warga',
            'blok' => 'A',
            'nomor_rumah' => '1',
            'alamat' => 'Blok A No. 1, Perumahan Taman Gading',
            'saldo' => $saldo,
            'saldo_ditahan' => 0,
        ])->save();

        return $u;
    }

    private function createAdmin(): User
    {
        $u = new User();
        $u->forceFill([
            'name' => 'Admin Bank Sampah',
            'email' => 'admin@example.test',
            'password' => bcrypt('Password123'),
            'role' => 'admin',
            'status' => 'aktif',
            'email_verified_at' => now(),
            'must_change_password' => false,
        ])->save();

        return $u;
    }

    public function test_nasabah_dapat_mengajukan_penarikan_tunai(): void
    {
        $user = $this->createNasabah(100000);

        $response = $this->actingAs($user)->post(route('nasabah.penarikan.store'), [
            'nominal' => '25000',
            'metode' => 'tunai',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('penarikan_dana', [
            'nasabah_id' => $user->nasabah->id,
            'nominal' => '25000.00',
            'metode' => 'tunai',
            'nama_bank' => null,
            'nomor_rekening' => null,
            'nama_pemilik' => null,
            'status' => 'menunggu',
        ]);

        $nasabah = $user->nasabah->fresh();
        $this->assertEquals('25000.00', $nasabah->saldo_ditahan);
    }

    public function test_nasabah_dapat_mengajukan_penarikan_transfer_dan_log_disamarkan(): void
    {
        $user = $this->createNasabah(100000);

        $response = $this->actingAs($user)->post(route('nasabah.penarikan.store'), [
            'nominal' => '50000',
            'metode' => 'transfer',
            'nama_bank' => 'BCA',
            'nomor_rekening' => '1234567890',
            'nama_pemilik' => 'Nasabah Test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $penarikan = PenarikanDana::where('nasabah_id', $user->nasabah->id)->first();
        $this->assertNotNull($penarikan);
        $this->assertSame('transfer', $penarikan->metode);
        $this->assertSame('BCA', $penarikan->nama_bank);
        $this->assertSame('1234567890', $penarikan->nomor_rekening);
        $this->assertSame('Nasabah Test', $penarikan->nama_pemilik);
        $this->assertSame('******7890', $penarikan->nomor_rekening_masked);

        // Rekening tidak disimpan di tabel nasabah (tidak disimpan di profil)
        $nasabah = $user->nasabah->fresh();
        $this->assertArrayNotHasKey('nomor_rekening', $nasabah->getAttributes());

        // Verifikasi log aktivitas tidak menyimpan nomor rekening secara utuh
        $log = LogAktivitas::where('aksi', 'pengajuan_penarikan_dana')
            ->where('objek_id', $penarikan->id)
            ->first();
        $this->assertNotNull($log);
        $this->assertIsArray($log->keterangan);
        $this->assertSame('******7890', $log->keterangan['nomor_rekening']);
        $this->assertStringNotContainsString('1234567890', json_encode($log->keterangan));
    }

    public function test_pengajuan_transfer_wajib_mengisi_bank_rekening_dan_nama_pemilik(): void
    {
        $user = $this->createNasabah(100000);

        $response = $this->actingAs($user)->post(route('nasabah.penarikan.store'), [
            'nominal' => '50000',
            'metode' => 'transfer',
            // Field transfer kosong
        ]);

        $response->assertSessionHasErrors(['nama_bank', 'nomor_rekening', 'nama_pemilik']);
    }

    public function test_admin_melihat_detail_transfer_dan_dapat_mencairkan_dengan_catatan_referensi(): void
    {
        $nasabahUser = $this->createNasabah(100000);
        $adminUser = $this->createAdmin();

        // Nasabah ajukan transfer
        $this->actingAs($nasabahUser)->post(route('nasabah.penarikan.store'), [
            'nominal' => '50000',
            'metode' => 'transfer',
            'nama_bank' => 'Bank Mandiri',
            'nomor_rekening' => '987654321',
            'nama_pemilik' => 'Nasabah Transfer',
        ]);

        $penarikan = PenarikanDana::where('nasabah_id', $nasabahUser->nasabah->id)->firstOrFail();

        // Admin buka halaman index penarikan
        $responseAdmin = $this->actingAs($adminUser)->get(route('admin.penarikan.index'));
        $responseAdmin->assertOk();
        $responseAdmin->assertSee('Bank Mandiri');
        $responseAdmin->assertSee('987654321');
        $responseAdmin->assertSee('Nasabah Transfer');
        $responseAdmin->assertSee('Salin Rekening');
        $responseAdmin->assertSee('Salin Nominal');

        // Admin setujui penarikan
        $setujuiRes = $this->actingAs($adminUser)->post(route('admin.penarikan.setujui', $penarikan));
        if ($setujuiRes->isRedirect()) {
            // check session
        }
        $setujuiRes->assertSessionHas('success');
        $setujuiRes->assertRedirect();
        $penarikan->refresh();
        $this->assertSame('disetujui', $penarikan->status);

        // Admin cairkan dengan catatan referensi
        $cairkanResponse = $this->actingAs($adminUser)->post(route('admin.penarikan.cairkan', $penarikan), [
            'catatan_referensi' => 'TRF-MBANK-20261123-999',
        ]);

        $cairkanResponse->assertRedirect();
        $cairkanResponse->assertSessionHas('success');

        $penarikan->refresh();
        $this->assertSame('dicairkan', $penarikan->status);
        $this->assertSame('TRF-MBANK-20261123-999', $penarikan->catatan_referensi);

        // Saldo nasabah terpotong permanen
        $nasabah = $nasabahUser->nasabah->fresh();
        $this->assertEquals('50000.00', $nasabah->saldo);
        $this->assertEquals('0.00', $nasabah->saldo_ditahan);
    }
}
