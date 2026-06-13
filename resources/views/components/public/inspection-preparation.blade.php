@php
    $preparation = __('inspection_process.preparation');
@endphp

<section class="inspection-preparation-section" aria-labelledby="inspection-preparation-title">
    <div class="inspection-preparation-shell">
        <p class="inspection-preparation-kicker">{{ $preparation['eyebrow'] }}</p>

        <div class="inspection-preparation-grid">
            <div class="inspection-preparation-admin">
                <div class="inspection-preparation-heading">
                    <span aria-hidden="true">
                        <x-lucide-clipboard-list />
                    </span>
                    <h2 id="inspection-preparation-title">{{ $preparation['administrative_title'] }}</h2>
                </div>

                <div class="inspection-preparation-admin-body">
                    <figure class="inspection-preparation-image">
                        <img
                            src="{{ asset('images/inspection-process-carte-grise.png') }}"
                            alt=""
                            loading="eager"
                            decoding="async"
                        />
                    </figure>

                    <ul class="inspection-preparation-list">
                        @foreach ($preparation['administrative_items'] as $item)
                            <li>
                                <x-lucide-circle-check aria-hidden="true" />
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="inspection-preparation-vehicle">
                <div class="inspection-preparation-heading">
                    <span aria-hidden="true">
                        <x-lucide-car-front />
                    </span>
                    <h2>{{ $preparation['vehicle_title'] }}</h2>
                </div>

                <ul class="inspection-preparation-list inspection-preparation-list--vehicle">
                    @foreach ($preparation['vehicle_items'] as $item)
                        <li>
                            <x-lucide-circle-check aria-hidden="true" />
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <p class="inspection-preparation-note">
            <x-lucide-info aria-hidden="true" />
            <span>
                <strong>{{ $preparation['note_label'] }}</strong>
                <em>{{ $preparation['note_text'] }}</em>
            </span>
        </p>
    </div>
</section>
