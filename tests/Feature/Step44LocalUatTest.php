<?php

namespace Tests\Feature;

use App\Enums\CenterStatus;
use App\Models\Center;
use Database\Seeders\CenterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Step 44 — checklist items covered by automated local UAT (see docs/UAT_CHECKLIST.md).
 */
class Step44LocalUatTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_centers_match_three_active_and_two_under_construction(): void
    {
        $this->seed(CenterSeeder::class);

        $this->assertSame(3, Center::query()->where('status', CenterStatus::ACTIVE->value)->count());
        $this->assertSame(2, Center::query()->where('status', CenterStatus::CONSTRUCTION->value)->count());
    }

    public function test_homepage_renders_current_design_sections_and_floating_book_cta(): void
    {
        $response = $this->get(route('home'))->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('hero-showcase', $html);
        $this->assertStringContainsString('technical-check-inner', $html);
        $this->assertStringContainsString(route('book-inspection'), $html);
        $this->assertStringContainsString(__('home.hero.cta_book'), $html);
    }

    public function test_inspection_process_page_renders_journey_and_preparation_sections(): void
    {
        $this->get(route('inspection-process'))
            ->assertOk()
            ->assertSee('inspection-process-hero', false)
            ->assertSee('inspection-journey', false);
    }

    public function test_sitemap_and_robots_endpoints_are_available(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('content-type', 'application/xml; charset=UTF-8');
        $this->get(route('robots'))->assertOk();
    }
}
