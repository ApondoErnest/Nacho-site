@php
    $statusClasses = [
        'draft' => 'bg-gray-100 text-gray-700',
        'published' => 'bg-green-50 text-green-700',
        'closing_soon' => 'bg-yellow-50 text-yellow-700',
        'closed' => 'bg-blue-50 text-blue-700',
        'filled' => 'bg-indigo-50 text-indigo-700',
        'archived' => 'bg-red-50 text-red-700',
    ];
@endphp

<x-admin-layout title="Career Vacancy Details">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('admin.career-posts.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to careers</span>
                </a>
                <p class="mt-4 break-all text-sm font-semibold text-gray-500">{{ $post->reference }} · {{ $post->slug }}</p>
                <h2 class="mt-1 break-words text-2xl font-bold tracking-normal text-gray-950">{{ $post->title_en }}</h2>
                <p class="mt-1 break-words text-sm text-gray-500">{{ $post->department?->name_en ?: 'No department' }} · {{ $post->center?->name_en ?: 'All centers' }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <span @class([
                    'inline-flex min-h-10 items-center rounded-full px-3 py-1.5 text-sm font-bold',
                    $statusClasses[$post->status->value] ?? 'bg-gray-100 text-gray-600',
                ])>
                    {{ str($post->status->value)->replace('_', ' ')->title() }}
                </span>
                @adminCan('careers.update')
                    <a href="{{ route('admin.career-posts.edit', $post) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                        <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                        <span>Edit</span>
                    </a>
                @endadminCan
                @adminCan('careers.delete')
                    <form method="POST" action="{{ route('admin.career-posts.destroy', $post) }}" onsubmit="return confirm('Archive this career vacancy?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-red-200 bg-white px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-50">
                            <x-lucide-archive class="h-4 w-4" aria-hidden="true" />
                            <span>Archive</span>
                        </button>
                    </form>
                @endadminCan
            </div>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Status', 'value' => str($post->status->value)->replace('_', ' ')->title(), 'icon' => 'activity'],
                ['label' => 'Published', 'value' => $post->published_at?->format('Y-m-d H:i') ?: 'Not scheduled', 'icon' => 'calendar-days'],
                ['label' => 'Closes', 'value' => $post->closes_at?->format('Y-m-d') ?: 'Open', 'icon' => 'calendar-clock'],
                ['label' => 'Email applications', 'value' => $post->allow_email_application ? 'Enabled' : 'Disabled', 'icon' => 'mail'],
            ] as $summary)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">{{ $summary['label'] }}</p>
                            <p class="mt-2 break-words text-xl font-bold text-gray-950">{{ $summary['value'] }}</p>
                        </div>
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                            <x-dynamic-component :component="'lucide-' . $summary['icon']" class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_26rem]">
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Vacancy content</h3>
                </div>
                <dl class="grid gap-5 p-5 text-sm md:grid-cols-2">
                    @foreach ([
                        'title_fr' => 'Title FR',
                        'summary_en' => 'Summary EN',
                        'summary_fr' => 'Summary FR',
                        'description_en' => 'Role purpose EN',
                        'description_fr' => 'Role purpose FR',
                        'responsibilities_en' => 'Responsibilities EN',
                        'responsibilities_fr' => 'Responsibilities FR',
                        'requirements_en' => 'Essential requirements EN',
                        'requirements_fr' => 'Essential requirements FR',
                        'preferred_requirements_en' => 'Preferred requirements EN',
                        'preferred_requirements_fr' => 'Preferred requirements FR',
                        'skills_en' => 'Skills EN',
                        'skills_fr' => 'Skills FR',
                    ] as $field => $label)
                        <div>
                            <dt class="font-semibold text-gray-500">{{ $label }}</dt>
                            <dd class="mt-1 whitespace-pre-line break-words text-gray-800">{{ $post->{$field} ?: 'Not set' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">Application email</h3>
                    </div>
                    <dl class="space-y-3 p-5 text-sm">
                        <div>
                            <dt class="font-semibold text-gray-500">Recipient</dt>
                            <dd class="mt-1 break-all text-gray-800">{{ $post->application_email ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Subject template</dt>
                            <dd class="mt-1 break-words text-gray-800">{{ $post->application_subject ?: 'Default subject' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Documents EN</dt>
                            <dd class="mt-1 whitespace-pre-line break-words text-gray-800">{{ $post->application_documents_en ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Instructions EN</dt>
                            <dd class="mt-1 whitespace-pre-line break-words text-gray-800">{{ $post->application_instructions_en ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Mailto preview</dt>
                            <dd class="mt-1 break-all text-gray-800">
                                @if ($mailtoPreview)
                                    <a href="{{ $mailtoPreview }}" class="font-bold text-nacho-primary hover:text-nacho-primary-dark">Preview email link</a>
                                @else
                                    Not available
                                @endif
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">SEO</h3>
                    </div>
                    <dl class="space-y-3 p-5 text-sm">
                        <div>
                            <dt class="font-semibold text-gray-500">SEO title EN</dt>
                            <dd class="mt-1 break-words text-gray-800">{{ $post->seo_title_en ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Meta description EN</dt>
                            <dd class="mt-1 break-words text-gray-800">{{ $post->meta_description_en ?: 'Not set' }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </div>
</x-admin-layout>
