<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Identity</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Reference</span>
            <input name="reference" value="{{ old('reference', $post->reference) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('reference')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Slug</span>
            <input name="slug" value="{{ old('slug', $post->slug) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Title EN</span>
            <input name="title_en" value="{{ old('title_en', $post->title_en) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('title_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Title FR</span>
            <input name="title_fr" value="{{ old('title_fr', $post->title_fr) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('title_fr')" class="mt-2" />
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Assignment and Publishing</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-3">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Department</span>
            <select name="department_id" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected((string) old('department_id', $post->department_id) === (string) $department->id)>{{ $department->name_en }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Center</span>
            <select name="center_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                <option value="">All centers</option>
                @foreach ($centers as $center)
                    <option value="{{ $center->id }}" @selected((string) old('center_id', $post->center_id) === (string) $center->id)>{{ $center->name_en }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('center_id')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Employment type</span>
            <select name="employment_type" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                <option value="">Not set</option>
                @foreach ($employmentTypes as $value => $label)
                    <option value="{{ $value }}" @selected(old('employment_type', $post->employment_type) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('employment_type')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Status</span>
            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                @foreach ($statuses as $case)
                    <option value="{{ $case->value }}" @selected(old('status', $post->status?->value ?? $post->status) === $case->value)>{{ str($case->value)->replace('_', ' ')->title() }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Published at</span>
            <input type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Closes at</span>
            <input type="date" name="closes_at" value="{{ old('closes_at', $post->closes_at?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('closes_at')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Vacancies count</span>
            <input type="number" min="1" name="vacancies_count" value="{{ old('vacancies_count', $post->vacancies_count) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('vacancies_count')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Display order</span>
            <input type="number" min="0" name="display_order" value="{{ old('display_order', $post->display_order ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('display_order')" class="mt-2" />
        </label>
        <div class="flex items-end">
            <label class="flex min-h-11 w-full items-center gap-3 rounded-md border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700">
                <input type="checkbox" name="allow_email_application" value="1" @checked(old('allow_email_application', $post->allow_email_application)) class="rounded border-gray-300 text-nacho-primary focus:ring-nacho-primary">
                <span>Allow email application</span>
            </label>
        </div>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Summary and Role Purpose</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        @foreach ([
            'summary_en' => ['Summary EN', 4],
            'summary_fr' => ['Summary FR', 4],
            'description_en' => ['Role purpose EN', 7],
            'description_fr' => ['Role purpose FR', 7],
        ] as $field => [$label, $rows])
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">{{ $label }}</span>
                <textarea name="{{ $field }}" rows="{{ $rows }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old($field, $post->{$field}) }}</textarea>
                <x-input-error :messages="$errors->get($field)" class="mt-2" />
            </label>
        @endforeach
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Requirements</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        @foreach ([
            'responsibilities_en' => 'Responsibilities EN',
            'responsibilities_fr' => 'Responsibilities FR',
            'requirements_en' => 'Essential requirements EN',
            'requirements_fr' => 'Essential requirements FR',
            'preferred_requirements_en' => 'Preferred requirements EN',
            'preferred_requirements_fr' => 'Preferred requirements FR',
            'skills_en' => 'Skills EN',
            'skills_fr' => 'Skills FR',
        ] as $field => $label)
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">{{ $label }}</span>
                <textarea name="{{ $field }}" rows="5" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old($field, $post->{$field}) }}</textarea>
                <x-input-error :messages="$errors->get($field)" class="mt-2" />
            </label>
        @endforeach
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">Email Application</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Application email</span>
            <input type="email" name="application_email" value="{{ old('application_email', $post->application_email) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('application_email')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Application subject</span>
            <input name="application_subject" value="{{ old('application_subject', $post->application_subject) }}" placeholder="Application - {title} - {reference}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            <x-input-error :messages="$errors->get('application_subject')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Application documents EN</span>
            <textarea name="application_documents_en" rows="4" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('application_documents_en', $post->application_documents_en) }}</textarea>
            <x-input-error :messages="$errors->get('application_documents_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Application documents FR</span>
            <textarea name="application_documents_fr" rows="4" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('application_documents_fr', $post->application_documents_fr) }}</textarea>
            <x-input-error :messages="$errors->get('application_documents_fr')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Application instructions EN</span>
            <textarea name="application_instructions_en" rows="5" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('application_instructions_en', $post->application_instructions_en) }}</textarea>
            <x-input-error :messages="$errors->get('application_instructions_en')" class="mt-2" />
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Application instructions FR</span>
            <textarea name="application_instructions_fr" rows="5" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('application_instructions_fr', $post->application_instructions_fr) }}</textarea>
            <x-input-error :messages="$errors->get('application_instructions_fr')" class="mt-2" />
        </label>
    </div>
</section>

<section class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-5 py-4">
        <h3 class="text-base font-bold tracking-normal text-gray-950">SEO</h3>
    </div>
    <div class="grid gap-4 p-5 md:grid-cols-2">
        @foreach ([
            'seo_title_en' => ['SEO title EN', 'input'],
            'seo_title_fr' => ['SEO title FR', 'input'],
            'meta_description_en' => ['Meta description EN', 'textarea'],
            'meta_description_fr' => ['Meta description FR', 'textarea'],
        ] as $field => [$label, $type])
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">{{ $label }}</span>
                @if ($type === 'input')
                    <input name="{{ $field }}" value="{{ old($field, $post->{$field}) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                @else
                    <textarea name="{{ $field }}" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old($field, $post->{$field}) }}</textarea>
                @endif
                <x-input-error :messages="$errors->get($field)" class="mt-2" />
            </label>
        @endforeach
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
