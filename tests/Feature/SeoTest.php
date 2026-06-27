<?php

namespace Tests\Feature;

use App\Enums\CareerPostStatus;
use App\Enums\CenterStatus;
use App\Enums\ContentStatus;
use App\Models\CareerDepartment;
use App\Models\CareerPost;
use App\Models\Center;
use App\Models\Page;
use App\Models\Service;
use App\Models\Tariff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_core_seo_meta_open_graph_and_home_schema(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>Contrôle technique automobile professionnel au Cameroun | NACHO Vehicle Inspection</title>', false)
            ->assertSee('<meta name="description" content="Réservez votre visite technique avec NACHO', false)
            ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
            ->assertSee('<meta property="og:locale" content="fr_CM">', false)
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('"@type": "AutomotiveBusiness"', false)
            ->assertSee('"slogan": "Roulez en sécurité. Restez conforme. Faites confiance à NACHO."', false);
    }

    public function test_legal_pages_prefer_database_seo_fields(): void
    {
        Page::factory()->create([
            'slug' => 'privacy-policy',
            'title_en' => 'Database Privacy',
            'title_fr' => 'Confidentialite database',
            'content_en' => 'English privacy body.',
            'content_fr' => 'Corps confidentialite.',
            'seo_title_en' => 'Database Privacy SEO',
            'seo_title_fr' => 'Confidentialite SEO',
            'meta_description_en' => 'English privacy SEO description.',
            'meta_description_fr' => 'Description SEO confidentialite.',
            'status' => ContentStatus::PUBLISHED->value,
        ]);

        $this->get(route('legal.privacy'))
            ->assertOk()
            ->assertSee('<title>Confidentialite SEO | NACHO Vehicle Inspection</title>', false)
            ->assertSee('<meta name="description" content="Description SEO confidentialite.">', false);

        $this->withSession(['locale' => 'en'])
            ->get(route('legal.privacy'))
            ->assertOk()
            ->assertSee('<title>Database Privacy SEO | NACHO Vehicle Inspection</title>', false)
            ->assertSee('<meta name="description" content="English privacy SEO description.">', false);
    }

    public function test_centers_page_outputs_local_business_item_list_schema(): void
    {
        Center::factory()->create([
            'name_en' => 'NACHO Schema Center',
            'name_fr' => 'Centre Schema NACHO',
            'slug' => 'schema-center',
            'address_en' => 'Schema Street',
            'address_fr' => 'Rue Schema',
            'latitude' => 4.051056,
            'longitude' => 9.767869,
            'status' => CenterStatus::ACTIVE->value,
            'booking_enabled' => true,
            'is_active' => true,
        ]);

        $this->get(route('centers.index'))
            ->assertOk()
            ->assertSee('"@type": "ItemList"', false)
            ->assertSee('"@type": "AutomotiveBusiness"', false)
            ->assertSee('"name": "Centre Schema NACHO"', false)
            ->assertSee('"latitude": 4.051056', false)
            ->assertSee(route('centers.index').'#center-card-schema-center', false);
    }

    public function test_careers_page_outputs_job_posting_schema_for_open_vacancies(): void
    {
        $department = CareerDepartment::factory()->create([
            'name_en' => 'Technical',
            'name_fr' => 'Technique',
        ]);
        $center = Center::factory()->create([
            'name_en' => 'NACHO Jobs Center',
            'name_fr' => 'Centre Emplois NACHO',
        ]);
        CareerPost::factory()->create([
            'department_id' => $department->id,
            'center_id' => $center->id,
            'title_en' => 'Schema Inspector',
            'title_fr' => 'Inspecteur Schema',
            'slug' => 'schema-inspector',
            'reference' => 'NCH-SEO-001',
            'status' => CareerPostStatus::PUBLISHED->value,
            'published_at' => now()->subDay(),
            'closes_at' => today()->addMonth(),
        ]);

        $this->get(route('careers.index'))
            ->assertOk()
            ->assertSee('"@type": "JobPosting"', false)
            ->assertSee('"title": "Inspecteur Schema"', false)
            ->assertSee('"value": "NCH-SEO-001"', false)
            ->assertSee(route('careers.index', ['vacancy' => 'schema-inspector']), false);
    }

    public function test_sitemap_lists_public_urls_and_excludes_private_routes(): void
    {
        Service::factory()->create([
            'slug' => 'sitemap-service',
            'is_active' => true,
        ]);
        Tariff::factory()->create([
            'category_slug' => 'sitemap-tariff',
            'is_active' => true,
            'is_bookable' => true,
        ]);
        CareerPost::factory()->create([
            'slug' => 'sitemap-vacancy',
            'status' => CareerPostStatus::PUBLISHED->value,
            'published_at' => now()->subDay(),
            'closes_at' => today()->addMonth(),
        ]);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false)
            ->assertSee('<loc>'.route('home').'</loc>', false)
            ->assertSee('<loc>'.route('services.index').'</loc>', false)
            ->assertSee('<loc>'.route('careers.index', ['vacancy' => 'sitemap-vacancy']).'</loc>', false)
            ->assertDontSee('/admin')
            ->assertDontSee('/login')
            ->assertDontSee('/services/sitemap-service');
    }

    public function test_robots_txt_disallows_private_routes_and_references_sitemap(): void
    {
        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee("User-agent: *\n", false)
            ->assertSee("Allow: /\n", false)
            ->assertSee("Disallow: /admin\n", false)
            ->assertSee("Disallow: /login\n", false)
            ->assertSee('Sitemap: '.route('sitemap'), false);
    }
}
