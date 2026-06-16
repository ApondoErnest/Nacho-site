@extends('layouts.public')

@section('title', __('book_inspection.meta.title'))

@section('content')
    @php
        $locale = app()->getLocale();
        $browserLocale = $locale === 'fr' ? 'fr-CM' : 'en-CM';
        $serviceOptions = [
            ['value' => 'periodic', 'label' => __('book_inspection.services.periodic')],
            ['value' => 'light', 'label' => __('book_inspection.services.light')],
            ['value' => 'heavy', 'label' => __('book_inspection.services.heavy')],
            ['value' => 'counter', 'label' => __('book_inspection.services.counter')],
            ['value' => 'pre_purchase', 'label' => __('book_inspection.services.pre_purchase')],
        ];
        $assistanceCards = [
            [
                'icon' => 'circle-help',
                'title' => __('book_inspection.support.tariffs.title'),
                'button' => __('book_inspection.support.tariffs.button'),
                'href' => route('tariffs'),
            ],
            [
                'icon' => 'phone',
                'title' => __('book_inspection.support.contacts.title'),
                'button' => __('book_inspection.support.contacts.button'),
                'href' => route('contact') . '#contact-centers',
            ],
        ];
        $bookingBenefits = [
            ['icon' => 'clipboard-check', 'title' => __('book_inspection.benefits.items.professional')],
            ['icon' => 'shield-check', 'title' => __('book_inspection.benefits.items.assessment')],
            ['icon' => 'tag', 'title' => __('book_inspection.benefits.items.pricing')],
            ['icon' => 'badge-check', 'title' => __('book_inspection.benefits.items.standards')],
        ];
        $hourOptions = collect(range(7, 17))
            ->map(fn (int $hour) => str_pad((string) $hour, 2, '0', STR_PAD_LEFT))
            ->all();
        $minuteOptions = ['00', '15', '30', '45'];
        $centerOptions = collect(config('centers.centers', []))
            ->where('status', 'operational')
            ->values()
            ->map(fn (array $center) => [
                'value' => $center['slug'],
                'label' => $center['name'] . ' - ' . $center['city'],
            ])
            ->all();
        $categoryOptions = collect(config('home.tariff_preview', []))
            ->values()
            ->map(function (array $row, int $index) use ($locale) {
                $category = $row["category_{$locale}"] ?? $row['category_en'];
                $vehicleType = $row["vehicle_type_{$locale}"] ?? $row['vehicle_type_en'];
                $validity = $row["validity_{$locale}"] ?? $row['validity_en'];
                $priceAmount = (int) preg_replace('/\D+/', '', $row['price']);
                $price = ($locale === 'fr' ? number_format($priceAmount, 0, ',', ' ') : number_format($priceAmount)) . ' FCFA';

                return [
                    'value' => 'tariff-' . ($row['number'] ?? $index + 1),
                    'filterId' => $row['filter_id'] ?? null,
                    'label' => $category . ' - ' . $vehicleType,
                    'category' => $category,
                    'vehicleType' => $vehicleType,
                    'price' => $price,
                    'validity' => $validity,
                ];
            })
            ->all();
        $requestedCategory = request()->query('category');
        $initialCategory = collect($categoryOptions)->firstWhere('filterId', $requestedCategory)['value']
            ?? collect($categoryOptions)->firstWhere('value', $requestedCategory)['value']
            ?? '';
        $featuredCategory = collect($categoryOptions)->firstWhere('filterId', 'private') ?? ($categoryOptions[0] ?? null);
    @endphp

    <section
        class="book-inspection-page"
        aria-labelledby="book-inspection-title"
        x-data="{
            service: '',
            center: '',
            vehicleCategory: @js($initialCategory),
            registration: '',
            preferredDate: '',
            preferredHour: '',
            preferredMinute: '',
            datePickerOpen: false,
            calendarCursor: new Date(),
            notSelected: @js(__('book_inspection.summary.not_selected')),
            samplePlate: @js(__('book_inspection.form.registration.sample_plate')),
            browserLocale: @js($browserLocale),
            datePlaceholder: @js(__('book_inspection.form.date.placeholder')),
            weekDays: @js(__('book_inspection.form.date.weekdays')),
            services: @js($serviceOptions),
            centers: @js($centerOptions),
            categories: @js($categoryOptions),
            featuredCategory: @js($featuredCategory),
            get preferredTime() {
                return this.preferredHour && this.preferredMinute ? `${this.preferredHour}:${this.preferredMinute}` : '';
            },
            findByValue(items, value) {
                return items.find((item) => item.value === value) || null;
            },
            toDateValue(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            },
            get currentMonthLabel() {
                return new Intl.DateTimeFormat(this.browserLocale, {
                    month: 'long',
                    year: 'numeric',
                }).format(this.calendarCursor);
            },
            get calendarCells() {
                const year = this.calendarCursor.getFullYear();
                const month = this.calendarCursor.getMonth();
                const firstDay = new Date(year, month, 1);
                const startOffset = (firstDay.getDay() + 6) % 7;

                return Array.from({ length: 42 }, (_, index) => {
                    const date = new Date(year, month, index - startOffset + 1);

                    return {
                        label: date.getDate(),
                        value: this.toDateValue(date),
                        currentMonth: date.getMonth() === month,
                    };
                });
            },
            moveCalendar(offset) {
                this.calendarCursor = new Date(
                    this.calendarCursor.getFullYear(),
                    this.calendarCursor.getMonth() + offset,
                    1,
                );
            },
            selectDate(value) {
                const selected = new Date(`${value}T00:00:00`);
                this.preferredDate = value;
                this.calendarCursor = new Date(selected.getFullYear(), selected.getMonth(), 1);
                this.datePickerOpen = false;
            },
            isSelectedDate(value) {
                return this.preferredDate === value;
            },
            isToday(value) {
                return value === this.toDateValue(new Date());
            },
            get selectedService() {
                return this.findByValue(this.services, this.service);
            },
            get selectedCenter() {
                return this.findByValue(this.centers, this.center);
            },
            get selectedCategory() {
                return this.findByValue(this.categories, this.vehicleCategory);
            },
            get tariffPreview() {
                return this.selectedCategory || this.featuredCategory;
            },
            get plateText() {
                const value = this.registration.trim().replace(/\s+/g, ' ').toUpperCase();
                return value || this.samplePlate;
            },
            formatDate(value) {
                if (! value) {
                    return this.notSelected;
                }

                return new Intl.DateTimeFormat(this.browserLocale, {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                }).format(new Date(`${value}T00:00:00`));
            },
            formatTime(value) {
                if (! value) {
                    return this.notSelected;
                }

                const [hour, minute] = value.split(':');

                return this.browserLocale.startsWith('fr') ? `${hour} h ${minute}` : `${hour}:${minute}`;
            },
        }"
    >
        <div class="book-inspection-inner">
            <div class="book-inspection-heading">
                <p>{{ __('book_inspection.hero.eyebrow') }}</p>
                <h1 id="book-inspection-title">{{ __('book_inspection.hero.title') }}</h1>
                <span>{{ __('book_inspection.hero.subtitle') }}</span>
            </div>

            <div class="book-inspection-notice" role="note">
                <x-lucide-info class="book-inspection-notice-icon" aria-hidden="true" />
                <p>{{ __('book_inspection.hero.notice') }}</p>
            </div>

            <div class="book-inspection-layout">
                <form action="#" method="POST" class="book-inspection-form" novalidate>
                    @csrf

                    <fieldset class="book-inspection-fieldset">
                        <legend>
                            <x-lucide-car-front aria-hidden="true" />
                            <span>{{ __('book_inspection.form.inspection_details') }}</span>
                        </legend>

                        <div class="book-inspection-form-grid">
                            <div class="book-inspection-field">
                                <label for="inspection-service">
                                    {{ __('book_inspection.form.service.label') }}
                                    <span aria-hidden="true">*</span>
                                    <span class="sr-only">({{ __('book_inspection.form.required') }})</span>
                                </label>
                                <select id="inspection-service" name="service" x-model="service" required>
                                    <option value="">{{ __('book_inspection.form.service.placeholder') }}</option>
                                    @foreach ($serviceOptions as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="book-inspection-field">
                                <label for="inspection-center">
                                    {{ __('book_inspection.form.center.label') }}
                                    <span aria-hidden="true">*</span>
                                    <span class="sr-only">({{ __('book_inspection.form.required') }})</span>
                                </label>
                                <select id="inspection-center" name="center" x-model="center" required>
                                    <option value="">{{ __('book_inspection.form.center.placeholder') }}</option>
                                    @foreach ($centerOptions as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="book-inspection-field">
                                <label for="inspection-registration">
                                    {{ __('book_inspection.form.registration.label') }}
                                    <span aria-hidden="true">*</span>
                                    <span class="sr-only">({{ __('book_inspection.form.required') }})</span>
                                </label>
                                <div class="book-inspection-registration-row">
                                    <input
                                        id="inspection-registration"
                                        name="vehicle_registration"
                                        type="text"
                                        placeholder="{{ __('book_inspection.form.registration.placeholder') }}"
                                        x-model="registration"
                                        required
                                    />
                                    <div class="book-inspection-plate" aria-hidden="true">
                                        <span></span>
                                        <strong x-text="plateText"></strong>
                                        <small>CM</small>
                                    </div>
                                </div>
                                <p>{{ __('book_inspection.form.registration.hint') }}</p>
                            </div>

                            <div class="book-inspection-field">
                                <label for="inspection-category">
                                    {{ __('book_inspection.form.category.label') }}
                                    <span aria-hidden="true">*</span>
                                    <span class="sr-only">({{ __('book_inspection.form.required') }})</span>
                                </label>
                                <select id="inspection-category" name="vehicle_category" x-model="vehicleCategory" required>
                                    <option value="">{{ __('book_inspection.form.category.placeholder') }}</option>
                                    @foreach ($categoryOptions as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="book-inspection-tariff-strip" x-show="tariffPreview" x-cloak>
                            <x-lucide-tag aria-hidden="true" />
                            <span x-text="tariffPreview.category"></span>
                            <span aria-hidden="true">•</span>
                            <span x-text="tariffPreview.vehicleType"></span>
                            <span aria-hidden="true">•</span>
                            <span x-text="tariffPreview.price"></span>
                            <span aria-hidden="true">•</span>
                            <span>
                                {{ __('book_inspection.form.category.validity') }}:
                                <span x-text="tariffPreview.validity"></span>
                            </span>
                        </div>

                        <div class="book-inspection-field book-inspection-field--full">
                            <label for="inspection-reference">
                                {{ __('book_inspection.form.previous_reference.label') }}
                                <span class="book-inspection-optional">({{ __('book_inspection.form.optional') }})</span>
                            </label>
                            <input
                                id="inspection-reference"
                                name="previous_reference"
                                type="text"
                                placeholder="{{ __('book_inspection.form.previous_reference.placeholder') }}"
                            />
                        </div>

                        <label class="book-inspection-checkbox" for="inspection-no-reference">
                            <input id="inspection-no-reference" name="previous_reference_unavailable" type="checkbox" value="1" />
                            <span>{{ __('book_inspection.form.previous_reference.none') }}</span>
                        </label>
                    </fieldset>

                    <fieldset class="book-inspection-fieldset book-inspection-fieldset--visit">
                        <legend>
                            <x-lucide-calendar-days aria-hidden="true" />
                            <span>{{ __('book_inspection.form.visit_details') }}</span>
                        </legend>

                        <div class="book-inspection-form-grid">
                            <div class="book-inspection-field book-inspection-date-field">
                                <label for="inspection-date-trigger">
                                    {{ __('book_inspection.form.date.label') }}
                                    <span aria-hidden="true">*</span>
                                    <span class="sr-only">({{ __('book_inspection.form.required') }})</span>
                                </label>

                                <input id="inspection-date" name="preferred_date" type="hidden" x-model="preferredDate" />

                                <div class="book-inspection-date-shell" @click.outside="datePickerOpen = false">
                                    <button
                                        id="inspection-date-trigger"
                                        type="button"
                                        class="book-inspection-date-trigger"
                                        @click="datePickerOpen = ! datePickerOpen"
                                        :aria-expanded="datePickerOpen.toString()"
                                        aria-controls="inspection-date-picker"
                                    >
                                        <span
                                            class="book-inspection-date-value"
                                            :class="{ 'is-empty': ! preferredDate }"
                                            x-text="preferredDate ? formatDate(preferredDate) : datePlaceholder"
                                        ></span>
                                        <x-lucide-calendar-days aria-hidden="true" />
                                    </button>

                                    <div
                                        id="inspection-date-picker"
                                        class="book-inspection-date-picker"
                                        :class="{ 'is-open': datePickerOpen }"
                                        :aria-hidden="(! datePickerOpen).toString()"
                                    >
                                        <div class="book-inspection-date-picker-header">
                                            <button type="button" @click="moveCalendar(-1)" aria-label="{{ __('book_inspection.form.date.previous_month') }}">
                                                <x-lucide-chevron-left aria-hidden="true" />
                                            </button>
                                            <strong x-text="currentMonthLabel"></strong>
                                            <button type="button" @click="moveCalendar(1)" aria-label="{{ __('book_inspection.form.date.next_month') }}">
                                                <x-lucide-chevron-right aria-hidden="true" />
                                            </button>
                                        </div>

                                        <div class="book-inspection-date-weekdays" aria-hidden="true">
                                            <template x-for="day in weekDays" :key="day">
                                                <span x-text="day"></span>
                                            </template>
                                        </div>

                                        <div class="book-inspection-date-days">
                                            <template x-for="day in calendarCells" :key="day.value">
                                                <button
                                                    type="button"
                                                    class="book-inspection-date-day"
                                                    :class="{
                                                        'is-muted': ! day.currentMonth,
                                                        'is-selected': isSelectedDate(day.value),
                                                        'is-today': isToday(day.value),
                                                    }"
                                                    :aria-pressed="isSelectedDate(day.value).toString()"
                                                    @click="selectDate(day.value)"
                                                >
                                                    <span x-text="day.label"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="book-inspection-field">
                                <label id="inspection-time-label">
                                    {{ __('book_inspection.form.time.label') }}
                                    <span aria-hidden="true">*</span>
                                    <span class="sr-only">({{ __('book_inspection.form.required') }})</span>
                                </label>
                                <div class="book-inspection-time-selects" role="group" aria-labelledby="inspection-time-label">
                                    <select id="inspection-hour" name="preferred_hour" x-model="preferredHour" required aria-label="{{ __('book_inspection.form.time.hour_placeholder') }}">
                                        <option value="">{{ __('book_inspection.form.time.hour_placeholder') }}</option>
                                        @foreach ($hourOptions as $hour)
                                            <option value="{{ $hour }}">{{ $hour }}</option>
                                        @endforeach
                                    </select>
                                    <select id="inspection-minute" name="preferred_minute" x-model="preferredMinute" required aria-label="{{ __('book_inspection.form.time.minute_placeholder') }}">
                                        <option value="">{{ __('book_inspection.form.time.minute_placeholder') }}</option>
                                        @foreach ($minuteOptions as $minute)
                                            <option value="{{ $minute }}">{{ $minute }}</option>
                                        @endforeach
                                    </select>
                                    <input id="inspection-time" name="preferred_time" type="hidden" :value="preferredTime" />
                                </div>
                            </div>

                            <div class="book-inspection-field">
                                <label for="inspection-full-name">
                                    {{ __('book_inspection.form.full_name.label') }}
                                    <span aria-hidden="true">*</span>
                                    <span class="sr-only">({{ __('book_inspection.form.required') }})</span>
                                </label>
                                <input
                                    id="inspection-full-name"
                                    name="full_name"
                                    type="text"
                                    placeholder="{{ __('book_inspection.form.full_name.placeholder') }}"
                                    required
                                />
                            </div>

                            <div class="book-inspection-field">
                                <label for="inspection-phone">
                                    {{ __('book_inspection.form.phone.label') }}
                                    <span aria-hidden="true">*</span>
                                    <span class="sr-only">({{ __('book_inspection.form.required') }})</span>
                                </label>
                                <div class="book-inspection-phone-row">
                                    <select name="phone_country" aria-label="Country code">
                                        <option value="+237">+237</option>
                                    </select>
                                    <input
                                        id="inspection-phone"
                                        name="phone"
                                        type="tel"
                                        placeholder="{{ __('book_inspection.form.phone.placeholder') }}"
                                        required
                                    />
                                </div>
                            </div>

                            <div class="book-inspection-field">
                                <label for="inspection-email">
                                    {{ __('book_inspection.form.email.label') }}
                                    <span class="book-inspection-optional">({{ __('book_inspection.form.optional') }})</span>
                                </label>
                                <input
                                    id="inspection-email"
                                    name="email"
                                    type="email"
                                    placeholder="{{ __('book_inspection.form.email.placeholder') }}"
                                />
                            </div>

                            <div class="book-inspection-field">
                                <label for="inspection-comment">
                                    {{ __('book_inspection.form.additional_information.label') }}
                                    <span class="book-inspection-optional">({{ __('book_inspection.form.optional') }})</span>
                                </label>
                                <textarea
                                    id="inspection-comment"
                                    name="additional_information"
                                    placeholder="{{ __('book_inspection.form.additional_information.placeholder') }}"
                                ></textarea>
                            </div>
                        </div>

                        <label class="book-inspection-checkbox book-inspection-consent" for="inspection-consent">
                            <input id="inspection-consent" name="consent" type="checkbox" value="1" required />
                            <span>{{ __('book_inspection.form.consent') }}</span>
                        </label>

                        <button type="submit" class="book-inspection-submit">
                            <x-lucide-send aria-hidden="true" />
                            <span>{{ __('book_inspection.form.submit') }}</span>
                        </button>

                        <p class="book-inspection-payment-note">
                            <x-lucide-lock-keyhole aria-hidden="true" />
                            <span>{{ __('book_inspection.form.payment_note') }}</span>
                        </p>
                    </fieldset>
                </form>

                <aside class="book-inspection-summary" aria-live="polite">
                    <h2>{{ __('book_inspection.summary.title') }}</h2>

                    <div class="book-inspection-summary-list">
                        <div class="book-inspection-summary-item">
                            <x-lucide-clipboard-check aria-hidden="true" />
                            <div>
                                <h3>{{ __('book_inspection.summary.service') }}</h3>
                                <p x-text="selectedService ? selectedService.label : notSelected"></p>
                            </div>
                        </div>

                        <div class="book-inspection-summary-item">
                            <x-lucide-building-2 aria-hidden="true" />
                            <div>
                                <h3>{{ __('book_inspection.summary.center') }}</h3>
                                <p x-text="selectedCenter ? selectedCenter.label : notSelected"></p>
                            </div>
                        </div>

                        <div class="book-inspection-summary-item">
                            <x-lucide-car-front aria-hidden="true" />
                            <div>
                                <h3>{{ __('book_inspection.summary.vehicle_category') }}</h3>
                                <p x-text="selectedCategory ? selectedCategory.label : notSelected"></p>
                            </div>
                        </div>

                        <div class="book-inspection-summary-item">
                            <x-lucide-lock-keyhole aria-hidden="true" />
                            <div>
                                <h3>{{ __('book_inspection.summary.published_tariff') }}</h3>
                                <p x-text="selectedCategory ? `${selectedCategory.price} · ${selectedCategory.validity}` : notSelected"></p>
                            </div>
                        </div>

                        <div class="book-inspection-summary-item">
                            <x-lucide-calendar-days aria-hidden="true" />
                            <div>
                                <h3>{{ __('book_inspection.summary.preferred_date') }}</h3>
                                <p x-text="formatDate(preferredDate)"></p>
                            </div>
                        </div>

                        <div class="book-inspection-summary-item">
                            <x-lucide-clock aria-hidden="true" />
                            <div>
                                <h3>{{ __('book_inspection.summary.preferred_time') }}</h3>
                                <p x-text="formatTime(preferredTime)"></p>
                            </div>
                        </div>
                    </div>

                    <div class="book-inspection-secure">
                        <x-lucide-shield-check aria-hidden="true" />
                        <div>
                            <h3>{{ __('book_inspection.summary.secure_title') }}</h3>
                            <p>{{ __('book_inspection.summary.secure_text') }}</p>
                        </div>
                    </div>
                </aside>
            </div>

            <section class="book-inspection-support-panel" aria-labelledby="book-inspection-support-title">
                <div class="book-inspection-support-block">
                    <h2 id="book-inspection-support-title">{{ __('book_inspection.support.title') }}</h2>

                    <div class="book-inspection-assistance-grid">
                        @foreach ($assistanceCards as $card)
                            <article class="book-inspection-assistance-card">
                                <span class="book-inspection-assistance-icon" aria-hidden="true">
                                    <x-dynamic-component :component="'lucide-' . $card['icon']" />
                                </span>
                                <div>
                                    <h3>{{ $card['title'] }}</h3>
                                    <a href="{{ $card['href'] }}">{{ $card['button'] }}</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="book-inspection-benefits-block" aria-labelledby="book-inspection-benefits-title">
                    <h2 id="book-inspection-benefits-title">{{ __('book_inspection.benefits.title') }}</h2>

                    <div class="book-inspection-benefits-grid">
                        @foreach ($bookingBenefits as $benefit)
                            <div class="book-inspection-benefit-item">
                                <span class="book-inspection-benefit-icon" aria-hidden="true">
                                    <x-dynamic-component :component="'lucide-' . $benefit['icon']" />
                                </span>
                                <h3>{{ $benefit['title'] }}</h3>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </section>
@endsection
