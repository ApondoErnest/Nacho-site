@php
    $journey = __('inspection_process.journey');
    $stages = $journey['stages'];
    $hotspots = $journey['technical']['hotspots'];
    $initialStage = $stages[0]['key'] ?? 'booking';
    $initialHotspot = $hotspots[0]['key'] ?? 'side-slip-plate';
@endphp

<section
    class="inspection-journey-section"
    aria-labelledby="inspection-journey-title"
    x-data="{
        activeStage: '{{ $initialStage }}',
        mobileStage: '{{ $initialStage }}',
        activeHotspot: '{{ $initialHotspot }}',
        hotspots: @js($hotspots),
        get activeHotspotData() {
            return this.hotspots.find((hotspot) => hotspot.key === this.activeHotspot) || this.hotspots[0];
        },
        setStage(stage) {
            this.activeStage = stage;
        },
        openMobileStage(stage, event) {
            this.mobileStage = stage;
            this.activeStage = stage;
            const trigger = event.currentTarget;
            this.$nextTick(() => {
                window.setTimeout(() => {
                    const offset = 148;
                    const top = trigger.getBoundingClientRect().top + window.scrollY - offset;
                    window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
                }, 50);
            });
        },
    }"
>
    <div class="inspection-journey-shell">
        <div class="inspection-journey-heading">
            <p>{{ $journey['eyebrow'] }}</p>
            <h2 id="inspection-journey-title">{{ $journey['title'] }}</h2>
        </div>

        <div class="inspection-stage-tabs" role="tablist" aria-label="{{ $journey['tab_label'] }}">
            @foreach ($stages as $stage)
                <button
                    type="button"
                    id="inspection-stage-tab-{{ $stage['key'] }}"
                    class="inspection-stage-tab"
                    role="tab"
                    aria-controls="inspection-stage-panel-{{ $stage['key'] }}"
                    :aria-selected="(activeStage === '{{ $stage['key'] }}').toString()"
                    :class="{ 'is-active': activeStage === '{{ $stage['key'] }}' }"
                    @click="setStage('{{ $stage['key'] }}')"
                >
                    <span class="inspection-stage-number">{{ $stage['number'] }}</span>
                    <span class="inspection-stage-icon" aria-hidden="true">
                        <x-dynamic-component :component="'lucide-' . $stage['icon']" />
                    </span>
                    <span class="inspection-stage-title">{{ $stage['title'] }}</span>
                </button>
            @endforeach
        </div>

        <div class="inspection-stage-panels">
            @foreach ($stages as $stage)
                <article
                    id="inspection-stage-panel-{{ $stage['key'] }}"
                    class="inspection-stage-panel"
                    role="tabpanel"
                    aria-labelledby="inspection-stage-tab-{{ $stage['key'] }}"
                    x-show="activeStage === '{{ $stage['key'] }}'"
                    x-transition.opacity.duration.200ms
                    @if (! $loop->first) x-cloak @endif
                >
                    <x-public.inspection-journey-stage
                        :stage="$stage"
                        :journey="$journey"
                        :hotspots="$hotspots"
                    />
                </article>
            @endforeach
        </div>

        <div class="inspection-stage-accordion">
            @foreach ($stages as $stage)
                <article class="inspection-stage-accordion-item">
                    <button
                        type="button"
                        class="inspection-stage-accordion-trigger"
                        :class="{ 'is-active': mobileStage === '{{ $stage['key'] }}' }"
                        :aria-expanded="(mobileStage === '{{ $stage['key'] }}').toString()"
                        aria-controls="inspection-stage-accordion-panel-{{ $stage['key'] }}"
                        @click="openMobileStage('{{ $stage['key'] }}', $event)"
                    >
                        <span class="inspection-stage-number">{{ $stage['number'] }}</span>
                        <span class="inspection-stage-accordion-icon" aria-hidden="true">
                            <x-dynamic-component :component="'lucide-' . $stage['icon']" />
                        </span>
                        <span>{{ $stage['title'] }}</span>
                        <x-lucide-chevron-down class="inspection-stage-accordion-chevron" aria-hidden="true" />
                    </button>

                    <div
                        id="inspection-stage-accordion-panel-{{ $stage['key'] }}"
                        class="inspection-stage-accordion-panel"
                        x-show="mobileStage === '{{ $stage['key'] }}'"
                        @if (! $loop->first) x-cloak @endif
                    >
                        <x-public.inspection-journey-stage
                            :stage="$stage"
                            :journey="$journey"
                            :hotspots="$hotspots"
                        />
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
