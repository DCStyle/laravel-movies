<!-- Year Filter Component -->
<div class="bg-[rgba(30,30,30,.9)] rounded-lg overflow-hidden">
    <div class="p-4 flex items-center justify-between cursor-pointer">
        <h3 class="text-lg font-medium">Năm phát hành</h3>
        <svg class="w-5 h-5 transition-transform" viewBox="0 0 24 24" fill="none">
            <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
        <div class="grid grid-cols-3 gap-2 p-4">
            @php
                $currentYear = date('Y');
                $years = range($currentYear, 1974);
            @endphp

            @foreach($years as $year)
                <a href="{{ route('release-years.show', ['year' => $year]) }}"
                   class="py-2 px-3 text-center rounded-lg transition-colors
                          {{ request()->get('year') == $year ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-[rgba(45,45,45,.9)] hover:text-white' }}">
                    {{ $year }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Trending Movies Component for Sidebar -->
<div class="space-y-4">
    <!-- Featured Movie Card -->
    <a href="{{ route('movies.show', $sidebarFeaturedMovie->slug) }}" class="block relative aspect-video rounded-lg overflow-hidden">
        <img src="{{ \Illuminate\Support\Facades\Storage::url($sidebarFeaturedMovie->thumbnail) }}" alt="{{ $sidebarFeaturedMovie->title }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent">
            <div class="absolute bottom-0 p-4">
                <h3 class="text-lg font-medium text-white">{{ $sidebarFeaturedMovie->title }}</h3>
                <p class="text-gray-300">{{ $sidebarFeaturedMovie->release_year }}</p>
            </div>
        </div>
    </a>

    <!-- Movie List -->
    <div class="space-y-4">
        @foreach($sidebarTrendingMovies as $sidebarTrendingMovie)
            <a href="{{ route('movies.show', $sidebarTrendingMovie->slug) }}" class="flex gap-3 group bg-[rgba(30,30,30,.9)] hover:bg-gray-800">
                <!-- Thumbnail -->
                <div class="w-16 h-24 shrink-0">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($sidebarTrendingMovie->thumbnail) }}" alt="{{ $sidebarTrendingMovie->title }}" class="w-full h-full object-cover">
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0 px-2 py-4">
                    <h4 class="font-medium text-white group-hover:text-blue-500 transition line-clamp-2">
                        {{ $sidebarTrendingMovie->title }}
                    </h4>

                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 24 24">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                            </svg>
                            <span class="text-white">{{ $sidebarTrendingMovie->rating }}</span>
                        </div>
                        <span class="text-gray-500">{{ $sidebarTrendingMovie->release_year }}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>