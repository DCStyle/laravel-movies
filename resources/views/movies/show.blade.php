@extends('layouts.app')

@section('title', $movie->title . ' - ' . config('app.name'))
@section('meta_description', Str::limit($movie->description, 160))

@section('seo')
    {!! seo($movie->getDynamicSEOData()) !!}
@endsection

@section('head')
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Movie",
            "name": "{{ $movie->title }}",
            "description": "{{ $movie->description }}",
            "duration": "PT{{ $movie->duration }}M",
            "datePublished": "{{ $movie->created_at->toISOString() }}"
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
                    @if($movie->sources->count() > 0)
                        <x-video-player :source="$movie->sources->where('is_primary', true)->first() ?? $movie->sources->first()" />
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
                        <div class="flex flex-wrap gap-2">
                            @foreach($movie->sources as $source)
                                <button
                                        onclick="changeSource('{{ $source->id }}')"
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 {{ $source->is_primary
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white' }}"
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

    <!-- Content Grid with Creative Layout -->
    <div class="mt-12 grid lg:grid-cols-12 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Movie Info with Creative Elements -->
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                {{ $movie->release_year }}
                            </div>
                        @endif

                        @if($movie->duration)
                            <div class="flex items-center gap-2 floating-element" style="animation-delay: 1s">
                                    <span class="w-8 h-8 flex items-center justify-center rounded-full bg-purple-500/10">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                {{ $movie->duration }} phút
                            </div>
                        @endif

                        <div class="flex items-center gap-2 floating-element" style="animation-delay: 1.5s">
                                <span class="w-8 h-8 flex items-center justify-center rounded-full bg-pink-500/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </span>
                            {{ number_format($movie->views_count) }} lượt xem
                        </div>
                    </div>

                    @if($movie->description)
                        <div class="relative">
                            <div class="absolute -inset-4 rounded-2xl bg-gradient-to-r from-blue-500/5 to-purple-500/5 blur-xl"></div>

                            <div class="flex items-center justify-between mb-4">
                                <h2 class="relative text-white text-xl font-medium">
                                    <span class="border-l-4 border-blue-500 pl-2">Thông tin</span>
                                </h2>
                            </div>

                            <p class="relative text-lg leading-relaxed text-gray-300">
                                {{ $movie->description }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Creative Ad Space -->
            <div class="relative rounded-3xl overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 animate-pulse"></div>
                <div class="relative p-4">
                    <div id="movie-detail-ad">
                        <img src="https://placehold.co/728x90" alt="Ad" class="w-full rounded-xl" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar with Creative Elements -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Stylized Ad Space -->
            <div class="rounded-3xl backdrop-blur-xl bg-white/5 p-4 glow">
                <div id="sidebar-ad">
                    <img src="https://placehold.co/400" alt="Ad" class="w-full rounded-xl" />
                </div>
            </div>

            <!-- Related Movies with Creative Cards -->
            <div class="rounded-3xl backdrop-blur-xl bg-white/5 p-6">
                <h3 class="text-xl font-bold mb-6 bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Xem thêm
                </h3>
                <div class="space-y-4">
                    @foreach($relatedMovies ?? [] as $relatedMovie)
                        <x-related-movie-card :movie="$relatedMovie" class="hover:scale-105 transition-transform duration-300" />
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
		let fbVideoPlayer;

		// Subscribe to Facebook video events
		FB.Event.subscribe('xfbml.ready', function(msg) {
			if (msg.type === 'video') {
				fbVideoPlayer = msg.instance;
			}
		});

		function changeSource(sourceId) {
			fetch(`/api/movies/sources/${sourceId}`)
				.then(response => response.json())
				.then(data => {
					const playerContainer = document.getElementById('player-container');
					playerContainer.innerHTML = data.player_html;

					// Reinitialize Facebook SDK if it's a Facebook video
					if (data.player_html.includes('fb-video')) {
						if (window.FB) {
							FB.XFBML.parse(playerContainer);
						}
					}

					// Update button states
					document.querySelectorAll('[onclick^="changeSource"]').forEach(button => {
						button.classList.remove('bg-blue-500', 'text-white');
						button.classList.add('bg-gray-700', 'text-gray-300');
					});

					const activeButton = document.querySelector(`[onclick*="${sourceId}"]`);
					if (activeButton) {
						activeButton.classList.remove('bg-gray-700', 'text-gray-300');
						activeButton.classList.add('bg-blue-500', 'text-white');
					}
				})
				.catch(error => {
					console.error('Error:', error);
					showErrorMessage(sourceId);
				});
		}

		function showErrorMessage(sourceId) {
			const playerContainer = document.getElementById('player-container');
			playerContainer.innerHTML = `
            <div class="w-full h-full flex items-center justify-center bg-black/90">
                <div class="text-center p-6">
                    <div class="w-16 h-16 rounded-full bg-gray-800/50 mx-auto flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-400 mb-4">Failed to load video source</p>
                    <button
                        onclick="changeSource('${sourceId}')"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200"
                    >
                        Retry
                    </button>
                </div>
            </div>
        `;
		}
    </script>
@endpush