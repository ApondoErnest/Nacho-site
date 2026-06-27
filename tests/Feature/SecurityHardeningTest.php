<?php

namespace Tests\Feature;

use App\Enums\CenterStatus;
use App\Models\Booking;
use App\Models\Center;
use App\Models\ContactMessage;
use App\Models\Service;
use App\Models\Tariff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_responses_include_security_headers(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');

        $this->assertStringContainsString("default-src 'self'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString("form-action 'self'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString("frame-ancestors 'self'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString('geolocation=(self)', $response->headers->get('Permissions-Policy'));
    }

    public function test_hsts_header_is_only_added_for_secure_requests(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeaderMissing('Strict-Transport-Security');

        $this->get('https://localhost/')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
    }

    public function test_auth_pages_prevent_browser_caching(): void
    {
        $response = $this->get(route('login'))
            ->assertOk()
            ->assertHeader('Pragma', 'no-cache')
            ->assertHeader('Expires', '0');

        $cacheControl = $response->headers->get('Cache-Control');

        foreach (['no-store', 'no-cache', 'must-revalidate', 'private'] as $directive) {
            $this->assertStringContainsString($directive, $cacheControl);
        }
    }

    public function test_cookie_banner_accessible_label_is_translated(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('aria-label="Consentement aux cookies"', false);

        $this->withSession(['locale' => 'en'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('aria-label="Cookie consent"', false);
    }

    public function test_public_contact_submission_is_rate_limited(): void
    {
        $ip = '10.41.0.1';
        RateLimiter::clear('contact.store|'.$ip);

        $center = Center::factory()->create([
            'status' => CenterStatus::ACTIVE->value,
        ]);

        for ($attempt = 1; $attempt <= 6; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->post(route('contact.store'), $this->validContactPayload($center, [
                    'email' => "owner{$attempt}@example.com",
                ]))
                ->assertRedirect(route('contact').'#contact-form');
        }

        $this->assertSame(6, ContactMessage::query()->count());

        $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->post(route('contact.store'), $this->validContactPayload($center, [
                'email' => 'owner7@example.com',
            ]))
            ->assertTooManyRequests();

        $this->assertSame(6, ContactMessage::query()->count());
    }

    public function test_public_booking_submission_is_rate_limited(): void
    {
        $ip = '10.41.0.2';
        RateLimiter::clear('book-inspection.store|'.$ip);
        [$center, $service, $tariff] = $this->bookableBookingDependencies();

        for ($attempt = 1; $attempt <= 6; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->post(route('book-inspection.store'), $this->validBookingPayload($center, $service, $tariff, [
                    'vehicle_registration' => "NW {$attempt}23 AN",
                    'email' => "booking{$attempt}@example.com",
                ]))
                ->assertRedirect(route('book-inspection').'#book-inspection-form');
        }

        $this->assertSame(6, Booking::query()->count());

        $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->post(route('book-inspection.store'), $this->validBookingPayload($center, $service, $tariff, [
                'vehicle_registration' => 'NW 723 AN',
                'email' => 'booking7@example.com',
            ]))
            ->assertTooManyRequests();

        $this->assertSame(6, Booking::query()->count());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validContactPayload(Center $center, array $overrides = []): array
    {
        $reason = __('contact.form.reasons')[0];

        return array_merge([
            'full_name' => 'Vehicle Owner',
            'phone' => '(+237) 678 456 789',
            'email' => 'owner@example.com',
            'preferred_center' => $center->slug,
            'reason' => str($reason)->slug()->toString(),
            'message' => 'I need help choosing an inspection center.',
            'consent' => '1',
            'website' => '',
        ], $overrides);
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

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validBookingPayload(Center $center, Service $service, Tariff $tariff, array $overrides = []): array
    {
        return array_merge([
            'service' => $service->slug,
            'center' => $center->slug,
            'vehicle_registration' => 'NW 123 AN',
            'vehicle_category' => $tariff->category_slug,
            'previous_reference' => 'NVI-2024-00123',
            'preferred_date' => today()->addDays(2)->toDateString(),
            'preferred_hour' => '09',
            'preferred_minute' => '30',
            'full_name' => 'Vehicle Owner',
            'phone_country' => '+237',
            'phone' => '6 78 45 67 89',
            'email' => 'booking@example.com',
            'additional_information' => 'Please call before confirming.',
            'consent' => '1',
        ], $overrides);
    }
}
