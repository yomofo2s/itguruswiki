<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\RefreshDatabase;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    private function googleReturns(string $email, bool $verified = true, string $id = 'g-123'): void
    {
        config(['services.google.client_id' => 'id']);

        $user = (new SocialiteUser)->setRaw(['email_verified' => $verified])
            ->map(['id' => $id, 'name' => 'Ngozi Obi', 'email' => $email]);
        $user->user = ['email_verified' => $verified];

        Socialite::shouldReceive('driver->user')->andReturn($user);
    }

    public function test_button_is_hidden_until_google_is_configured(): void
    {
        $this->get('/login')->assertDontSee('Continue with Google');

        config(['services.google.client_id' => 'id']);
        $this->get('/login')->assertSee('Continue with Google');
        $this->get('/register')->assertSee('Continue with Google');
    }

    public function test_new_google_user_gets_a_verified_member_account(): void
    {
        $this->googleReturns('Ngozi@Example.com');

        $this->get('/auth/google/callback')->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'ngozi@example.com')->sole();
        $this->assertSame('g-123', $user->google_id);
        $this->assertSame(Role::Member, $user->role);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertAuthenticatedAs($user);
    }

    public function test_google_links_to_an_existing_account_with_the_same_email(): void
    {
        $existing = User::factory()->create(['email' => 'ngozi@example.com', 'role' => Role::Editor]);
        $this->googleReturns('ngozi@example.com');

        $this->get('/auth/google/callback');

        $this->assertSame(1, User::count());
        $this->assertSame('g-123', $existing->fresh()->google_id);
        $this->assertSame(Role::Editor, $existing->fresh()->role);
        $this->assertAuthenticatedAs($existing);
    }

    public function test_linking_an_unverified_account_invalidates_its_old_password(): void
    {
        $existing = User::factory()->unverified()->create(['email' => 'ngozi@example.com', 'password' => 'attacker-knows-this']);
        $this->googleReturns('ngozi@example.com');

        $this->get('/auth/google/callback');

        $this->assertFalse(Hash::check('attacker-knows-this', $existing->fresh()->password));
        $this->assertTrue($existing->fresh()->hasVerifiedEmail());
    }

    public function test_unverified_google_email_is_rejected(): void
    {
        $this->googleReturns('ngozi@example.com', verified: false);

        $this->get('/auth/google/callback')->assertRedirect(route('login'));

        $this->assertSame(0, User::count());
        $this->assertGuest();
    }
}
