<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uploaded_by' => User::factory(),
            'file_name' => fake()->word().'.jpg',
            'file_path' => 'uploads/'.fake()->uuid().'.jpg',
            'file_type' => 'image',
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(10_000, 2_000_000),
            'alt_text_en' => fake()->sentence(),
            'alt_text_fr' => fake()->sentence(),
        ];
    }
}
