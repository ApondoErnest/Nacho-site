<?php

namespace Tests\Feature\Admin;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminUserRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function super_admin_can_filter_and_view_staff_users(): void
    {
        $actor = User::factory()->for($this->role('super-admin'))->create(['name' => 'Root Admin']);
        $contentRole = $this->role('content-manager');
        $adminRole = $this->role('admin');

        User::factory()->for($contentRole)->create([
            'name' => 'Content Lead',
            'email' => 'content.lead@example.com',
            'phone' => '+237600000001',
            'status' => UserStatus::ACTIVE->value,
            'last_login_at' => now()->subHour(),
        ]);

        User::factory()->for($adminRole)->create([
            'name' => 'Hidden Inactive',
            'email' => 'hidden@example.com',
            'status' => UserStatus::INACTIVE->value,
        ]);

        $this->actingAs($actor)
            ->get(route('admin.users.index', [
                'search' => 'Content',
                'status' => UserStatus::ACTIVE->value,
                'role_id' => $contentRole->id,
            ]))
            ->assertOk()
            ->assertSee('User management')
            ->assertSee('Content Lead')
            ->assertSee('content.lead@example.com')
            ->assertSee('Content Manager')
            ->assertSee('Active')
            ->assertDontSee('Hidden Inactive')
            ->assertDontSee('hidden@example.com');
    }

    #[Test]
    public function super_admin_can_create_update_reset_password_and_deactivate_staff(): void
    {
        $actor = User::factory()->for($this->role('super-admin'))->create();
        $adminRole = $this->role('admin');
        $receptionistRole = $this->role('receptionist');

        $response = $this->actingAs($actor)
            ->post(route('admin.users.store'), $this->validUserPayload($adminRole));

        $staff = User::query()->where('email', 'staff.user@example.com')->firstOrFail();

        $response->assertRedirect(route('admin.users.show', $staff));
        $this->assertSame('Staff User', $staff->name);
        $this->assertSame($adminRole->id, $staff->role_id);
        $this->assertSame(UserStatus::ACTIVE, $staff->status);
        $this->assertTrue(Hash::check('SecurePass2026', $staff->password));
        $this->assertNotNull($staff->email_verified_at);

        $this->actingAs($actor)
            ->put(route('admin.users.update', $staff), $this->validUserPayload($receptionistRole, [
                'name' => 'Updated Staff',
                'email' => 'updated.staff@example.com',
                'phone' => '+237600000099',
                'password' => 'ChangedPass2026',
                'password_confirmation' => 'ChangedPass2026',
            ]))
            ->assertRedirect(route('admin.users.show', $staff));

        $staff->refresh();
        $this->assertSame('Updated Staff', $staff->name);
        $this->assertSame('updated.staff@example.com', $staff->email);
        $this->assertSame($receptionistRole->id, $staff->role_id);
        $this->assertTrue(Hash::check('ChangedPass2026', $staff->password));

        $this->actingAs($actor)
            ->delete(route('admin.users.destroy', $staff))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame(UserStatus::INACTIVE, $staff->refresh()->status);
    }

    #[Test]
    public function super_admin_can_view_and_update_role_labels_without_changing_slug(): void
    {
        $actor = User::factory()->for($this->role('super-admin'))->create();
        $role = $this->role('content-manager');

        $this->actingAs($actor)
            ->get(route('admin.roles.index'))
            ->assertOk()
            ->assertSee('Role management')
            ->assertSee('content-manager')
            ->assertSee('Content Manager')
            ->assertSee('Abilities');

        $this->actingAs($actor)
            ->get(route('admin.roles.show', $role))
            ->assertOk()
            ->assertSee('Role Details')
            ->assertSee('Content Manager')
            ->assertSee('services.*')
            ->assertSee('media.*')
            ->assertSee('pages.*');

        $this->actingAs($actor)
            ->put(route('admin.roles.update', $role), [
                'name' => 'Content Lead',
                'slug' => 'content-manager',
                'description' => 'Owns bilingual public content updates.',
            ])
            ->assertRedirect(route('admin.roles.show', $role));

        $role->refresh();
        $this->assertSame('Content Lead', $role->name);
        $this->assertSame('content-manager', $role->slug);
        $this->assertSame('Owns bilingual public content updates.', $role->description);

        $this->actingAs($actor)
            ->from(route('admin.roles.edit', $role))
            ->put(route('admin.roles.update', $role), [
                'name' => 'Changed Slug Attempt',
                'slug' => 'admin',
                'description' => 'Should fail.',
            ])
            ->assertRedirect(route('admin.roles.edit', $role))
            ->assertSessionHasErrors(['slug']);
    }

    #[Test]
    public function non_super_admin_staff_are_blocked_from_users_and_roles(): void
    {
        $admin = User::factory()->for($this->role('admin'))->create();
        $staff = User::factory()->for($this->role('content-manager'))->create();
        $role = $this->role('content-manager');

        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $staff), $this->validUserPayload($role))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    #[Test]
    public function super_admin_cannot_deactivate_or_demote_their_own_account(): void
    {
        $superRole = $this->role('super-admin');
        $adminRole = $this->role('admin');
        $actor = User::factory()->for($superRole)->create([
            'email' => 'root@example.com',
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->actingAs($actor)
            ->from(route('admin.users.edit', $actor))
            ->put(route('admin.users.update', $actor), $this->validUserPayload($adminRole, [
                'name' => $actor->name,
                'email' => $actor->email,
                'status' => UserStatus::INACTIVE->value,
                'password' => null,
                'password_confirmation' => null,
            ]))
            ->assertRedirect(route('admin.users.edit', $actor))
            ->assertSessionHasErrors(['role_id']);

        $actor->refresh();
        $this->assertSame($superRole->id, $actor->role_id);
        $this->assertSame(UserStatus::ACTIVE, $actor->status);

        $this->actingAs($actor)
            ->from(route('admin.users.show', $actor))
            ->delete(route('admin.users.destroy', $actor))
            ->assertRedirect(route('admin.users.show', $actor))
            ->assertSessionHasErrors(['user']);

        $this->assertSame(UserStatus::ACTIVE, $actor->refresh()->status);
    }

    #[Test]
    public function user_validation_rejects_duplicate_email_weak_password_and_unknown_role(): void
    {
        $actor = User::factory()->for($this->role('super-admin'))->create();
        $role = $this->role('admin');
        User::factory()->create(['email' => 'duplicate@example.com']);
        $unmappedRole = Role::factory()->create(['slug' => 'temporary-role']);

        $this->actingAs($actor)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), $this->validUserPayload($role, [
                'email' => 'duplicate@example.com',
                'role_id' => $unmappedRole->id,
                'password' => 'short',
                'password_confirmation' => 'different',
                'status' => 'paused',
            ]))
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrors(['email', 'role_id', 'password', 'status']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validUserPayload(Role $role, array $overrides = []): array
    {
        return [
            'name' => 'Staff User',
            'email' => 'staff.user@example.com',
            'phone' => '+237600000000',
            'role_id' => $role->id,
            'status' => UserStatus::ACTIVE->value,
            'password' => 'SecurePass2026',
            'password_confirmation' => 'SecurePass2026',
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
