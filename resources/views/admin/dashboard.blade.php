@php
    $toneClasses = [
        'slate' => 'border-gray-200 bg-white text-gray-700',
        'green' => 'border-green-200 bg-green-50 text-green-700',
        'amber' => 'border-amber-200 bg-amber-50 text-amber-700',
        'orange' => 'border-orange-200 bg-orange-50 text-nacho-primary',
    ];
@endphp

<x-admin-layout
    title="Dashboard"
    :pending-bookings="$metrics['pending_bookings']"
    :unread-messages="$metrics['unread_contact_messages']"
>
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-6" aria-label="Admin dashboard summary">
            @foreach ($cards as $card)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-500">{{ $card['label'] }}</p>
                            <p class="mt-3 text-3xl font-bold leading-none tracking-normal text-gray-950">{{ number_format($card['value']) }}</p>
                        </div>
                        <span @class([
                            'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md border',
                            $toneClasses[$card['tone']] ?? $toneClasses['slate'],
                        ])>
                            <x-dynamic-component :component="'lucide-' . $card['icon']" class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm" aria-labelledby="admin-priority-title">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 id="admin-priority-title" class="text-base font-bold tracking-normal text-gray-950">Priority queue</h2>
                </div>

                <div class="divide-y divide-gray-100">
                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-orange-50 text-nacho-primary">
                                <x-lucide-calendar-clock class="h-5 w-5" aria-hidden="true" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-950">Pending booking requests</p>
                                <p class="text-sm text-gray-500">Requests waiting for staff confirmation.</p>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-gray-950">{{ number_format($metrics['pending_bookings']) }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 px-5 py-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                                <x-lucide-mail-warning class="h-5 w-5" aria-hidden="true" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-950">Unread contact messages</p>
                                <p class="text-sm text-gray-500">New public enquiries not yet reviewed.</p>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-gray-950">{{ number_format($metrics['unread_contact_messages']) }}</span>
                    </div>
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm" aria-labelledby="admin-network-title">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 id="admin-network-title" class="text-base font-bold tracking-normal text-gray-950">Network status</h2>
                </div>

                <dl class="space-y-4 p-5">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-sm font-semibold text-gray-500">Operational</dt>
                        <dd class="text-sm font-bold text-green-700">{{ number_format($metrics['operational_centers']) }} centers</dd>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                        @php
                            $centerTotal = max((int) $metrics['total_centers'], 1);
                            $operationalPercent = ((int) $metrics['operational_centers'] / $centerTotal) * 100;
                        @endphp
                        <div class="h-full rounded-full bg-green-600" style="width: {{ $operationalPercent }}%"></div>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-sm font-semibold text-gray-500">Expansion</dt>
                        <dd class="text-sm font-bold text-amber-700">{{ number_format($metrics['expansion_centers']) }} centers</dd>
                    </div>
                </dl>
            </section>
        </div>
    </div>
</x-admin-layout>
