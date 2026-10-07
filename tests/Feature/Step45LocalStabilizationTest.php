<?php

namespace Tests\Feature;

use App\Enums\CenterStatus;
use App\Models\Center;
use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Step 45 — local stabilization gate (see docs/UAT_CHECKLIST.md §7 and docs/CENTERS_DATA.md).
 */
class Step45LocalStabilizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_runs_without_error(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThan(0, Center::query()->count());
        $this->assertGreaterThan(0, SiteSetting::query()->count());
    }

    public function test_seeded_centers_align_with_centers_data_document(): void
    {
        $this->seed(DatabaseSeeder::class);

        $expectedSlugs = [
            'nacho-yaounde' => CenterStatus::ACTIVE,
            'nacho-nkwen-bamenda' => CenterStatus::ACTIVE,
            'nacho-mankon-bamenda' => CenterStatus::ACTIVE,
            'nacho-douala' => CenterStatus::CONSTRUCTION,
            'nacho-kumba' => CenterStatus::CONSTRUCTION,
        ];

        foreach ($expectedSlugs as $slug => $status) {
            $center = Center::query()->where('slug', $slug)->first();
            $this->assertNotNull($center, "Missing center slug: {$slug}");
            $this->assertSame($status->value, $center->status->value);
        }

        $hq = Center::query()->where('slug', 'nacho-mankon-bamenda')->firstOrFail();
        $this->assertTrue($hq->is_headquarters);
        $this->assertSame('P.O. Box 100 Mankon-Bamenda', $hq->postal_address);
        $this->assertTrue($hq->booking_enabled);

        $yaounde = Center::query()->where('slug', 'nacho-yaounde')->firstOrFail();
        $this->assertSame('Mendong Market, Yaounde', $yaounde->address_en);

        foreach (['nacho-douala', 'nacho-kumba'] as $constructionSlug) {
            $center = Center::query()->where('slug', $constructionSlug)->firstOrFail();
            $this->assertFalse($center->booking_enabled);
            $this->assertSame('Before November 2026', $center->target_date_text_en);
        }
    }

    public function test_site_settings_match_headquarters_contact_from_centers_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(
            'noblevehicletestingcompany@gmail.com',
            SiteSetting::query()->where('key', 'contact_email')->value('value'),
        );
        $this->assertSame(
            '(+237) 33142037',
            SiteSetting::query()->where('key', 'contact_phone')->value('value'),
        );
        $this->assertSame(
            'P.O. Box 100 Mankon-Bamenda',
            SiteSetting::query()->where('key', 'postal_box')->value('value'),
        );
    }

    public function test_seeded_center_featured_images_use_existing_homepage_assets(): void
    {
        $this->seed(DatabaseSeeder::class);

        $yaounde = Center::query()->where('slug', 'nacho-yaounde')->firstOrFail();
        $this->assertSame('images/homepage/yaounde-1.png', $yaounde->featured_image);
        $this->assertFileExists(public_path($yaounde->featured_image));
    }

    public function test_public_contact_page_surfaces_headquarters_contact(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('noblevehicletestingcompany@gmail.com', false)
            ->assertSee('33142037', false);
    }
}
