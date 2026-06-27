<?php

namespace App\Http\Requests\Admin;

use App\Enums\BookingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingUpdateRequest extends FormRequest
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
            'status' => ['required', Rule::enum(BookingStatus::class)],
            'preferred_date' => ['required', 'date'],
            'preferred_time' => ['required', 'regex:/^(0[7-9]|1[0-7]):(00|15|30|45)$/'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'preferred_time.regex' => 'Use a center operating time between 07:00 and 17:45 in 15-minute steps.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'admin_notes' => $this->string('admin_notes')->trim()->toString() ?: null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function bookingAttributes(): array
    {
        return $this->validated();
    }
}
