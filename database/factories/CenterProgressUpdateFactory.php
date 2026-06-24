<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\CenterProgressUpdate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CenterProgressUpdate>
 */
class CenterProgressUpdateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'center_id' => Center::factory()->construction(),
            'phase' => 'Civil Works in Progress',
            'update_en' => fake()->paragraph(),
            'update_fr' => fake()->paragraph(),
            'published_at' => now(),
            'image_path' => null,
            'is_published' => true,
        ];
    }
}
