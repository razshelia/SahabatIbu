<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoriAlamatNasabahTest extends TestCase
{
    use RefreshDatabase;

    private function data(array $o = []): array
    {
        return array_merge([
            'name' => 'Siti Aminah', 'email' => 'siti@example.test', 'telepon' => '081234567890',
            'password' => 'Password123', 'password_confirmation' => 'Password123', 'persetujuan_privasi' => '1',
        ], $o);
    }

    public function test_warga_daftar_dengan_blok_dan_nomor_rumah(): void
    {
        $this->post('/register', $this->data(['kategori' => 'warga', 'blok' => 'b', 'nomor_rumah' => '12']))->assertRedirect();
        $n = Nasabah::firstOrFail();
        $this->assertSame('warga', $n->kategori);
        $this->assertSame('B', $n->blok);
        $this->assertSame('Blok B No. 12, Perumahan Taman Gading', $n->alamat);
    }

    public function test_warga_tanpa_blok_ditolak(): void
    {
        $this->post('/register', $this->data(['kategori' => 'warga']))->assertSessionHasErrors(['blok', 'nomor_rumah']);
        $this->assertSame(0, Nasabah::count());
    }

    public function test_umum_daftar_dengan_alamat_bebas(): void
    {
        $this->post('/register', $this->data(['kategori' => 'umum', 'alamat' => 'Jl. Mawar 5, Sumbersari']))->assertRedirect();
        $n = Nasabah::firstOrFail();
        $this->assertSame('umum', $n->kategori);
        $this->assertNull($n->blok);
        $this->assertSame('Jl. Mawar 5, Sumbersari', $n->alamat);
        $this->assertSame('menunggu', $n->user->status);
    }

    public function test_umum_tanpa_alamat_ditolak(): void
    {
        $this->post('/register', $this->data(['kategori' => 'umum']))->assertSessionHasErrors('alamat');
    }

    public function test_registration_rejects_an_email_that_is_already_in_use(): void
    {
        User::forceCreate([
            'name' => 'Pengguna Lama',
            'email' => 'siti@example.test',
            'password' => 'PasswordLama123',
            'role' => 'nasabah',
            'status' => 'aktif',
            'must_change_password' => false,
        ]);

        $this->followingRedirects()
            ->from(route('register'))
            ->post(route('register'), $this->data([
                'email' => 'SITI@EXAMPLE.TEST',
                'kategori' => 'umum',
                'alamat' => 'Jl. Mawar 5, Sumbersari',
            ]))
            ->assertOk()
            ->assertSee('Email ini sudah terdaftar.');

        $this->assertSame(1, User::where('email', 'siti@example.test')->count());
    }

    public function test_nasabah_tidak_bisa_mengubah_alamat_lewat_profil(): void
    {
        $this->post('/register', $this->data(['kategori' => 'warga', 'blok' => 'A', 'nomor_rumah' => '1']));
        $u = User::where('email', 'siti@example.test')->firstOrFail();
        $u->forceFill(['status' => 'aktif'])->save();
        $this->actingAs($u)->put(route('nasabah.profile.update'), [
            'telepon' => '081111111111', 'alamat' => 'Jl. Luar 99', 'kategori' => 'umum',
        ])->assertSessionHasNoErrors();
        $n = $u->nasabah->fresh();
        $this->assertSame('081111111111', $n->telepon);
        $this->assertSame('Blok A No. 1, Perumahan Taman Gading', $n->alamat);
        $this->assertSame('warga', $n->kategori);
    }
}
