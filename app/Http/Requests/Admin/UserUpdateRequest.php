<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserStatus;
use App\Support\AdminAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserUpdateRequest extends FormRequest
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
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->whereIn('slug', array_keys(AdminAccess::matrix()))),
            ],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'password' => ['nullable', 'confirmed', Password::min(12)->letters()->numbers()],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => $this->input('phone') ?: null,
            'password' => $this->input('password') ?: null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function userAttributes(): array
    {
        $attributes = $this->validated();

        if (blank($attributes['password'] ?? null)) {
            unset($attributes['password']);
        }

        return $attributes;
    }

    public function ensureCurrentUserKeepsAccess(): void
    {
        $target = $this->route('user');
        $actor = $this->user();

        if (! $target || ! $actor || ! $target->is($actor)) {
            return;
        }

        $currentRoleId = (string) $target->role_id;
        $newRoleId = (string) $this->validated('role_id');
        $newStatus = $this->validated('status');

        if ($currentRoleId !== $newRoleId || $newStatus !== UserStatus::ACTIVE->value) {
            throw ValidationException::withMessages([
                'role_id' => 'You cannot change your own role or deactivate your own account.',
            ]);
        }

        if (! AdminAccess::can($target, 'users.update')) {
            throw ValidationException::withMessages([
                'role_id' => 'Your account must keep user-management access.',
            ]);
        }
    }
}
