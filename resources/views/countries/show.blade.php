@extends('layouts.app')

@section('seo')
    {!! seo($SEOData) !!}
@endsection

@section('content')
    <section>
        <!-- Header -->
        <x-page-hero-header :title="'Phim ' . $countryName" />

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-white text-xl font-medium">
                <span class="border-l-4 border-blue-500 pl-2">Danh sách phim {{ $countryName }} mới cập nhật</span>
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