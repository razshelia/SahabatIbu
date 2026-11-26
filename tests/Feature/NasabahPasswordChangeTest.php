<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NasabahPasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_password_change_shows_an_auto_dismissing_message_below_submit_button(): void
    {
        $user = User::forceCreate([
            'name' => 'Nasabah Test',
            'email' => 'nasabah-password@example.test',
            'password' => Hash::make('PasswordLama123'),
            'role' => 'nasabah',
            'status' => 'aktif',
            'must_change_password' => false,
        ]);
        Nasabah::create([
            'user_id' => $user->id,
            'nomor_nasabah' => 'NSB-0001',
            'telepon' => '08123456789',
            'alamat' => 'Jl. Mawar No. 1',
        ]);

        $this->actingAs($user)
            ->from(route('nasabah.profile.show'))
            ->post(route('password.change.update'), [
                'current_password' => 'PasswordLama123',
                'password' => 'PasswordBaru456',
                'password_confirmation' => 'PasswordBaru456',
            ])
            ->assertRedirect(route('nasabah.profile.show'));

        $this->get(route('nasabah.profile.show'))
            ->assertOk()
            ->assertSeeInOrder([
                'Simpan kata sandi</button>',
                'id="password-change-success"',
                'Kata sandi berhasil diperbarui.',
            ], false)
            ->assertSee('5000', false)
            ->assertSee('aria-label="Tampilkan kata sandi saat ini"', false)
            ->assertSee('aria-label="Tampilkan kata sandi baru"', false)
            ->assertSee('aria-label="Tampilkan konfirmasi kata sandi"', false);

        $this->assertTrue(Hash::check('PasswordBaru456', $user->fresh()->password));
    }

    public function test_forced_password_change_continues_to_nasabah_dashboard(): void
    {
        $user = User::forceCreate([
            'name' => 'Nasabah Test',
            'email' => 'nasabah-forced-password@example.test',
            'password' => Hash::make('PasswordLama123'),
            'role' => 'nasabah',
            'status' => 'aktif',
            'must_change_password' => true,
        ]);

        $this->actingAs($user)
            ->post(route('password.change.update'), [
                'password' => 'PasswordBaru456',
                'password_confirmation' => 'PasswordBaru456',
            ])
            ->assertRedirect(route('nasabah.dashboard'));

        $this->assertFalse($user->fresh()->must_change_password);
    }
}
