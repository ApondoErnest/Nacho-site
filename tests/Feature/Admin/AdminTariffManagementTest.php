<?php

namespace Tests\Feature\Admin;

use App\Enums\TariffRevisionStatus;
use App\Models\Role;
use App\Models\Tariff;
use App\Models\TariffAuditLog;
use App\Models\TariffRevision;
use App\Models\User;
use App\Support\PublicSiteData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminTariffManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authorized_staff_can_view_tariff_index_and_details(): void
    {
        $user = User::factory()->for($this->role('receptionist'))->create();
        $admin = User::factory()->for($this->role('admin'))->create();
        $tariff = Tariff::factory()->create([
            'category_code' => 'B',
            'category_slug' => 'category-b-private',
            'name_en' => 'Private Vehicle',
            'price_fcfa' => 17900,
        ]);
        TariffRevision::factory()->for($tariff)->for($admin, 'creator')->create([
            'status' => TariffRevisionStatus::SCHEDULED->value,
            'effective_date' => today()->addMonth(),
            'snapshot' => ['price_fcfa' => 19900, 'validity_value' => 12, 'validity_unit' => 'months'],
        ]);
        TariffAuditLog::factory()->for($tariff)->for($admin, 'user')->create([
            'changes' => ['price_fcfa' => ['old' => 15000, 'new' => 17900]],
        ]);

        $this->actingAs($user)
            ->get('/admin/tariffs')
            ->assertOk()
            ->assertSee('Tariff management')
            ->assertSee('Private Vehicle')
            ->assertSee('category-b-private')
            ->assertSee('Active');

        $this->actingAs($user)
            ->get(route('admin.tariffs.show', $tariff))
            ->assertOk()
            ->assertSee('Tariff Details')
            ->assertSee('Revision history')
            ->assertSee('Audit log')
            ->assertDontSee('Schedule tariff revision');
    }

    #[Test]
    public function admin_can_create_a_tariff_with_an_audit_record(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();

        $response = $this->actingAs($user)->post('/admin/tariffs', $this->validPayload());

        $tariff = Tariff::query()->where('category_slug', 'category-e-special-machinery')->firstOrFail();
        $auditLog = $tariff->auditLogs()->firstOrFail();

        $response->assertRedirect(route('admin.tariffs.show', $tariff));
        $this->assertDatabaseHas('tariffs', [
            'category_slug' => 'category-e-special-machinery',
            'category_code' => 'E',
            'name_en' => 'Special Machinery',
            'price_fcfa' => 41750,
            'is_active' => true,
            'is_bookable' => true,
        ]);
        $this->assertSame($user->id, $auditLog->user_id);
        $this->assertArrayHasKey('created', $auditLog->changes);
    }

    #[Test]
    public function admin_can_update_a_tariff_and_changes_are_audited(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        $tariff = Tariff::factory()->create([
            'category_slug' => 'category-b-private',
            'name_en' => 'Private Vehicle',
            'price_fcfa' => 17900,
            'is_bookable' => true,
        ]);

        $response = $this->actingAs($user)->put(route('admin.tariffs.update', $tariff), $this->validPayload([
            'category_slug' => 'category-b-private-updated',
            'name_en' => 'Updated Private Vehicle',
            'price_fcfa' => 19900,
            'is_bookable' => '0',
        ]));

        $tariff->refresh();
        $auditLog = $tariff->auditLogs()->latest()->firstOrFail();

        $response->assertRedirect(route('admin.tariffs.show', $tariff));
        $this->assertSame('category-b-private-updated', $tariff->category_slug);
        $this->assertSame(19900, $tariff->price_fcfa);
        $this->assertFalse($tariff->is_bookable);
        $this->assertSame(17900, $auditLog->changes['price_fcfa']['old']);
        $this->assertSame(19900, $auditLog->changes['price_fcfa']['new']);
        $this->assertSame(1, $auditLog->changes['is_bookable']['old']);
        $this->assertFalse($auditLog->changes['is_bookable']['new']);
    }

    #[Test]
    public function admin_can_schedule_a_revision_and_public_tariffs_use_current_effective_snapshot(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        $tariff = Tariff::factory()->create([
            'category_code' => 'B',
            'category_slug' => 'category-b-private',
            'name_en' => 'Private Vehicle',
            'name_fr' => 'Vehicule particulier',
            'price_fcfa' => 17900,
            'validity_value' => 12,
            'validity_unit' => 'months',
            'is_active' => true,
            'is_bookable' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.tariffs.revisions.store', $tariff), [
            'price_fcfa' => 21500,
            'validity_value' => 6,
            'validity_unit' => 'months',
            'effective_date' => today()->format('Y-m-d'),
            'regulatory_reference' => 'NACHO-RATE-2026',
            'is_active' => '1',
            'is_bookable' => '1',
        ]);

        $revision = $tariff->revisions()->firstOrFail();
        $previewRow = app(PublicSiteData::class)->tariffPreview()->firstWhere('category_slug', 'category-b-private');

        $response->assertRedirect(route('admin.tariffs.show', $tariff));
        $this->assertSame(TariffRevisionStatus::SCHEDULED, $revision->status);
        $this->assertSame(21500, $revision->snapshot['price_fcfa']);
        $this->assertSame('21 500 FCFA', $previewRow['price']);
        $this->assertArrayHasKey('scheduled_revision', $tariff->auditLogs()->firstOrFail()->changes);
    }

    #[Test]
    public function admin_deactivates_instead_of_hard_deleting_a_tariff(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        $tariff = Tariff::factory()->create([
            'is_active' => true,
            'is_bookable' => true,
        ]);

        $this->actingAs($user)
            ->delete(route('admin.tariffs.destroy', $tariff))
            ->assertRedirect(route('admin.tariffs.index'));

        $tariff->refresh();
        $auditLog = $tariff->auditLogs()->firstOrFail();

        $this->assertFalse($tariff->is_active);
        $this->assertFalse($tariff->is_bookable);
        $this->assertDatabaseHas('tariffs', ['id' => $tariff->id]);
        $this->assertSame(1, $auditLog->changes['is_active']['old']);
        $this->assertFalse($auditLog->changes['is_active']['new']);
    }

    #[Test]
    public function tariff_permissions_allow_view_only_staff_but_block_management(): void
    {
        $receptionist = User::factory()->for($this->role('receptionist'))->create();
        $contentManager = User::factory()->for($this->role('content-manager'))->create();
        $tariff = Tariff::factory()->create();

        $this->actingAs($receptionist)
            ->get(route('admin.tariffs.index'))
            ->assertOk();

        $this->actingAs($receptionist)
            ->get(route('admin.tariffs.create'))
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->put(route('admin.tariffs.update', $tariff), $this->validPayload())
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->post(route('admin.tariffs.revisions.store', $tariff), [])
            ->assertForbidden();

        $this->actingAs($contentManager)
            ->get(route('admin.tariffs.index'))
            ->assertForbidden();
    }

    #[Test]
    public function tariff_validation_rejects_duplicate_slugs_invalid_icons_and_bad_dates(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        Tariff::factory()->create(['category_slug' => 'duplicate-tariff']);

        $this->actingAs($user)
            ->from(route('admin.tariffs.create'))
            ->post('/admin/tariffs', $this->validPayload([
                'category_slug' => 'duplicate-tariff',
                'vehicle_icon' => 'not-a-real-icon',
                'effective_date' => today()->format('Y-m-d'),
                'expiry_date' => today()->subDay()->format('Y-m-d'),
            ]))
            ->assertRedirect(route('admin.tariffs.create'))
            ->assertSessionHasErrors(['category_slug', 'vehicle_icon', 'expiry_date']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'category_code' => 'E',
            'category_slug' => 'category-e-special-machinery',
            'name_en' => 'Special Machinery',
            'name_fr' => 'Engins speciaux',
            'description_en' => 'Technical inspection tariff for special machinery.',
            'description_fr' => 'Tarif de controle technique pour engins speciaux.',
            'price_fcfa' => 41750,
            'validity_value' => 6,
            'validity_unit' => 'months',
            'minimum_weight_kg' => 0,
            'maximum_weight_kg' => 10000,
            'vehicle_icon' => 'construction',
            'effective_date' => today()->format('Y-m-d'),
            'expiry_date' => null,
            'regulatory_reference' => 'NACHO-RATE-2026',
            'last_verified_at' => now()->format('Y-m-d\TH:i'),
            'is_active' => '1',
            'is_bookable' => '1',
            'display_order' => 8,
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
