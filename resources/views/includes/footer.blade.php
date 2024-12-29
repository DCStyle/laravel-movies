@php
    use App\Models\FooterColumn;
    $columns = FooterColumn::with('items')->orderBy('order')->get();
@endphp

<footer class="border-t border-t-white border-opacity-5 text-gray-400 py-8">
    <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- First column with logo and description - Using settings -->
        <div>
            <a href="/" class="flex items-center space-x-2 text-white">
                <img src="{{ \Illuminate\Support\Facades\Storage::url(setting('site_logo')) }}" alt="Phimmoi" class="h-10">
            </a>
            <p class="mt-4">
                {{ setting('site_description') }}
            </p>
            <p class="mt-2 text-sm">
                {{ setting('site_meta_keywords') }}
            </p>
        </div>

        <!-- Dynamic Footer Columns -->
        @foreach($columns as $column)
            <div>
                <h4 class="text-white font-bold mb-4">{{ $column->title }}</h4>
                <ul class="space-y-2">
                    @foreach($column->items as $item)
                        <li>
                            <a href="{{ $item->url }}" class="text-blue-500 hover:underline">
                                {{ $item->label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    <!-- Bottom section with copyright and social links -->
    <div class="border-t border-t-white border-opacity-5 mt-8 pt-4">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between">
            <p class="text-sm text-gray-400">{{ footer_setting('copyright') }}</p>
            <div class="flex space-x-4">
                @if(footer_setting('facebook'))
                    <a href="{{ footer_setting('facebook') }}" class="text-gray-400 hover:text-white" target="_blank" rel="noopener">
                        <i class="fab fa-facebook"></i>
                    </a>
                @endif
                @if(footer_setting('twitter'))
                    <a href="{{ footer_setting('twitter') }}" class="text-gray-400 hover:text-white" target="_blank" rel="noopener">
                        <i class="fab fa-twitter"></i>
                    </a>
                @endif
                @if(footer_setting('instagram'))
                    <a href="{{ footer_setting('instagram') }}" class="text-gray-400 hover:text-white" target="_blank" rel="noopener">
                        <i class="fab fa-instagram"></i>
                    </a>
                @endif
                @if(footer_setting('youtube'))
                    <a href="{{ footer_setting('youtube') }}" class="text-gray-400 hover:text-white" target="_blank" rel="noopener">
                        <i class="fab fa-youtube"></i>
                    </a>
                @endif
                <a href="#" class="text-gray-400 hover:text-white scroll-to-top">
                    <i class="fas fa-arrow-up"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Add this script for smooth scroll to top -->
<script>
	document.querySelector('.scroll-to-top').addEventListener('click', function(e) {
		e.preventDefault();
		window.scrollTo({
			top: 0,
			behavior: 'smooth'
		});
	});
</script>
