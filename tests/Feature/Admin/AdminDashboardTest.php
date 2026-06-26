<?php

namespace Tests\Feature\Admin;

use App\Enums\BookingStatus;
use App\Enums\CareerPostStatus;
use App\Enums\CenterStatus;
use App\Enums\ContactMessageStatus;
use App\Enums\ContentStatus;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\CareerPost;
use App\Models\Center;
use App\Models\ContactMessage;
use App\Models\Role;
use App\Models\Service;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authorized_staff_can_see_dashboard_counts(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();

        $center = Center::factory()->create(['status' => CenterStatus::ACTIVE->value]);
        Center::factory()->create(['status' => CenterStatus::ACTIVE->value]);
        Center::factory()->construction()->create();
        $service = Service::factory()->create();
        Service::factory()->count(3)->create();
        $tariff = Tariff::factory()->create();
        Tariff::factory()->count(4)->create();
        Booking::factory()
            ->for($center)
            ->for($service)
            ->for($tariff)
            ->count(3)
            ->create(['status' => BookingStatus::PENDING->value]);
        Booking::factory()
            ->for($center)
            ->for($service)
            ->for($tariff)
            ->create(['status' => BookingStatus::CONFIRMED->value]);
        ContactMessage::factory()->count(2)->create(['status' => ContactMessageStatus::NEW->value]);
        ContactMessage::factory()->create(['status' => ContactMessageStatus::READ->value]);
        BlogPost::factory()->count(2)->create([
            'status' => ContentStatus::PUBLISHED->value,
            'published_at' => now()->subMinute(),
        ]);
        BlogPost::factory()->create([
            'status' => ContentStatus::DRAFT->value,
            'published_at' => null,
        ]);
        CareerPost::factory()->create([
            'center_id' => $center->id,
            'status' => CareerPostStatus::PUBLISHED->value,
            'published_at' => now()->subMinute(),
            'closes_at' => today()->addWeek(),
        ]);
        CareerPost::factory()->create([
            'center_id' => $center->id,
            'status' => CareerPostStatus::CLOSED->value,
            'published_at' => now()->subMinute(),
            'closes_at' => today()->subDay(),
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Total centers')
            ->assertSee('Operational centers')
            ->assertSee('Under construction')
            ->assertSee('Pending bookings')
            ->assertSee('Unread messages')
            ->assertSee('Published posts')
            ->assertSee('Open vacancies')
            ->assertSeeInOrder([
                'Total centers',
                '3',
                'Operational centers',
                '2',
                'Under construction',
                '1',
                'Total services',
                '4',
                'Total tariffs',
                '5',
                'Total bookings',
                '4',
                'Pending bookings',
                '3',
                'Contact messages',
                '3',
                'Unread messages',
                '2',
                'Published posts',
                '2',
                'Open vacancies',
                '1',
            ])
            ->assertSee('3 pending')
            ->assertSee('2 unread');
    }

    #[Test]
    public function sidebar_navigation_is_filtered_by_staff_abilities(): void
    {
        $contentManager = User::factory()->for($this->role('content-manager'))->create();

        $response = $this->actingAs($contentManager)->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Services')
            ->assertSee('Blog')
            ->assertSee('Careers')
            ->assertSee('Pages')
            ->assertSee('Media')
            ->assertDontSee('Bookings')
            ->assertDontSee('Messages')
            ->assertDontSee('Settings')
            ->assertDontSee('Users')
            ->assertDontSee('Roles');
    }

    #[Test]
    public function super_admin_sees_sensitive_navigation_items(): void
    {
        $superAdmin = User::factory()->for($this->role('super-admin'))->create();

        $this->actingAs($superAdmin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Users')
            ->assertSee('Roles')
            ->assertSee('Settings');
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
