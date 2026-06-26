<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\Center;
use App\Models\Role;
use App\Models\Service;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authorized_staff_can_view_service_index_and_details(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $center = Center::factory()->create(['name_en' => 'NACHO Bamenda']);
        $service = Service::factory()->create([
            'title_en' => 'Technical Inspection',
            'slug' => 'technical-inspection',
        ]);
        $center->services()->attach($service, [
            'is_available' => true,
            'booking_enabled' => true,
        ]);
        Booking::factory()
            ->for($center)
            ->for($service)
            ->for(Tariff::factory())
            ->create();

        $this->actingAs($user)
            ->get('/admin/services')
            ->assertOk()
            ->assertSee('Technical Inspection')
            ->assertSee('technical-inspection')
            ->assertSee('Active');

        $this->actingAs($user)
            ->get(route('admin.services.show', $service))
            ->assertOk()
            ->assertSee('Service Details')
            ->assertSee('NACHO Bamenda')
            ->assertSee('Bookings');
    }

    #[Test]
    public function content_manager_can_create_a_service(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();

        $response = $this->actingAs($user)->post('/admin/services', $this->validPayload());

        $service = Service::query()->where('slug', 'roadworthiness-check')->firstOrFail();

        $response->assertRedirect(route('admin.services.show', $service));
        $this->assertDatabaseHas('services', [
            'slug' => 'roadworthiness-check',
            'title_en' => 'Roadworthiness Check',
            'title_fr' => 'Controle Technique',
            'icon' => 'shield-check',
            'is_active' => true,
            'display_order' => 4,
        ]);
    }

    #[Test]
    public function content_manager_can_update_a_service(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $service = Service::factory()->create([
            'title_en' => 'Old Service',
            'slug' => 'old-service',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(route('admin.services.update', $service), $this->validPayload([
            'title_en' => 'Updated Inspection Service',
            'slug' => 'updated-inspection-service',
            'is_active' => '0',
            'icon' => 'clipboard-list',
        ]));

        $service->refresh();

        $response->assertRedirect(route('admin.services.show', $service));
        $this->assertSame('Updated Inspection Service', $service->title_en);
        $this->assertSame('updated-inspection-service', $service->slug);
        $this->assertSame('clipboard-list', $service->icon);
        $this->assertFalse($service->is_active);
    }

    #[Test]
    public function admin_can_archive_a_service(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        $service = Service::factory()->create(['title_en' => 'Archive Service']);

        $this->actingAs($user)
            ->delete(route('admin.services.destroy', $service))
            ->assertRedirect(route('admin.services.index'));

        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    #[Test]
    public function receptionist_cannot_manage_services(): void
    {
        $receptionist = User::factory()->for($this->role('receptionist'))->create();
        $service = Service::factory()->create();

        $this->actingAs($receptionist)
            ->get(route('admin.services.index'))
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->get(route('admin.services.create'))
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->put(route('admin.services.update', $service), $this->validPayload())
            ->assertForbidden();
    }

    #[Test]
    public function service_validation_rejects_duplicate_slugs_and_invalid_icons(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        Service::factory()->create(['slug' => 'duplicate-service']);

        $this->actingAs($user)
            ->from(route('admin.services.create'))
            ->post('/admin/services', $this->validPayload([
                'slug' => 'duplicate-service',
                'icon' => 'not-a-real-icon',
            ]))
            ->assertRedirect(route('admin.services.create'))
            ->assertSessionHasErrors(['slug', 'icon']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'slug' => 'roadworthiness-check',
            'title_en' => 'Roadworthiness Check',
            'title_fr' => 'Controle Technique',
            'short_description_en' => 'Core vehicle inspection service.',
            'short_description_fr' => 'Service principal de controle vehicule.',
            'full_description_en' => 'Full English service description.',
            'full_description_fr' => 'Description francaise complete du service.',
            'icon' => 'shield-check',
            'featured_image' => 'images/services/roadworthiness.jpg',
            'is_active' => '1',
            'display_order' => 4,
            'seo_title_en' => 'Roadworthiness check in Cameroon',
            'seo_title_fr' => 'Controle technique au Cameroun',
            'meta_description_en' => 'Book a NACHO roadworthiness inspection.',
            'meta_description_fr' => 'Reservez un controle technique NACHO.',
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
