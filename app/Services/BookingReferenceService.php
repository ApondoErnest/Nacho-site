<?php

namespace App\Services;

use App\Models\Booking;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use RuntimeException;

class BookingReferenceService
{
    public function generate(DateTimeInterface|string|null $date = null): string
    {
        $date = $date instanceof DateTimeInterface
            ? Carbon::instance($date)
            : ($date ? Carbon::parse($date) : today());

        for ($attempt = 0; $attempt < 20; $attempt++) {
            $reference = sprintf('NACHO-%s-%04d', $date->format('Ymd'), random_int(0, 9999));

            if (! Booking::query()->where('booking_reference', $reference)->exists()) {
                return $reference;
            }
        }

        throw new RuntimeException('Unable to generate a unique booking reference.');
    }
}
