<?php

namespace Database\Factories;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        $title = rtrim(fake()->unique()->sentence(6), '.');

        return [
            'category_id' => Category::factory(),
            'author_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(18),
            'body' => "## Overview\n\n".implode("\n\n", fake()->paragraphs(4))."\n\n## Steps\n\n1. ".implode("\n1. ", fake()->sentences(3)),
            'status' => ArticleStatus::Draft,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDays(fake()->numberBetween(1, 60)),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => ArticleStatus::Pending]);
    }
}
