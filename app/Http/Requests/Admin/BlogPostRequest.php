<?php

namespace App\Http\Requests\Admin;

use App\Enums\ContentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogPostRequest extends FormRequest
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
        $post = $this->route('blogPost');

        return [
            'blog_category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'title_en' => ['required', 'string', 'max:255'],
            'title_fr' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('blog_posts', 'slug')->ignore($post),
            ],
            'excerpt_en' => ['nullable', 'string', 'max:1000'],
            'excerpt_fr' => ['nullable', 'string', 'max:1000'],
            'content_en' => ['nullable', 'string', 'max:50000'],
            'content_fr' => ['nullable', 'string', 'max:50000'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'published_at' => ['nullable', 'date'],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_title_fr' => ['nullable', 'string', 'max:255'],
            'meta_description_en' => ['nullable', 'string', 'max:500'],
            'meta_description_fr' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'blog_category_id' => $this->input('blog_category_id') ?: null,
            'slug' => str($this->input('slug'))->slug()->toString(),
            'published_at' => $this->input('published_at') ?: null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function postAttributes(): array
    {
        return $this->validated();
    }
}
