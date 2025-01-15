@extends('layouts.app')

@section('seo')
    {!! seo($SEOData) !!}
@endsection

@section('content')
    <x-movie-list-hero-slider :latestMovies="$latestContent" />

    @if(setting('site_h1_tag'))
        <x-page-hero-header :title="setting('site_h1_tag')" />
    @endif

    <x-movie-list-horizontal title="Phim mới nổi bật" :movies="$highlightedContent" />

    <x-movie-list-horizontal title="Phim chiếu rạp mới cập nhật" :movies="$theatersMovies" />

    <x-movie-list-horizontal title="Phim bộ mới cập nhật" :movies="$tvSeries" />

    <x-movie-list-horizontal title="Phim lẻ mới cập nhật" :movies="$movies" />

    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-white text-xl font-medium">
                <span class="border-l-4 border-blue-500 pl-2">Tin tức</span>
            </h2>
        </div>

        <div class="space-y-6">
            @foreach($latestArticles as $article)
                <article class="flex gap-4 items-start hover:bg-gray-900 p-4 rounded-lg transition">
                    <div class="w-16 text-center">
                        <span class="text-3xl font-bold text-blue-600">{{ $article->created_at->format('d') }}</span>
                        <div class="text-sm uppercase text-gray-500">{{ $article->created_at->format('M') }}</div>
                    </div>

                    <div class="flex-1">
                        <h2 class="text-xl font-semibold mb-2">
                            <a href="{{ route('articles.show', $article) }}">
                                {{ $article->title }}
                            </a>
                        </h2>
                        <p class="text-gray-600">
                            {{ Str::limit(strip_tags(html_entity_decode($article->content)), 200) }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection