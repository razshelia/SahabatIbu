<?php

namespace Tests\Feature;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_sixth_wrong_password_attempt_shows_limit_message_and_logs_failure(): void
    {
        $this->freezeTime();

        $email = 'login-limit@example.test';
        User::forceCreate([
            'name' => 'Login Test',
            'email' => $email,
            'password' => Hash::make('CorrectPassword123'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        $credentials = [
            'email' => $email,
            'password' => 'WrongPassword123',
        ];

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from(route('login'))
                ->post(route('login'), $credentials)
                ->assertRedirect(route('login'));
        }

        $sixthAttempt = $this->from(route('login'))
            ->post(route('login'), $credentials);

        $sixthAttempt->assertRedirect(route('login'));
        $this->assertGuest();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('id="toast" class="on"', false)
            ->assertSee('Terlalu banyak percobaan masuk yang gagal.')
            ->assertSee('Coba lagi dalam');

        $this->assertSame(6, LogAktivitas::where('aksi', 'login_gagal')->count());
    }
}
