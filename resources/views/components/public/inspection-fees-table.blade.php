@php
    $locale = app()->getLocale();
    $rows = collect(config('home.tariff_preview', []))->map(function (array $row, int $index) use ($locale) {
        return [
            'number' => $row['number'] ?? $index + 1,
            'category' => $locale === 'fr' ? $row['category_fr'] : $row['category_en'],
            'details' => $locale === 'fr' ? $row['vehicle_type_fr'] : $row['vehicle_type_en'],
            'test_type' => $locale === 'fr' ? ($row['test_type_fr'] ?? 'All') : ($row['test_type_en'] ?? 'All'),
            'validity' => $locale === 'fr' ? $row['validity_fr'] : $row['validity_en'],
            'price' => $row['price'],
        ];
    })->values();
@endphp

<section {{ $attributes->class(['inspection-fees-showcase']) }}>
    <h2 class="inspection-fees-title">{{ __('home.tariffs.fees_title') }}</h2>

    <div class="inspection-fees-table-wrap">
        <table class="inspection-fees-table">
            <thead>
                <tr>
                    <th scope="col">{{ __('home.tariffs.table.number') }}</th>
                    <th scope="col">{{ __('home.tariffs.table.vehicle_type') }}</th>
                    <th scope="col">{{ __('home.tariffs.table.details') }}</th>
                    <th scope="col">{{ __('home.tariffs.table.test_type') }}</th>
                    <th scope="col">{{ __('home.tariffs.table.validity') }}</th>
                    <th scope="col">{{ __('home.tariffs.table.price') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td class="inspection-fees-number">{{ $row['number'] }}</td>
                        <td class="inspection-fees-category">{{ $row['category'] }}</td>
                        <td>{{ $row['details'] }}</td>
                        <td>{{ $row['test_type'] }}</td>
                        <td>{{ $row['validity'] }}</td>
                        <td>
                            <span class="inspection-fees-price">{{ str_replace(' FCFA', '', $row['price']) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="inspection-fees-mobile">
        @foreach ($rows as $row)
            <article class="inspection-fees-mobile-card">
                <div class="inspection-fees-mobile-top">
                    <span class="inspection-fees-mobile-number">{{ $row['number'] }}</span>
                    <span class="inspection-fees-price">{{ str_replace(' FCFA', '', $row['price']) }}</span>
                </div>
                <h3>{{ $row['category'] }}</h3>
                <p>{{ $row['details'] }}</p>
                <dl>
                    <div>
                        <dt>{{ __('home.tariffs.table.test_type') }}</dt>
                        <dd>{{ $row['test_type'] }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('home.tariffs.table.validity') }}</dt>
                        <dd>{{ $row['validity'] }}</dd>
                    </div>
                </dl>
            </article>
        @endforeach
    </div>

    <p class="inspection-fees-note">{{ __('home.tariffs.vat_note') }}</p>
</section>
