<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\PublicSiteData;
use App\Support\SiteSettingRegistry;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminSiteSettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_staff_can_view_site_settings(): void
    {
        $this->seed(SiteSettingSeeder::class);
        $user = User::factory()->for($this->role('admin'))->create();

        $this->actingAs($user)
            ->get(route('admin.site-settings.index'))
            ->assertOk()
            ->assertSee('Site settings')
            ->assertSee('General')
            ->assertSee('Contact')
            ->assertSee('Branding')
            ->assertSee('Careers')
            ->assertSee('site_name')
            ->assertSee('NACHO Vehicle Inspection')
            ->assertSee('maintenance_mode');
    }

    #[Test]
    public function admin_staff_can_update_site_settings_used_by_public_data(): void
    {
        $this->seed(SiteSettingSeeder::class);
        $user = User::factory()->for($this->role('admin'))->create();

        $response = $this->actingAs($user)
            ->put(route('admin.site-settings.update'), $this->settingsPayload([
                'site_name' => 'NACHO Cameroon Inspection',
                'contact_email' => 'info@nacho.example',
                'contact_phone' => '(+237) 600000111',
                'primary_color' => '#1F7A4D',
                'maintenance_mode' => '1',
                'careers_general_application_email' => 'careers@nacho.example',
                'tariff_logistics_payment_en' => 'Pay at the selected center after confirmation.',
            ]));

        $response->assertRedirect(route('admin.site-settings.index'));

        $this->assertSame('NACHO Cameroon Inspection', $this->setting('site_name')->value);
        $this->assertSame('info@nacho.example', $this->setting('contact_email')->value);
        $this->assertSame('(+237) 600000111', $this->setting('contact_phone')->value);
        $this->assertSame('#1F7A4D', $this->setting('primary_color')->value);
        $this->assertSame('1', $this->setting('maintenance_mode')->value);
        $this->assertTrue($this->setting('maintenance_mode')->typedValue());
        $this->assertSame('careers@nacho.example', $this->setting('careers_general_application_email')->value);
        $this->assertSame('Pay at the selected center after confirmation.', $this->setting('tariff_logistics_payment_en')->value);

        $publicData = app(PublicSiteData::class);
        $this->assertSame('info@nacho.example', $publicData->setting('contact_email'));
        $this->assertSame('careers@nacho.example', $publicData->setting('careers_general_application_email'));
    }

    #[Test]
    public function validation_rejects_invalid_setting_values(): void
    {
        $this->seed(SiteSettingSeeder::class);
        $user = User::factory()->for($this->role('admin'))->create();

        $this->actingAs($user)
            ->from(route('admin.site-settings.index'))
            ->put(route('admin.site-settings.update'), $this->settingsPayload([
                'default_language' => 'de',
                'contact_email' => 'not-an-email',
                'careers_general_application_email' => 'not-an-email',
                'facebook_url' => 'not-a-url',
                'primary_color' => 'orange',
            ]))
            ->assertRedirect(route('admin.site-settings.index'))
            ->assertSessionHasErrors([
                'settings.default_language',
                'settings.contact_email',
                'settings.careers_general_application_email',
                'settings.facebook_url',
                'settings.primary_color',
            ]);
    }

    #[Test]
    public function unauthorized_staff_are_blocked_from_site_settings(): void
    {
        $this->seed(SiteSettingSeeder::class);
        $contentManager = User::factory()->for($this->role('content-manager'))->create();

        $this->get(route('admin.site-settings.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($contentManager)
            ->get(route('admin.site-settings.index'))
            ->assertForbidden();

        $this->actingAs($contentManager)
            ->put(route('admin.site-settings.update'), $this->settingsPayload([
                'site_name' => 'Blocked',
            ]))
            ->assertForbidden();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, array<string, mixed>>
     */
    private function settingsPayload(array $overrides = []): array
    {
        $settings = [];

        foreach (SiteSettingRegistry::definitions() as $key => $definition) {
            $settings[$key] = SiteSetting::query()->where('key', $key)->value('value');

            if ($settings[$key] === null) {
                $settings[$key] = '';
            }
        }

        return [
            'settings' => [
                ...$settings,
                ...$overrides,
            ],
        ];
    }

    private function setting(string $key): SiteSetting
    {
        return SiteSetting::query()->where('key', $key)->firstOrFail();
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
