<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title_en' => $title,
            'title_fr' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'content_en' => fake()->paragraphs(3, true),
            'content_fr' => fake()->paragraphs(3, true),
            'status' => ContentStatus::PUBLISHED->value,
            'seo_title_en' => null,
            'seo_title_fr' => null,
            'meta_description_en' => null,
            'meta_description_fr' => null,
        ];
    }
}
