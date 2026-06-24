<?php

namespace Database\Factories;

use App\Enums\TariffRevisionStatus;
use App\Models\Tariff;
use App\Models\TariffRevision;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TariffRevision>
 */
class TariffRevisionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tariff_id' => Tariff::factory(),
            'created_by' => User::factory(),
            'snapshot' => [
                'price_fcfa' => fake()->numberBetween(4000, 50000),
                'validity_value' => fake()->randomElement([3, 6, 12]),
                'validity_unit' => 'months',
            ],
            'effective_date' => today(),
            'published_at' => now(),
            'status' => TariffRevisionStatus::ACTIVE->value,
        ];
    }
}
