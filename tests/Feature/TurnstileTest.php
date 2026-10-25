<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\RefreshDatabase;
use Tests\TestCase;

class TurnstileTest extends TestCase
{
    use RefreshDatabase;

    private function enable(): void
    {
        config(['services.turnstile.site_key' => 'site', 'services.turnstile.secret' => 'secret']);
        Notification::fake();
    }

    private function contact(array $extra = []): array
    {
        return $extra + ['name' => 'Chidi', 'email' => 'chidi@example.com', 'subject' => 'Hello', 'message' => 'A message that is long enough.'];
    }

    public function test_widget_is_only_rendered_when_configured(): void
    {
        $this->get('/contact')->assertDontSee('cf-turnstile');

        $this->enable();
        $this->get('/contact')->assertSee('cf-turnstile', false);
        $this->get('/register')->assertSee('cf-turnstile', false);
        $this->get('/community')->assertSee('cf-turnstile', false);
    }

    public function test_forms_work_without_a_token_while_turnstile_is_disabled(): void
    {
        Notification::fake();

        $this->post('/contact', $this->contact())->assertSessionHasNoErrors();
        $this->assertSame(1, ContactMessage::count());
    }

    public function test_missing_or_failed_token_is_rejected(): void
    {
        $this->enable();
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);

        $this->post('/contact', $this->contact())->assertSessionHasErrors('cf-turnstile-response');
        $this->post('/contact', $this->contact(['cf-turnstile-response' => 'bad']))->assertSessionHasErrors('cf-turnstile-response');
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_valid_token_is_accepted_and_sent_to_cloudflare(): void
    {
        $this->enable();
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

        $this->post('/contact', $this->contact(['cf-turnstile-response' => 'good']))->assertSessionHasNoErrors();

        $this->assertSame(1, ContactMessage::count());
        Http::assertSent(fn ($request) => $request['secret'] === 'secret' && $request['response'] === 'good');
    }

    public function test_cloudflare_outage_does_not_lock_people_out(): void
    {
        $this->enable();
        Http::fake(['challenges.cloudflare.com/*' => Http::response('', 500)]);

        $this->post('/contact', $this->contact(['cf-turnstile-response' => 'x']))->assertSessionHasNoErrors();

        $this->assertSame(1, ContactMessage::count());
    }

    public function test_volunteer_and_register_forms_are_protected_too(): void
    {
        $this->enable();
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);
        $started = Crypt::encryptString((string) now()->subSeconds(10)->timestamp);

        $this->post('/community/volunteer', [
            'name' => 'Ngozi', 'email' => 'ngozi@example.com', 'interest' => 'writer', 'started_at' => $started,
        ])->assertSessionHasErrors('cf-turnstile-response');

        $this->post('/register', [
            'name' => 'Ada', 'email' => 'ada@example.com', 'password' => 'secret-password-1',
            'password_confirmation' => 'secret-password-1', 'terms' => '1', 'started_at' => $started,
        ])->assertSessionHasErrors('cf-turnstile-response');

        $this->assertSame(0, Volunteer::count());
        $this->assertSame(0, User::count());
    }
}
