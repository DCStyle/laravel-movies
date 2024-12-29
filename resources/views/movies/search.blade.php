@extends('layouts.app')

@section('title', 'Search: ' . $query . ' - ' . config('app.name'))
@section('meta_description', 'Search results for ' . $query)

@section('content')
    <div class="space-y-8">
        <!-- Search Header -->
        <section class="space-y-4">
            <h1 class="text-2xl font-bold">
                Search Results for "{{ $query }}"
            </h1>
            <p class="text-gray-400">
                Found {{ $movies->total() }} results
            </p>
        </section>

        <!-- Filters and Results -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <form action="{{ route('movies.search') }}" method="GET" class="space-y-6">
                    <!-- Preserve search query -->
                    <input type="hidden" name="q" value="{{ $query }}">

                    <!-- Year Filter -->
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Year</h3>
                        <select name="year" class="w-full bg-gray-800 border-gray-700 rounded-lg text-white">
                            <option value="">All Years</option>
                            @foreach(range(date('Y'), 2000) as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quality Filter -->
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Quality</h3>
                        <div class="space-y-2">
                            @foreach(['4k', '1080p', '720p', '480p', '360p'] as $quality)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox"
                                           name="quality[]"
                                           value="{{ $quality }}"
                                           {{ in_array($quality, request('quality', [])) ? 'checked' : '' }}
                                           class="rounded bg-gray-700 border-gray-600 text-blue-500 focus:ring-blue-500">
                                    <span>{{ strtoupper($quality) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Source Type Filter -->
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Source</h3>
                        <div class="space-y-2">
                            @foreach(['direct', 'youtube', 'fshare', 'gdrive'] as $source)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox"
                                           name="source[]"
                                           value="{{ $source }}"
                                           {{ in_array($source, request('source', [])) ? 'checked' : '' }}
                                           class="rounded bg-gray-700 border-gray-600 text-blue-500 focus:ring-blue-500">
                                    <span>{{ ucfirst($source) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Apply Filters Button -->
                    <button type="submit"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition">
                        Apply Filters
                    </button>
                </form>
            </div>

            <!-- Results -->
            <div class="lg:col-span-3">
                <!-- Sort Options -->
                <div class="flex justify-end mb-6">
                    <select name="sort"
                            onchange="window.location.href = updateQueryString(window.location.href, 'sort', this.value)"
                            class="bg-gray-800 border-gray-700 rounded-lg text-white">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>Most Viewed</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z</option>
                    </select>
                </div>

                <!-- Movies Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($movies as $movie)
                        <x-movie-card :movie="$movie" />
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-400">No movies found matching your search criteria.</p>
                            <a href="{{ route('movies.search', ['q' => $query]) }}"
                               class="text-blue-500 hover:underline mt-2 inline-block">
                                Clear all filters
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $movies->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
		function updateQueryString(uri, key, value) {
			var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
			var separator = uri.indexOf('?') !== -1 ? "&" : "?";

			if (uri.match(re)) {
				return uri.replace(re, '$1' + key + "=" + value + '$2');
			} else {
				return uri + separator + key + "=" + value;
			}
		}
    </script>
@endpush