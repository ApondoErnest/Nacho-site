@props([
    'stage',
    'journey',
    'hotspots',
])

@php
    $stageKey = $stage['key'];
    $booking = $journey['booking'];
    $checkin = $journey['checkin'];
    $technical = $journey['technical'];
    $report = $journey['report'];
    $certificate = $journey['certificate'];
@endphp

<div class="inspection-stage-content inspection-stage-content--{{ $stageKey }}">
    <div class="inspection-stage-copy">
        <span class="inspection-stage-copy-number">{{ $stage['number'] }}</span>
        <div>
            <h3>{{ $stage['title'] }}</h3>
            <p>{{ $stage['description'] }}</p>
        </div>
    </div>

    @switch($stageKey)
        @case('booking')
            <div class="inspection-booking-layout">
                <form class="inspection-booking-card" aria-label="{{ $booking['form_label'] }}">
                    <h4>{{ $booking['form_title'] }}</h4>

                    @foreach ($booking['fields'] as $field)
                        <label>
                            <span>{{ $field['label'] }}</span>
                            <span class="inspection-booking-input">
                                <span>{{ $field['value'] }}</span>
                                @if ($field['type'] === 'date')
                                    <x-lucide-calendar-days aria-hidden="true" />
                                @else
                                    <x-lucide-chevron-down aria-hidden="true" />
                                @endif
                            </span>
                        </label>
                    @endforeach

                    <button type="button">{{ $booking['button'] }}</button>
                </form>

                <div class="inspection-booking-receives">
                    <h4>{{ $booking['receives_title'] }}</h4>
                    <ul>
                        @foreach ($booking['receives'] as $item)
                            <li>
                                <x-lucide-check aria-hidden="true" />
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="inspection-booking-visual" aria-hidden="true">
                    <div class="inspection-booking-calendar">
                        <span></span>
                        <span></span>
                        <span></span>
                        <strong>24</strong>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div class="inspection-booking-pin">
                        <x-lucide-map-pin />
                    </div>
                    <div class="inspection-booking-car">
                        <x-lucide-car-front />
                    </div>
                </div>
            </div>
            @break

        @case('checkin')
            <div class="inspection-checkin-layout">
                <figure class="inspection-checkin-visual">
                    <img
                        src="{{ asset('images/inspection-process-checkin.png') }}"
                        alt=""
                        loading="lazy"
                    />
                </figure>

                <div class="inspection-checkin-list">
                    <h4>{{ $checkin['checklist_title'] }}</h4>
                    <ul>
                        @foreach ($checkin['checklist'] as $item)
                            <li>
                                <x-lucide-check aria-hidden="true" />
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @break

        @case('technical')
            <div class="inspection-technical-layout">
                <div class="inspection-lane-scroll">
                    <svg
                        class="inspection-lane-diagram"
                        viewBox="0 0 920 520"
                        role="img"
                        aria-labelledby="inspection-lane-title inspection-lane-desc"
                    >
                        <title id="inspection-lane-title">{{ $technical['diagram_title'] }}</title>
                        <desc id="inspection-lane-desc">{{ $technical['diagram_description'] }}</desc>

                        <rect x="18" y="18" width="884" height="484" rx="28" fill="#f8fafc" stroke="#dfe5ea" stroke-width="3" />
                        <rect x="128" y="52" width="120" height="416" rx="12" fill="#1f2933" opacity=".09" />
                        <rect x="672" y="52" width="120" height="416" rx="12" fill="#1f2933" opacity=".09" />
                        <rect x="265" y="50" width="390" height="420" rx="30" fill="#ffffff" stroke="#dfe5ea" stroke-width="2" />
                        <path d="M300 75h320M300 445h320" stroke="#f15a24" stroke-width="5" stroke-linecap="round" opacity=".75" />
                        <path d="M338 92v338M582 92v338" stroke="#aeb7c0" stroke-width="4" stroke-dasharray="18 16" stroke-linecap="round" />

                        <g aria-hidden="true">
                            <rect x="386" y="126" width="148" height="276" rx="56" fill="#ffffff" stroke="#344250" stroke-width="6" />
                            <path d="M404 183c14-42 38-63 56-63s42 21 56 63" fill="#dce4eb" />
                            <rect x="398" y="203" width="124" height="118" rx="24" fill="#182736" />
                            <rect x="415" y="334" width="90" height="42" rx="13" fill="#dce4eb" />
                            <circle cx="402" cy="190" r="13" fill="#f15a24" opacity=".9" />
                            <circle cx="518" cy="190" r="13" fill="#f15a24" opacity=".9" />
                            <circle cx="402" cy="357" r="13" fill="#2f3f4d" />
                            <circle cx="518" cy="357" r="13" fill="#2f3f4d" />
                        </g>

                        <g aria-hidden="true" class="inspection-lane-equipment">
                            <rect x="66" y="84" width="70" height="104" rx="10" />
                            <rect x="784" y="84" width="70" height="104" rx="10" />
                            <rect x="66" y="332" width="70" height="104" rx="10" />
                            <rect x="784" y="332" width="70" height="104" rx="10" />
                            <rect x="145" y="210" width="98" height="36" rx="12" />
                            <rect x="677" y="210" width="98" height="36" rx="12" />
                        </g>

                        @foreach ($hotspots as $hotspot)
                            <g
                                class="inspection-lane-hotspot"
                                :class="{ 'is-active': activeHotspot === '{{ $hotspot['key'] }}' }"
                                role="button"
                                tabindex="0"
                                aria-label="{{ $hotspot['title'] }}. {{ $hotspot['text'] }}"
                                @mouseenter="activeHotspot = '{{ $hotspot['key'] }}'"
                                @focus="activeHotspot = '{{ $hotspot['key'] }}'"
                                @click="activeHotspot = '{{ $hotspot['key'] }}'"
                                @keydown.enter.prevent="activeHotspot = '{{ $hotspot['key'] }}'"
                                @keydown.space.prevent="activeHotspot = '{{ $hotspot['key'] }}'"
                            >
                                <rect
                                    x="{{ $hotspot['label_x'] }}"
                                    y="{{ $hotspot['label_y'] }}"
                                    width="{{ $hotspot['label_width'] }}"
                                    height="52"
                                    rx="17"
                                    class="inspection-lane-hotspot-label"
                                />
                                <circle
                                    cx="{{ $hotspot['x'] }}"
                                    cy="{{ $hotspot['y'] }}"
                                    r="24"
                                    class="inspection-lane-hotspot-ring"
                                />
                                <circle
                                    cx="{{ $hotspot['x'] }}"
                                    cy="{{ $hotspot['y'] }}"
                                    r="17"
                                    class="inspection-lane-hotspot-dot"
                                />
                                <text
                                    x="{{ $hotspot['x'] }}"
                                    y="{{ $hotspot['y'] + 7 }}"
                                    class="inspection-lane-hotspot-number"
                                    text-anchor="middle"
                                >{{ $hotspot['number'] }}</text>
                                <text
                                    x="{{ $hotspot['label_text_x'] }}"
                                    y="{{ $hotspot['label_y'] + 32 }}"
                                    class="inspection-lane-hotspot-text"
                                    text-anchor="{{ $hotspot['text_anchor'] }}"
                                >{{ $hotspot['short_title'] }}</text>
                            </g>
                        @endforeach
                    </svg>
                </div>

                <aside class="inspection-hotspot-card" aria-live="polite">
                    <p>{{ $technical['details_label'] }}</p>
                    <h4 x-text="activeHotspotData.title"></h4>
                    <span x-text="activeHotspotData.text"></span>
                </aside>
            </div>
            @break

        @case('report')
            <div class="inspection-report-layout">
                <div class="inspection-report-preview" aria-label="{{ $report['preview_label'] }}">
                    <div class="inspection-report-header">
                        <span class="inspection-report-brand">{{ $report['issuer'] }}</span>
                        <span class="inspection-report-badge">{{ $report['badge'] }}</span>
                    </div>

                    <dl class="inspection-report-details">
                        @foreach ($report['details'] as $detail)
                            <div>
                                <dt>{{ $detail['label'] }}</dt>
                                <dd>{{ $detail['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="inspection-report-systems">
                        <h4>{{ $report['systems_title'] }}</h4>
                        <ul>
                            @foreach ($report['systems'] as $system)
                                <li>
                                    <span>{{ $system }}</span>
                                    <x-lucide-circle-check aria-hidden="true" />
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="inspection-report-observation">
                        <h4>{{ $report['observations_title'] }}</h4>
                        <p>{{ $report['observations'] }}</p>
                    </div>

                    <div class="inspection-report-action">
                        <span>{{ $report['next_action_label'] }}</span>
                        <strong>{{ $report['next_action'] }}</strong>
                    </div>
                </div>

                <div class="inspection-outcome-grid" aria-label="{{ $report['outcomes_label'] }}">
                    @foreach ($report['outcomes'] as $outcome)
                        <article class="inspection-outcome-card inspection-outcome-card--{{ $outcome['key'] }}">
                            <span aria-hidden="true">
                                <x-dynamic-component :component="'lucide-' . $outcome['icon']" />
                            </span>
                            <h4>{{ $outcome['title'] }}</h4>
                            <p>{{ $outcome['text'] }}</p>
                        </article>
                    @endforeach
                </div>

                <p class="inspection-report-note">{{ $report['configurable_note'] }}</p>
            </div>
            @break

        @case('certificate')
            <div class="inspection-certificate-layout">
                <div class="inspection-certificate-preview" aria-hidden="true">
                    <div class="inspection-certificate-paper">
                        <span class="inspection-certificate-mark">{{ $certificate['issuer'] }}</span>
                        <strong>{{ $certificate['document_title'] }}</strong>
                        <em>NW023AN</em>
                        <span></span>
                        <span></span>
                        <span></span>
                        <x-lucide-circle-check />
                    </div>
                </div>

                <div class="inspection-certificate-summary">
                    <h4>{{ $certificate['summary_title'] }}</h4>
                    <dl>
                        @foreach ($certificate['summary'] as $item)
                            <div>
                                <dt>{{ $item['label'] }}</dt>
                                <dd>{{ $item['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="inspection-certificate-actions">
                        <a href="{{ route('book-inspection') }}" class="inspection-certificate-primary">
                            {{ $certificate['primary_action'] }}
                        </a>
                        <a href="{{ route('tariffs') }}" class="inspection-certificate-link">
                            {{ $certificate['tariffs_link'] }}
                        </a>
                        <a href="{{ route('contact') }}" class="inspection-certificate-link">
                            {{ $certificate['contact_link'] }}
                        </a>
                    </div>
                </div>
            </div>
            @break
    @endswitch
</div>
