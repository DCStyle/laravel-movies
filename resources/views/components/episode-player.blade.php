@props(['episode', 'movie'])

<div class="relative rounded-3xl overflow-hidden backdrop-blur-xl bg-black/30 shadow-2xl">
    <section class="bg-gradient-to-b from-gray-900 to-gray-800 rounded-2xl overflow-hidden shadow-2xl">
        <!-- Video Player -->
        <div class="relative" style="padding-top: 56.25%">
            <div id="player-container" class="absolute inset-0">
                @if($episode->sources->isNotEmpty())
                    <x-video-player
                            :source="$episode->sources->firstWhere('is_primary', true) ?? $episode->sources->first()"
                            :key="'player-' . ($episode->sources->firstWhere('is_primary', true)?->id ?? $episode->sources->first()?->id)"
                    />
                @else
                    <div class="w-full h-full flex items-center justify-center bg-black/50 backdrop-blur-xl">
                        <p class="text-gray-400">No sources available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Source Selection Bar -->
        @if($episode->sources->count() > 1)
            <div class="bg-gray-800/50 backdrop-blur-sm border-t border-gray-700">
                <div class="max-w-3xl mx-auto px-6 py-4">
                    <div class="flex flex-wrap gap-2" role="group" aria-label="Video sources">
                        @foreach($episode->sources as $source)
                            <button
                                    type="button"
                                    onclick="changeSource('{{ $source->id }}', 'episode')"
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