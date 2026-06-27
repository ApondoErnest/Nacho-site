<?php

namespace Tests\Feature\Admin;

use App\Enums\ContentStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminBlogManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authorized_staff_can_filter_and_view_blog_posts(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $category = BlogCategory::factory()->create([
            'name_en' => 'Road Safety',
            'slug' => 'road-safety',
        ]);
        $otherCategory = BlogCategory::factory()->create([
            'name_en' => 'Company News',
            'slug' => 'company-news',
        ]);

        BlogPost::factory()
            ->for($category, 'category')
            ->for($user, 'author')
            ->create([
                'title_en' => 'Road Safety Basics',
                'slug' => 'road-safety-basics',
                'excerpt_en' => 'Safety advice for vehicle owners.',
                'status' => ContentStatus::PUBLISHED->value,
                'published_at' => now()->subDay(),
            ]);

        BlogPost::factory()
            ->for($otherCategory, 'category')
            ->create([
                'title_en' => 'Hidden Draft',
                'slug' => 'hidden-draft',
                'status' => ContentStatus::DRAFT->value,
                'published_at' => null,
            ]);

        $this->actingAs($user)
            ->get(route('admin.blog-posts.index', [
                'search' => 'Safety',
                'status' => ContentStatus::PUBLISHED->value,
                'blog_category_id' => $category->id,
            ]))
            ->assertOk()
            ->assertSee('Blog management')
            ->assertSee('Road Safety Basics')
            ->assertSee('road-safety-basics')
            ->assertSee('Road Safety')
            ->assertSee('Published')
            ->assertDontSee('Hidden Draft')
            ->assertDontSee('hidden-draft');
    }

    #[Test]
    public function authorized_staff_can_view_blog_post_details(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $category = BlogCategory::factory()->create(['name_en' => 'Maintenance']);
        $post = BlogPost::factory()
            ->for($category, 'category')
            ->for($user, 'author')
            ->create([
                'title_en' => 'Inspection Preparation',
                'title_fr' => 'Preparation inspection',
                'slug' => 'inspection-preparation',
                'content_en' => 'Bring your vehicle documents and arrive early.',
                'content_fr' => 'Apportez vos documents du vehicule.',
                'seo_title_en' => 'Prepare for inspection',
                'status' => ContentStatus::PUBLISHED->value,
            ]);

        $this->actingAs($user)
            ->get(route('admin.blog-posts.show', $post))
            ->assertOk()
            ->assertSee('Blog Post Details')
            ->assertSee('Inspection Preparation')
            ->assertSee('Preparation inspection')
            ->assertSee('Maintenance')
            ->assertSee('Bring your vehicle documents and arrive early.')
            ->assertSee('Prepare for inspection')
            ->assertSee('Edit')
            ->assertSee('Archive');
    }

    #[Test]
    public function content_manager_can_create_and_publish_a_blog_post(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $category = BlogCategory::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.blog-posts.store'), $this->validPostPayload($category, [
                'status' => ContentStatus::PUBLISHED->value,
                'published_at' => null,
            ]));

        $post = BlogPost::query()->where('slug', 'safe-braking-guide')->firstOrFail();

        $response->assertRedirect(route('admin.blog-posts.show', $post));
        $this->assertSame($user->id, $post->author_id);
        $this->assertSame(ContentStatus::PUBLISHED, $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertSame($category->id, $post->blog_category_id);
        $this->assertSame('Safe Braking Guide', $post->title_en);
    }

    #[Test]
    public function content_manager_can_update_and_archive_a_blog_post(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();
        $category = BlogCategory::factory()->create();
        $newCategory = BlogCategory::factory()->create();
        $post = BlogPost::factory()
            ->for($category, 'category')
            ->create([
                'title_en' => 'Old Post',
                'slug' => 'old-post',
                'status' => ContentStatus::DRAFT->value,
                'published_at' => null,
            ]);

        $response = $this->actingAs($user)
            ->put(route('admin.blog-posts.update', $post), $this->validPostPayload($newCategory, [
                'title_en' => 'Updated Road Safety',
                'slug' => 'updated-road-safety',
                'status' => ContentStatus::DRAFT->value,
                'published_at' => null,
            ]));

        $post->refresh();

        $response->assertRedirect(route('admin.blog-posts.show', $post));
        $this->assertSame('Updated Road Safety', $post->title_en);
        $this->assertSame('updated-road-safety', $post->slug);
        $this->assertSame(ContentStatus::DRAFT, $post->status);
        $this->assertNull($post->published_at);
        $this->assertSame($newCategory->id, $post->blog_category_id);

        $this->actingAs($user)
            ->delete(route('admin.blog-posts.destroy', $post))
            ->assertRedirect(route('admin.blog-posts.index'));

        $post->refresh();
        $this->assertSame(ContentStatus::ARCHIVED, $post->status);
        $this->assertFalse($post->trashed());
    }

    #[Test]
    public function content_manager_can_manage_blog_categories(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();

        $response = $this->actingAs($user)
            ->post(route('admin.blog-categories.store'), $this->validCategoryPayload());

        $category = BlogCategory::query()->where('slug', 'technical-advice')->firstOrFail();

        $response->assertRedirect(route('admin.blog-categories.show', $category));
        $this->assertSame('Technical Advice', $category->name_en);

        $this->actingAs($user)
            ->put(route('admin.blog-categories.update', $category), $this->validCategoryPayload([
                'name_en' => 'Updated Advice',
                'slug' => 'updated-advice',
            ]))
            ->assertRedirect(route('admin.blog-categories.show', $category));

        $category->refresh();
        $this->assertSame('Updated Advice', $category->name_en);
        $this->assertSame('updated-advice', $category->slug);

        $post = BlogPost::factory()->for($category, 'category')->create();

        $this->actingAs($user)
            ->delete(route('admin.blog-categories.destroy', $category))
            ->assertRedirect(route('admin.blog-categories.index'));

        $this->assertDatabaseMissing('blog_categories', ['id' => $category->id]);
        $this->assertNull($post->refresh()->blog_category_id);
    }

    #[Test]
    public function unauthorized_staff_are_blocked_from_blog_management(): void
    {
        $receptionist = User::factory()->for($this->role('receptionist'))->create();
        $post = BlogPost::factory()->create();
        $category = BlogCategory::factory()->create();

        $this->get(route('admin.blog-posts.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($receptionist)
            ->get(route('admin.blog-posts.index'))
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->get(route('admin.blog-posts.create'))
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->put(route('admin.blog-posts.update', $post), $this->validPostPayload($category))
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->get(route('admin.blog-categories.index'))
            ->assertForbidden();
    }

    #[Test]
    public function blog_validation_rejects_duplicate_slugs_bad_status_and_unknown_category(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        $category = BlogCategory::factory()->create();
        BlogPost::factory()->create(['slug' => 'duplicate-post']);
        BlogCategory::factory()->create(['slug' => 'duplicate-category']);

        $this->actingAs($user)
            ->from(route('admin.blog-posts.create'))
            ->post(route('admin.blog-posts.store'), $this->validPostPayload($category, [
                'slug' => 'duplicate-post',
                'status' => 'live',
                'blog_category_id' => 999999,
            ]))
            ->assertRedirect(route('admin.blog-posts.create'))
            ->assertSessionHasErrors(['slug', 'status', 'blog_category_id']);

        $this->actingAs($user)
            ->from(route('admin.blog-categories.create'))
            ->post(route('admin.blog-categories.store'), $this->validCategoryPayload([
                'slug' => 'duplicate-category',
            ]))
            ->assertRedirect(route('admin.blog-categories.create'))
            ->assertSessionHasErrors(['slug']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPostPayload(BlogCategory $category, array $overrides = []): array
    {
        return [
            'blog_category_id' => $category->id,
            'title_en' => 'Safe Braking Guide',
            'title_fr' => 'Guide de freinage securise',
            'slug' => 'safe-braking-guide',
            'excerpt_en' => 'Simple habits that keep braking safer.',
            'excerpt_fr' => 'Des habitudes simples pour freiner en securite.',
            'content_en' => 'Inspect tyres, brakes, lights, and documents before a technical visit.',
            'content_fr' => 'Verifiez pneus, freins, feux et documents avant la visite technique.',
            'featured_image' => 'images/blog/safe-braking.jpg',
            'status' => ContentStatus::DRAFT->value,
            'published_at' => null,
            'seo_title_en' => 'Safe braking guide',
            'seo_title_fr' => 'Guide de freinage securise',
            'meta_description_en' => 'Learn safe braking habits before vehicle inspection.',
            'meta_description_fr' => 'Apprenez les bonnes habitudes de freinage.',
            ...$overrides,
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validCategoryPayload(array $overrides = []): array
    {
        return [
            'name_en' => 'Technical Advice',
            'name_fr' => 'Conseils techniques',
            'slug' => 'technical-advice',
            'description_en' => 'Practical inspection and safety guidance.',
            'description_fr' => 'Conseils pratiques pour inspection et securite.',
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
