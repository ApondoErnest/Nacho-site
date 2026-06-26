<?php

namespace App\Http\Requests\Admin;

use App\Models\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TariffRevisionRequest extends FormRequest
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
        return [
            'price_fcfa' => ['required', 'integer', 'min:0', 'max:100000000'],
            'validity_value' => ['required', 'integer', 'min:1', 'max:120'],
            'validity_unit' => ['required', Rule::in(Tariff::VALIDITY_UNITS)],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:effective_date'],
            'regulatory_reference' => ['nullable', 'string', 'max:255'],
            'last_verified_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'is_bookable' => ['nullable', 'boolean'],
            'effective_date' => ['required', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_bookable' => $this->boolean('is_bookable'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function snapshotAttributes(): array
    {
        return collect($this->validated())
            ->except('effective_date')
            ->all();
    }
}
