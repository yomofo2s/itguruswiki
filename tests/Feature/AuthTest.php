<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_people_can_register_and_must_verify_their_email(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Ada Obi',
            'email' => 'ada@example.com',
            'password' => 'secret-password-1',
            'password_confirmation' => 'secret-password-1',
            'terms' => '1',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'ada@example.com')->firstOrFail();
        $this->assertSame(Role::Member, $user->role);
        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->get('/dashboard')->assertRedirect(route('verification.notice'));

        $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $user->id, 'hash' => sha1($user->email)]);
        $this->get($url)->assertRedirect('/dashboard');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->get('/dashboard')->assertOk();
    }

    public function test_registration_rejects_bots_filling_the_honeypot(): void
    {
        $this->post('/register', [
            'name' => 'Bot', 'email' => 'bot@example.com',
            'password' => 'secret-password-1', 'password_confirmation' => 'secret-password-1',
            'terms' => '1', 'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseMissing('users', ['email' => 'bot@example.com']);
    }

    public function test_users_can_log_in_and_out(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse-1']);

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'correct-horse-1'])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 5) as $i) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong']);
        }

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_password_can_be_reset(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $this->get('/reset-password/'.$notification->token.'?email='.$user->email)->assertOk();

            $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'brand-new-pass-9',
                'password_confirmation' => 'brand-new-pass-9',
            ])->assertRedirect(route('login'));

            return true;
        });

        $this->post('/login', ['email' => $user->email, 'password' => 'brand-new-pass-9']);
        $this->assertAuthenticatedAs($user);
    }

    public function test_forgot_password_does_not_reveal_unknown_emails(): void
    {
        $this->post('/forgot-password', ['email' => 'nobody@example.com'])
            ->assertSessionHas('status')
            ->assertSessionHasNoErrors();
    }

    public function test_profile_can_be_updated_and_account_deleted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', ['name' => 'New Name', 'email' => $user->email, 'bio' => 'Hi'])
            ->assertSessionHasNoErrors();
        $this->assertSame('New Name', $user->fresh()->name);

        $this->actingAs($user)->delete('/profile', ['password' => 'wrong'])->assertSessionHasErrorsIn('deletion', 'password');
        $this->actingAs($user)->delete('/profile', ['password' => 'password'])->assertRedirect('/');
        $this->assertModelMissing($user);
    }
}
