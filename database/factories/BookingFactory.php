<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Center;
use App\Models\Service;
use App\Models\Tariff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_reference' => 'NACHO-'.now()->format('Ymd').'-'.fake()->unique()->numerify('####'),
            'full_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'center_id' => Center::factory(),
            'service_id' => Service::factory(),
            'tariff_id' => Tariff::factory(),
            'vehicle_registration' => strtoupper(fake()->bothify('??-###-??')),
            'preferred_date' => today()->addDays(fake()->numberBetween(1, 14)),
            'preferred_time' => fake()->randomElement(['08:00', '09:30', '11:00', '14:00']),
            'document_path' => null,
            'comment' => null,
            'consent' => true,
            'status' => BookingStatus::PENDING->value,
            'admin_notes' => null,
        ];
    }
}
