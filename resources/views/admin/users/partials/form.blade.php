<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Identity</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Name</span>
            <input name="name" value="{{ old('name', $staffUser->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Email</span>
            <input type="email" name="email" value="{{ old('email', $staffUser->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Phone</span>
            <input name="phone" value="{{ old('phone', $staffUser->phone) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Status</span>
            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                @foreach ($statuses as $case)
                    <option value="{{ $case->value }}" @selected(old('status', $staffUser->status?->value ?? $staffUser->status) === $case->value)>{{ str($case->value)->title() }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Access</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block md:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Role</span>
            <select name="role_id" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                <option value="">Select role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((string) old('role_id', $staffUser->role_id) === (string) $role->id)>{{ $role->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">{{ $passwordRequired ? 'Password' : 'New password' }}</span>
            <input type="password" name="password" @required($passwordRequired) autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Confirm password</span>
            <input type="password" name="password_confirmation" @required($passwordRequired) autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
        </label>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600 md:col-span-2">
            <p class="font-bold text-gray-800">Password rule</p>
            <p class="mt-1">Use at least 12 characters with letters and numbers. Leave password fields blank when editing to keep the current password.</p>
        </div>
    </div>
</section>

<div class="flex items-center justify-end gap-3">
    <a href="{{ $cancelUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
        <x-lucide-save class="h-4 w-4" aria-hidden="true" />
        <span>{{ $submitLabel }}</span>
    </button>
</div>
