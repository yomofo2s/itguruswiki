<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Volunteer;
use App\Notifications\ContactMessageReceived;
use App\Notifications\VolunteerSignedUp;
use Tests\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ContactAndVolunteerTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_stores_message_and_notifies_team(): void
    {
        Notification::fake();

        $this->post('/contact', [
            'name' => 'Chidi', 'email' => 'chidi@example.com',
            'subject' => 'Question about Blue Card', 'message' => 'What salary do I need for a Blue Card?',
        ])->assertRedirect('/contact')->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', ['email' => 'chidi@example.com']);
        Notification::assertSentTo(new AnonymousNotifiable, ContactMessageReceived::class,
            fn ($n, $channels, $notifiable) => $notifiable->routes['mail'] === config('itgurus.notify_email'));
    }

    public function test_contact_form_validates_and_blocks_honeypot(): void
    {
        $this->post('/contact', ['name' => '', 'email' => 'bad', 'subject' => '', 'message' => 'short'])
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

        $this->post('/contact', [
            'name' => 'Bot', 'email' => 'bot@example.com', 'subject' => 'Buy', 'message' => 'Cheap stuff here!!', 'website' => 'x',
        ])->assertSessionHasErrors('website');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_contact_form_is_rate_limited(): void
    {
        Notification::fake();
        $data = ['name' => 'A', 'email' => 'a@example.com', 'subject' => 'Hi', 'message' => 'Hello there, friends'];

        foreach (range(1, 5) as $i) {
            $this->post('/contact', $data);
        }

        $this->post('/contact', $data)->assertTooManyRequests();
    }

    private function volunteerForm(array $overrides = [], int $secondsAgo = 10): array
    {
        return $overrides + [
            'name' => 'Ngozi', 'email' => 'ngozi@example.com', 'interest' => 'developer', 'skills' => 'Laravel, Vue',
            'started_at' => Crypt::encryptString(now()->subSeconds($secondsAgo)->timestamp),
        ];
    }

    public function test_volunteer_sign_up(): void
    {
        Notification::fake();

        $this->post('/community/volunteer', $this->volunteerForm())
            ->assertRedirect(route('community').'#volunteer');

        $this->assertSame('Develop the website', Volunteer::sole()->interestLabel());
        Notification::assertSentTo(new AnonymousNotifiable, VolunteerSignedUp::class);

        $this->post('/community/volunteer', $this->volunteerForm(['email' => 'x@example.com', 'interest' => 'hacker']))
            ->assertSessionHasErrors('interest');
    }

    public function test_volunteer_form_rejects_instant_stale_and_link_spam(): void
    {
        Notification::fake();

        $this->post('/community/volunteer', $this->volunteerForm(secondsAgo: 0))->assertStatus(422);
        $this->post('/community/volunteer', $this->volunteerForm(secondsAgo: 9000))->assertStatus(422);
        $this->post('/community/volunteer', $this->volunteerForm(['skills' => 'visit https://spam.example']))->assertStatus(422);
        $this->post('/community/volunteer', $this->volunteerForm(['started_at' => 'garbage']))->assertStatus(422);

        $this->assertSame(0, Volunteer::count());
        Notification::assertNothingSent();
    }

    public function test_repeat_volunteer_email_is_not_stored_or_mailed_twice(): void
    {
        Notification::fake();

        $this->post('/community/volunteer', $this->volunteerForm());
        $this->post('/community/volunteer', $this->volunteerForm());

        $this->assertSame(1, Volunteer::count());
        Notification::assertSentToTimes(new AnonymousNotifiable, VolunteerSignedUp::class, 1);
    }

    public function test_volunteer_alert_emails_are_capped_per_hour(): void
    {
        Notification::fake();
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        foreach (range(1, 15) as $i) {
            $this->post('/community/volunteer', $this->volunteerForm(['email' => "v$i@example.com"]));
        }

        $this->assertSame(15, Volunteer::count());
        Notification::assertSentToTimes(new AnonymousNotifiable, VolunteerSignedUp::class, 10);
    }

    public function test_contact_form_still_works_when_the_mail_server_fails(): void
    {
        Notification::shouldReceive('route')->andThrow(new \RuntimeException('SMTP down'));

        $this->post('/contact', [
            'name' => 'Chidi', 'email' => 'chidi@example.com', 'website' => '',
            'subject' => 'Question', 'message' => 'Is the site working without mail?',
        ])->assertRedirect('/contact')->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', ['email' => 'chidi@example.com']);
    }
}
