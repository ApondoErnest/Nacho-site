<?php

namespace App\Http\Requests\Admin;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
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
        $service = $this->route('service');

        return [
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('services', 'slug')->ignore($service),
            ],
            'title_en' => ['required', 'string', 'max:255'],
            'title_fr' => ['required', 'string', 'max:255'],
            'short_description_en' => ['nullable', 'string', 'max:1000'],
            'short_description_fr' => ['nullable', 'string', 'max:1000'],
            'full_description_en' => ['nullable', 'string', 'max:10000'],
            'full_description_fr' => ['nullable', 'string', 'max:10000'],
            'icon' => ['nullable', 'string', 'max:80', Rule::in(Service::LUCIDE_ICONS)],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_title_fr' => ['nullable', 'string', 'max:255'],
            'meta_description_en' => ['nullable', 'string', 'max:500'],
            'meta_description_fr' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'icon.in' => 'Choose one of the supported service icons.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => str($this->input('slug'))->slug()->toString(),
            'icon' => str($this->input('icon'))->slug()->toString() ?: null,
            'is_active' => $this->boolean('is_active'),
            'display_order' => $this->input('display_order', 0),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function serviceAttributes(): array
    {
        return $this->validated();
    }
}
