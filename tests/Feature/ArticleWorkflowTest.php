<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Notifications\ArticleReviewed;
use App\Notifications\ArticleSubmitted;
use Tests\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function guide(array $overrides = []): array
    {
        return $overrides + [
            'title' => 'How to register your address (Anmeldung)',
            'category_id' => Category::factory()->create()->id,
            'excerpt' => 'Everything you need for the Bürgeramt appointment.',
            'body' => "## Documents\n\n- Passport\n- Wohnungsgeberbestätigung from your landlord",
            'source_url' => 'https://www.berlin.de/',
            'action' => 'submit',
        ];
    }

    public function test_member_submits_guide_editor_publishes_it(): void
    {
        Notification::fake();
        $member = User::factory()->create();
        $editor = User::factory()->editor()->create();

        $this->actingAs($member)->post(route('dashboard.articles.store'), $this->guide())
            ->assertRedirect(route('dashboard'));

        $article = Article::sole();
        $this->assertSame(ArticleStatus::Pending, $article->status);
        $this->assertSame('how-to-register-your-address-anmeldung', $article->slug);
        $this->assertTrue($article->author->is($member));
        Notification::assertSentTo($editor, ArticleSubmitted::class);

        // Not public yet, but the author can preview it
        $this->post('/logout');
        $this->get(route('guides.show', $article))->assertForbidden();
        $this->actingAs($member)->get(route('guides.show', $article))->assertOk()->assertSee('Preview');

        // Member cannot publish their own guide
        $this->actingAs($member)->post(route('admin.articles.publish', $article))->assertForbidden();

        $this->actingAs($editor)->get(route('admin.articles.index'))->assertOk()->assertSee($article->title);
        $this->actingAs($editor)->post(route('admin.articles.publish', $article))->assertRedirect();

        $article->refresh();
        $this->assertTrue($article->isPublished());
        $this->assertTrue($article->reviewer->is($editor));
        Notification::assertSentTo($member, ArticleReviewed::class);

        auth()->logout();
        $this->get(route('guides.show', $article))->assertOk();
        $this->get(route('guides.index'))->assertSee($article->title);
    }

    public function test_editor_can_request_changes_and_author_can_edit(): void
    {
        Notification::fake();
        $member = User::factory()->create();
        $editor = User::factory()->editor()->create();
        $article = Article::factory()->pending()->for($member, 'author')->create();

        $this->actingAs($editor)->post(route('admin.articles.reject', $article), ['review_note' => 'Please add the official link.'])
            ->assertRedirect();
        $this->assertSame(ArticleStatus::Rejected, $article->fresh()->status);
        Notification::assertSentTo($member, ArticleReviewed::class);

        $this->actingAs($member)->get(route('dashboard'))->assertSee('Please add the official link.');
        $this->actingAs($member)->put(route('dashboard.articles.update', $article), $this->guide(['category_id' => $article->category_id, 'title' => 'Updated title']))
            ->assertRedirect(route('dashboard'));

        $this->assertSame(ArticleStatus::Pending, $article->fresh()->status);
        $this->assertSame('Updated title', $article->fresh()->title);
    }

    public function test_authors_cannot_edit_published_or_other_peoples_guides(): void
    {
        $member = User::factory()->create();
        $published = Article::factory()->published()->for($member, 'author')->create();
        $other = Article::factory()->create();

        $this->actingAs($member)->get(route('dashboard.articles.edit', $published))->assertForbidden();
        $this->actingAs($member)->get(route('dashboard.articles.edit', $other))->assertForbidden();
        $this->actingAs($member)->delete(route('dashboard.articles.destroy', $other))->assertForbidden();
        $this->assertModelExists($other);
    }

    public function test_unverified_users_cannot_write_guides(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get(route('dashboard.articles.create'))->assertRedirect(route('verification.notice'));
    }

    public function test_drafts_stay_private_and_duplicate_titles_get_unique_slugs(): void
    {
        $member = User::factory()->create();
        Article::factory()->published()->create(['title' => 'Same title', 'slug' => 'same-title']);

        $this->actingAs($member)->post(route('dashboard.articles.store'), $this->guide(['title' => 'Same title', 'action' => 'draft']));

        $draft = Article::where('author_id', $member->id)->sole();
        $this->assertSame(ArticleStatus::Draft, $draft->status);
        $this->assertSame('same-title-2', $draft->slug);
    }

    public function test_editor_can_publish_directly_with_cover_image(): void
    {
        Storage::fake('public');
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->post(route('dashboard.articles.store'), $this->guide([
            'action' => 'publish',
            'cover' => UploadedFile::fake()->image('cover.jpg', 1200, 600),
        ]))->assertSessionHasNoErrors();

        $article = Article::sole();
        $this->assertTrue($article->isPublished());
        Storage::disk('public')->assertExists($article->cover_path);
    }

    public function test_member_publish_action_is_downgraded_to_draft(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->post(route('dashboard.articles.store'), $this->guide(['action' => 'publish']));

        $this->assertSame(ArticleStatus::Draft, Article::sole()->status);
    }

    public function test_validation_errors_are_shown(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->post(route('dashboard.articles.store'), ['title' => 'x', 'body' => '', 'source_url' => 'javascript:alert(1)'])
            ->assertSessionHasErrors(['title', 'body', 'category_id', 'source_url']);
    }

    public function test_markdown_preview_endpoint(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->postJson(route('dashboard.preview'), ['body' => '**bold** <b>raw</b>'])
            ->assertOk()
            ->assertJsonPath('html', "<p><strong>bold</strong> &lt;b&gt;raw&lt;/b&gt;</p>\n");
    }

    public function test_editor_can_feature_unpublish_and_delete(): void
    {
        $editor = User::factory()->editor()->create();
        $article = Article::factory()->published()->create();

        $this->actingAs($editor)->post(route('admin.articles.feature', $article));
        $this->assertTrue($article->fresh()->featured);

        $this->actingAs($editor)->post(route('admin.articles.unpublish', $article));
        $this->assertFalse($article->fresh()->isPublished());

        $this->actingAs($editor)->delete(route('admin.articles.destroy', $article))->assertRedirect(route('admin.articles.index'));
        $this->assertModelMissing($article);
    }
}
