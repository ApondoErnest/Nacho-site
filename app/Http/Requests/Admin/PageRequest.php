<?php

namespace App\Http\Requests\Admin;

use App\Enums\ContentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
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
        $page = $this->route('page');

        return [
            'title_en' => ['required', 'string', 'max:255'],
            'title_fr' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('pages', 'slug')->ignore($page),
            ],
            'content_en' => ['nullable', 'string', 'max:100000'],
            'content_fr' => ['nullable', 'string', 'max:100000'],
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_title_fr' => ['nullable', 'string', 'max:255'],
            'meta_description_en' => ['nullable', 'string', 'max:500'],
            'meta_description_fr' => ['nullable', 'string', 'max:500'],
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
    public function pageAttributes(): array
    {
        return $this->validated();
    }
}
