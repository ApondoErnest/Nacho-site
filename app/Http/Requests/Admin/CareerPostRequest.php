<?php

namespace App\Http\Requests\Admin;

use App\Enums\CareerPostStatus;
use App\Models\CareerPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CareerPostRequest extends FormRequest
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
        $post = $this->route('careerPost');

        return [
            'reference' => [
                'required',
                'string',
                'max:80',
                Rule::unique('career_posts', 'reference')->ignore($post),
            ],
            'title_en' => ['required', 'string', 'max:255'],
            'title_fr' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('career_posts', 'slug')->ignore($post),
            ],
            'department_id' => ['required', 'integer', 'exists:career_departments,id'],
            'center_id' => ['nullable', 'integer', 'exists:centers,id'],
            'employment_type' => ['nullable', 'string', Rule::in(CareerPost::EMPLOYMENT_TYPES)],
            'summary_en' => ['nullable', 'string', 'max:1000'],
            'summary_fr' => ['nullable', 'string', 'max:1000'],
            'description_en' => ['nullable', 'string', 'max:50000'],
            'description_fr' => ['nullable', 'string', 'max:50000'],
            'responsibilities_en' => ['nullable', 'string', 'max:50000'],
            'responsibilities_fr' => ['nullable', 'string', 'max:50000'],
            'requirements_en' => ['nullable', 'string', 'max:50000'],
            'requirements_fr' => ['nullable', 'string', 'max:50000'],
            'preferred_requirements_en' => ['nullable', 'string', 'max:50000'],
            'preferred_requirements_fr' => ['nullable', 'string', 'max:50000'],
            'skills_en' => ['nullable', 'string', 'max:50000'],
            'skills_fr' => ['nullable', 'string', 'max:50000'],
            'application_documents_en' => ['nullable', 'string', 'max:3000'],
            'application_documents_fr' => ['nullable', 'string', 'max:3000'],
            'application_email' => ['nullable', 'email', 'max:255'],
            'application_subject' => ['nullable', 'string', 'max:255'],
            'application_instructions_en' => ['nullable', 'string', 'max:3000'],
            'application_instructions_fr' => ['nullable', 'string', 'max:3000'],
            'vacancies_count' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'published_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date'],
            'status' => ['required', Rule::enum(CareerPostStatus::class)],
            'allow_email_application' => ['nullable', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_title_fr' => ['nullable', 'string', 'max:255'],
            'meta_description_en' => ['nullable', 'string', 'max:500'],
            'meta_description_fr' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'reference' => str($this->input('reference'))->upper()->replace(' ', '-')->toString(),
            'slug' => str($this->input('slug'))->slug()->toString(),
            'center_id' => $this->input('center_id') ?: null,
            'employment_type' => $this->input('employment_type') ?: null,
            'published_at' => $this->input('published_at') ?: null,
            'closes_at' => $this->input('closes_at') ?: null,
            'vacancies_count' => $this->input('vacancies_count') ?: null,
            'allow_email_application' => $this->boolean('allow_email_application'),
            'display_order' => $this->input('display_order', 0),
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
