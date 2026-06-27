@php
    $statusClasses = [
        'new' => 'bg-orange-50 text-orange-700',
        'read' => 'bg-blue-50 text-blue-700',
        'replied' => 'bg-green-50 text-green-700',
        'archived' => 'bg-gray-100 text-gray-600',
    ];

    $summaryCards = [
        ['label' => 'Total messages', 'value' => $counts['total'], 'icon' => 'inbox'],
        ['label' => 'New', 'value' => $counts['new'], 'icon' => 'mail'],
        ['label' => 'Read', 'value' => $counts['read'], 'icon' => 'eye'],
        ['label' => 'Replied', 'value' => $counts['replied'], 'icon' => 'reply'],
        ['label' => 'Archived', 'value' => $counts['archived'], 'icon' => 'archive'],
    ];
@endphp

<x-admin-layout title="Messages">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div>
            <p class="text-sm font-semibold text-gray-500">Review public contact submissions and track staff follow-up</p>
            <h2 class="mt-1 text-xl font-bold tracking-normal text-gray-950">Contact message management</h2>
        </div>

        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($summaryCards as $card)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">{{ $card['label'] }}</p>
                            <p class="mt-2 text-2xl font-bold text-gray-950">{{ number_format($card['value']) }}</p>
                        </div>
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                            <x-dynamic-component :component="'lucide-' . $card['icon']" class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="grid gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm lg:grid-cols-[minmax(0,1fr)_12rem_10rem_10rem_auto]">
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Search</span>
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Name, email, phone, subject"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary"
                >
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Status</span>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $case)
                        <option value="{{ $case->value }}" @selected($status === $case->value)>{{ str($case->value)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">From</span>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-gray-700">To</span>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
            </label>

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-800 hover:bg-gray-50">
                    <x-lucide-search class="h-4 w-4" aria-hidden="true" />
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-gray-200 px-3 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="grid gap-3 lg:hidden">
            @forelse ($messages as $message)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase text-nacho-primary">{{ $message->created_at?->format('M j, Y H:i') }}</p>
                            <h3 class="mt-1 break-words text-sm font-bold text-gray-950">{{ $message->subject ?: 'No subject' }}</h3>
                            <p class="mt-1 break-words text-xs font-semibold text-gray-500">{{ $message->full_name }} · {{ $message->email }}</p>
                        </div>
                        <span @class([
                            'shrink-0 rounded-full px-2.5 py-1 text-xs font-bold',
                            $statusClasses[$message->status->value] ?? 'bg-gray-100 text-gray-600',
                        ])>
                            {{ str($message->status->value)->replace('_', ' ')->title() }}
                        </span>
                    </div>

                    <p class="mt-3 break-words text-sm text-gray-700">{{ str($message->message)->limit(150) }}</p>

                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('admin.contact-messages.show', $message) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                            <span>View</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-10 text-center text-sm font-semibold text-gray-500 shadow-sm">
                    No contact messages match the current filters.
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">Contact</th>
                            <th scope="col" class="px-4 py-3">Message</th>
                            <th scope="col" class="px-4 py-3">Submitted</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($messages as $message)
                            <tr class="align-top">
                                <td class="px-4 py-4">
                                    <div class="font-bold text-gray-950">{{ $message->full_name }}</div>
                                    <div class="mt-1 break-all text-gray-500">{{ $message->email }}</div>
                                    <div class="mt-1 text-xs font-semibold text-gray-500">{{ $message->phone ?: 'No phone' }}</div>
                                </td>
                                <td class="max-w-xl px-4 py-4">
                                    <div class="font-bold text-gray-950">{{ $message->subject ?: 'No subject' }}</div>
                                    <div class="mt-1 break-words text-gray-600">{{ str($message->message)->limit(140) }}</div>
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $message->created_at?->format('M j, Y') }}<br>{{ $message->created_at?->format('H:i') }}</td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                        $statusClasses[$message->status->value] ?? 'bg-gray-100 text-gray-600',
                                    ])>
                                        {{ str($message->status->value)->replace('_', ' ')->title() }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end">
                                        <a href="{{ route('admin.contact-messages.show', $message) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-950">
                                            <span class="sr-only">View message from {{ $message->full_name }}</span>
                                            <x-lucide-eye class="h-4 w-4" aria-hidden="true" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm font-semibold text-gray-500">
                                    No contact messages match the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $messages->links() }}
    </div>
</x-admin-layout>
