@php
    $formatMoney = fn (mixed $value): string => number_format((int) $value, 0, ',', ' ') . ' FCFA';
    $formatValue = function (mixed $value): string {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES) ?: '[]';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i');
        }

        return blank($value) ? 'Not set' : (string) $value;
    };
@endphp

<x-admin-layout title="Tariff Details">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('admin.tariffs.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to tariffs</span>
                </a>
                <div class="mt-4 flex items-center gap-3">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                        <x-dynamic-component :component="'lucide-' . $tariff->lucideIcon()" class="h-6 w-6" aria-hidden="true" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-gray-500">{{ $tariff->category_slug }}</p>
                        <h2 class="mt-1 text-2xl font-bold tracking-normal text-gray-950">{{ $tariff->category_code }} · {{ $tariff->name_en }}</h2>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                @adminCan('tariffs.update')
                    <a href="{{ route('admin.tariffs.edit', $tariff) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                        <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                        <span>Edit</span>
                    </a>
                @endadminCan
                @adminCan('tariffs.delete')
                    <form method="POST" action="{{ route('admin.tariffs.destroy', $tariff) }}" onsubmit="return confirm('Deactivate this tariff row?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-red-200 bg-white px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-50">
                            <x-lucide-ban class="h-4 w-4" aria-hidden="true" />
                            <span>Deactivate</span>
                        </button>
                    </form>
                @endadminCan
            </div>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Current public price', 'value' => $formatMoney($effectiveSnapshot['price_fcfa'] ?? $tariff->price_fcfa), 'icon' => 'banknote'],
                ['label' => 'Validity', 'value' => ($effectiveSnapshot['validity_value'] ?? $tariff->validity_value) . ' ' . ($effectiveSnapshot['validity_unit'] ?? $tariff->validity_unit), 'icon' => 'calendar-days'],
                ['label' => 'Status', 'value' => $tariff->is_active ? 'Active' : 'Inactive', 'icon' => 'activity'],
                ['label' => 'Bookings', 'value' => $tariff->bookings_count, 'icon' => 'clipboard-check'],
            ] as $summary)
                <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">{{ $summary['label'] }}</p>
                            <p class="mt-2 text-xl font-bold text-gray-950">{{ $summary['value'] }}</p>
                        </div>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-gray-100 text-gray-700">
                            <x-dynamic-component :component="'lucide-' . $summary['icon']" class="h-5 w-5" aria-hidden="true" />
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_26rem]">
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Tariff content</h3>
                </div>
                <dl class="grid gap-4 p-5 text-sm md:grid-cols-2">
                    <div>
                        <dt class="font-semibold text-gray-500">French name</dt>
                        <dd class="mt-1 font-bold text-gray-950">{{ $tariff->name_fr }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Bookable</dt>
                        <dd class="mt-1 text-gray-800">{{ $tariff->is_bookable ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Weight range</dt>
                        <dd class="mt-1 text-gray-800">
                            {{ $tariff->minimum_weight_kg ?: 'No min' }} - {{ $tariff->maximum_weight_kg ?: 'No max' }} kg
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Display order</dt>
                        <dd class="mt-1 text-gray-800">{{ $tariff->display_order }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="font-semibold text-gray-500">Description EN</dt>
                        <dd class="mt-1 text-gray-800">{{ $tariff->description_en ?: 'Not set' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="font-semibold text-gray-500">Description FR</dt>
                        <dd class="mt-1 text-gray-800">{{ $tariff->description_fr ?: 'Not set' }}</dd>
                    </div>
                </dl>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">Regulatory metadata</h3>
                    </div>
                    <dl class="space-y-3 p-5 text-sm">
                        <div>
                            <dt class="font-semibold text-gray-500">Effective date</dt>
                            <dd class="mt-1 text-gray-800">{{ $tariff->effective_date?->format('Y-m-d') ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Expiry date</dt>
                            <dd class="mt-1 text-gray-800">{{ $tariff->expiry_date?->format('Y-m-d') ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Reference</dt>
                            <dd class="mt-1 text-gray-800">{{ $tariff->regulatory_reference ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Last verified</dt>
                            <dd class="mt-1 text-gray-800">{{ $tariff->last_verified_at?->format('Y-m-d H:i') ?: 'Not set' }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>

        @adminCan('tariffs.update')
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Schedule tariff revision</h3>
                </div>
                <form method="POST" action="{{ route('admin.tariffs.revisions.store', $tariff) }}" class="grid gap-4 p-5 md:grid-cols-3">
                    @csrf
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">New price FCFA</span>
                        <input type="number" min="0" name="price_fcfa" value="{{ old('price_fcfa', $effectiveSnapshot['price_fcfa'] ?? $tariff->price_fcfa) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                        <x-input-error :messages="$errors->get('price_fcfa')" class="mt-2" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Validity value</span>
                        <input type="number" min="1" name="validity_value" value="{{ old('validity_value', $effectiveSnapshot['validity_value'] ?? $tariff->validity_value) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                        <x-input-error :messages="$errors->get('validity_value')" class="mt-2" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Validity unit</span>
                        <select name="validity_unit" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                            @foreach ($validityUnits as $unit => $label)
                                <option value="{{ $unit }}" @selected(old('validity_unit', $effectiveSnapshot['validity_unit'] ?? $tariff->validity_unit) === $unit)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('validity_unit')" class="mt-2" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Effective date</span>
                        <input type="date" name="effective_date" value="{{ old('effective_date', today()->addDay()->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                        <x-input-error :messages="$errors->get('effective_date')" class="mt-2" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Expiry date</span>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date', $effectiveSnapshot['expiry_date'] ?? $tariff->expiry_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                        <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Last verified at</span>
                        <input type="datetime-local" name="last_verified_at" value="{{ old('last_verified_at') }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                        <x-input-error :messages="$errors->get('last_verified_at')" class="mt-2" />
                    </label>
                    <label class="block md:col-span-3">
                        <span class="text-sm font-semibold text-gray-700">Regulatory reference</span>
                        <input name="regulatory_reference" value="{{ old('regulatory_reference', $effectiveSnapshot['regulatory_reference'] ?? $tariff->regulatory_reference) }}" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                        <x-input-error :messages="$errors->get('regulatory_reference')" class="mt-2" />
                    </label>
                    <div class="grid gap-3 sm:grid-cols-2 md:col-span-2">
                        <label class="flex min-h-11 items-center gap-3 rounded-md border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $effectiveSnapshot['is_active'] ?? $tariff->is_active)) class="rounded border-gray-300 text-nacho-primary focus:ring-nacho-primary">
                            <span>Active when effective</span>
                        </label>
                        <label class="flex min-h-11 items-center gap-3 rounded-md border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700">
                            <input type="checkbox" name="is_bookable" value="1" @checked(old('is_bookable', $effectiveSnapshot['is_bookable'] ?? $tariff->is_bookable)) class="rounded border-gray-300 text-nacho-primary focus:ring-nacho-primary">
                            <span>Bookable when effective</span>
                        </label>
                    </div>
                    <div class="flex items-end justify-end">
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                            <x-lucide-calendar-plus class="h-4 w-4" aria-hidden="true" />
                            <span>Schedule revision</span>
                        </button>
                    </div>
                </form>
            </section>
        @endadminCan

        <div class="grid gap-5 xl:grid-cols-2">
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Revision history</h3>
                </div>
                <div class="space-y-3 p-5">
                    @forelse ($tariff->revisions as $revision)
                        <article class="rounded-md border border-gray-200 px-3 py-3 text-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-gray-950">{{ str($revision->status->value)->title() }} · {{ $revision->effective_date?->format('Y-m-d') }}</p>
                                    <p class="mt-1 text-xs font-semibold text-gray-500">Created by {{ $revision->creator?->name ?: 'System' }}</p>
                                </div>
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-700">
                                    {{ $formatMoney($revision->snapshot['price_fcfa'] ?? 0) }}
                                </span>
                            </div>
                            <dl class="mt-3 grid gap-2 text-xs sm:grid-cols-2">
                                <div>
                                    <dt class="font-semibold text-gray-500">Validity</dt>
                                    <dd class="text-gray-800">{{ $revision->snapshot['validity_value'] ?? 'Not set' }} {{ $revision->snapshot['validity_unit'] ?? '' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-gray-500">Reference</dt>
                                    <dd class="break-words text-gray-800">{{ $revision->snapshot['regulatory_reference'] ?? 'Not set' }}</dd>
                                </div>
                            </dl>
                        </article>
                    @empty
                        <p class="text-sm font-semibold text-gray-500">No revisions scheduled yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Audit log</h3>
                </div>
                <div class="space-y-3 p-5">
                    @forelse ($tariff->auditLogs as $log)
                        <article class="rounded-md border border-gray-200 px-3 py-3 text-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-gray-950">{{ $log->user?->name ?: 'System' }}</p>
                                    <p class="mt-1 text-xs font-semibold text-gray-500">{{ $log->created_at?->format('Y-m-d H:i') }}</p>
                                </div>
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-700">{{ count($log->changes) }} change(s)</span>
                            </div>
                            <div class="mt-3 space-y-2">
                                @foreach ($log->changes as $field => $change)
                                    <div class="rounded-md bg-gray-50 px-3 py-2 text-xs">
                                        <p class="font-bold text-gray-800">{{ str($field)->replace('_', ' ')->title() }}</p>
                                        <p class="mt-1 break-words text-gray-600">{{ $formatValue($change['old'] ?? null) }} -> {{ $formatValue($change['new'] ?? null) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @empty
                        <p class="text-sm font-semibold text-gray-500">No audit records yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-admin-layout>
