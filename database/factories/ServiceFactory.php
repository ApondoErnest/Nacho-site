<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'title_en' => Str::title($title),
            'title_fr' => Str::title($title),
            'short_description_en' => fake()->sentence(),
            'short_description_fr' => fake()->sentence(),
            'full_description_en' => fake()->paragraph(),
            'full_description_fr' => fake()->paragraph(),
            'icon' => 'clipboard-check',
            'featured_image' => null,
            'is_active' => true,
            'display_order' => fake()->numberBetween(1, 50),
            'seo_title_en' => null,
            'seo_title_fr' => null,
            'meta_description_en' => null,
            'meta_description_fr' => null,
        ];
    }
}
