<?php

namespace Tests\Feature\Admin;

use App\Enums\CenterStatus;
use App\Models\Center;
use App\Models\CenterContact;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminCenterManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authorized_staff_can_view_center_index_and_details(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        $service = Service::factory()->create(['title_en' => 'Technical Inspection']);
        $center = Center::factory()->create([
            'name_en' => 'NACHO Bamenda',
            'city_en' => 'Bamenda',
            'status' => CenterStatus::ACTIVE->value,
        ]);
        $center->services()->attach($service, [
            'is_available' => true,
            'booking_enabled' => true,
        ]);
        CenterContact::factory()->for($center)->create(['value' => '(+237) 675117327']);

        $this->actingAs($user)
            ->get('/admin/centers')
            ->assertOk()
            ->assertSee('NACHO Bamenda')
            ->assertSee('Bamenda')
            ->assertSee('Enabled');

        $this->actingAs($user)
            ->get(route('admin.centers.show', $center))
            ->assertOk()
            ->assertSee('Center Details')
            ->assertSee('Technical Inspection')
            ->assertSee('(+237) 675117327');
    }

    #[Test]
    public function admin_can_create_a_center_with_service_assignments(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        $service = Service::factory()->create();

        $response = $this->actingAs($user)->post('/admin/centers', [
            ...$this->validPayload(),
            'service_ids' => [$service->id],
        ]);

        $center = Center::query()->where('slug', 'nacho-test-center')->firstOrFail();

        $response->assertRedirect(route('admin.centers.show', $center));
        $this->assertDatabaseHas('centers', [
            'slug' => 'nacho-test-center',
            'name_en' => 'NACHO Test Center',
            'status' => CenterStatus::CONSTRUCTION->value,
            'booking_enabled' => true,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('center_service', [
            'center_id' => $center->id,
            'service_id' => $service->id,
            'is_available' => true,
            'booking_enabled' => true,
        ]);
    }

    #[Test]
    public function admin_can_update_a_center_and_sync_services(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        $center = Center::factory()->create([
            'name_en' => 'Old Center',
            'slug' => 'old-center',
            'booking_enabled' => true,
        ]);
        $oldService = Service::factory()->create();
        $newService = Service::factory()->create();
        $center->services()->attach($oldService, [
            'is_available' => true,
            'booking_enabled' => true,
        ]);

        $response = $this->actingAs($user)->put(route('admin.centers.update', $center), [
            ...$this->validPayload([
                'name_en' => 'NACHO Updated Center',
                'slug' => 'nacho-updated-center',
                'status' => CenterStatus::ACTIVE->value,
                'booking_enabled' => '0',
            ]),
            'service_ids' => [$newService->id],
        ]);

        $center->refresh();

        $response->assertRedirect(route('admin.centers.show', $center));
        $this->assertSame('NACHO Updated Center', $center->name_en);
        $this->assertSame('nacho-updated-center', $center->slug);
        $this->assertFalse($center->booking_enabled);
        $this->assertDatabaseMissing('center_service', [
            'center_id' => $center->id,
            'service_id' => $oldService->id,
        ]);
        $this->assertDatabaseHas('center_service', [
            'center_id' => $center->id,
            'service_id' => $newService->id,
            'booking_enabled' => false,
        ]);
    }

    #[Test]
    public function admin_can_archive_a_center(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        $center = Center::factory()->create(['name_en' => 'Archive Me']);

        $this->actingAs($user)
            ->delete(route('admin.centers.destroy', $center))
            ->assertRedirect(route('admin.centers.index'));

        $this->assertSoftDeleted('centers', ['id' => $center->id]);
    }

    #[Test]
    public function center_manager_can_edit_but_cannot_create_or_delete_centers(): void
    {
        $manager = User::factory()->for($this->role('center-manager'))->create();
        $center = Center::factory()->create();

        $this->actingAs($manager)
            ->get(route('admin.centers.edit', $center))
            ->assertOk();

        $this->actingAs($manager)
            ->get(route('admin.centers.create'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->delete(route('admin.centers.destroy', $center))
            ->assertForbidden();
    }

    #[Test]
    public function center_validation_rejects_duplicate_slugs_and_invalid_coordinates(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        Center::factory()->create(['slug' => 'duplicate-center']);

        $this->actingAs($user)
            ->from(route('admin.centers.create'))
            ->post('/admin/centers', $this->validPayload([
                'slug' => 'duplicate-center',
                'latitude' => '120',
            ]))
            ->assertRedirect(route('admin.centers.create'))
            ->assertSessionHasErrors(['slug', 'latitude']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'name_en' => 'NACHO Test Center',
            'name_fr' => 'NACHO Centre Test',
            'slug' => 'nacho-test-center',
            'city_en' => 'Test City',
            'city_fr' => 'Ville Test',
            'region_en' => 'Test Region',
            'region_fr' => 'Region Test',
            'address_en' => 'Road 1',
            'address_fr' => 'Route 1',
            'postal_address' => 'P.O. Box 1',
            'status' => CenterStatus::CONSTRUCTION->value,
            'description_en' => 'English description',
            'description_fr' => 'Description francaise',
            'latitude' => '5.9600000',
            'longitude' => '10.1500000',
            'google_maps_url' => 'https://maps.google.com/?q=5.96,10.15',
            'nearby_landmark' => 'Main road',
            'search_keywords' => 'test center',
            'vehicle_categories_en' => 'Cars, buses',
            'vehicle_categories_fr' => 'Voitures, bus',
            'featured_image' => 'images/centers/test.jpg',
            'target_opening_date' => today()->addMonth()->format('Y-m-d'),
            'target_date_text_en' => 'Before November 2026',
            'target_date_text_fr' => 'Avant novembre 2026',
            'expansion_phase' => 'Fit-out',
            'expansion_updated_at' => now()->format('Y-m-d\TH:i'),
            'display_order' => 9,
            'is_headquarters' => '1',
            'booking_enabled' => '1',
            'is_featured' => '1',
            'is_active' => '1',
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
