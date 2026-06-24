<?php

namespace App\Http\Requests;

use App\Models\Center;
use App\Models\Service;
use App\Models\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
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
            'service' => ['required', 'string', 'max:255'],
            'center' => ['required', 'string', 'max:255'],
            'vehicle_registration' => ['required', 'string', 'max:40'],
            'vehicle_category' => ['required', 'string', 'max:255'],
            'previous_reference' => ['nullable', 'string', 'max:100'],
            'previous_reference_unavailable' => ['nullable', 'boolean'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_hour' => ['required', 'date_format:H'],
            'preferred_minute' => ['required', 'in:00,15,30,45'],
            'preferred_time' => ['required', 'regex:/^(0[7-9]|1[0-7]):(00|15|30|45)$/'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone_country' => ['nullable', 'in:+237'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+().\-\s]+$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'additional_information' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'consent.accepted' => __('book_inspection.validation.consent_accepted'),
            'preferred_time.regex' => __('book_inspection.validation.preferred_time'),
            'phone.regex' => __('book_inspection.validation.phone'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $center = $this->bookingCenterQuery()->first();
            $service = $this->bookingServiceQuery()->first();
            $tariff = $this->bookingTariffQuery()->first();

            if (! $center) {
                $validator->errors()->add('center', __('book_inspection.validation.center_unavailable'));
            }

            if (! $service) {
                $validator->errors()->add('service', __('book_inspection.validation.service_unavailable'));
            }

            if (! $tariff) {
                $validator->errors()->add('vehicle_category', __('book_inspection.validation.tariff_unavailable'));
            }

            if (! $center || ! $service) {
                return;
            }

            $serviceIsBookableAtCenter = $center->services()
                ->whereKey($service->getKey())
                ->wherePivot('is_available', true)
                ->wherePivot('booking_enabled', true)
                ->exists();

            if (! $serviceIsBookableAtCenter) {
                $validator->errors()->add('service', __('book_inspection.validation.service_center_unavailable'));
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $preferredHour = $this->input('preferred_hour');
        $preferredMinute = $this->input('preferred_minute');

        $this->merge([
            'full_name' => $this->string('full_name')->trim()->toString(),
            'phone' => $this->string('phone')->trim()->toString(),
            'email' => $this->string('email')->trim()->toString() ?: null,
            'vehicle_registration' => $this->string('vehicle_registration')->trim()->upper()->toString(),
            'preferred_time' => $preferredHour && $preferredMinute ? "{$preferredHour}:{$preferredMinute}" : $this->input('preferred_time'),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return route('book-inspection').'#book-inspection-form';
    }

    public function bookingCenter(): Center
    {
        return $this->bookingCenterQuery()->firstOrFail();
    }

    public function bookingService(): Service
    {
        return $this->bookingServiceQuery()->firstOrFail();
    }

    public function bookingTariff(): Tariff
    {
        return $this->bookingTariffQuery()->firstOrFail();
    }

    public function normalizedPhone(): string
    {
        $phone = preg_replace('/\s+/', ' ', trim((string) $this->validated('phone')));

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        return trim(($this->validated('phone_country') ?? '+237').' '.$phone);
    }

    public function bookingComment(): ?string
    {
        $parts = collect([
            $this->validated('additional_information') ?? null,
        ])->filter(fn (?string $part): bool => filled($part))->values();

        if (filled($this->validated('previous_reference') ?? null)) {
            $parts->push(__('book_inspection.storage.previous_reference', [
                'reference' => $this->validated('previous_reference'),
            ]));
        } elseif ($this->boolean('previous_reference_unavailable')) {
            $parts->push(__('book_inspection.storage.previous_reference_unavailable'));
        }

        return $parts->isNotEmpty() ? $parts->implode(PHP_EOL) : null;
    }

    private function bookingCenterQuery()
    {
        return Center::query()
            ->bookable()
            ->where('slug', (string) $this->input('center'));
    }

    private function bookingServiceQuery()
    {
        return Service::query()
            ->active()
            ->where('slug', (string) $this->input('service'));
    }

    private function bookingTariffQuery()
    {
        return Tariff::query()
            ->active()
            ->bookable()
            ->effective()
            ->where('category_slug', (string) $this->input('vehicle_category'));
    }
}
