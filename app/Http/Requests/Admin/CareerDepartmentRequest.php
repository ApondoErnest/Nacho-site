<?php

namespace App\Http\Requests\Admin;

use App\Models\CareerDepartment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CareerDepartmentRequest extends FormRequest
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
        $department = $this->route('careerDepartment');

        return [
            'name_en' => ['required', 'string', 'max:255'],
            'name_fr' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('career_departments', 'slug')->ignore($department),
            ],
            'description_en' => ['nullable', 'string', 'max:3000'],
            'description_fr' => ['nullable', 'string', 'max:3000'],
            'icon' => ['nullable', 'string', 'max:80', Rule::in(CareerDepartment::LUCIDE_ICONS)],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'icon.in' => 'Choose one of the supported department icons.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => str($this->input('slug'))->slug()->toString(),
            'icon' => str($this->input('icon'))->slug()->toString() ?: CareerDepartment::DEFAULT_ICON,
            'display_order' => $this->input('display_order', 0),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function departmentAttributes(): array
    {
        return $this->validated();
    }
}
