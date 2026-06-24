<?php

namespace Database\Factories;

use App\Enums\ContactType;
use App\Models\Center;
use App\Models\CenterContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CenterContact>
 */
class CenterContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'center_id' => Center::factory(),
            'type' => ContactType::PHONE->value,
            'label_en' => 'Primary',
            'label_fr' => 'Principal',
            'value' => fake()->phoneNumber(),
            'is_primary' => true,
            'is_public' => true,
            'display_order' => 1,
        ];
    }
}
