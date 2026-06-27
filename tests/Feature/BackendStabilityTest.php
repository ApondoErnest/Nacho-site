<?php

namespace Tests\Feature;

use App\Enums\CareerPostStatus;
use App\Enums\CenterStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\CareerDepartment;
use App\Models\CareerPost;
use App\Models\Center;
use App\Models\ContactMessage;
use App\Models\Media;
use App\Models\Page;
use App\Models\Role;
use App\Models\Service;
use App\Models\Tariff;
use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BackendStabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_backend_schema_matches_v1_contract(): void
    {
        $expectedTables = [
            'roles' => ['name', 'slug', 'description'],
            'users' => ['name', 'email', 'role_id', 'phone', 'status', 'last_login_at'],
            'centers' => ['name_en', 'name_fr', 'slug', 'status', 'is_headquarters', 'booking_enabled', 'is_active'],
            'center_contacts' => ['center_id', 'type', 'value', 'is_primary', 'is_public'],
            'center_hours' => ['center_id', 'day_of_week', 'opens_at', 'closes_at', 'is_closed'],
            'center_service' => ['center_id', 'service_id', 'is_available', 'booking_enabled', 'effective_date'],
            'center_progress_updates' => ['center_id', 'phase', 'update_en', 'update_fr', 'published_at', 'is_published'],
            'services' => ['slug', 'title_en', 'title_fr', 'is_active', 'display_order'],
            'tariffs' => ['category_code', 'category_slug', 'price_fcfa', 'effective_date', 'expiry_date', 'is_bookable'],
            'tariff_revisions' => ['tariff_id', 'created_by', 'snapshot', 'effective_date', 'status'],
            'tariff_audit_logs' => ['tariff_id', 'user_id', 'changes', 'created_at'],
            'bookings' => ['booking_reference', 'center_id', 'service_id', 'tariff_id', 'preferred_date', 'status'],
            'contact_messages' => ['full_name', 'email', 'phone', 'subject', 'message', 'status'],
            'blog_categories' => ['name_en', 'name_fr', 'slug'],
            'blog_posts' => ['blog_category_id', 'author_id', 'title_en', 'title_fr', 'slug', 'status', 'published_at'],
            'career_departments' => ['name_en', 'name_fr', 'slug', 'is_active'],
            'career_posts' => ['reference', 'slug', 'department_id', 'center_id', 'status', 'allow_email_application'],
            'pages' => ['title_en', 'title_fr', 'slug', 'content_en', 'content_fr', 'status'],
            'media' => ['uploaded_by', 'file_name', 'file_path', 'file_type', 'mime_type', 'file_size'],
            'site_settings' => ['key', 'value', 'type'],
        ];

        foreach ($expectedTables as $table => $columns) {
            $this->assertTrue(Schema::hasTable($table), "Missing backend table [{$table}].");
            $this->assertTrue(
                Schema::hasColumns($table, $columns),
                'Missing expected columns on ['.$table.']: '.implode(', ', $columns),
            );
        }

        $this->assertFalse(
            Schema::hasTable('job_applications'),
            'Careers remain email-only in v1; do not add server-side job application storage.',
        );
    }

    public function test_representative_admin_backend_routes_require_authentication_and_render_for_super_admin(): void
    {
        $fixtures = $this->adminFixtures();
        $routes = $this->representativeAdminRoutes($fixtures);

        foreach ($routes as $label => $url) {
            $this->get($url)->assertRedirect(route('login'), "Guest should be redirected from [{$label}].");
        }

        foreach ($routes as $label => $url) {
            $this->actingAs($fixtures['superAdmin'])
                ->get($url)
                ->assertOk("Super Admin should render [{$label}] without backend errors.");
        }
    }

    public function test_non_staff_users_are_blocked_from_admin_backend(): void
    {
        $regularUser = User::factory()->create(['role_id' => null]);

        $this->actingAs($regularUser)
            ->get(route('admin.home'))
            ->assertForbidden();

        $this->actingAs($regularUser)
            ->get(route('admin.bookings.index'))
            ->assertForbidden();
    }

    public function test_public_backend_state_supports_booking_preselect_and_email_only_careers(): void
    {
        $center = Center::factory()->create([
            'name_en' => 'NACHO Yaounde',
            'name_fr' => 'NACHO Yaounde',
            'slug' => 'nacho-yaounde',
            'city_en' => 'Yaounde',
            'city_fr' => 'Yaounde',
            'status' => CenterStatus::ACTIVE->value,
            'booking_enabled' => true,
            'display_order' => 1,
        ]);
        Service::factory()->create(['display_order' => 1]);
        Tariff::factory()->create([
            'category_code' => 'B',
            'category_slug' => 'category-b-private',
            'name_en' => 'Private vehicles',
            'name_fr' => 'Vehicules particuliers',
            'display_order' => 1,
        ]);

        $bookingResponse = $this->get(route('book-inspection', [
            'center' => 'nacho-yaounde',
            'category' => 'private',
        ]))->assertOk();

        $this->assertMatchesRegularExpression(
            '/<option value="nacho-yaounde" selected>/',
            $bookingResponse->getContent(),
        );
        $this->assertMatchesRegularExpression(
            '/<option value="category-b-private" selected>/',
            $bookingResponse->getContent(),
        );

        $department = CareerDepartment::factory()->create(['slug' => 'technical-operations']);
        $openVacancy = CareerPost::factory()
            ->for($department, 'department')
            ->for($center)
            ->create([
                'reference' => 'NCH-CAR-2026-043',
                'title_en' => 'Inspection Technician',
                'title_fr' => 'Technicien inspection',
                'slug' => 'inspection-technician',
                'status' => CareerPostStatus::PUBLISHED->value,
                'published_at' => now()->subDay(),
                'closes_at' => today()->addMonth(),
                'application_email' => 'careers@nacho.local',
                'application_subject' => 'Application - {title} - {reference}',
            ]);
        $closedVacancy = CareerPost::factory()
            ->for($department, 'department')
            ->for($center)
            ->create([
                'reference' => 'NCH-CAR-2026-044',
                'slug' => 'filled-technician',
                'status' => CareerPostStatus::FILLED->value,
                'published_at' => now()->subDays(10),
                'closes_at' => today()->subDay(),
                'allow_email_application' => false,
            ]);

        $careersResponse = $this->get(route('careers.index', [
            'vacancy' => $openVacancy->slug,
        ]))->assertOk();

        $careersResponse
            ->assertSee($openVacancy->reference)
            ->assertSee($openVacancy->slug, false)
            ->assertSee('mailto:careers@nacho.local', false)
            ->assertSee(rawurlencode($openVacancy->reference), false)
            ->assertDontSee($closedVacancy->reference)
            ->assertDontSee('type="file"', false);

        $this->assertFalse(Schema::hasTable('job_applications'));
    }

    /**
     * @return array<string, mixed>
     */
    private function adminFixtures(): array
    {
        $roles = collect(array_keys(AdminAccess::matrix()))
            ->mapWithKeys(fn (string $slug): array => [$slug => $this->role($slug)]);

        $superAdmin = User::factory()->for($roles['super-admin'])->create();
        $staffUser = User::factory()->for($roles['admin'])->create();
        $center = Center::factory()->create();
        $service = Service::factory()->create();
        $tariff = Tariff::factory()->create();
        $booking = Booking::factory()
            ->for($center)
            ->for($service)
            ->for($tariff)
            ->create();
        $contactMessage = ContactMessage::factory()->create();
        $blogCategory = BlogCategory::factory()->create();
        $blogPost = BlogPost::factory()
            ->for($blogCategory, 'category')
            ->for($staffUser, 'author')
            ->create();
        $careerDepartment = CareerDepartment::factory()->create();
        $careerPost = CareerPost::factory()
            ->for($careerDepartment, 'department')
            ->for($center)
            ->for($staffUser, 'creator')
            ->create();
        $page = Page::factory()->create();
        $media = Media::factory()->for($staffUser, 'uploader')->create();

        return compact(
            'superAdmin',
            'staffUser',
            'center',
            'service',
            'tariff',
            'booking',
            'contactMessage',
            'blogCategory',
            'blogPost',
            'careerDepartment',
            'careerPost',
            'page',
            'media',
            'roles',
        );
    }

    /**
     * @param  array<string, mixed>  $fixtures
     * @return array<string, string>
     */
    private function representativeAdminRoutes(array $fixtures): array
    {
        return [
            'admin dashboard' => route('admin.home'),
            'centers index' => route('admin.centers.index'),
            'centers create' => route('admin.centers.create'),
            'centers show' => route('admin.centers.show', $fixtures['center']),
            'services index' => route('admin.services.index'),
            'services create' => route('admin.services.create'),
            'services show' => route('admin.services.show', $fixtures['service']),
            'tariffs index' => route('admin.tariffs.index'),
            'tariffs create' => route('admin.tariffs.create'),
            'tariffs show' => route('admin.tariffs.show', $fixtures['tariff']),
            'bookings index' => route('admin.bookings.index'),
            'bookings show' => route('admin.bookings.show', $fixtures['booking']),
            'contact messages index' => route('admin.contact-messages.index'),
            'contact messages show' => route('admin.contact-messages.show', $fixtures['contactMessage']),
            'blog posts index' => route('admin.blog-posts.index'),
            'blog posts create' => route('admin.blog-posts.create'),
            'blog posts show' => route('admin.blog-posts.show', $fixtures['blogPost']),
            'blog categories index' => route('admin.blog-categories.index'),
            'blog categories create' => route('admin.blog-categories.create'),
            'blog categories show' => route('admin.blog-categories.show', $fixtures['blogCategory']),
            'career posts index' => route('admin.career-posts.index'),
            'career posts create' => route('admin.career-posts.create'),
            'career posts show' => route('admin.career-posts.show', $fixtures['careerPost']),
            'career departments index' => route('admin.career-departments.index'),
            'career departments create' => route('admin.career-departments.create'),
            'career departments show' => route('admin.career-departments.show', $fixtures['careerDepartment']),
            'pages index' => route('admin.pages.index'),
            'pages create' => route('admin.pages.create'),
            'pages show' => route('admin.pages.show', $fixtures['page']),
            'media index' => route('admin.media.index'),
            'media create' => route('admin.media.create'),
            'media show' => route('admin.media.show', $fixtures['media']),
            'users index' => route('admin.users.index'),
            'users create' => route('admin.users.create'),
            'users show' => route('admin.users.show', $fixtures['staffUser']),
            'roles index' => route('admin.roles.index'),
            'roles show' => route('admin.roles.show', $fixtures['roles']['admin']),
            'site settings index' => route('admin.site-settings.index'),
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
