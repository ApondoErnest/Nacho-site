<?php

namespace Database\Factories;

use App\Models\Tariff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tariff>
 */
class TariffFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_code' => fake()->randomElement(['A', 'B', 'B1', 'C', 'D']),
            'category_slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'name_en' => Str::title($name),
            'name_fr' => Str::title($name),
            'description_en' => fake()->sentence(),
            'description_fr' => fake()->sentence(),
            'price_fcfa' => fake()->numberBetween(4000, 50000),
            'validity_value' => fake()->randomElement([3, 6, 12]),
            'validity_unit' => 'months',
            'minimum_weight_kg' => null,
            'maximum_weight_kg' => null,
            'vehicle_icon' => 'car-front',
            'effective_date' => null,
            'expiry_date' => null,
            'regulatory_reference' => null,
            'last_verified_at' => null,
            'is_active' => true,
            'is_bookable' => true,
            'display_order' => fake()->numberBetween(1, 50),
        ];
    }
}
