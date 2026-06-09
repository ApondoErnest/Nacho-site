@php
    $testimonials = __('home.testimonials.items');
@endphp

<section {{ $attributes->class(['client-testimonials-showcase']) }}>
    <h2 class="client-testimonials-title">{{ __('home.testimonials.title') }}</h2>

    <div class="client-testimonials-grid">
        @foreach ($testimonials as $testimonial)
            <article class="client-testimonial-card">
                <blockquote class="client-testimonial-quote">
                    &ldquo;{{ $testimonial['quote'] }}&rdquo;
                </blockquote>

                <p class="client-testimonial-author">
                    &mdash; {{ $testimonial['name'] }}, {{ $testimonial['location'] }}
                </p>

                <div class="client-testimonial-rating" aria-label="5 out of 5 stars">
                    @for ($i = 0; $i < 5; $i++)
                        <x-lucide-star class="fill-current" aria-hidden="true" />
                    @endfor
                </div>
            </article>
        @endforeach
    </div>
</section>
