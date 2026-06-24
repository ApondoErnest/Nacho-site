<?php

namespace Database\Factories;

use App\Enums\CenterStatus;
use App\Models\Center;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Center>
 */
class CenterFactory extends Factory
{
    public function definition(): array
    {
        $name = 'NACHO '.fake()->unique()->city();

        return [
            'name_en' => $name,
            'name_fr' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'city_en' => fake()->city(),
            'city_fr' => fake()->city(),
            'region_en' => fake()->city(),
            'region_fr' => fake()->city(),
            'address_en' => fake()->streetAddress(),
            'address_fr' => fake()->streetAddress(),
            'postal_address' => null,
            'status' => CenterStatus::ACTIVE->value,
            'is_headquarters' => false,
            'booking_enabled' => true,
            'description_en' => fake()->paragraph(),
            'description_fr' => fake()->paragraph(),
            'latitude' => fake()->latitude(2, 13),
            'longitude' => fake()->longitude(8, 16),
            'google_maps_url' => null,
            'nearby_landmark' => fake()->streetName(),
            'search_keywords' => null,
            'vehicle_categories_en' => null,
            'vehicle_categories_fr' => null,
            'featured_image' => null,
            'target_opening_date' => null,
            'target_date_text_en' => null,
            'target_date_text_fr' => null,
            'expansion_phase' => null,
            'expansion_updated_at' => null,
            'display_order' => fake()->numberBetween(1, 50),
            'is_featured' => false,
            'is_active' => true,
        ];
    }

    public function construction(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CenterStatus::CONSTRUCTION->value,
            'booking_enabled' => false,
            'target_date_text_en' => 'Before November 2026',
            'target_date_text_fr' => 'Avant novembre 2026',
        ]);
    }
}
