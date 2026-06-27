<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogCategoryRequest extends FormRequest
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
        $category = $this->route('blogCategory');

        return [
            'name_en' => ['required', 'string', 'max:255'],
            'name_fr' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('blog_categories', 'slug')->ignore($category),
            ],
            'description_en' => ['nullable', 'string', 'max:3000'],
            'description_fr' => ['nullable', 'string', 'max:3000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => str($this->input('slug'))->slug()->toString(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function categoryAttributes(): array
    {
        return $this->validated();
    }
}
