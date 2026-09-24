<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Article;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\User;
use App\Models\Volunteer;
use Tests\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_members_cannot_access_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_all_admin_pages_render_for_admins(): void
    {
        $admin = User::factory()->admin()->create();
        Article::factory()->pending()->create();
        $post = Post::factory()->event()->create();
        $category = Category::factory()->create();
        $message = ContactMessage::create(['name' => 'A', 'email' => 'a@example.com', 'subject' => 'Hello', 'message' => 'Hello there, friends']);
        Volunteer::create(['name' => 'V', 'email' => 'v@example.com', 'interest' => 'writer']);

        foreach ([
            '/admin', '/admin/articles', '/admin/articles?status=all', '/admin/articles?status=published',
            '/admin/posts', '/admin/posts/create', route('admin.posts.edit', $post),
            '/admin/categories', route('admin.categories.edit', $category),
            '/admin/messages', route('admin.messages.show', $message), '/admin/volunteers', '/admin/users',
            '/dashboard', '/dashboard/articles/create', '/profile',
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_editors_cannot_manage_users(): void
    {
        $editor = User::factory()->editor()->create();
        $member = User::factory()->create();

        $this->actingAs($editor)->get('/admin')->assertOk();
        $this->actingAs($editor)->get('/admin/users')->assertForbidden();
        $this->actingAs($editor)->patch(route('admin.users.update', $member), ['role' => 'admin'])->assertForbidden();
        $this->assertSame(Role::Member, $member->fresh()->role);
    }

    public function test_admin_can_change_roles_but_not_demote_themselves(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $this->actingAs($admin)->patch(route('admin.users.update', $member), ['role' => 'editor'])->assertSessionHasNoErrors();
        $this->assertSame(Role::Editor, $member->fresh()->role);

        $this->actingAs($admin)->patch(route('admin.users.update', $admin), ['role' => 'member'])->assertSessionHasErrors('role');
        $this->assertSame(Role::Admin, $admin->fresh()->role);
    }

    public function test_editor_manages_news_and_events(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->post(route('admin.posts.store'), [
            'type' => 'event', 'title' => 'Career meetup in Frankfurt', 'body' => 'Join us for networking.',
            'published_at' => now()->subMinute()->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors('event_starts_at');

        $this->actingAs($editor)->post(route('admin.posts.store'), [
            'type' => 'event', 'title' => 'Career meetup in Frankfurt', 'body' => 'Join us for networking.',
            'event_starts_at' => now()->addWeek()->format('Y-m-d\TH:i'), 'event_location' => 'Frankfurt',
            'published_at' => now()->subMinute()->format('Y-m-d\TH:i'),
        ])->assertRedirect(route('admin.posts.index'));

        $post = Post::sole();
        $this->assertTrue($post->isEvent());
        $this->get('/')->assertSee('Career meetup in Frankfurt');

        $this->actingAs($editor)->put(route('admin.posts.update', $post), [
            'type' => 'news', 'title' => 'Career meetup recap', 'body' => 'It was great.', 'published_at' => '',
        ])->assertRedirect();
        $post->refresh();
        $this->assertNull($post->event_starts_at);
        $this->assertFalse($post->isPublished());
        $this->assertSame('career-meetup-recap', $post->slug);

        $this->actingAs($editor)->delete(route('admin.posts.destroy', $post));
        $this->assertModelMissing($post);
    }

    public function test_topics_can_be_managed_and_non_empty_topics_are_protected(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->post(route('admin.categories.store'), ['name' => 'Driving Licence', 'icon' => 'sparkles', 'sort_order' => 5])
            ->assertRedirect(route('admin.categories.index'));
        $category = Category::where('slug', 'driving-licence')->sole();

        Article::factory()->for($category)->create();
        $this->actingAs($editor)->delete(route('admin.categories.destroy', $category))->assertSessionHasErrors('category');
        $this->assertModelExists($category);
    }

    #[Group('console')]
    public function test_create_admin_command(): void
    {
        $this->artisan('app:create-admin', ['email' => 'boss@example.com', '--name' => 'Boss'])
            ->expectsQuestion('Password (min. 12 characters)', 'a-very-long-password')
            ->assertSuccessful();

        $user = User::where('email', 'boss@example.com')->sole();
        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->hasVerifiedEmail());
    }
}
