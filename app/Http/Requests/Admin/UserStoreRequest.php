<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserStatus;
use App\Support\AdminAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->whereIn('slug', array_keys(AdminAccess::matrix()))),
            ],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => $this->input('phone') ?: null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function userAttributes(): array
    {
        return $this->validated();
    }
}
