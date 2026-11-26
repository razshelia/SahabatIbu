<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_link_is_emailed_and_not_exposed_on_the_web_page(): void
    {
        Notification::fake();

        $user = User::forceCreate([
            'name' => 'Nasabah Test',
            'email' => 'nasabah-reset@example.test',
            'password' => Hash::make('PasswordLama123'),
            'role' => 'nasabah',
            'status' => 'aktif',
            'must_change_password' => false,
        ]);

        $this->from(route('password.forgot.form'))
            ->post(route('password.forgot.send'), ['email' => $user->email])
            ->assertRedirect(route('password.forgot.form'))
            ->assertSessionHas('status', 'Jika email terdaftar, tautan untuk mengatur ulang kata sandi akan dikirim.')
            ->assertSessionMissing('demo_reset_link');

        Notification::assertSentTo($user, ResetPassword::class);

        $notification = Notification::sent($user, ResetPassword::class)->first();
        $this->assertNotNull($notification);

        $resetUrl = $notification->toMail($user)->actionUrl;
        $this->assertSame(route('password.reset.form', [
            'token' => $notification->token,
            'email' => $user->email,
        ]), $resetUrl);

        $this->get(route('password.forgot.form'))
            ->assertOk()
            ->assertDontSee('Tautan untuk uji lokal')
            ->assertDontSee($notification->token);

        $this->get($resetUrl)->assertOk();

        $this->post(route('password.reset.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'PasswordBaru456',
            'password_confirmation' => 'PasswordBaru456',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Kata sandi berhasil diatur ulang. Silakan masuk dengan kata sandi baru.');

        $this->assertTrue(Hash::check('PasswordBaru456', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_unknown_email_gets_the_same_generic_reset_confirmation(): void
    {
        Notification::fake();

        $this->from(route('password.forgot.form'))
            ->post(route('password.forgot.send'), ['email' => 'unknown@example.test'])
            ->assertRedirect(route('password.forgot.form'))
            ->assertSessionHas('status', 'Jika email terdaftar, tautan untuk mengatur ulang kata sandi akan dikirim.')
            ->assertSessionMissing('demo_reset_link');

        Notification::assertNothingSent();
    }
}
