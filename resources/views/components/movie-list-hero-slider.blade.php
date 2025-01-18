@props(['latestMovies'])

@php $uniqueId = uniqid('latest-movies-'); @endphp

<div class="relative overflow-hidden">
    <div class="swiper latest-movies-slider {{ $uniqueId }}">
        <div class="swiper-wrapper">
            @foreach($latestMovies as $movie)
                <div class="swiper-slide relative aspect-[16/9]">
                    <a href="{{ route('movies.show', $movie->slug) }}" class="block w-full h-full">
                        <img src="{{ $movie->getBanner() ?? $movie->getThumbnail() ?? 'https://placeholder.co/300x450' }}"
                             alt="{{ $movie->title }}"
                             class="w-full h-full object-cover">

                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>

                        <div class="absolute bottom-0 left-0 p-6 w-full">
                            <h2 class="text-3xl font-bold text-white mb-2">{{ $movie->title }}</h2>
                            <div class="flex items-center gap-4 text-white/90">
                                <span>{{ $movie->release_year }}</span>
                                @if($movie->quality)
                                    <span class="bg-blue-600 px-3 py-1 rounded text-sm">
                                        {{ $movie->quality }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="swiper-pagination"></div>
    </div>
</div>

@push('scripts')
    <script>
		document.addEventListener('DOMContentLoaded', function() {
			new Swiper('.{{ $uniqueId }}', {
				slidesPerView: 1,
				spaceBetween: 16,
				loop: true,
				autoplay: {
					delay: 5000,
					disableOnInteraction: false,
				},
				pagination: {
					el: '.swiper-pagination',
					clickable: true
				},
				breakpoints: {
					768: { slidesPerView: 2 }
				}
			});
		});
    </script>
@endpush