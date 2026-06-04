@props([
    'rows' => [],
])

<div {{ $attributes->class(['hidden overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-nacho-dark/10 lg:block']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-nacho-dark/10 text-left text-sm">
            <thead class="bg-nacho-cream">
                <tr>
                    <th scope="col" class="px-4 py-3 font-semibold text-nacho-dark">{{ __('components.tariff.category') }}</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-nacho-dark">{{ __('components.tariff.vehicle_type') }}</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-nacho-dark">{{ __('components.tariff.price') }}</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-nacho-dark">{{ __('components.tariff.validity') }}</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-nacho-dark">{{ __('components.tariff.documents') }}</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-nacho-dark"><span class="sr-only">{{ __('components.tariff.book') }}</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-nacho-dark/5 bg-white">
                @forelse ($rows as $row)
                    <tr class="hover:bg-nacho-cream/50">
                        <td class="px-4 py-3 font-medium text-nacho-dark">{{ $row['category'] ?? '' }}</td>
                        <td class="px-4 py-3 text-nacho-dark/80">{{ $row['vehicle_type'] ?? '' }}</td>
                        <td class="px-4 py-3 font-semibold text-nacho-primary">{{ $row['price'] ?? '' }}</td>
                        <td class="px-4 py-3 text-nacho-dark/75">{{ $row['validity'] ?? '—' }}</td>
                        <td class="max-w-xs px-4 py-3 text-nacho-dark/75">{{ $row['documents'] ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ $row['book_url'] ?? route('book-inspection') }}" class="btn-nacho-primary text-xs">
                                {{ __('components.tariff.book') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    {{ $slot }}
                @endforelse
            </tbody>
        </table>
    </div>
</div>
