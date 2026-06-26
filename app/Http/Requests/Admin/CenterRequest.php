<?php

namespace App\Http\Requests\Admin;

use App\Enums\CenterStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CenterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $center = $this->route('center');

        return [
            'name_en' => ['required', 'string', 'max:255'],
            'name_fr' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('centers', 'slug')->ignore($center),
            ],
            'city_en' => ['required', 'string', 'max:255'],
            'city_fr' => ['required', 'string', 'max:255'],
            'region_en' => ['nullable', 'string', 'max:255'],
            'region_fr' => ['nullable', 'string', 'max:255'],
            'address_en' => ['nullable', 'string', 'max:2000'],
            'address_fr' => ['nullable', 'string', 'max:2000'],
            'postal_address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(CenterStatus::class)],
            'description_en' => ['nullable', 'string', 'max:3000'],
            'description_fr' => ['nullable', 'string', 'max:3000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'google_maps_url' => ['nullable', 'url', 'max:255'],
            'nearby_landmark' => ['nullable', 'string', 'max:255'],
            'search_keywords' => ['nullable', 'string', 'max:2000'],
            'vehicle_categories_en' => ['nullable', 'string', 'max:2000'],
            'vehicle_categories_fr' => ['nullable', 'string', 'max:2000'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'target_opening_date' => ['nullable', 'date'],
            'target_date_text_en' => ['nullable', 'string', 'max:255'],
            'target_date_text_fr' => ['nullable', 'string', 'max:255'],
            'expansion_phase' => ['nullable', 'string', 'max:255'],
            'expansion_updated_at' => ['nullable', 'date'],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_headquarters' => ['nullable', 'boolean'],
            'booking_enabled' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => str($this->input('slug'))->slug()->toString(),
            'display_order' => $this->input('display_order', 0),
            'is_headquarters' => $this->boolean('is_headquarters'),
            'booking_enabled' => $this->boolean('booking_enabled'),
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active'),
            'service_ids' => collect($this->input('service_ids', []))
                ->filter()
                ->map(fn (mixed $value): int => (int) $value)
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function centerAttributes(): array
    {
        return collect($this->validated())
            ->except('service_ids')
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function serviceSyncPayload(): array
    {
        return collect($this->validated('service_ids', []))
            ->mapWithKeys(fn (int $serviceId): array => [
                $serviceId => [
                    'is_available' => true,
                    'booking_enabled' => $this->boolean('booking_enabled'),
                    'effective_date' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ])
            ->all();
    }
}
