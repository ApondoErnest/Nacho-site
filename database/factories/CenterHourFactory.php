<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\CenterHour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CenterHour>
 */
class CenterHourFactory extends Factory
{
    public function definition(): array
    {
        return [
            'center_id' => Center::factory(),
            'day_of_week' => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']),
            'opens_at' => '08:00',
            'closes_at' => '16:00',
            'is_closed' => false,
            'special_note_en' => null,
            'special_note_fr' => null,
        ];
    }
}
