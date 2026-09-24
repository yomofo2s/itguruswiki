<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Category;
use App\Models\User;
use Tests\RefreshDatabase;
use Tests\TestCase;

class SetupTest extends TestCase
{
    use RefreshDatabase;

    private string $token = 'test-token-0123456789abcdefghijklmnop';

    public function test_setup_link_is_disabled_without_token(): void
    {
        config(['itgurus.setup_token' => null]);
        $this->get('/_setup/anything')->assertNotFound();

        config(['itgurus.setup_token' => 'short']);
        $this->get('/_setup/short')->assertNotFound();
    }

    public function test_wrong_token_is_rejected(): void
    {
        config(['itgurus.setup_token' => $this->token]);

        $this->get('/_setup/wrong-token-0123456789abcdefghijklmnop')->assertNotFound();
    }

    public function test_setup_migrates_seeds_and_promotes_admin(): void
    {
        config(['itgurus.setup_token' => $this->token]);
        $user = User::factory()->unverified()->create(['email' => 'boss@example.com']);

        $this->get('/_setup/'.$this->token.'?admin=boss@example.com')
            ->assertOk()
            ->assertSee('SETUP OK')
            ->assertSee('boss@example.com is now an administrator.');

        $this->assertGreaterThan(0, Category::count());
        $user->refresh();
        $this->assertSame(Role::Admin, $user->role);
        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_setup_creates_missing_admin_account(): void
    {
        config(['itgurus.setup_token' => $this->token]);

        $this->get('/_setup/'.$this->token.'?admin=New.Admin@Example.com')
            ->assertOk()
            ->assertSee('Created administrator new.admin@example.com')
            ->assertSee('Temporary password:');

        $user = User::where('email', 'new.admin@example.com')->sole();
        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->hasVerifiedEmail());
    }
}
