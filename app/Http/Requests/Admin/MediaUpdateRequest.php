<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MediaUpdateRequest extends FormRequest
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
            'file_name' => ['required', 'string', 'max:255'],
            'alt_text_en' => ['nullable', 'string', 'max:255'],
            'alt_text_fr' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'file_name' => $this->input('file_name') ?: null,
            'alt_text_en' => $this->input('alt_text_en') ?: null,
            'alt_text_fr' => $this->input('alt_text_fr') ?: null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function mediaAttributes(): array
    {
        return $this->validated();
    }
}
