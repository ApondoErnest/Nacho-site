<?php

namespace Tests\Feature;

use App\Models\Center;
use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncPublicBrandingCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_restores_novetesco_center_names_after_manual_drift(): void
    {
        $this->seed(DatabaseSeeder::class);

        Center::query()->where('slug', 'nacho-yaounde')->update([
            'name_en' => 'NACHO Yaounde',
            'name_fr' => 'NACHO Yaounde',
        ]);

        SiteSetting::query()->where('key', 'contact_email')->update([
            'value' => 'nachovehicletestingstation@yahoo.com',
        ]);

        $this->artisan('site:sync-public-branding')->assertSuccessful();

        $yaounde = Center::query()->where('slug', 'nacho-yaounde')->firstOrFail();
        $this->assertSame('NOVETESCO Yaounde', $yaounde->name_en);
        $this->assertSame('NOVETESCO Yaounde', $yaounde->name_fr);

        $email = SiteSetting::query()->where('key', 'contact_email')->value('value');
        $this->assertSame('noblevehicletestingcompany@gmail.com', $email);
    }
}
