<div x-data="{ mobileMenu: false }">
    <nav class="sticky top-0 z-50 w-full bg-gradient-to-r from-[rgba(15,15,15,0.95)] to-[rgba(15,15,15,0.85)] backdrop-blur-lg border-b border-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-8">
                    <a href="/" class="flex-shrink-0 transition-transform hover:scale-105">
                        <img class="h-10" src="{{ Storage::url(setting('site_logo')) }}" alt="Logo">
                    </a>

                    <x-main-menu />
                </div>

                <!-- Search Bar -->
                <div class="relative flex-1 max-w-xl mx-8" x-data="searchMovies()">
                    <div class="relative">
                        <input type="text"
                               x-model="query"
                               @input.debounce.300ms="search()"
                               class="w-full bg-gray-800/50 text-gray-300 rounded-xl pl-12 pr-4 py-3 text-sm border border-gray-700/50
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-transparent
                                      transition-all duration-200"
                               placeholder="Nhập tên phim, danh mục, thể loại...">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div x-show="query.length > 0" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute mt-2 w-full bg-gray-800 rounded-xl shadow-2xl border border-gray-700/50 overflow-hidden">

                        <template x-if="loading">
                            <div class="p-4 flex items-center justify-center">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-500"></div>
                            </div>
                        </template>

                        <template x-if="!loading && results.length > 0">
                            <div class="max-h-[70vh] overflow-y-auto">
                                <template x-for="movie in results" :key="movie.id">
                                    <a :href="movie.url" class="flex p-4 hover:bg-gray-700/50 transition-colors">
                                        <img :src="movie.thumbnail"
                                             class="w-16 h-24 object-cover rounded-lg shadow-lg"
                                             :alt="movie.title">
                                        <div class="ml-4 flex-1">
                                            <h3 class="text-white font-medium line-clamp-1" x-text="movie.title"></h3>
                                            <div class="text-sm text-gray-500" x-text="movie.title_en"></div>
                                            <div class="flex items-center mt-1 space-x-2">
                                                <span class="px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded-full" x-text="movie.year"></span>
                                                <div class="h-1 w-1 bg-gray-600 rounded-full"></div>
                                                <span class="text-sm text-gray-400" x-text="movie.rating"></span>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-400 line-clamp-2" x-text="movie.description"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </template>

                        <template x-if="!loading && results.length === 0">
                            <div class="p-8 text-center">
                                <div class="text-gray-400 mb-2">
                                    Không tìm thấy kết quả
                                </div>
                                <p class="text-sm text-gray-500">
                                    Hãy thử nhập từ khóa khác
                                </p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenu = !mobileMenu"
                        class="lg:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700/50 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div x-show="mobileMenu"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="lg:hidden fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="relative bg-[rgba(15,15,15,0.98)] h-screen w-full overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-8">
                    <img class="h-8" src="{{ Storage::url(setting('site_logo')) }}" alt="Logo">
                    <button @click="mobileMenu = false" class="p-2 text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <x-mobile-menu />
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
		function searchMovies() {
			return {
				query: '',
				results: [],
				loading: false,

				async search() {
					if (this.query.length < 2) {
						this.results = [];
						return;
					}

					this.loading = true;
					try {
						const response = await fetch(`/api/search?q=${encodeURIComponent(this.query)}`);
						const data = await response.json();
						this.results = data.data;
					} catch (error) {
						console.error('Search error:', error);
						this.results = [];
					}
					this.loading = false;
				}
			}
		}
    </script>
@endpush