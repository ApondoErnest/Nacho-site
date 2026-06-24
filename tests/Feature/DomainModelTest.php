<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\CareerPostStatus;
use App\Enums\CenterStatus;
use App\Enums\ContactMessageStatus;
use App\Enums\ContactType;
use App\Enums\ContentStatus;
use App\Enums\SettingType;
use App\Enums\TariffRevisionStatus;
use App\Enums\UserStatus;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\CareerPost;
use App\Models\Center;
use App\Models\CenterContact;
use App\Models\ContactMessage;
use App\Models\Role;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Tariff;
use App\Models\TariffAuditLog;
use App\Models\TariffRevision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_relationship_and_active_scope(): void
    {
        $role = Role::factory()->create(['slug' => 'content-manager']);
        $activeUser = User::factory()->create([
            'role_id' => $role->id,
            'status' => UserStatus::ACTIVE->value,
        ]);
        User::factory()->create(['status' => UserStatus::INACTIVE->value]);

        $this->assertTrue($activeUser->role->is($role));
        $this->assertSame(UserStatus::ACTIVE, $activeUser->status);
        $this->assertTrue(User::active()->whereKey($activeUser)->exists());
        $this->assertSame(1, User::active()->count());
    }

    public function test_center_relationships_scopes_and_localized_fields(): void
    {
        $center = Center::factory()->create([
            'name_en' => 'NACHO Test Center',
            'name_fr' => 'Centre Test NACHO',
            'status' => CenterStatus::ACTIVE->value,
            'booking_enabled' => true,
            'is_active' => true,
        ]);
        Center::factory()->construction()->create();

        $contact = CenterContact::factory()->for($center)->create([
            'type' => ContactType::PHONE->value,
            'is_primary' => true,
            'is_public' => true,
        ]);
        $service = Service::factory()->create();
        $center->services()->attach($service->id, [
            'is_available' => true,
            'booking_enabled' => true,
            'effective_date' => today(),
        ]);

        app()->setLocale('fr');

        $this->assertSame('Centre Test NACHO', $center->localized('name'));
        $this->assertSame(CenterStatus::ACTIVE, $center->status);
        $this->assertTrue($center->contacts()->public()->primary()->first()->is($contact));
        $this->assertTrue($center->services()->first()->is($service));
        $this->assertTrue(Center::bookable()->whereKey($center)->exists());
        $this->assertSame(1, Center::expansion()->count());
    }

    public function test_booking_and_tariff_scopes_casts_and_relationships(): void
    {
        $currentTariff = Tariff::factory()->create([
            'effective_date' => today()->subDay(),
            'expiry_date' => today()->addDay(),
            'is_active' => true,
            'is_bookable' => true,
        ]);
        Tariff::factory()->create([
            'effective_date' => today()->subDays(10),
            'expiry_date' => today()->subDay(),
            'is_active' => true,
            'is_bookable' => true,
        ]);

        $booking = Booking::factory()->create([
            'tariff_id' => $currentTariff->id,
            'status' => BookingStatus::PENDING->value,
        ]);

        $this->assertSame(BookingStatus::PENDING, $booking->status);
        $this->assertInstanceOf(Center::class, $booking->center);
        $this->assertInstanceOf(Service::class, $booking->service);
        $this->assertTrue($booking->tariff->is($currentTariff));
        $this->assertTrue(Booking::pending()->whereKey($booking)->exists());
        $this->assertSame(1, Tariff::active()->bookable()->effective()->count());
    }

    public function test_content_and_career_public_scopes(): void
    {
        $publishedPost = BlogPost::factory()->create([
            'status' => ContentStatus::PUBLISHED->value,
            'published_at' => now()->subMinute(),
        ]);
        BlogPost::factory()->create([
            'status' => ContentStatus::DRAFT->value,
            'published_at' => null,
        ]);

        $openCareerPost = CareerPost::factory()->create([
            'status' => CareerPostStatus::PUBLISHED->value,
            'published_at' => now()->subMinute(),
            'closes_at' => today()->addWeek(),
        ]);
        CareerPost::factory()->create([
            'status' => CareerPostStatus::PUBLISHED->value,
            'published_at' => now()->subMinute(),
            'closes_at' => today()->subDay(),
        ]);

        $this->assertTrue(BlogPost::published()->first()->is($publishedPost));
        $this->assertSame(1, BlogPost::published()->count());
        $this->assertTrue(CareerPost::open()->first()->is($openCareerPost));
        $this->assertSame(1, CareerPost::open()->count());
    }

    public function test_contact_messages_settings_and_tariff_history_casts(): void
    {
        $message = ContactMessage::factory()->create([
            'status' => ContactMessageStatus::NEW->value,
        ]);
        $setting = SiteSetting::factory()->create([
            'type' => SettingType::BOOLEAN->value,
            'value' => '1',
        ]);
        $revision = TariffRevision::factory()->create([
            'status' => TariffRevisionStatus::ACTIVE->value,
            'snapshot' => ['price_fcfa' => 17900],
        ]);
        $auditLog = TariffAuditLog::factory()->create([
            'changes' => ['price_fcfa' => ['old' => 15000, 'new' => 17900]],
        ]);

        $this->assertSame(ContactMessageStatus::NEW, $message->status);
        $this->assertTrue(ContactMessage::open()->whereKey($message)->exists());
        $this->assertTrue($setting->typedValue());
        $this->assertSame(TariffRevisionStatus::ACTIVE, $revision->status);
        $this->assertSame(17900, $revision->snapshot['price_fcfa']);
        $this->assertSame(15000, $auditLog->changes['price_fcfa']['old']);
    }
}
