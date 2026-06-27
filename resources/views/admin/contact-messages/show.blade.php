@php
    $statusClasses = [
        'new' => 'bg-orange-50 text-orange-700',
        'read' => 'bg-blue-50 text-blue-700',
        'replied' => 'bg-green-50 text-green-700',
        'archived' => 'bg-gray-100 text-gray-600',
    ];
    $canUpdateMessage = \App\Support\AdminAccess::can(auth()->user(), 'contact-messages.update');
    $quickStatuses = [
        \App\Enums\ContactMessageStatus::READ->value => ['label' => 'Mark read', 'icon' => 'eye'],
        \App\Enums\ContactMessageStatus::REPLIED->value => ['label' => 'Mark replied', 'icon' => 'reply'],
        \App\Enums\ContactMessageStatus::ARCHIVED->value => ['label' => 'Archive', 'icon' => 'archive'],
    ];
@endphp

<x-admin-layout title="Message Details">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to messages</span>
                </a>
                <p class="mt-4 text-sm font-semibold text-gray-500">{{ $message->created_at?->format('M j, Y H:i') }}</p>
                <h2 class="mt-1 break-words text-2xl font-bold tracking-normal text-gray-950">{{ $message->subject ?: 'No subject' }}</h2>
                <p class="mt-1 break-words text-sm text-gray-500">{{ $message->full_name }}{{ $message->email ? ' · '.$message->email : '' }}</p>
            </div>

            <span @class([
                'inline-flex w-fit rounded-full px-3 py-1.5 text-sm font-bold',
                $statusClasses[$message->status->value] ?? 'bg-gray-100 text-gray-600',
            ])>
                {{ str($message->status->value)->replace('_', ' ')->title() }}
            </span>
        </div>

        <section class="grid gap-4 md:grid-cols-3">
            @foreach ([
                ['label' => 'Name', 'value' => $message->full_name, 'icon' => 'user'],
                ['label' => 'Email', 'value' => $message->email, 'icon' => 'mail'],
                ['label' => 'Phone', 'value' => $message->phone ?: 'Not set', 'icon' => 'phone'],
            ] as $summary)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">{{ $summary['label'] }}</p>
                            <p class="mt-2 break-words text-lg font-bold text-gray-950">{{ $summary['value'] }}</p>
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
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Message</h3>
                </div>
                <div class="space-y-5 p-5 text-sm">
                    <div>
                        <p class="font-semibold text-gray-500">Subject</p>
                        <p class="mt-1 break-words font-bold text-gray-950">{{ $message->subject ?: 'No subject' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-500">Body</p>
                        <p class="mt-1 whitespace-pre-line break-words text-gray-800">{{ $message->message }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-500">Admin notes</p>
                        <p class="mt-1 whitespace-pre-line break-words text-gray-800">{{ $message->admin_notes ?: 'Not set' }}</p>
                    </div>
                </div>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">Contact details</h3>
                    </div>
                    <dl class="space-y-3 p-5 text-sm">
                        <div>
                            <dt class="font-semibold text-gray-500">Full name</dt>
                            <dd class="mt-1 text-gray-800">{{ $message->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Email</dt>
                            <dd class="mt-1 break-all text-gray-800">
                                <a href="mailto:{{ $message->email }}" class="font-bold text-nacho-primary hover:text-nacho-primary-dark">{{ $message->email }}</a>
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Phone</dt>
                            <dd class="mt-1 text-gray-800">{{ $message->phone ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Submitted</dt>
                            <dd class="mt-1 text-gray-800">{{ $message->created_at?->format('Y-m-d H:i') }}</dd>
                        </div>
                    </dl>
                </section>

                @if ($canUpdateMessage)
                    <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-200 px-5 py-4">
                            <h3 class="text-base font-bold tracking-normal text-gray-950">Quick actions</h3>
                        </div>
                        <div class="grid gap-2 p-5">
                            @foreach ($quickStatuses as $value => $action)
                                @continue($message->status->value === $value)

                                <form method="POST" action="{{ route('admin.contact-messages.status.update', $message) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $value }}">
                                    <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
                                        <x-dynamic-component :component="'lucide-' . $action['icon']" class="h-4 w-4" aria-hidden="true" />
                                        <span>{{ $action['label'] }}</span>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </section>
                @endif
            </aside>
        </div>

        @if ($canUpdateMessage)
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Update message</h3>
                </div>
                <form method="POST" action="{{ route('admin.contact-messages.update', $message) }}" class="grid gap-4 p-5 md:grid-cols-[16rem_minmax(0,1fr)]">
                    @csrf
                    @method('PUT')
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Status</span>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                            @foreach ($statuses as $case)
                                <option value="{{ $case->value }}" @selected(old('status', $message->status->value) === $case->value)>{{ str($case->value)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Admin notes</span>
                        <textarea name="admin_notes" rows="5" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('admin_notes', $message->admin_notes) }}</textarea>
                        <x-input-error :messages="$errors->get('admin_notes')" class="mt-2" />
                    </label>
                    <div class="flex justify-end md:col-span-2">
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                            <x-lucide-save class="h-4 w-4" aria-hidden="true" />
                            <span>Save message</span>
                        </button>
                    </div>
                </form>
            </section>
        @endif
    </div>
</x-admin-layout>
