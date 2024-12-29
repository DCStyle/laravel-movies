@extends('layouts.app')

@section('seo')
    {!! seo($category->getDynamicSEOData()) !!}
@endsection

@section('content')
    <x-movie-list-hero-slider :latestMovies="$mostViewedMovies" />

    <section class="mt-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-white text-xl font-medium">
                <span class="border-l-4 border-blue-500 pl-2">{{ $category->name }} mới cập nhật</span>
            </h2>
        </div>

        <!-- Movies Grid -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
            @foreach($latestMovies as $movie)
                <x-movie-card :movie="$movie" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $latestMovies->links() }}
        </div>
    </section>
@endsection