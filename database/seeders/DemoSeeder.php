<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Sample content for local development only: php artisan db:seed --class=DemoSeeder */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        $admin = User::factory()->admin()->create(['name' => 'Demo Admin', 'email' => 'admin@example.com']);
        $editor = User::factory()->editor()->create(['name' => 'Demo Editor', 'email' => 'editor@example.com']);
        $members = User::factory(4)->create();

        Category::all()->each(function (Category $category) use ($members) {
            Article::factory(3)->published()->for($category)->recycle($members)->create();
        });

        Article::query()->inRandomOrder()->limit(3)->update(['featured' => true]);
        Article::factory(2)->pending()->recycle($members)->recycle(Category::all())->create();

        Post::factory(4)->recycle([$admin, $editor])->create();
        Post::factory(2)->event()->recycle([$admin, $editor])->create();
    }
}
