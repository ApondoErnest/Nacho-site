<?php

namespace Tests\Feature\Admin;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminContactMessageManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authorized_staff_can_filter_and_view_contact_message_index(): void
    {
        $user = User::factory()->for($this->role('receptionist'))->create();
        $submittedAt = today()->subDay()->setTime(9, 30);

        ContactMessage::factory()->create([
            'full_name' => 'Grace Tabi',
            'email' => 'grace@example.test',
            'phone' => '+237 670 111 222',
            'subject' => 'Vehicle inspection question',
            'message' => 'I need help choosing the right center.',
            'status' => ContactMessageStatus::NEW->value,
            'created_at' => $submittedAt,
        ]);

        ContactMessage::factory()->create([
            'full_name' => 'Hidden Person',
            'email' => 'hidden@example.test',
            'subject' => 'Archived request',
            'message' => 'This message should be filtered away.',
            'status' => ContactMessageStatus::ARCHIVED->value,
            'created_at' => today()->subDays(4),
        ]);

        $this->actingAs($user)
            ->get(route('admin.contact-messages.index', [
                'search' => 'Grace',
                'status' => ContactMessageStatus::NEW->value,
                'date_from' => $submittedAt->format('Y-m-d'),
                'date_to' => $submittedAt->format('Y-m-d'),
            ]))
            ->assertOk()
            ->assertSee('Contact message management')
            ->assertSee('Total messages')
            ->assertSee('New')
            ->assertSee('Grace Tabi')
            ->assertSee('Vehicle inspection question')
            ->assertSee('grace@example.test')
            ->assertDontSee('Hidden Person')
            ->assertDontSee('Archived request');
    }

    #[Test]
    public function authorized_staff_can_view_contact_message_details(): void
    {
        $user = User::factory()->for($this->role('receptionist'))->create();
        $message = ContactMessage::factory()->create([
            'full_name' => 'Muna Alain',
            'email' => 'alain@example.test',
            'phone' => '+237 699 222 333',
            'subject' => 'Inspection documents',
            'message' => 'Which documents should I bring for inspection?',
            'admin_notes' => 'Needs follow-up from reception.',
            'status' => ContactMessageStatus::READ->value,
        ]);

        $this->actingAs($user)
            ->get(route('admin.contact-messages.show', $message))
            ->assertOk()
            ->assertSee('Message Details')
            ->assertSee('Inspection documents')
            ->assertSee('Muna Alain')
            ->assertSee('alain@example.test')
            ->assertSee('Which documents should I bring for inspection?')
            ->assertSee('Needs follow-up from reception.')
            ->assertSee('Quick actions')
            ->assertSee('Update message')
            ->assertSee('Save message');
    }

    #[Test]
    public function receptionist_can_update_contact_message_status_and_notes(): void
    {
        $user = User::factory()->for($this->role('receptionist'))->create();
        $message = ContactMessage::factory()->create([
            'status' => ContactMessageStatus::NEW->value,
            'admin_notes' => null,
        ]);

        $response = $this->actingAs($user)
            ->put(route('admin.contact-messages.update', $message), [
                'status' => ContactMessageStatus::REPLIED->value,
                'admin_notes' => '  Replied by email and shared center hours.  ',
            ]);

        $response->assertRedirect(route('admin.contact-messages.show', $message));
        $message->refresh();

        $this->assertSame(ContactMessageStatus::REPLIED, $message->status);
        $this->assertSame('Replied by email and shared center hours.', $message->admin_notes);
    }

    #[Test]
    public function receptionist_can_use_quick_status_actions(): void
    {
        $user = User::factory()->for($this->role('receptionist'))->create();
        $message = ContactMessage::factory()->create([
            'status' => ContactMessageStatus::NEW->value,
        ]);

        $this->actingAs($user)
            ->patch(route('admin.contact-messages.status.update', $message), [
                'status' => ContactMessageStatus::READ->value,
            ])
            ->assertRedirect(route('admin.contact-messages.show', $message));

        $message->refresh();
        $this->assertSame(ContactMessageStatus::READ, $message->status);
    }

    #[Test]
    public function unauthorized_and_guest_users_are_blocked_from_contact_message_management(): void
    {
        $contentManager = User::factory()->for($this->role('content-manager'))->create();
        $inspector = User::factory()->for($this->role('inspector'))->create();
        $message = ContactMessage::factory()->create();

        $this->get(route('admin.contact-messages.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($contentManager)
            ->get(route('admin.contact-messages.index'))
            ->assertForbidden();

        $this->actingAs($inspector)
            ->get(route('admin.contact-messages.show', $message))
            ->assertForbidden();

        $this->actingAs($contentManager)
            ->patch(route('admin.contact-messages.status.update', $message), [
                'status' => ContactMessageStatus::ARCHIVED->value,
            ])
            ->assertForbidden();
    }

    #[Test]
    public function contact_message_update_validation_rejects_invalid_status_and_long_notes(): void
    {
        $user = User::factory()->for($this->role('admin'))->create();
        $message = ContactMessage::factory()->create([
            'status' => ContactMessageStatus::NEW->value,
            'admin_notes' => 'Original note',
        ]);

        $this->actingAs($user)
            ->from(route('admin.contact-messages.show', $message))
            ->put(route('admin.contact-messages.update', $message), [
                'status' => 'closed',
                'admin_notes' => str_repeat('a', 5001),
            ])
            ->assertRedirect(route('admin.contact-messages.show', $message))
            ->assertSessionHasErrors(['status', 'admin_notes']);

        $message->refresh();
        $this->assertSame(ContactMessageStatus::NEW, $message->status);
        $this->assertSame('Original note', $message->admin_notes);
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
