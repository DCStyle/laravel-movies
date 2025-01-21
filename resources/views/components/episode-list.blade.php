@props(['movie', 'currentSeason' => null, 'currentEpisode' => null])

<div class="grid grid-cols-4 sm:grid-cols-6 xl:grid-cols-8 gap-2">
    @foreach(($currentSeason ?? $movie->seasons->first())->episodes as $episode)
        <a href="{{ route('movies.episode', ['movie' => $movie->slug, 'season' => $episode->season->number, 'episode' => $episode->number]) }}"
           class="relative p-2 text-center rounded transition-all duration-300 hover:scale-105
                    {{ ($currentEpisode && $currentEpisode->id === $episode->id)
                        ? 'bg-blue-500 text-white'
                        : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white' }}">
            <div class="text-xs font-medium whitespace-nowrap">
                Tập {{ $episode->number }}
            </div>
        </a>
    @endforeach
</div>