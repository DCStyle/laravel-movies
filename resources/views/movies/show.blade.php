@extends('layouts.app')

@section('title', e($movie->title) . ' - ' . config('app.name'))
@section('meta_description', Str::limit(strip_tags($movie->description), 160))

@section('seo')
    {!! seo($movie->getDynamicSEOData()) !!}

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": {{ $movie->type === 'series' ? '"TVSeries"' : '"Movie"' }},
            "name": @json($movie->title),
            "description": @json($movie->description),
        @if($movie->type === 'single')
            "duration": "PT{{ (int) $movie->duration }}M",
        @else
            "numberOfSeasons": {{ $movie->total_seasons ?? $movie->seasons->count() }},
                "numberOfEpisodes": {{ $movie->total_episodes ?? $movie->seasons->sum(fn($season) => $season->episodes->count()) }},
        @endif
        "datePublished": "{{ $movie->created_at->toISOString() }}",
            "contentUrl": "{{ route('movies.show', $movie) }}"
        }
    </script>
@endsection

@section('content')
    <!-- Hero Section -->
    @if($movie->type === 'single')
        <!-- Single Movie Player -->
        <div class="relative rounded-3xl overflow-hidden backdrop-blur-xl bg-black/30 shadow-2xl mb-12">
            <section class="bg-gradient-to-b from-gray-900 to-gray-800 rounded-2xl overflow-hidden shadow-2xl">
                <!-- Video Player -->
                <div class="relative" style="padding-top: 56.25%">
                    <div id="player-container" class="absolute inset-0">
                        @if($movie->sources->isNotEmpty())
                            <x-video-player
                                    :source="$movie->sources->firstWhere('is_primary', true) ?? $movie->sources->first()"
                                    :key="'player-' . ($movie->sources->firstWhere('is_primary', true)?->id ?? $movie->sources->first()?->id)"
                            />
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-black/50 backdrop-blur-xl">
                                <p class="text-gray-400">No sources available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Source Selection Bar -->
                @if($movie->sources->count() > 1)
                    <div class="bg-gray-800/50 backdrop-blur-sm border-t border-gray-700">
                        <div class="max-w-3xl mx-auto px-6 py-4">
                            <div class="flex flex-wrap gap-2" role="group" aria-label="Video sources">
                                @foreach($movie->sources as $source)
                                    <button
                                            type="button"
                                            onclick="changeSource('{{ $source->id }}', 'movie')"
                                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 source-button
                                                {{ $source->is_primary ? 'bg-blue-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white' }}"
                                            aria-pressed="{{ $source->is_primary ? 'true' : 'false' }}"
                                            data-source-id="{{ $source->id }}"
                                    >
                                            <span class="flex items-center gap-2">
                                                {{ ucfirst($source->source_type) }} - {{ strtoupper($source->quality) }}
                                            </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    @else
        <!-- Series Player (if episode is selected) -->
        @if(isset($currentEpisode))
            <div class="mb-12">
                <x-episode-player :episode="$currentEpisode" :movie="$movie" />
            </div>
        @endif
    @endif

    <!-- Content Grid -->
    <div class="grid lg:grid-cols-12 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Movie/Series Info -->
            <div class="relative p-8 rounded-3xl backdrop-blur-xl bg-white/5">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-500/5 rounded-3xl"></div>
                <div class="relative space-y-6">
                    <div class="flex flex-col space-y-4">
                        <h1 class="text-5xl font-bold bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">
                            {{ $movie->title }}
                        </h1>

                        @if($movie->type === 'series')
                            <div class="relative group">
                                @if($movie->seasons->count() > 0)
                                    <button class="flex items-center space-x-2 px-4 py-2 bg-white/5 hover:bg-white/10 rounded-lg transition-colors duration-200">
                                        <span class="text-gray-300">Mùa {{ $currentSeason?->number ?? 1 }}</span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                        <span class="text-sm text-gray-500 ml-2">({{ $movie->total_seasons ?? $movie->seasons->count() }} mùa)</span>
                                    </button>

                                    <!-- Dropdown -->
                                    <div class="absolute left-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl border border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                        <div class="py-2">
                                            @foreach($movie->seasons as $season)
                                                @php $firstSeasonEpisode = $season->episodes->first(); @endphp
                                                <a href="{{ $firstSeasonEpisode
                                                        ? route('movies.episode', ['movie' => $movie->slug, 'season' => $season->number, 'episode' => $firstSeasonEpisode->number])
                                                        : '#' }}"
                                                   class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-white/5 {{ ($currentSeason && $currentSeason->id === $season->id) ? 'bg-blue-500/20 text-white' : '' }}"
                                                >
                                                    Mùa {{ $season->number }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="inline-block px-4 py-2 bg-primary rounded-lg">
                                        <span class="text-gray-300">COMMING SOON</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Movie/Series Metadata -->
                    <div class="flex flex-wrap items-center gap-6 text-gray-300">
                        @if($movie->release_year)
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <span>{{ $movie->release_year }}</span>
                            </div>
                        @endif

                        @if($movie->type === 'single' && $movie->duration)
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 flex items-center justify-center rounded-full bg-purple-500/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <span>{{ $movie->duration }} phút</span>
                            </div>
                            @endif

                            <div class="flex items-center gap-2">
                            <span class="w-8 h-8 flex items-center justify-center rounded-full bg-pink-500/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </span>
                                <span>{{ number_format($movie->views_count) }} lượt xem</span>
                            </div>
                    </div>

                    <!-- Episodes List (for series) -->
                    @if($movie->type === 'series')
                        <div class="relative">
                            <div class="absolute -inset-4 rounded-2xl bg-gradient-to-r from-blue-500/5 to-purple-500/5 blur-xl"></div>
                            <div class="flex items-center mb-4">
                                <h2 class="relative text-white text-xl font-medium">
                                    <span class="border-l-4 border-blue-500 pl-2">Chọn tập phim</span>
                                </h2>
                            </div>
                            <x-episode-list
                                    :movie="$movie"
                                    :currentSeason="$currentSeason ?? null"
                                    :currentEpisode="$currentEpisode ?? null"
                            />
                        </div>
                    @endif

                    <!-- Movie/Series Description -->
                    @if($movie->description)
                        <div class="relative">
                            <div class="absolute -inset-4 rounded-2xl bg-gradient-to-r from-blue-500/5 to-purple-500/5 blur-xl"></div>
                            <div class="flex items-center mb-4">
                                <h2 class="relative text-white text-xl font-medium">
                                    <span class="border-l-4 border-blue-500 pl-2">Thông tin phim</span>
                                </h2>
                            </div>
                            <div class="relative text-lg leading-relaxed text-gray-300">
                                {!! nl2br(e($movie->description)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Advertisement Section -->
            @if(config('features.ads.enabled'))
                <div class="relative rounded-3xl overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 animate-pulse"></div>
                    <div class="relative p-4">
                        <div id="movie-detail-ad" class="min-h-[90px]">
                            <!-- Ad content will be dynamically inserted -->
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Sidebar Advertisement -->
            @if(config('features.ads.enabled'))
                <div class="rounded-3xl backdrop-blur-xl bg-white/5 p-4">
                    <div id="sidebar-ad" class="min-h-[400px]">
                        <!-- Ad content will be dynamically inserted -->
                    </div>
                </div>
            @endif

            @if($movie->type === 'series' && isset($currentEpisode))
                @if($nextEpisode = $movie->getNextEpisode($currentEpisode))
                    <div class="rounded-3xl backdrop-blur-xl bg-white/5 p-6">
                        <h3 class="text-xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                            Tiếp theo
                        </h3>
                        <a href="{{ route('movies.episode', ['movie' => $movie->slug, 'season' => $nextEpisode->season->number, 'episode' => $nextEpisode->number]) }}"
                           class="group block p-4 rounded-lg hover:bg-white/5 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-medium text-white group-hover:text-blue-400 transition-colors duration-300">
                                        Tập {{ $nextEpisode->number }}
                                    </h4>
                                    <p class="text-gray-400 mt-1">{{ $nextEpisode->title }}</p>
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                        @if($nextEpisode->duration)
                                            <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $nextEpisode->duration }} phút
                                </span>
                                        @endif
                                        <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                Mùa {{ $nextEpisode->season->number }}
                            </span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-500/10 group-hover:bg-blue-500/20 transition-colors duration-300">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @endif

            <!-- Related Content -->
            @if($relatedContent?->isNotEmpty())
                <div class="rounded-3xl backdrop-blur-xl bg-white/5 p-6">
                    <h3 class="text-xl font-bold mb-6 bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                        {{ $movie->type === 'series' ? 'Phim bộ liên quan' : 'Phim liên quan' }}
                    </h3>
                    <div class="space-y-4">
                        @foreach($relatedContent as $relatedMovie)
                            <x-related-movie-card
                                    :movie="$relatedMovie"
                                    class="hover:scale-105 transition-transform duration-300"
                            />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
		// Track the active source button
		function updateSourceButtons(activeSourceId) {
			document.querySelectorAll('.source-button').forEach(button => {
				const isActive = button.dataset.sourceId === activeSourceId;
				button.classList.toggle('bg-blue-500', isActive);
				button.classList.toggle('text-white', isActive);
				button.classList.toggle('bg-gray-700', !isActive);
				button.classList.toggle('text-gray-300', !isActive);
				button.setAttribute('aria-pressed', isActive.toString());
			});
		}

		// Source change function
		async function changeSource(sourceId, type = 'movie') {
			const playerContainer = document.getElementById('player-container');
			if (!playerContainer) return;

			try {
				// Show loading state
				playerContainer.innerHTML = `
                    <div class="w-full h-full flex items-center justify-center bg-black/90">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
                    </div>
                `;

				// Fetch new player HTML
				const response = await fetch(`/api/${type}s/sources/${sourceId}`);
				if (!response.ok) throw new Error('Failed to fetch source');

				const data = await response.json();
				playerContainer.innerHTML = data.player_html;

				// Initialize new player
				const newPlayerContainer = playerContainer.querySelector('[id^="player-"]');
				if (newPlayerContainer) {
					initializePlayer(newPlayerContainer.id);
				}

				// Update button states
				updateSourceButtons(sourceId);

			} catch (error) {
				console.error('Error changing source:', error);
				showErrorMessage(sourceId, type);
			}
		}

		// Error handling
		function showErrorMessage(sourceId, type) {
			const playerContainer = document.getElementById('player-container');
			if (!playerContainer) return;

			playerContainer.innerHTML = `
                <div class="w-full h-full flex items-center justify-center bg-black/90">
                    <div class="text-center p-6">
                        <div class="w-16 h-16 rounded-full bg-gray-800/50 mx-auto flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-400 mb-4">Failed to load source</p>
                        <button onclick="changeSource('${sourceId}', '${type}')"
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200">
                            Retry
                        </button>
                    </div>
                </div>
            `;
		}
    </script>
@endpush