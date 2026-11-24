<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleProfileCompletionNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_back_action_on_profile_completion_returns_to_login_and_clears_session(): void
    {
        $user = User::forceCreate([
            'name' => 'Google Test',
            'email' => 'google@example.test',
            'password' => bcrypt('Password123'),
            'google_id' => 'google-test-id',
            'role' => 'nasabah',
            'status' => 'menunggu',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('profile.complete.form'))
            ->assertOk()
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('Kembali dan keluar')
            ->assertDontSee('<a class="brand g" href="'.url('/').'"', false);

        $this->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertGuest();
    }
}
