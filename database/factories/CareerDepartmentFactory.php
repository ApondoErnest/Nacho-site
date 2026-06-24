<?php

namespace Database\Factories;

use App\Models\CareerDepartment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CareerDepartment>
 */
class CareerDepartmentFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name_en' => Str::title($name),
            'name_fr' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'description_en' => fake()->sentence(),
            'description_fr' => fake()->sentence(),
            'icon' => 'briefcase-business',
            'display_order' => fake()->numberBetween(1, 50),
            'is_active' => true,
        ];
    }
}
