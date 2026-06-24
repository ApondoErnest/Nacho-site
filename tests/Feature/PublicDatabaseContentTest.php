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

class PublicDatabaseContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_centers_page_uses_database_centers_and_services(): void
    {
        $center = Center::factory()->create([
            'name_en' => 'NACHO Database Center',
            'name_fr' => 'Centre Database NACHO',
            'city_en' => 'Test City',
            'city_fr' => 'Ville Test',
            'status' => CenterStatus::ACTIVE->value,
            'booking_enabled' => true,
        ]);
        $service = Service::factory()->create([
            'slug' => 'database-inspection',
            'title_en' => 'Database Inspection',
            'title_fr' => 'Inspection Database',
        ]);
        $center->services()->attach($service->id, [
            'is_available' => true,
            'booking_enabled' => true,
        ]);

        $this->get(route('centers.index'))
            ->assertOk()
            ->assertSee('Centre Database NACHO')
            ->assertSee('Inspection Database');
    }

    public function test_booking_and_tariff_pages_use_database_rows(): void
    {
        Center::factory()->create([
            'name_en' => 'NACHO Booking Center',
            'name_fr' => 'Centre Booking NACHO',
            'city_en' => 'Booking City',
            'city_fr' => 'Ville Booking',
            'status' => CenterStatus::ACTIVE->value,
            'booking_enabled' => true,
        ]);
        Service::factory()->create([
            'slug' => 'booking-inspection',
            'title_en' => 'Booking Inspection',
            'title_fr' => 'Inspection Booking',
        ]);
        Tariff::factory()->create([
            'category_code' => 'Z',
            'category_slug' => 'category-z-database',
            'name_en' => 'Database Vehicle',
            'name_fr' => 'Vehicule Database',
            'price_fcfa' => 12345,
            'validity_value' => 9,
            'validity_unit' => 'months',
            'is_active' => true,
            'is_bookable' => true,
            'display_order' => 1,
        ]);

        $this->get(route('book-inspection'))
            ->assertOk()
            ->assertSee('Centre Booking NACHO')
            ->assertSee('Vehicule Database');

        $this->get(route('tariffs'))
            ->assertOk()
            ->assertSee('12 345');
    }

    public function test_legal_page_uses_database_page_content(): void
    {
        Page::factory()->create([
            'slug' => 'privacy-policy',
            'title_en' => 'Database Privacy Policy',
            'title_fr' => 'Politique Database',
            'content_en' => 'Database privacy content.',
            'content_fr' => 'Contenu confidentialite database.',
            'status' => ContentStatus::PUBLISHED->value,
        ]);

        $this->get(route('legal.privacy'))
            ->assertOk()
            ->assertSee('Politique Database')
            ->assertSee('Contenu confidentialite database.');
    }

    public function test_careers_page_uses_database_vacancies(): void
    {
        $department = CareerDepartment::factory()->create([
            'name_en' => 'Database Careers',
            'name_fr' => 'Carrieres Database',
            'slug' => 'database-careers',
        ]);
        $center = Center::factory()->create([
            'name_en' => 'NACHO Career Center',
            'name_fr' => 'Centre Career NACHO',
        ]);
        CareerPost::factory()->create([
            'department_id' => $department->id,
            'center_id' => $center->id,
            'title_en' => 'Database Inspector',
            'title_fr' => 'Inspecteur Database',
            'slug' => 'database-inspector',
            'status' => CareerPostStatus::PUBLISHED->value,
            'published_at' => now()->subMinute(),
            'closes_at' => today()->addMonth(),
        ]);

        $this->get(route('careers.index'))
            ->assertOk()
            ->assertSee('Inspecteur Database')
            ->assertSee('Carrieres Database');
    }
}
