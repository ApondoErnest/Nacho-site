<?php

namespace App\Http\Requests;

use App\Models\Center;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+().\-\s]+$/'],
            'email' => ['required', 'email', 'max:255'],
            'preferred_center' => ['required', 'string', 'max:255'],
            'reason' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:5', 'max:4000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'consent.accepted' => __('contact.validation.consent_accepted'),
            'phone.regex' => __('contact.validation.phone'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if (! $this->contactCenter()) {
                $validator->errors()->add('preferred_center', __('contact.validation.center_unavailable'));
            }

            if (! $this->reasonLabel()) {
                $validator->errors()->add('reason', __('contact.validation.reason_unavailable'));
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_name' => $this->string('full_name')->trim()->toString(),
            'phone' => $this->string('phone')->trim()->toString(),
            'email' => $this->string('email')->trim()->lower()->toString(),
            'message' => $this->string('message')->trim()->toString(),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return route('contact').'#contact-form';
    }

    public function isSpam(): bool
    {
        return filled($this->input('website'));
    }

    public function contactCenter(): ?Center
    {
        return Center::query()
            ->active()
            ->operational()
            ->where('slug', (string) $this->input('preferred_center'))
            ->first();
    }

    public function reasonLabel(): ?string
    {
        return $this->reasonOptions()->get((string) $this->input('reason'));
    }

    public function contactSubject(): string
    {
        return __('contact.storage.subject', [
            'reason' => $this->reasonLabel(),
            'center' => $this->contactCenter()?->localized('name'),
        ]);
    }

    public function contactMessageBody(): string
    {
        $center = $this->contactCenter();

        return collect([
            $this->validated('message'),
            __('contact.storage.preferred_center', [
                'center' => $center?->localized('name'),
            ]),
            __('contact.storage.reason', [
                'reason' => $this->reasonLabel(),
            ]),
        ])->filter()->implode(PHP_EOL.PHP_EOL);
    }

    /**
     * @return Collection<string, string>
     */
    private function reasonOptions(): Collection
    {
        return collect(__('contact.form.reasons'))
            ->mapWithKeys(fn (string $reason): array => [str($reason)->slug()->toString() => $reason]);
    }
}
