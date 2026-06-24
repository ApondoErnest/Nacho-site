<?php

namespace Database\Factories;

use App\Enums\SettingType;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    public function definition(): array
    {
        $key = Str::slug(fake()->unique()->words(3, true), '_');

        return [
            'key' => $key,
            'value' => fake()->sentence(),
            'type' => SettingType::TEXT->value,
        ];
    }
}
