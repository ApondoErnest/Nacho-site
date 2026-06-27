<?php

namespace Tests\Feature\Admin;

use App\Enums\ContentStatus;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminPageManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authorized_staff_can_filter_and_view_pages(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();

        Page::factory()->create([
            'title_en' => 'Privacy Policy',
            'title_fr' => 'Politique de confidentialite',
            'slug' => 'privacy-policy',
            'content_en' => 'Privacy content for NACHO visitors.',
            'status' => ContentStatus::PUBLISHED->value,
        ]);

        Page::factory()->create([
            'title_en' => 'Internal Draft',
            'slug' => 'internal-draft',
            'status' => ContentStatus::DRAFT->value,
        ]);

        $this->actingAs($user)
            ->get(route('admin.pages.index', [
                'search' => 'Privacy',
                'status' => ContentStatus::PUBLISHED->value,
            ]))
            ->assertOk()
            ->assertSee('Page management')
            ->assertSee('Privacy Policy')
            ->assertSee('privacy-policy')
            ->assertSee('Published')
            ->assertSee('/privacy-policy')
            ->assertDontSee('Internal Draft')
            ->assertDontSee('internal-draft');
    }

    #[Test]
    public function authorized_staff_can_view_page_details(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $page = Page::factory()->create([
            'title_en' => 'Legal Notice',
            'title_fr' => 'Mentions legales',
            'slug' => 'legal-notice',
            'content_en' => 'NACHO legal publisher details.',
            'content_fr' => 'Details juridiques NACHO.',
            'seo_title_en' => 'Legal Notice SEO',
            'meta_description_en' => 'Legal notice meta description.',
            'status' => ContentStatus::PUBLISHED->value,
        ]);

        $this->actingAs($user)
            ->get(route('admin.pages.show', $page))
            ->assertOk()
            ->assertSee('Page Details')
            ->assertSee('Legal Notice')
            ->assertSee('Mentions legales')
            ->assertSee('NACHO legal publisher details.')
            ->assertSee('Legal Notice SEO')
            ->assertSee('/legal-notice')
            ->assertSee('Edit')
            ->assertSee('Archive');
    }

    #[Test]
    public function content_manager_can_create_a_page(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();

        $response = $this->actingAs($user)
            ->post(route('admin.pages.store'), $this->validPagePayload([
                'status' => ContentStatus::PUBLISHED->value,
            ]));

        $page = Page::query()->where('slug', 'inspection-rights')->firstOrFail();

        $response->assertRedirect(route('admin.pages.show', $page));
        $this->assertSame('Inspection Rights', $page->title_en);
        $this->assertSame(ContentStatus::PUBLISHED, $page->status);
        $this->assertSame('Know your rights before inspection.', $page->content_en);
    }

    #[Test]
    public function content_manager_can_update_archive_and_control_public_visibility(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $page = Page::factory()->create([
            'title_en' => 'Privacy Policy',
            'slug' => 'privacy-policy',
            'content_en' => 'Old privacy copy.',
            'status' => ContentStatus::DRAFT->value,
        ]);

        $this->get(route('legal.privacy'))
            ->assertOk()
            ->assertDontSee('Old privacy copy.');

        $response = $this->actingAs($user)
            ->put(route('admin.pages.update', $page), $this->validPagePayload([
                'title_en' => 'Updated Privacy Policy',
                'title_fr' => 'Politique de confidentialite mise a jour',
                'slug' => 'privacy-policy',
                'content_en' => 'Updated privacy copy from admin.',
                'content_fr' => 'Texte de confidentialite mis a jour.',
                'status' => ContentStatus::PUBLISHED->value,
            ]));

        $page->refresh();

        $response->assertRedirect(route('admin.pages.show', $page));
        $this->assertSame('Updated Privacy Policy', $page->title_en);
        $this->assertSame(ContentStatus::PUBLISHED, $page->status);

        $this->get(route('legal.privacy'))
            ->assertOk()
            ->assertSee('Texte de confidentialite mis a jour.');

        $this->actingAs($user)
            ->delete(route('admin.pages.destroy', $page))
            ->assertRedirect(route('admin.pages.index'));

        $page->refresh();
        $this->assertSame(ContentStatus::ARCHIVED, $page->status);
        $this->assertFalse($page->trashed());

        $this->get(route('legal.privacy'))
            ->assertOk()
            ->assertDontSee('Texte de confidentialite mis a jour.');
    }

    #[Test]
    public function unauthorized_staff_are_blocked_from_page_management(): void
    {
        $receptionist = User::factory()->for($this->role('receptionist'))->create();
        $page = Page::factory()->create();

        $this->get(route('admin.pages.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($receptionist)
            ->get(route('admin.pages.index'))
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->get(route('admin.pages.create'))
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->put(route('admin.pages.update', $page), $this->validPagePayload())
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->delete(route('admin.pages.destroy', $page))
            ->assertForbidden();
    }

    #[Test]
    public function page_validation_rejects_duplicate_slug_and_bad_status(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        Page::factory()->create(['slug' => 'duplicate-page']);

        $this->actingAs($user)
            ->from(route('admin.pages.create'))
            ->post(route('admin.pages.store'), $this->validPagePayload([
                'slug' => 'duplicate-page',
                'status' => 'live',
                'meta_description_en' => str_repeat('A', 501),
            ]))
            ->assertRedirect(route('admin.pages.create'))
            ->assertSessionHasErrors(['slug', 'status', 'meta_description_en']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPagePayload(array $overrides = []): array
    {
        return [
            'title_en' => 'Inspection Rights',
            'title_fr' => 'Droits inspection',
            'slug' => 'inspection-rights',
            'content_en' => 'Know your rights before inspection.',
            'content_fr' => 'Connaissez vos droits avant inspection.',
            'status' => ContentStatus::DRAFT->value,
            'seo_title_en' => 'Inspection rights',
            'seo_title_fr' => 'Droits inspection',
            'meta_description_en' => 'Guidance for vehicle inspection customers.',
            'meta_description_fr' => 'Conseils pour clients de controle technique.',
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
