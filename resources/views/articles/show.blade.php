@extends('layouts.app')

@section('seo')
    {!! seo($article->getDynamicSEOData()) !!}
@endsection

@section('content')
    <article class="max-w-4xl mx-auto sm:px-4 sm:py-8">
        <!-- Hero Section -->
        <div class="relative aspect-video mb-8 rounded-2xl overflow-hidden">
            @if($article->image)
                <img src="{{ Storage::url($article->image) }}"
                     alt="{{ $article->title }}"
                     class="w-full h-full object-cover">
            @endif
        </div>

        <!-- Article Header -->
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-bold mb-4">{{ $article->title }}</h1>
            <div class="flex items-center justify-center gap-4 text-gray-600">
                <time datetime="{{ $article->created_at }}">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    {{ $article->created_at->format('d/m/Y') }}
                </time>
                <span>
                    <i class="fas fa-eye mr-2"></i>
                    {{ number_format($article->views) }} lượt xem
                </span>
            </div>
        </header>

        <!-- Article Content -->
        <div class="prose prose-lg max-w-none">
            {!! $article->content !!}
        </div>

        <!-- Related Articles -->
        @if($relatedArticles->count() > 0)
            <div class="border-t border-gray-200 mt-12 pt-8">
                <h3 class="text-2xl font-bold mb-6">Bài viết liên quan</h3>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($relatedArticles as $related)
                        <a href="{{ route('articles.show', $related) }}"
                           class="group block bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <div class="aspect-video bg-gray-100">
                                @if($related->image)
                                    <img src="{{ asset('storage/' . $related->image) }}"
                                         alt="{{ $related->title }}"
                                         class="w-full h-full object-cover group-hover:opacity-90 transition-opacity">
                                @endif
                            </div>
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-900 group-hover:text-blue-600 line-clamp-2">
                                    {{ $related->title }}
                                </h4>
                                <time class="text-sm text-gray-600 mt-2 block">
                                    {{ $related->created_at->format('d/m/Y') }}
                                </time>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>
@endsection