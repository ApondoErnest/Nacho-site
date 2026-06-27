<?php

namespace Tests\Feature\Admin;

use App\Enums\CareerPostStatus;
use App\Models\CareerDepartment;
use App\Models\CareerPost;
use App\Models\Center;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminCareerManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authorized_staff_can_filter_and_view_career_vacancies(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $department = CareerDepartment::factory()->create([
            'name_en' => 'Technical Inspection',
            'slug' => 'technical-inspection',
        ]);
        $otherDepartment = CareerDepartment::factory()->create([
            'name_en' => 'Administration',
            'slug' => 'administration',
        ]);
        $center = Center::factory()->create(['name_en' => 'NACHO Yaounde']);
        $otherCenter = Center::factory()->create(['name_en' => 'NACHO Bamenda']);

        CareerPost::factory()
            ->for($department, 'department')
            ->for($center)
            ->for($user, 'creator')
            ->create([
                'reference' => 'NCH-CAR-2026-101',
                'title_en' => 'Vehicle Inspector',
                'slug' => 'vehicle-inspector',
                'summary_en' => 'Inspect vehicles and support road safety.',
                'status' => CareerPostStatus::PUBLISHED->value,
                'published_at' => now()->subDay(),
                'closes_at' => today()->addMonth(),
            ]);

        CareerPost::factory()
            ->for($otherDepartment, 'department')
            ->for($otherCenter)
            ->create([
                'reference' => 'NCH-CAR-2026-202',
                'title_en' => 'Hidden Draft',
                'slug' => 'hidden-draft',
                'status' => CareerPostStatus::DRAFT->value,
                'published_at' => null,
            ]);

        $this->actingAs($user)
            ->get(route('admin.career-posts.index', [
                'search' => 'Inspector',
                'status' => CareerPostStatus::PUBLISHED->value,
                'department_id' => $department->id,
                'center_id' => $center->id,
            ]))
            ->assertOk()
            ->assertSee('Career vacancy management')
            ->assertSee('Vehicle Inspector')
            ->assertSee('NCH-CAR-2026-101')
            ->assertSee('Technical Inspection')
            ->assertSee('NACHO Yaounde')
            ->assertDontSee('Hidden Draft')
            ->assertDontSee('NCH-CAR-2026-202');
    }

    #[Test]
    public function authorized_staff_can_view_career_vacancy_details_and_mailto_preview(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $department = CareerDepartment::factory()->create(['name_en' => 'Center Operations']);
        $center = Center::factory()->create(['name_en' => 'NACHO Kumba']);
        $post = CareerPost::factory()
            ->for($department, 'department')
            ->for($center)
            ->for($user, 'creator')
            ->create([
                'reference' => 'NCH-CAR-2026-303',
                'title_en' => 'Reception Officer',
                'title_fr' => 'Agent accueil',
                'slug' => 'reception-officer',
                'description_en' => 'Coordinate visitor flow at the center.',
                'requirements_en' => 'Customer care experience.',
                'application_email' => 'careers@example.test',
                'application_subject' => 'Apply - {title} - {reference}',
                'application_instructions_en' => 'Send CV and cover letter.',
                'allow_email_application' => true,
                'status' => CareerPostStatus::PUBLISHED->value,
            ]);

        $this->actingAs($user)
            ->get(route('admin.career-posts.show', $post))
            ->assertOk()
            ->assertSee('Career Vacancy Details')
            ->assertSee('Reception Officer')
            ->assertSee('Agent accueil')
            ->assertSee('Center Operations')
            ->assertSee('NACHO Kumba')
            ->assertSee('Coordinate visitor flow at the center.')
            ->assertSee('Customer care experience.')
            ->assertSee('Preview email link')
            ->assertSee('Edit')
            ->assertSee('Archive');
    }

    #[Test]
    public function content_manager_can_create_and_publish_a_career_vacancy(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $department = CareerDepartment::factory()->create();
        $center = Center::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.career-posts.store'), $this->validPostPayload($department, $center, [
                'status' => CareerPostStatus::PUBLISHED->value,
                'published_at' => null,
            ]));

        $post = CareerPost::query()->where('slug', 'vehicle-inspector-trainee')->firstOrFail();

        $response->assertRedirect(route('admin.career-posts.show', $post));
        $this->assertSame($user->id, $post->created_by);
        $this->assertSame(CareerPostStatus::PUBLISHED, $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertSame($department->id, $post->department_id);
        $this->assertSame($center->id, $post->center_id);
        $this->assertTrue($post->allow_email_application);
    }

    #[Test]
    public function content_manager_can_update_and_archive_a_career_vacancy(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $department = CareerDepartment::factory()->create();
        $newDepartment = CareerDepartment::factory()->create();
        $center = Center::factory()->create();
        $post = CareerPost::factory()
            ->for($department, 'department')
            ->for($center)
            ->create([
                'title_en' => 'Old Vacancy',
                'slug' => 'old-vacancy',
                'status' => CareerPostStatus::DRAFT->value,
                'published_at' => null,
                'allow_email_application' => true,
            ]);

        $response = $this->actingAs($user)
            ->put(route('admin.career-posts.update', $post), $this->validPostPayload($newDepartment, null, [
                'title_en' => 'Updated Inspector Role',
                'slug' => 'updated-inspector-role',
                'status' => CareerPostStatus::DRAFT->value,
                'published_at' => null,
                'allow_email_application' => '0',
            ]));

        $post->refresh();

        $response->assertRedirect(route('admin.career-posts.show', $post));
        $this->assertSame('Updated Inspector Role', $post->title_en);
        $this->assertSame('updated-inspector-role', $post->slug);
        $this->assertSame(CareerPostStatus::DRAFT, $post->status);
        $this->assertNull($post->published_at);
        $this->assertSame($newDepartment->id, $post->department_id);
        $this->assertNull($post->center_id);
        $this->assertFalse($post->allow_email_application);

        $this->actingAs($user)
            ->delete(route('admin.career-posts.destroy', $post))
            ->assertRedirect(route('admin.career-posts.index'));

        $post->refresh();
        $this->assertSame(CareerPostStatus::ARCHIVED, $post->status);
        $this->assertFalse($post->allow_email_application);
        $this->assertFalse($post->trashed());
    }

    #[Test]
    public function content_manager_can_manage_career_departments(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();

        $response = $this->actingAs($user)
            ->post(route('admin.career-departments.store'), $this->validDepartmentPayload());

        $department = CareerDepartment::query()->where('slug', 'inspection-team')->firstOrFail();

        $response->assertRedirect(route('admin.career-departments.show', $department));
        $this->assertSame('Inspection Team', $department->name_en);
        $this->assertSame('clipboard-check', $department->icon);
        $this->assertTrue($department->is_active);

        $this->actingAs($user)
            ->put(route('admin.career-departments.update', $department), $this->validDepartmentPayload([
                'name_en' => 'Updated Inspection Team',
                'slug' => 'updated-inspection-team',
                'is_active' => '0',
            ]))
            ->assertRedirect(route('admin.career-departments.show', $department));

        $department->refresh();
        $this->assertSame('Updated Inspection Team', $department->name_en);
        $this->assertSame('updated-inspection-team', $department->slug);
        $this->assertFalse($department->is_active);

        $this->actingAs($user)
            ->delete(route('admin.career-departments.destroy', $department))
            ->assertRedirect(route('admin.career-departments.index'));

        $this->assertFalse($department->refresh()->is_active);
        $this->assertDatabaseHas('career_departments', ['id' => $department->id]);
    }

    #[Test]
    public function unauthorized_staff_are_blocked_from_career_management(): void
    {
        $receptionist = User::factory()->for($this->role('receptionist'))->create();
        $department = CareerDepartment::factory()->create();
        $post = CareerPost::factory()->for($department, 'department')->create();

        $this->get(route('admin.career-posts.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($receptionist)
            ->get(route('admin.career-posts.index'))
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->get(route('admin.career-posts.create'))
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->put(route('admin.career-posts.update', $post), $this->validPostPayload($department, null))
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->get(route('admin.career-departments.index'))
            ->assertForbidden();
    }

    #[Test]
    public function career_validation_rejects_duplicate_identifiers_invalid_options_and_bad_relationships(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        $department = CareerDepartment::factory()->create();
        CareerPost::factory()->create([
            'reference' => 'NCH-CAR-2026-900',
            'slug' => 'duplicate-vacancy',
        ]);
        CareerDepartment::factory()->create(['slug' => 'duplicate-department']);

        $this->actingAs($user)
            ->from(route('admin.career-posts.create'))
            ->post(route('admin.career-posts.store'), $this->validPostPayload($department, null, [
                'reference' => 'NCH-CAR-2026-900',
                'slug' => 'duplicate-vacancy',
                'department_id' => 999999,
                'center_id' => 999999,
                'employment_type' => 'volunteer',
                'application_email' => 'not-an-email',
                'status' => 'live',
            ]))
            ->assertRedirect(route('admin.career-posts.create'))
            ->assertSessionHasErrors(['reference', 'slug', 'department_id', 'center_id', 'employment_type', 'application_email', 'status']);

        $this->actingAs($user)
            ->from(route('admin.career-departments.create'))
            ->post(route('admin.career-departments.store'), $this->validDepartmentPayload([
                'slug' => 'duplicate-department',
                'icon' => 'not-a-real-icon',
            ]))
            ->assertRedirect(route('admin.career-departments.create'))
            ->assertSessionHasErrors(['slug', 'icon']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPostPayload(CareerDepartment $department, ?Center $center, array $overrides = []): array
    {
        return [
            'reference' => 'NCH-CAR-2026-501',
            'title_en' => 'Vehicle Inspector Trainee',
            'title_fr' => 'Stagiaire inspecteur vehicule',
            'slug' => 'vehicle-inspector-trainee',
            'department_id' => $department->id,
            'center_id' => $center?->id,
            'employment_type' => 'full-time',
            'summary_en' => 'Support inspection operations and customer safety.',
            'summary_fr' => 'Soutenir les operations inspection et la securite client.',
            'description_en' => 'Entry-level technical role inside an inspection center.',
            'description_fr' => 'Role technique debutant dans un centre inspection.',
            'responsibilities_en' => 'Inspect vehicles and prepare reports.',
            'responsibilities_fr' => 'Inspecter les vehicules et preparer les rapports.',
            'requirements_en' => 'Technical training and attention to detail.',
            'requirements_fr' => 'Formation technique et attention aux details.',
            'preferred_requirements_en' => 'Automotive experience.',
            'preferred_requirements_fr' => 'Experience automobile.',
            'skills_en' => 'Diagnostics, teamwork, reporting.',
            'skills_fr' => 'Diagnostic, travail equipe, reporting.',
            'application_documents_en' => 'CV and cover letter',
            'application_documents_fr' => 'CV et lettre de motivation',
            'application_email' => 'careers@example.test',
            'application_subject' => 'Application - {title} - {reference}',
            'application_instructions_en' => 'Send your CV and cover letter by email.',
            'application_instructions_fr' => 'Envoyez votre CV et lettre par email.',
            'vacancies_count' => 2,
            'published_at' => null,
            'closes_at' => today()->addMonth()->format('Y-m-d'),
            'status' => CareerPostStatus::DRAFT->value,
            'allow_email_application' => '1',
            'display_order' => 4,
            'seo_title_en' => 'Vehicle inspector trainee',
            'seo_title_fr' => 'Stagiaire inspecteur vehicule',
            'meta_description_en' => 'Apply by email for a NACHO inspection role.',
            'meta_description_fr' => 'Postulez par email pour un role NACHO.',
            ...$overrides,
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validDepartmentPayload(array $overrides = []): array
    {
        return [
            'name_en' => 'Inspection Team',
            'name_fr' => 'Equipe inspection',
            'slug' => 'inspection-team',
            'description_en' => 'Vehicle inspection roles.',
            'description_fr' => 'Roles inspection vehicule.',
            'icon' => 'clipboard-check',
            'display_order' => 3,
            'is_active' => '1',
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
