<?php

namespace App\Http\Requests\Admin;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class MediaUploadRequest extends FormRequest
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
            'file' => [
                'required',
                'file',
                'max:'.Media::MAX_UPLOAD_KILOBYTES,
                'mimes:'.implode(',', Media::ALLOWED_EXTENSIONS),
            ],
            'alt_text_en' => ['nullable', 'string', 'max:255'],
            'alt_text_fr' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $file = $this->file('file');

            if (! $file || ! str_starts_with((string) $file->getMimeType(), 'image/')) {
                return;
            }

            if (@getimagesize($file->getRealPath()) === false) {
                $validator->errors()->add('file', 'The uploaded image could not be verified.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'alt_text_en' => $this->input('alt_text_en') ?: null,
            'alt_text_fr' => $this->input('alt_text_fr') ?: null,
        ]);
    }
}
