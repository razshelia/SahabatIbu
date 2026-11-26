<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NasabahApprovalVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_and_approved_nasabah_remain_visible_after_approval(): void
    {
        $admin = User::forceCreate([
            'name' => 'Admin Test',
            'email' => 'admin-approval@example.test',
            'password' => 'Password123',
            'role' => 'admin',
            'status' => 'aktif',
            'must_change_password' => false,
        ]);

        $pending = $this->createNasabah('Pendaftar Menunggu', 'pending@example.test', 'menunggu', 'NSB-1001');
        $approved = $this->createNasabah('Nasabah Disetujui', 'approved@example.test', 'aktif', 'NSB-1002');

        $this->actingAs($admin)
            ->get(route('admin.nasabah.pendaftar'))
            ->assertOk()
            ->assertSee($pending->name)
            ->assertSee($approved->name)
            ->assertSee('Belum diverifikasi')
            ->assertSee('Disetujui')
            ->assertSee('Setujui');

        $this->actingAs($admin)
            ->get(route('admin.nasabah.index'))
            ->assertOk()
            ->assertSee($pending->name)
            ->assertSee($approved->name);

        $this->actingAs($admin)
            ->post(route('admin.nasabah.verifikasi', $pending), ['aksi' => 'setujui'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $pending->id,
            'status' => 'aktif',
            'verified_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('nasabah', ['user_id' => $pending->id]);
        $this->assertSame(1, Nasabah::where('user_id', $pending->id)->count());

        $this->actingAs($admin)
            ->get(route('admin.nasabah.pendaftar'))
            ->assertOk()
            ->assertSee($pending->name)
            ->assertSee($approved->name)
            ->assertDontSee('Setujui');
    }

    private function createNasabah(string $name, string $email, string $status, string $number): User
    {
        $user = User::forceCreate([
            'name' => $name,
            'email' => $email,
            'password' => 'Password123',
            'role' => 'nasabah',
            'status' => $status,
            'must_change_password' => false,
        ]);

        Nasabah::create([
            'user_id' => $user->id,
            'nomor_nasabah' => $number,
            'telepon' => '08123456789',
            'alamat' => 'Jl. Mawar No. 1',
        ]);

        return $user;
    }
}
