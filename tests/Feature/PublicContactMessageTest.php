<?php

namespace Tests\Feature;

use App\Enums\CenterStatus;
use App\Enums\ContactMessageStatus;
use App\Models\Center;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_contact_form_stores_new_contact_message(): void
    {
        $center = Center::factory()->create([
            'name_en' => 'NACHO Contact Center',
            'name_fr' => 'Centre Contact NACHO',
            'slug' => 'nacho-contact-center',
            'status' => CenterStatus::ACTIVE->value,
        ]);
        $reason = __('contact.form.reasons')[1];

        $response = $this->post(route('contact.store'), $this->validPayload($center, [
            'email' => 'OWNER@EXAMPLE.COM',
            'reason' => str($reason)->slug()->toString(),
        ]));

        $response
            ->assertRedirect(route('contact').'#contact-form')
            ->assertSessionHas('contact_message_sent', true);

        $message = ContactMessage::query()->sole();

        $this->assertSame('Vehicle Owner', $message->full_name);
        $this->assertSame('owner@example.com', $message->email);
        $this->assertSame('(+237) 678 456 789', $message->phone);
        $this->assertSame(ContactMessageStatus::NEW, $message->status);
        $this->assertStringContainsString($reason, $message->subject);
        $this->assertStringContainsString('Centre Contact NACHO', $message->subject);
        $this->assertStringContainsString('I need help choosing an inspection center.', $message->message);
        $this->assertStringContainsString('Centre Contact NACHO', $message->message);
        $this->assertStringContainsString($reason, $message->message);
    }

    public function test_public_contact_form_rejects_non_operational_center(): void
    {
        $center = Center::factory()->construction()->create();

        $response = $this->from(route('contact'))->post(route('contact.store'), $this->validPayload($center));

        $response
            ->assertRedirect(route('contact').'#contact-form')
            ->assertSessionHasErrors('preferred_center');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_public_contact_form_rejects_unknown_reason(): void
    {
        $center = Center::factory()->create([
            'status' => CenterStatus::ACTIVE->value,
        ]);

        $response = $this->from(route('contact'))->post(route('contact.store'), $this->validPayload($center, [
            'reason' => 'not-a-contact-reason',
        ]));

        $response
            ->assertRedirect(route('contact').'#contact-form')
            ->assertSessionHasErrors('reason');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_form_validation_errors_render_on_the_form_fields(): void
    {
        $center = Center::factory()->create([
            'status' => CenterStatus::ACTIVE->value,
        ]);

        $response = $this->from(route('contact'))->post(route('contact.store'), $this->validPayload($center, [
            'email' => 'not-an-email',
        ]));

        $response
            ->assertRedirect(route('contact').'#contact-form')
            ->assertSessionHasErrors('email');

        $this->followingRedirects()
            ->from(route('contact'))
            ->post(route('contact.store'), $this->validPayload($center, [
                'email' => 'not-an-email',
            ]))
            ->assertOk()
            ->assertSee('data-form-feedback-state="error"', false)
            ->assertSee('id="contact-email-error"', false)
            ->assertSee('aria-invalid="true"', false)
            ->assertDontSee(__('contact.feedback.error_title'))
            ->assertSee('value="not-an-email"', false);
    }

    public function test_public_contact_form_honeypot_redirects_without_storing_message(): void
    {
        $center = Center::factory()->create([
            'status' => CenterStatus::ACTIVE->value,
        ]);

        $response = $this->post(route('contact.store'), $this->validPayload($center, [
            'website' => 'https://spam.example',
        ]));

        $response
            ->assertRedirect(route('contact').'#contact-form')
            ->assertSessionHas('contact_message_sent', true);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_page_posts_to_contact_store_route(): void
    {
        $center = Center::factory()->create([
            'name_en' => 'NACHO Contact Center',
            'name_fr' => 'Centre Contact NACHO',
            'status' => CenterStatus::ACTIVE->value,
        ]);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee(route('contact.store'), false)
            ->assertSee('name="website"', false)
            ->assertSee('value="'.$center->slug.'"', false);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(Center $center, array $overrides = []): array
    {
        $reason = __('contact.form.reasons')[0];

        return array_merge([
            'full_name' => 'Vehicle Owner',
            'phone' => '(+237) 678 456 789',
            'email' => 'owner@example.com',
            'preferred_center' => $center->slug,
            'reason' => str($reason)->slug()->toString(),
            'message' => 'I need help choosing an inspection center.',
            'consent' => '1',
            'website' => '',
        ], $overrides);
    }
}
