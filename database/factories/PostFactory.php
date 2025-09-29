<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6, true);

        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title) . '-' . \Illuminate\Support\Str::random(8),
            'content' => fake()->paragraphs(fake()->numberBetween(5, 15), true),
            'excerpt' => fake()->paragraph(3),
            'cover_image' => fake()->boolean(30) ? 'https://picsum.photos/800/400?random=' . fake()->numberBetween(1, 1000) : null,
            'featured_image' => fake()->boolean(50) ? 'posts/sample-' . fake()->numberBetween(1, 10) . '.jpg' : null,
            'featured_image_alt' => fake()->boolean(70) ? fake()->sentence(4) : null,
            'status' => fake()->randomElement(['draft', 'published']),
            'like_count' => fake()->numberBetween(0, 100),
        ];
    }
}
