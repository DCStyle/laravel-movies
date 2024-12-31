@extends('layouts.app')

@section('title', e($movie->title) . ' - ' . config('app.name'))
@section('meta_description', Str::limit(strip_tags($movie->description), 160))

@section('seo')
    {!! seo($movie->getDynamicSEOData()) !!}

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Movie",
            "name": @json($movie->title),
            "description": @json($movie->description),
            "duration": "PT{{ (int) $movie->duration }}M",
            "datePublished": "{{ $movie->created_at->toISOString() }}",
            "contentUrl": "{{ route('movies.show', $movie) }}"
        }
    </script>
@endsection

@section('content')
    <!-- Hero Section -->
    <div class="relative rounded-3xl overflow-hidden backdrop-blur-xl bg-black/30 shadow-2xl">
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
                            <p class="text-gray-400">Hiện chưa có dữ liệu</p>
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
                                        onclick="changeSource('{{ $source->id }}')"
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 source-button {{ $source->is_primary ? 'bg-blue-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white' }}"
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

    <!-- Content Grid -->
    <div class="mt-12 grid lg:grid-cols-12 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Movie Info -->
            <div class="relative p-8 rounded-3xl backdrop-blur-xl bg-white/5">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-500/5 rounded-3xl"></div>
                <div class="relative space-y-6">
                    <h1 class="text-5xl font-bold bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">
                        {{ $movie->title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-6 text-gray-300">
                        @if($movie->release_year)
                            <div class="flex items-center gap-2 floating-element" style="animation-delay: 0.5s">
                                <span class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <span>{{ $movie->release_year }}</span>
                            </div>
                        @endif

                        @if($movie->duration)
                            <div class="flex items-center gap-2 floating-element" style="animation-delay: 1s">
                                <span class="w-8 h-8 flex items-center justify-center rounded-full bg-purple-500/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <span>{{ $movie->duration }} phút</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-2 floating-element" style="animation-delay: 1.5s">
                            <span class="w-8 h-8 flex items-center justify-center rounded-full bg-pink-500/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </span>
                            <span>{{ number_format($movie->views_count) }} lượt xem</span>
                        </div>
                    </div>

                    @if($movie->description)
                        <div class="relative">
                            <div class="absolute -inset-4 rounded-2xl bg-gradient-to-r from-blue-500/5 to-purple-500/5 blur-xl"></div>
                            <div class="flex items-center mb-4">
                                <h2 class="relative text-white text-xl font-medium">
                                    <span class="border-l-4 border-blue-500 pl-2">Thông tin phim</span>
                                </h2>
                                <h3 class="text-white text-xl font-medium pl-2">
                                    {{ $movie->title }}
                                </h3>
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

            <!-- Related Movies -->
            @if($relatedMovies?->isNotEmpty())
                <div class="rounded-3xl backdrop-blur-xl bg-white/5 p-6">
                    <h3 class="text-xl font-bold mb-6 bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                        Xem thêm
                    </h3>
                    <div class="space-y-4">
                        @foreach($relatedMovies as $relatedMovie)
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
    <!-- Initialize Facebook SDK if needed -->
    @if($movie->sources->where('source_type', 'facebook')->isNotEmpty())
        <div id="fb-root"></div>
        <script async defer crossorigin="anonymous"
                src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0">
        </script>
    @endif

    <!-- Initialize YouTube API if needed -->
    @if($movie->sources->where('source_type', 'youtube')->isNotEmpty())
        <script src="https://www.youtube.com/iframe_api"></script>
    @endif

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

		// Handle source change errors
		function showErrorMessage(sourceId) {
			const playerContainer = document.getElementById('player-container');
			if (!playerContainer) return;

			playerContainer.innerHTML = `
                <div class="w-full h-full flex items-center justify-center bg-black/90">
                    <div class="text-center p-6">
                        <div class="w-16 h-16 rounded-full bg-gray-800/50 mx-auto flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-400 mb-4">Không thể tải nguồn phim</p>
                        <button
                            onclick="changeSource('${sourceId}')"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200"
                        >
                            Thử lại
                        </button>
                    </div>
                </div>
            `;
		}

		// Source change function
		async function changeSource(sourceId) {
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
				const response = await fetch(`/api/movies/sources/${sourceId}`);
				if (!response.ok) throw new Error('Failed to fetch source');

				const data = await response.json();

				// Update player container
				playerContainer.innerHTML = data.player_html;

				// Initialize new player
				const newPlayerContainer = playerContainer.querySelector('[id^="player-"]');
				if (newPlayerContainer) {
					initializePlayer(newPlayerContainer.id);
				}

				// Update button states
				updateSourceButtons(sourceId);

				// Handle Facebook videos
				if (data.player_html.includes('fb-video') && window.FB) {
					FB.XFBML.parse(playerContainer);
				}

			} catch (error) {
				console.error('Error changing source:', error);
				showErrorMessage(sourceId);
			}
		}

		// Initialize first player on page load
		document.addEventListener('DOMContentLoaded', function() {
			const firstPlayer = document.querySelector('[id^="player-"]');
			if (firstPlayer) {
				initializePlayer(firstPlayer.id);
			}
		});
    </script>
@endpush

@push('styles')
    <style>
        .source-button {
            transition: all 0.3s ease;
        }

        .source-button:focus {
            outline: 2px solid #60A5FA;
            outline-offset: 2px;
        }

        /* Add any additional custom styles here */
    </style>
@endpush