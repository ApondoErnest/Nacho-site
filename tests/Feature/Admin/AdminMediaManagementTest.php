<?php

namespace Tests\Feature\Admin;

use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminMediaManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authorized_staff_can_filter_and_view_media_library(): void
    {
        $user = User::factory()->for($this->role('content-manager'))->create();

        Media::factory()->for($user, 'uploader')->create([
            'file_name' => 'inspection-bay.jpg',
            'file_path' => 'media/inspection-bay.jpg',
            'file_type' => Media::TYPE_IMAGE,
            'mime_type' => 'image/jpeg',
            'alt_text_en' => 'Inspection bay with vehicle lift',
        ]);

        Media::factory()->create([
            'file_name' => 'policy.pdf',
            'file_path' => 'media/policy.pdf',
            'file_type' => Media::TYPE_DOCUMENT,
            'mime_type' => 'application/pdf',
            'alt_text_en' => 'Policy document',
        ]);

        $this->actingAs($user)
            ->get(route('admin.media.index', [
                'search' => 'inspection',
                'type' => Media::TYPE_IMAGE,
            ]))
            ->assertOk()
            ->assertSee('Media library')
            ->assertSee('inspection-bay.jpg')
            ->assertSee('Inspection bay with vehicle lift')
            ->assertSee('Image')
            ->assertDontSee('policy.pdf');
    }

    #[Test]
    public function content_manager_can_upload_an_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->for($this->role('content-manager'))->create();

        $response = $this->actingAs($user)
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->image('inspection-bay.jpg', 800, 500)->size(512),
                'alt_text_en' => 'Vehicle in inspection bay',
                'alt_text_fr' => 'Vehicule dans la baie inspection',
            ]);

        $media = Media::query()->where('file_name', 'inspection-bay.jpg')->firstOrFail();

        $response->assertRedirect(route('admin.media.show', $media));
        $this->assertSame($user->id, $media->uploaded_by);
        $this->assertSame(Media::TYPE_IMAGE, $media->file_type);
        $this->assertSame('Vehicle in inspection bay', $media->alt_text_en);
        $this->assertStringStartsWith('media/', $media->file_path);
        Storage::disk('public')->assertExists($media->file_path);

        $this->actingAs($user)
            ->get(route('admin.media.show', $media))
            ->assertOk()
            ->assertSee('Media Details')
            ->assertSee('inspection-bay.jpg')
            ->assertSee('Vehicle in inspection bay')
            ->assertSee($media->file_path)
            ->assertSee('Open file');
    }

    #[Test]
    public function content_manager_can_upload_a_document(): void
    {
        Storage::fake('public');
        $user = User::factory()->for($this->role('content-manager'))->create();

        $response = $this->actingAs($user)
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->create('inspection-guide.pdf', 600, 'application/pdf'),
                'alt_text_en' => 'Inspection guide document',
            ]);

        $media = Media::query()->where('file_name', 'inspection-guide.pdf')->firstOrFail();

        $response->assertRedirect(route('admin.media.show', $media));
        $this->assertSame(Media::TYPE_DOCUMENT, $media->file_type);
        $this->assertSame('application/pdf', $media->mime_type);
        Storage::disk('public')->assertExists($media->file_path);

        $this->actingAs($user)
            ->get(route('admin.media.show', $media))
            ->assertOk()
            ->assertSee('Inspection guide document')
            ->assertSee('Open document');
    }

    #[Test]
    public function content_manager_can_update_metadata_and_delete_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->for($this->role('content-manager'))->create();
        Storage::disk('public')->put('media/test/old.jpg', 'test-image-contents');

        $media = Media::factory()->for($user, 'uploader')->create([
            'file_name' => 'old.jpg',
            'file_path' => 'media/test/old.jpg',
            'file_type' => Media::TYPE_IMAGE,
            'mime_type' => 'image/jpeg',
            'alt_text_en' => null,
            'alt_text_fr' => null,
        ]);

        $this->actingAs($user)
            ->put(route('admin.media.update', $media), [
                'file_name' => 'updated-center-photo.jpg',
                'alt_text_en' => 'Updated center photo',
                'alt_text_fr' => 'Photo du centre mise a jour',
            ])
            ->assertRedirect(route('admin.media.show', $media));

        $media->refresh();
        $this->assertSame('updated-center-photo.jpg', $media->file_name);
        $this->assertSame('Updated center photo', $media->alt_text_en);
        Storage::disk('public')->assertExists('media/test/old.jpg');

        $this->actingAs($user)
            ->delete(route('admin.media.destroy', $media))
            ->assertRedirect(route('admin.media.index'));

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing('media/test/old.jpg');
    }

    #[Test]
    public function media_permissions_match_admin_role_matrix(): void
    {
        Storage::fake('public');
        $centerManager = User::factory()->for($this->role('center-manager'))->create();
        $receptionist = User::factory()->for($this->role('receptionist'))->create();
        $media = Media::factory()->create();

        $this->get(route('admin.media.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($receptionist)
            ->get(route('admin.media.index'))
            ->assertForbidden();

        $this->actingAs($centerManager)
            ->get(route('admin.media.index'))
            ->assertOk();

        $uploadResponse = $this->actingAs($centerManager)
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->image('center-upload.jpg')->size(256),
                'alt_text_en' => 'Center upload',
            ]);

        $uploaded = Media::query()->where('file_name', 'center-upload.jpg')->firstOrFail();
        $uploadResponse->assertRedirect(route('admin.media.show', $uploaded));

        $this->actingAs($centerManager)
            ->get(route('admin.media.edit', $media))
            ->assertForbidden();

        $this->actingAs($centerManager)
            ->delete(route('admin.media.destroy', $media))
            ->assertForbidden();
    }

    #[Test]
    public function media_validation_rejects_unsafe_or_oversized_uploads(): void
    {
        Storage::fake('public');
        $user = User::factory()->for($this->role('admin'))->create();

        $this->actingAs($user)
            ->from(route('admin.media.create'))
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->create('shell.php', 1, 'application/x-php'),
            ])
            ->assertRedirect(route('admin.media.create'))
            ->assertSessionHasErrors(['file']);

        $this->actingAs($user)
            ->from(route('admin.media.create'))
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->image('too-large.jpg')->size(11_000),
            ])
            ->assertRedirect(route('admin.media.create'))
            ->assertSessionHasErrors(['file']);

        $this->actingAs($user)
            ->from(route('admin.media.create'))
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->create('fake.jpg', 1, 'image/jpeg'),
            ])
            ->assertRedirect(route('admin.media.create'))
            ->assertSessionHasErrors(['file']);
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
