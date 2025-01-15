@props(['movie'])

<div class="movie-card" id="movie-{{ $movie->id }}">
    <div class="relative bg-gray-900 rounded-lg shadow-lg overflow-hidden group">
        <!-- Clickable Thumbnail Area -->
        <a href="{{ route('movies.show', $movie->slug) }}" class="block relative aspect-square sm:aspect-[2/3]">
            @if($movie->thumbnail)
                <img src="{{ Storage::url($movie->thumbnail) }}"
                     alt="{{ $movie->title }}"
                     class="w-full h-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:blur-sm"
                     loading="lazy">
            @else
                <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                    <span class="text-gray-400">No Image</span>
                </div>
            @endif

            <!-- Play Button (Visible on hover) -->
            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10">
                <svg class="w-16 h-16 text-white/90" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </div>

            <!-- Top Badge -->
            <div class="absolute top-2 left-2 z-20 flex gap-2">
                @if($movie->type === 'series')
                    <span class="bg-purple-600 text-white px-3 py-1 rounded-sm text-sm font-medium">
                        SERIES
                    </span>
                @endif
                <span class="bg-red-600 text-white px-3 py-1 rounded-sm text-sm font-medium">
                    NỔI BẬT
                </span>
            </div>

            <!-- Bottom Badge -->
            <div class="absolute bottom-2 left-2 z-20 flex flex-wrap gap-2">
                @if($movie->type === 'series')
                    <span class="bg-blue-600 text-white px-3 py-1 rounded-sm text-sm font-medium">
                        {{ $movie->seasons_count ?? $movie->seasons->count() }} Season{{ ($movie->seasons_count ?? $movie->seasons->count()) !== 1 ? 's' : '' }}
                    </span>
                @elseif (isset($movie->duration))
                    <span class="bg-blue-600 text-white px-3 py-1 rounded-sm text-sm font-medium">
                        {{ $movie->duration }} phút
                    </span>
                @endif

                @if($movie->rating)
                    <span class="bg-yellow-600 text-white px-3 py-1 rounded-sm text-sm font-medium">
                        {{ number_format($movie->rating, 1) }} ★
                    </span>
                @endif
            </div>

            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
        </a>
    </div>

    <!-- Content -->
    <div class="mt-3 z-20">
        <h3 class="text-white text-base font-medium line-clamp-2">
            <a href="{{ route('movies.show', $movie->slug) }}"
               class="font-bold hover:text-blue-500 transition">
                {{ $movie->title }}
            </a>
        </h3>

        <div class="mt-1 flex items-center gap-3 text-sm text-gray-400">
            <span>{{ $movie->release_year }}</span>
            @if($movie->genres->isNotEmpty())
                <span>•</span>
                <span class="line-clamp-1">
                    {{ $movie->genres->pluck('name')->take(2)->join(', ') }}
                </span>
            @endif
        </div>
    </div>
</div>