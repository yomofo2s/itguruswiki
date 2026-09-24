<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Post;
use Database\Seeders\CategorySeeder;
use Tests\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public static function pages(): array
    {
        return [['/'], ['/about'], ['/code-of-conduct'], ['/privacy'], ['/community'], ['/contact'], ['/guides'], ['/news'], ['/news?type=event'], ['/login'], ['/register'], ['/forgot-password'], ['/up']];
    }

    #[DataProvider('pages')]
    public function test_public_pages_load(string $url): void
    {
        $this->seed(CategorySeeder::class);
        Article::factory()->published()->recycle(Category::all())->count(3)->create();
        Post::factory()->count(2)->create();
        Post::factory()->event()->create();

        $this->get($url)->assertOk();
    }

    public function test_home_shows_topics_and_published_guides_only(): void
    {
        $this->seed(CategorySeeder::class);
        $published = Article::factory()->published()->recycle(Category::all())->create(['featured' => true]);
        $draft = Article::factory()->recycle(Category::all())->create();

        $this->get('/')
            ->assertOk()
            ->assertSee('Study in Germany')
            ->assertSee($published->title)
            ->assertDontSee($draft->title);
    }

    public function test_guides_can_be_searched_and_filtered_by_topic(): void
    {
        $study = Category::factory()->create(['name' => 'Study', 'slug' => 'study']);
        $work = Category::factory()->create(['name' => 'Work', 'slug' => 'work']);
        $a = Article::factory()->published()->for($study)->create(['title' => 'Opening a blocked account']);
        $b = Article::factory()->published()->for($work)->create(['title' => 'Applying for the EU Blue Card']);

        $this->get('/guides?q=blocked')->assertSee($a->title)->assertDontSee($b->title);
        $this->get('/guides/topic/work')->assertSee($b->title)->assertDontSee($a->title);
        $this->get('/guides?q=%25')->assertOk()->assertDontSee($a->title); // LIKE wildcards are escaped
    }

    public function test_published_guide_is_shown_and_view_counted(): void
    {
        $article = Article::factory()->published()->create(['body' => "## Steps\n\n1. Book an appointment\n\n| Doc | Needed |\n|---|---|\n| Passport | yes |"]);

        $this->get(route('guides.show', $article))
            ->assertOk()
            ->assertSee($article->title)
            ->assertSee('<h2>Steps</h2>', false)
            ->assertSee('<table>', false);

        $this->assertSame(1, $article->fresh()->views);
    }

    public function test_unpublished_guides_are_hidden_from_the_public(): void
    {
        $draft = Article::factory()->create();
        $pending = Article::factory()->pending()->create();
        $scheduled = Article::factory()->published()->create(['published_at' => now()->addDay()]);

        foreach ([$draft, $pending, $scheduled] as $article) {
            $this->get(route('guides.show', $article))->assertForbidden();
        }
    }

    public function test_markdown_cannot_inject_html_or_javascript(): void
    {
        $article = Article::factory()->published()->create([
            'body' => "Hello <script>alert('x')</script>\n\n[click](javascript:alert(1))\n\n<img src=x onerror=alert(1)>",
        ]);

        $this->get(route('guides.show', $article))
            ->assertOk()
            ->assertDontSee('<script>alert', false)
            ->assertDontSee('javascript:alert', false)
            ->assertDontSee('<img src=x', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_news_shows_published_posts_and_hides_drafts(): void
    {
        $post = Post::factory()->create();
        $draft = Post::factory()->draft()->create();
        $event = Post::factory()->event()->create();

        $this->get('/news')->assertSee($post->title)->assertSee($event->title)->assertDontSee($draft->title);
        $this->get('/news?type=event')->assertSee($event->title)->assertDontSee($post->title);
        $this->get(route('news.show', $post))->assertOk();
        $this->get(route('news.show', $draft))->assertNotFound();
    }

    public function test_unknown_pages_return_a_friendly_404(): void
    {
        $this->get('/guides/does-not-exist')->assertNotFound()->assertSee('find that page');
    }
}
