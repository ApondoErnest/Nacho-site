@php
    $statusClasses = [
        'pending' => 'bg-orange-50 text-orange-700',
        'confirmed' => 'bg-blue-50 text-blue-700',
        'arrived' => 'bg-indigo-50 text-indigo-700',
        'in_inspection' => 'bg-purple-50 text-purple-700',
        'completed' => 'bg-green-50 text-green-700',
        'cancelled' => 'bg-red-50 text-red-700',
        'no_show' => 'bg-red-50 text-red-700',
        'rescheduled' => 'bg-yellow-50 text-yellow-700',
    ];
    $canManageBooking = \App\Support\AdminAccess::can(auth()->user(), 'bookings.update');
    $canUpdateStatus = \App\Support\AdminAccess::can(auth()->user(), 'bookings.status.update');
@endphp

<x-admin-layout title="Booking Details">
    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-950">
                    <x-lucide-arrow-left class="h-4 w-4" aria-hidden="true" />
                    <span>Back to bookings</span>
                </a>
                <p class="mt-4 text-sm font-semibold text-gray-500">{{ $booking->booking_reference }}</p>
                <h2 class="mt-1 text-2xl font-bold tracking-normal text-gray-950">{{ $booking->full_name }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $booking->phone }}{{ $booking->email ? ' · '.$booking->email : '' }}</p>
            </div>

            <span @class([
                'inline-flex w-fit rounded-full px-3 py-1.5 text-sm font-bold',
                $statusClasses[$booking->status->value] ?? 'bg-gray-100 text-gray-600',
            ])>
                {{ str($booking->status->value)->replace('_', ' ')->title() }}
            </span>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Visit', 'value' => $booking->preferred_date?->format('M j, Y') . ' · ' . $booking->preferred_time, 'icon' => 'calendar-days'],
                ['label' => 'Center', 'value' => $booking->center?->name_en ?: 'Not set', 'icon' => 'building-2'],
                ['label' => 'Service', 'value' => $booking->service?->title_en ?: 'Not set', 'icon' => 'clipboard-check'],
                ['label' => 'Vehicle', 'value' => $booking->vehicle_registration, 'icon' => 'car-front'],
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
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Customer and vehicle</h3>
                </div>
                <dl class="grid gap-4 p-5 text-sm md:grid-cols-2">
                    <div>
                        <dt class="font-semibold text-gray-500">Full name</dt>
                        <dd class="mt-1 font-bold text-gray-950">{{ $booking->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Vehicle registration</dt>
                        <dd class="mt-1 font-bold text-gray-950">{{ $booking->vehicle_registration }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Phone</dt>
                        <dd class="mt-1 text-gray-800">{{ $booking->phone }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Email</dt>
                        <dd class="mt-1 text-gray-800">{{ $booking->email ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Consent</dt>
                        <dd class="mt-1 text-gray-800">{{ $booking->consent ? 'Accepted' : 'Not accepted' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500">Document</dt>
                        <dd class="mt-1 text-gray-800">{{ $booking->document_path ?: 'Not uploaded' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="font-semibold text-gray-500">Customer comment</dt>
                        <dd class="mt-1 whitespace-pre-line text-gray-800">{{ $booking->comment ?: 'Not set' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="font-semibold text-gray-500">Admin notes</dt>
                        <dd class="mt-1 whitespace-pre-line text-gray-800">{{ $booking->admin_notes ?: 'Not set' }}</dd>
                    </div>
                </dl>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4">
                        <h3 class="text-base font-bold tracking-normal text-gray-950">Inspection context</h3>
                    </div>
                    <dl class="space-y-3 p-5 text-sm">
                        <div>
                            <dt class="font-semibold text-gray-500">Center</dt>
                            <dd class="mt-1 text-gray-800">{{ $booking->center?->name_en ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Service</dt>
                            <dd class="mt-1 text-gray-800">{{ $booking->service?->title_en ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Tariff</dt>
                            <dd class="mt-1 text-gray-800">
                                @if ($booking->tariff)
                                    {{ $booking->tariff->category_code }} · {{ $booking->tariff->name_en }} · {{ number_format($booking->tariff->price_fcfa, 0, ',', ' ') }} FCFA
                                @else
                                    Not set
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Submitted</dt>
                            <dd class="mt-1 text-gray-800">{{ $booking->created_at?->format('Y-m-d H:i') }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>

        @if ($canManageBooking)
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Update booking</h3>
                </div>
                <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="grid gap-4 p-5 md:grid-cols-3">
                    @csrf
                    @method('PUT')
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Status</span>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                            @foreach ($statuses as $case)
                                <option value="{{ $case->value }}" @selected(old('status', $booking->status->value) === $case->value)>{{ str($case->value)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Preferred date</span>
                        <input type="date" name="preferred_date" value="{{ old('preferred_date', $booking->preferred_date?->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                        <x-input-error :messages="$errors->get('preferred_date')" class="mt-2" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Preferred time</span>
                        <input type="time" name="preferred_time" min="07:00" max="17:45" step="900" value="{{ old('preferred_time', $booking->preferred_time) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                        <x-input-error :messages="$errors->get('preferred_time')" class="mt-2" />
                    </label>
                    <label class="block md:col-span-3">
                        <span class="text-sm font-semibold text-gray-700">Admin notes</span>
                        <textarea name="admin_notes" rows="5" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                        <x-input-error :messages="$errors->get('admin_notes')" class="mt-2" />
                    </label>
                    <div class="flex justify-end md:col-span-3">
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                            <x-lucide-save class="h-4 w-4" aria-hidden="true" />
                            <span>Save booking</span>
                        </button>
                    </div>
                </form>
            </section>
        @elseif ($canUpdateStatus)
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-base font-bold tracking-normal text-gray-950">Update status</h3>
                </div>
                <form method="POST" action="{{ route('admin.bookings.status.update', $booking) }}" class="grid gap-4 p-5 md:grid-cols-[minmax(0,1fr)_auto]">
                    @csrf
                    @method('PATCH')
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Status</span>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-nacho-primary focus:ring-nacho-primary">
                            @foreach ($statuses as $case)
                                <option value="{{ $case->value }}" @selected(old('status', $booking->status->value) === $case->value)>{{ str($case->value)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </label>
                    <div class="flex items-end justify-end">
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-nacho-primary px-4 py-2 text-sm font-bold text-white hover:bg-nacho-primary-dark">
                            <x-lucide-save class="h-4 w-4" aria-hidden="true" />
                            <span>Save status</span>
                        </button>
                    </div>
                </form>
            </section>
        @endif
    </div>
</x-admin-layout>
