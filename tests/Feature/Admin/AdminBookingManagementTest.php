<?php

namespace Tests\Feature\Admin;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Center;
use App\Models\Role;
use App\Models\Service;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminBookingManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authorized_staff_can_filter_and_view_booking_index(): void
    {
        $user = User::factory()->for($this->role('receptionist'))->create();
        $center = Center::factory()->create([
            'name_en' => 'NACHO Kumba',
            'display_order' => 1,
        ]);
        $otherCenter = Center::factory()->create([
            'name_en' => 'NACHO Douala',
            'display_order' => 2,
        ]);
        $service = Service::factory()->create([
            'title_en' => 'Periodic Technical Inspection',
            'display_order' => 1,
        ]);
        $otherService = Service::factory()->create([
            'title_en' => 'Heavy Vehicle Review',
            'display_order' => 2,
        ]);
        $tariff = Tariff::factory()->create(['category_code' => 'B']);
        $otherTariff = Tariff::factory()->create(['category_code' => 'C']);
        $visitDate = today()->addDays(3);

        Booking::factory()
            ->for($center)
            ->for($service)
            ->for($tariff)
            ->create([
                'booking_reference' => 'NACHO-20260627-1001',
                'full_name' => 'Ambe Grace',
                'phone' => '+237 670 111 222',
                'vehicle_registration' => 'SW-123-AB',
                'preferred_date' => $visitDate,
                'preferred_time' => '09:30',
                'status' => BookingStatus::CONFIRMED->value,
            ]);

        Booking::factory()
            ->for($otherCenter)
            ->for($otherService)
            ->for($otherTariff)
            ->create([
                'booking_reference' => 'NACHO-20260627-2002',
                'full_name' => 'Hidden Person',
                'vehicle_registration' => 'LT-999-ZZ',
                'preferred_date' => today()->addDays(7),
                'status' => BookingStatus::PENDING->value,
            ]);

        $this->actingAs($user)
            ->get(route('admin.bookings.index', [
                'search' => 'Grace',
                'status' => BookingStatus::CONFIRMED->value,
                'center_id' => $center->id,
                'service_id' => $service->id,
                'date_from' => $visitDate->format('Y-m-d'),
                'date_to' => $visitDate->format('Y-m-d'),
            ]))
            ->assertOk()
            ->assertSee('Booking management')
            ->assertSee('NACHO-20260627-1001')
            ->assertSee('Ambe Grace')
            ->assertSee('NACHO Kumba')
            ->assertSee('Periodic Technical Inspection')
            ->assertDontSee('NACHO-20260627-2002')
            ->assertDontSee('Hidden Person')
            ->assertDontSee('LT-999-ZZ');
    }

    #[Test]
    public function authorized_staff_can_view_booking_details(): void
    {
        $user = User::factory()->for($this->role('receptionist'))->create();
        [$center, $service, $tariff] = $this->bookingDependencies();
        $booking = Booking::factory()
            ->for($center)
            ->for($service)
            ->for($tariff)
            ->create([
                'booking_reference' => 'NACHO-20260627-3003',
                'full_name' => 'Muna Alain',
                'phone' => '+237 699 222 333',
                'email' => 'alain@example.test',
                'vehicle_registration' => 'CE-456-CD',
                'document_path' => 'booking-documents/card.pdf',
                'comment' => 'Customer asked for a morning slot.',
                'admin_notes' => 'Bring vehicle logbook.',
                'status' => BookingStatus::PENDING->value,
            ]);

        $this->actingAs($user)
            ->get(route('admin.bookings.show', $booking))
            ->assertOk()
            ->assertSee('NACHO-20260627-3003')
            ->assertSee('Muna Alain')
            ->assertSee('Customer and vehicle')
            ->assertSee('Inspection context')
            ->assertSee('CE-456-CD')
            ->assertSee('Customer asked for a morning slot.')
            ->assertSee('Bring vehicle logbook.')
            ->assertSee('Update booking')
            ->assertSee('Save booking');
    }

    #[Test]
    public function receptionist_can_update_booking_schedule_status_and_notes(): void
    {
        $user = User::factory()->for($this->role('receptionist'))->create();
        [$center, $service, $tariff] = $this->bookingDependencies();
        $booking = Booking::factory()
            ->for($center)
            ->for($service)
            ->for($tariff)
            ->create(['status' => BookingStatus::PENDING->value]);

        $response = $this->actingAs($user)
            ->put(route('admin.bookings.update', $booking), $this->validPayload($booking, [
                'status' => BookingStatus::CONFIRMED->value,
                'preferred_date' => today()->addDays(5)->format('Y-m-d'),
                'preferred_time' => '10:15',
                'admin_notes' => '  Confirmed by phone.  ',
            ]));

        $response->assertRedirect(route('admin.bookings.show', $booking));
        $booking->refresh();

        $this->assertSame(BookingStatus::CONFIRMED, $booking->status);
        $this->assertSame(today()->addDays(5)->format('Y-m-d'), $booking->preferred_date->format('Y-m-d'));
        $this->assertSame('10:15', $booking->preferred_time);
        $this->assertSame('Confirmed by phone.', $booking->admin_notes);
    }

    #[Test]
    public function inspector_can_use_status_workflow_without_full_edit_access(): void
    {
        $inspector = User::factory()->for($this->role('inspector'))->create();
        [$center, $service, $tariff] = $this->bookingDependencies();
        $booking = Booking::factory()
            ->for($center)
            ->for($service)
            ->for($tariff)
            ->create(['status' => BookingStatus::ARRIVED->value]);

        $this->actingAs($inspector)
            ->get(route('admin.bookings.show', $booking))
            ->assertOk()
            ->assertSee('Update status')
            ->assertSee('Save status')
            ->assertDontSee('Save booking');

        $this->actingAs($inspector)
            ->put(route('admin.bookings.update', $booking), $this->validPayload($booking, [
                'status' => BookingStatus::COMPLETED->value,
            ]))
            ->assertForbidden();

        $this->actingAs($inspector)
            ->patch(route('admin.bookings.status.update', $booking), [
                'status' => BookingStatus::IN_INSPECTION->value,
            ])
            ->assertRedirect(route('admin.bookings.show', $booking));

        $booking->refresh();
        $this->assertSame(BookingStatus::IN_INSPECTION, $booking->status);
    }

    #[Test]
    public function unauthorized_and_guest_users_are_blocked_from_booking_management(): void
    {
        $contentManager = User::factory()->for($this->role('content-manager'))->create();
        [$center, $service, $tariff] = $this->bookingDependencies();
        $booking = Booking::factory()
            ->for($center)
            ->for($service)
            ->for($tariff)
            ->create();

        $this->get(route('admin.bookings.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($contentManager)
            ->get(route('admin.bookings.index'))
            ->assertForbidden();

        $this->actingAs($contentManager)
            ->get(route('admin.bookings.show', $booking))
            ->assertForbidden();

        $this->actingAs($contentManager)
            ->patch(route('admin.bookings.status.update', $booking), [
                'status' => BookingStatus::COMPLETED->value,
            ])
            ->assertForbidden();
    }

    #[Test]
    public function booking_update_validation_rejects_bad_status_dates_time_and_notes(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        [$center, $service, $tariff] = $this->bookingDependencies();
        $booking = Booking::factory()
            ->for($center)
            ->for($service)
            ->for($tariff)
            ->create([
                'status' => BookingStatus::PENDING->value,
                'preferred_time' => '09:00',
            ]);

        $this->actingAs($user)
            ->from(route('admin.bookings.show', $booking))
            ->put(route('admin.bookings.update', $booking), $this->validPayload($booking, [
                'status' => 'done',
                'preferred_date' => 'not-a-date',
                'preferred_time' => '18:10',
                'admin_notes' => str_repeat('a', 5001),
            ]))
            ->assertRedirect(route('admin.bookings.show', $booking))
            ->assertSessionHasErrors(['status', 'preferred_date', 'preferred_time', 'admin_notes']);

        $booking->refresh();
        $this->assertSame(BookingStatus::PENDING, $booking->status);
        $this->assertSame('09:00', $booking->preferred_time);
    }

    /**
     * @return array{Center, Service, Tariff}
     */
    private function bookingDependencies(): array
    {
        return [
            Center::factory()->create([
                'name_en' => 'NACHO Yaounde',
                'display_order' => 1,
            ]),
            Service::factory()->create([
                'title_en' => 'Vehicle Technical Inspection',
                'display_order' => 1,
            ]),
            Tariff::factory()->create([
                'category_code' => 'B',
                'name_en' => 'Private Vehicle',
                'price_fcfa' => 17900,
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(Booking $booking, array $overrides = []): array
    {
        return [
            'status' => $booking->status->value,
            'preferred_date' => $booking->preferred_date->format('Y-m-d'),
            'preferred_time' => $booking->preferred_time,
            'admin_notes' => $booking->admin_notes,
            ...$overrides,
        ];
    }

    private function role(string $slug): Role
    {
        return Role::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => str($slug)->replace('-', ' ')->title()->toString(),
                'description' => null,
            ],
        );
    }
}
