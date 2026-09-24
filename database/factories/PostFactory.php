<?php

namespace Database\Factories;

use App\Enums\PostType;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = rtrim(fake()->unique()->sentence(5), '.');

        return [
            'author_id' => User::factory(),
            'type' => PostType::News,
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(15),
            'body' => implode("\n\n", fake()->paragraphs(3)),
            'published_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['published_at' => null]);
    }

    public function event(): static
    {
        return $this->state(fn () => [
            'type' => PostType::Event,
            'event_starts_at' => now()->addDays(fake()->numberBetween(3, 60))->setTime(18, 0),
            'event_location' => fake()->randomElement(['Berlin', 'Munich', 'Frankfurt', 'Cologne', 'Online']),
        ]);
    }
}
