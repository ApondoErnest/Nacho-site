<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BlogPost>
 */
class BlogPostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'blog_category_id' => BlogCategory::factory(),
            'author_id' => User::factory(),
            'title_en' => $title,
            'title_fr' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'excerpt_en' => fake()->sentence(),
            'excerpt_fr' => fake()->sentence(),
            'content_en' => fake()->paragraphs(3, true),
            'content_fr' => fake()->paragraphs(3, true),
            'featured_image' => null,
            'status' => ContentStatus::PUBLISHED->value,
            'published_at' => now(),
            'seo_title_en' => null,
            'seo_title_fr' => null,
            'meta_description_en' => null,
            'meta_description_fr' => null,
        ];
    }
}
