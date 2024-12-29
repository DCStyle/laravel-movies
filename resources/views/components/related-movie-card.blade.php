@props(['movie'])

<div class="flex gap-4">
    <!-- Thumbnail -->
    <div class="w-24 h-16 flex-shrink-0 overflow-hidden rounded">
        <a href="{{ route('movies.show', $movie->slug) }}" class="block">
            @if($movie->thumbnail)
                <img
                        src="{{ Storage::url($movie->thumbnail) }}"
                        alt="{{ $movie->title }}"
                        class="w-full h-full object-cover"
                        loading="lazy"
                >
            @else
                <div class="w-full h-full bg-gray-700 rounded flex items-center justify-center">
                    <span class="text-gray-400 text-xs">No Image</span>
                </div>
            @endif
        </a>
    </div>

    <!-- Info -->
    <div class="flex-1 min-w-0">
        <h4 class="text-sm font-medium line-clamp-2">
            <a href="{{ route('movies.show', $movie->slug) }}" class="hover:text-blue-500">
                {{ $movie->title }}
            </a>
        </h4>
        <div class="mt-1 text-xs text-gray-400">
            <span>{{ $movie->release_year }}</span>
            <span class="mx-1">•</span>
            <span>{{ number_format($movie->views_count) }} views</span>
        </div>
    </div>
</div>