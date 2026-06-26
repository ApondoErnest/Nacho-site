<?php

namespace App\Http\Requests\Admin;

use App\Models\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TariffRequest extends FormRequest
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
        $tariff = $this->route('tariff');

        return [
            'category_code' => ['required', 'string', 'max:20'],
            'category_slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('tariffs', 'category_slug')->ignore($tariff),
            ],
            'name_en' => ['required', 'string', 'max:255'],
            'name_fr' => ['required', 'string', 'max:255'],
            'description_en' => ['nullable', 'string', 'max:3000'],
            'description_fr' => ['nullable', 'string', 'max:3000'],
            'price_fcfa' => ['required', 'integer', 'min:0', 'max:100000000'],
            'validity_value' => ['required', 'integer', 'min:1', 'max:120'],
            'validity_unit' => ['required', Rule::in(Tariff::VALIDITY_UNITS)],
            'minimum_weight_kg' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'maximum_weight_kg' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'vehicle_icon' => ['nullable', 'string', 'max:80', Rule::in(Tariff::VEHICLE_ICONS)],
            'effective_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:effective_date'],
            'regulatory_reference' => ['nullable', 'string', 'max:255'],
            'last_verified_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'is_bookable' => ['nullable', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vehicle_icon.in' => 'Choose one of the supported vehicle icons.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'category_code' => str($this->input('category_code'))->upper()->replace(' ', '')->toString(),
            'category_slug' => str($this->input('category_slug'))->slug()->toString(),
            'vehicle_icon' => str($this->input('vehicle_icon'))->slug()->toString() ?: null,
            'display_order' => $this->input('display_order', 0),
            'is_active' => $this->boolean('is_active'),
            'is_bookable' => $this->boolean('is_bookable'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function tariffAttributes(): array
    {
        return $this->validated();
    }
}
