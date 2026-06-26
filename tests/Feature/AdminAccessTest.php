<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_are_redirected_from_admin_home(): void
    {
        $this->get('/admin')
            ->assertRedirect('/login');
    }

    #[Test]
    public function active_admin_users_can_reach_admin_home(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard');
    }

    #[Test]
    public function authenticated_users_without_admin_roles_are_forbidden_from_admin_home(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function inactive_admin_users_cannot_reach_admin_home(): void
    {
        $user = User::factory()
            ->for($this->role('admin'))
            ->create(['status' => 'inactive']);

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    #[Test]
    public function admin_ability_helper_matches_the_role_matrix(): void
    {
        $admin = User::factory()->for($this->role('admin'))->create();
        $contentManager = User::factory()->for($this->role('content-manager'))->create();
        $inspector = User::factory()->for($this->role('inspector'))->create();

        $this->assertTrue(AdminAccess::can($admin, 'bookings.update'));
        $this->assertFalse(AdminAccess::can($admin, 'users.update'));

        $this->assertTrue(AdminAccess::can($contentManager, 'blog.create'));
        $this->assertFalse(AdminAccess::can($contentManager, 'bookings.view'));

        $this->assertTrue(AdminAccess::can($inspector, 'bookings.status.update'));
        $this->assertFalse(AdminAccess::can($inspector, 'bookings.delete'));
    }

    #[Test]
    public function ability_middleware_blocks_roles_without_the_required_ability(): void
    {
        Route::get('/admin-access-test/bookings', fn () => 'ok')
            ->middleware(['web', 'auth', 'admin.active', 'admin.ability:bookings.view']);

        $contentManager = User::factory()->for($this->role('content-manager'))->create();
        $receptionist = User::factory()->for($this->role('receptionist'))->create();

        $this->actingAs($contentManager)
            ->get('/admin-access-test/bookings')
            ->assertForbidden();

        $this->actingAs($receptionist)
            ->get('/admin-access-test/bookings')
            ->assertOk()
            ->assertSee('ok');
    }

    #[Test]
    public function role_middleware_allows_super_admin_for_sensitive_routes(): void
    {
        Route::get('/admin-access-test/users', fn () => 'ok')
            ->middleware(['web', 'auth', 'admin.active', 'role:super-admin']);

        $admin = User::factory()->for($this->role('admin'))->create();
        $superAdmin = User::factory()->for($this->role('super-admin'))->create();

        $this->actingAs($admin)
            ->get('/admin-access-test/users')
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->get('/admin-access-test/users')
            ->assertOk()
            ->assertSee('ok');
    }

    #[Test]
    public function blade_admin_can_directive_renders_per_current_user_ability(): void
    {
        $admin = User::factory()->for($this->role('admin'))->create();
        $inspector = User::factory()->for($this->role('inspector'))->create();

        $template = "@adminCan('site-settings.update') Settings @else Hidden @endadminCan";

        $this->actingAs($admin);
        $this->assertSame('Settings ', Blade::render($template));

        $this->actingAs($inspector);
        $this->assertSame('Hidden ', Blade::render($template));
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
