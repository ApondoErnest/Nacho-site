<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FrontendSmokeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string}>
     */
    public static function publicPages(): array
    {
        return [
            'home' => ['home'],
            'about' => ['about'],
            'centers' => ['centers.index'],
            'services' => ['services.index'],
            'tariffs' => ['tariffs'],
            'inspection process' => ['inspection-process'],
            'booking' => ['book-inspection'],
            'blog' => ['blog.index'],
            'compliance' => ['compliance'],
            'careers' => ['careers.index'],
            'contact' => ['contact'],
            'privacy' => ['legal.privacy'],
            'terms' => ['legal.terms'],
            'cookies' => ['legal.cookies'],
            'legal notice' => ['legal.notice'],
        ];
    }

    public function test_vite_build_assets_are_available_to_public_pages(): void
    {
        $this->assertFileDoesNotExist(
            public_path('hot'),
            'public/hot points Laravel at a Vite dev server. Remove stale hot files or run npm run dev before browsing.',
        );

        $manifestPath = public_path('build/manifest.json');
        $this->assertFileExists($manifestPath);

        $manifest = json_decode((string) file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);

        foreach (['resources/css/app.css', 'resources/js/app.js'] as $entry) {
            $this->assertArrayHasKey($entry, $manifest);
            $this->assertFileExists(public_path('build/'.$manifest[$entry]['file']));
        }

        $response = $this->get(route('home'))->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('/build/assets/', $html);
        $this->assertStringNotContainsString('@vite/client', $html);
        $this->assertStringNotContainsString(':5173', $html);
        $this->assertCompiledAssetsExist($html);
    }

    #[DataProvider('publicPages')]
    public function test_public_pages_render_shared_frontend_chrome(string $routeName): void
    {
        $response = $this->get(route($routeName))->assertOk();
        $html = $response->getContent();

        $response
            ->assertSee('class="nav-header"', false)
            ->assertSee('aria-label="'.__('navigation.main_navigation').'"', false)
            ->assertSee('id="mobile-navigation"', false)
            ->assertSee('aria-controls="mobile-navigation"', false)
            ->assertSee('class="site-footer"', false)
            ->assertSee('aria-label="'.__('footer.cookie_label').'"', false)
            ->assertSee(route('book-inspection'), false)
            ->assertSee('/language/fr', false)
            ->assertSee('/language/en', false);

        $this->assertCompiledAssetsExist($html);
        $this->assertLocalAssetReferencesExist($html);
    }

    public function test_frontend_interactive_pages_keep_required_markup_hooks(): void
    {
        $this->get(route('centers.index'))
            ->assertOk()
            ->assertSee('x-data="centersLocator({', false)
            ->assertSee('id="centers-search"', false)
            ->assertSee('id="centers-region"', false)
            ->assertSee('id="centers-service"', false)
            ->assertSee('role="group"', false)
            ->assertSee(':aria-pressed="(viewMode ===', false)
            ->assertSee('id="centers-locator-map"', false)
            ->assertSee('role="application"', false)
            ->assertSee('<noscript>', false);

        $this->get(route('tariffs'))
            ->assertOk()
            ->assertSee('id="tariff-finder"', false)
            ->assertSee('x-data="{ selectedTariff:', false)
            ->assertSee('data-tariff-card=', false)
            ->assertSee(':aria-pressed="(selectedTariff ===', false)
            ->assertSee('id="tariffs-mobile-category"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('id="tariff-documents"', false);

        $this->get(route('careers.index'))
            ->assertOk()
            ->assertSee('x-data="careersVacancies({', false)
            ->assertSee('id="career-vacancy-search"', false)
            ->assertSee('id="career-vacancy-department"', false)
            ->assertSee('id="career-vacancy-center"', false)
            ->assertSee('id="career-vacancy-employment-type"', false)
            ->assertSee('tabindex="0"', false)
            ->assertSee('@keydown.enter.prevent="selectVacancy', false)
            ->assertSee('mailto:', false);
    }

    public function test_public_forms_keep_accessible_controls_and_excluded_fields_absent(): void
    {
        $this->get(route('book-inspection', ['center' => 'nacho-yaounde', 'category' => 'private']))
            ->assertOk()
            ->assertSee('id="book-inspection-form"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="service"', false)
            ->assertSee('name="center"', false)
            ->assertSee('name="vehicle_registration"', false)
            ->assertSee('name="vehicle_category"', false)
            ->assertSee('name="preferred_date"', false)
            ->assertSee('name="preferred_time"', false)
            ->assertSee('name="full_name"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('name="consent"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertDontSee('name="reminder', false)
            ->assertDontSee('name="expiry', false);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('id="contact-form"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="full_name"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="preferred_center"', false)
            ->assertSee('name="reason"', false)
            ->assertSee('name="message"', false)
            ->assertSee('name="consent"', false)
            ->assertSee('name="website"', false);

        $this->get(route('careers.index'))
            ->assertOk()
            ->assertSee('mailto:', false)
            ->assertDontSee('type="file"', false)
            ->assertDontSee('name="cv"', false)
            ->assertDontSee('name="resume"', false);
    }

    private function assertCompiledAssetsExist(string $html): void
    {
        preg_match_all('#(?:href|src)="(?:https?://[^/"]+)?(/build/assets/[^"?]+)"#', $html, $matches);

        $assetPaths = array_unique($matches[1] ?? []);

        $this->assertNotEmpty($assetPaths, 'No compiled Vite assets were found in the rendered HTML.');

        $hasCss = false;
        $hasJs = false;

        foreach ($assetPaths as $assetPath) {
            $this->assertFileExists(public_path(ltrim($assetPath, '/')));
            $hasCss = $hasCss || str_ends_with($assetPath, '.css');
            $hasJs = $hasJs || str_ends_with($assetPath, '.js');
        }

        $this->assertTrue($hasCss, 'The rendered page is missing a compiled CSS asset.');
        $this->assertTrue($hasJs, 'The rendered page is missing a compiled JavaScript asset.');
    }

    private function assertLocalAssetReferencesExist(string $html): void
    {
        preg_match_all('#(?:src|href)="([^"]+)"#', $html, $matches);

        foreach (array_unique($matches[1] ?? []) as $url) {
            $path = parse_url(html_entity_decode($url), PHP_URL_PATH);

            if (! is_string($path) || ! preg_match('#^/(build|images|storage)/#', $path)) {
                continue;
            }

            $this->assertFileExists(public_path(ltrim($path, '/')), "Missing local asset referenced by frontend markup: {$path}");
        }
    }
}
