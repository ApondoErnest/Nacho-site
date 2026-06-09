@php
    $posts = __('home.blog.posts');
@endphp

<section {{ $attributes->class(['latest-articles-showcase']) }}>
    <div class="latest-articles-header">
        <h2 class="latest-articles-title">{{ __('home.blog.title') }}</h2>
        <a href="{{ route('blog.index') }}" class="latest-articles-view-all">
            {{ __('home.blog.view_all') }}
        </a>
    </div>

    <div class="latest-articles-grid">
        @foreach ($posts as $post)
            @php
                $imageUrl = isset($post['image']) && file_exists(public_path($post['image'])) ? asset($post['image']) : null;
            @endphp

            <article class="latest-article-card">
                <a href="{{ route('blog.index') }}" class="latest-article-image" aria-label="{{ $post['title'] }}">
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="" loading="eager" />
                    @endif
                </a>

                <div class="latest-article-body">
                    <h3>
                        <a href="{{ route('blog.index') }}">{{ $post['title'] }}</a>
                    </h3>

                    <p class="latest-article-meta">
                        <time>{{ $post['date'] }}</time>
                        <span aria-hidden="true">&bull;</span>
                        <span>{{ $post['category'] }}</span>
                    </p>

                    <a href="{{ route('blog.index') }}" class="latest-article-read-more">
                        {{ __('home.blog.read_more') }}
                    </a>
                </div>
            </article>
        @endforeach
    </div>
</section>
