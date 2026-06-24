<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\CenterStatus;
use App\Models\Booking;
use App\Models\Center;
use App\Models\Service;
use App\Models\Tariff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_booking_form_stores_pending_booking_with_reference(): void
    {
        [$center, $service, $tariff] = $this->bookableBookingDependencies();

        $response = $this->post(route('book-inspection.store'), [
            'service' => $service->slug,
            'center' => $center->slug,
            'vehicle_registration' => 'nw 123 an',
            'vehicle_category' => $tariff->category_slug,
            'previous_reference' => 'NVI-2024-00123',
            'preferred_date' => today()->addDays(2)->toDateString(),
            'preferred_hour' => '09',
            'preferred_minute' => '30',
            'full_name' => 'Jane Vehicle Owner',
            'phone_country' => '+237',
            'phone' => '6 78 45 67 89',
            'email' => 'jane@example.com',
            'additional_information' => 'Please call before confirming.',
            'consent' => '1',
        ]);

        $response
            ->assertRedirect(route('book-inspection').'#book-inspection-form')
            ->assertSessionHas('booking_reference');

        $booking = Booking::query()->sole();

        $this->assertMatchesRegularExpression('/^NACHO-'.today()->addDays(2)->format('Ymd').'-\d{4}$/', $booking->booking_reference);
        $this->assertSame(BookingStatus::PENDING, $booking->status);
        $this->assertTrue($booking->center->is($center));
        $this->assertTrue($booking->service->is($service));
        $this->assertTrue($booking->tariff->is($tariff));
        $this->assertSame('NW 123 AN', $booking->vehicle_registration);
        $this->assertSame('09:30', $booking->preferred_time);
        $this->assertSame('+237 6 78 45 67 89', $booking->phone);
        $this->assertStringContainsString('Please call before confirming.', $booking->comment);
        $this->assertStringContainsString('NVI-2024-00123', $booking->comment);
        $this->assertTrue($booking->consent);
    }

    public function test_public_booking_form_rejects_service_not_bookable_at_selected_center(): void
    {
        $center = Center::factory()->create([
            'status' => CenterStatus::ACTIVE->value,
            'booking_enabled' => true,
        ]);
        $service = Service::factory()->create();
        $tariff = Tariff::factory()->create();

        $response = $this->from(route('book-inspection'))->post(route('book-inspection.store'), [
            'service' => $service->slug,
            'center' => $center->slug,
            'vehicle_registration' => 'NW 123 AN',
            'vehicle_category' => $tariff->category_slug,
            'preferred_date' => today()->addDay()->toDateString(),
            'preferred_hour' => '10',
            'preferred_minute' => '00',
            'full_name' => 'Jane Vehicle Owner',
            'phone_country' => '+237',
            'phone' => '678456789',
            'consent' => '1',
        ]);

        $response
            ->assertRedirect(route('book-inspection').'#book-inspection-form')
            ->assertSessionHasErrors('service');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_public_booking_form_rejects_non_bookable_center(): void
    {
        $center = Center::factory()->construction()->create();
        $service = Service::factory()->create();
        $tariff = Tariff::factory()->create();
        $center->services()->attach($service->id, [
            'is_available' => true,
            'booking_enabled' => true,
        ]);

        $response = $this->from(route('book-inspection'))->post(route('book-inspection.store'), [
            'service' => $service->slug,
            'center' => $center->slug,
            'vehicle_registration' => 'NW 123 AN',
            'vehicle_category' => $tariff->category_slug,
            'preferred_date' => today()->addDay()->toDateString(),
            'preferred_hour' => '10',
            'preferred_minute' => '00',
            'full_name' => 'Jane Vehicle Owner',
            'phone_country' => '+237',
            'phone' => '678456789',
            'consent' => '1',
        ]);

        $response
            ->assertRedirect(route('book-inspection').'#book-inspection-form')
            ->assertSessionHasErrors('center');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_booking_form_validation_errors_render_on_the_form_fields(): void
    {
        [$center, $service, $tariff] = $this->bookableBookingDependencies();

        $payload = [
            'service' => $service->slug,
            'center' => $center->slug,
            'vehicle_registration' => 'NW 123 AN',
            'vehicle_category' => $tariff->category_slug,
            'preferred_date' => today()->addDay()->toDateString(),
            'preferred_hour' => '10',
            'preferred_minute' => '00',
            'full_name' => 'Jane Vehicle Owner',
            'phone_country' => '+237',
            'phone' => '678456789',
            'email' => 'not-an-email',
            'consent' => '1',
        ];

        $response = $this->from(route('book-inspection'))->post(route('book-inspection.store'), $payload);

        $response
            ->assertRedirect(route('book-inspection').'#book-inspection-form')
            ->assertSessionHasErrors('email');

        $this->followingRedirects()
            ->from(route('book-inspection'))
            ->post(route('book-inspection.store'), $payload)
            ->assertOk()
            ->assertSee('data-form-feedback-state="error"', false)
            ->assertSee('id="inspection-email-error"', false)
            ->assertSee('aria-invalid="true"', false)
            ->assertDontSee(__('book_inspection.feedback.error_title'))
            ->assertSee('value="not-an-email"', false);
    }

    public function test_booking_page_submits_stable_tariff_category_slug(): void
    {
        [$center, $service, $tariff] = $this->bookableBookingDependencies();

        $this->get(route('book-inspection'))
            ->assertOk()
            ->assertSee('id="book-inspection-form"', false)
            ->assertSee(route('book-inspection.store'), false)
            ->assertSee('value="'.$center->slug.'"', false)
            ->assertSee('value="'.$service->slug.'"', false)
            ->assertSee('value="'.$tariff->category_slug.'"', false);
    }

    /**
     * @return array{0: Center, 1: Service, 2: Tariff}
     */
    private function bookableBookingDependencies(): array
    {
        $center = Center::factory()->create([
            'status' => CenterStatus::ACTIVE->value,
            'booking_enabled' => true,
        ]);
        $service = Service::factory()->create();
        $tariff = Tariff::factory()->create();

        $center->services()->attach($service->id, [
            'is_available' => true,
            'booking_enabled' => true,
        ]);

        return [$center, $service, $tariff];
    }
}
