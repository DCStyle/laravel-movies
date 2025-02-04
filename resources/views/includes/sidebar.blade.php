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

@if(isset($latestArticles) && $latestArticles->isNotEmpty())
    <!-- Latest Articles Component for Sidebar -->
    <div class="space-y-4">
        @if($latestArticles->first())
            <!-- Featured Article Card -->
            <a href="{{ route('articles.show', $latestArticles->first()->slug) }}" class="block relative aspect-video rounded-lg overflow-hidden">
                <img src="{{ asset($latestArticles->first()->thumbnail()) ?? 'https://placehold.co/400' }}"
                     alt="{{ $latestArticles->first()->title }}"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent">
                    <div class="absolute bottom-0 p-4">
                        <h3 class="text-lg font-medium text-white">{{ $latestArticles->first()->title }}</h3>
                        <p class="text-gray-300">{{ $latestArticles->first()->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </a>
        @endif

        <!-- Articles List -->
        <div class="space-y-4">
            @foreach($latestArticles->skip(1) as $article)
                <a href="{{ route('articles.show', $article->slug) }}"
                   class="flex gap-3 group bg-[rgba(30,30,30,.9)] hover:bg-gray-800 rounded-lg overflow-hidden">
                    <!-- Thumbnail -->
                    <div class="w-16 h-24 shrink-0">
                        <img src="{{ asset($article->thumbnail) ?? 'https://placehold.co/400' }}"
                             alt="{{ $article->title }}"
                             class="w-full h-full object-cover">
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0 px-2 py-4">
                        <h4 class="font-medium text-white group-hover:text-blue-500 transition line-clamp-2">
                            {{ $article->title }}
                        </h4>

                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-gray-400">{{ $article->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif