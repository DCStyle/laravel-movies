@props(['title', 'movies'])

@php $uniqueId = uniqid(); @endphp

<section class="border-b border-b-white border-opacity-5 pb-4 mb-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-white text-xl font-medium">
            <span class="border-l-4 border-blue-500 pl-2">{{ $title }}</span>
        </h2>

        <!-- Navigation Arrows -->
        <div class="flex gap-1">
            <button class="p-2 text-gray-400 hover:text-white transition swiper-button-prev-{{ $uniqueId }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button class="p-2 text-gray-400 hover:text-white transition swiper-button-next-{{ $uniqueId }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Movies Swiper -->
    <div class="swiper movie-swiper-{{ $uniqueId }}">
        <div class="swiper-wrapper">
            @foreach($movies as $movie)
                <div class="swiper-slide">
                    <x-movie-card :movie="$movie" />
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('scripts')
    <script>
		document.addEventListener('DOMContentLoaded', function() {
			new Swiper('.movie-swiper-{{ $uniqueId }}', {
				slidesPerView: 5,
				spaceBetween: 16,
				navigation: {
					nextEl: '.swiper-button-next-{{ $uniqueId }}',
					prevEl: '.swiper-button-prev-{{ $uniqueId }}'
				},
				breakpoints: {
					320: { slidesPerView: 2 },
					640: { slidesPerView: 2 },
					768: { slidesPerView: 3 },
					1024: { slidesPerView: 4 },
					1280: { slidesPerView: 5 }
				}
			});
		});
    </script>
@endpush