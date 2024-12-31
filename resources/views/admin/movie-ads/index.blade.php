@extends('layouts.admin')

@section('title', 'Quản lý quảng cáo phim')
@section('header', 'Quản lý quảng cáo phim')

@push('styles')
    <style>
        .preview-container {
            transition: transform 0.2s;
        }
        .preview-container:hover {
            transform: scale(1.05);
        }
        .drag-handle {
            cursor: move;
            touch-action: none;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100">Tổng số quảng cáo</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $ads->count() }}</h3>
                    </div>
                    <div class="bg-blue-400/30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100">Đang hoạt động</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $ads->where('is_enabled', true)->count() }}</h3>
                    </div>
                    <div class="bg-emerald-400/30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-violet-100">Đã tạo trong tháng</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $ads->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                    </div>
                    <div class="bg-violet-400/30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <!-- Header with Actions -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <a href="{{ route('admin.movie-ads.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Thêm quảng cáo mới
                    </a>

                    <div class="flex gap-2">
                        <select id="type-filter" class="rounded-lg border-gray-300">
                            <option value="">Tất cả loại</option>
                            <option value="image">Hình ảnh</option>
                            <option value="video">Video</option>
                        </select>

                        <form method="GET" class="relative flex-1 sm:max-w-xs">
                            <input type="text" name="search" placeholder="Tìm kiếm quảng cáo..."
                                   value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div id="sortable-ads" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse($ads as $ad)
                    <div class="group relative bg-gray-50 dark:bg-gray-700/50 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all"
                         data-id="{{ $ad->id }}">
                        <!-- Preview -->
                        <div class="aspect-video relative overflow-hidden bg-gray-900">
                            @if($ad->type === 'image')
                                <img src="{{ $ad->content_url }}"
                                     alt="{{ $ad->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <video class="w-full h-full object-cover">
                                    <source src="{{ $ad->content_url }}" type="video/mp4">
                                </video>
                                <div class="absolute inset-0 flex items-center justify-center bg-black/50">
                                    <svg class="w-12 h-12 text-white/75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @endif

                            <!-- Type Badge -->
                            <div class="absolute top-2 left-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-md
                                    {{ $ad->type === 'image'
                                        ? 'bg-blue-500/80 text-white'
                                        : 'bg-purple-500/80 text-white' }}">
                                    {{ ucfirst($ad->type) }}
                                </span>
                            </div>

                            <!-- Drag Handle -->
                            <div class="absolute top-2 right-2 drag-handle">
                                <div class="p-1 rounded-md bg-gray-900/50 text-white cursor-move">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $ad->name }}</h3>
                                <!-- Status Toggle -->
                                <button onclick="toggleAdStatus({{ $ad->id }})"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out {{ $ad->is_enabled ? 'bg-green-500' : 'bg-gray-300' }}"
                                        role="switch"
                                        aria-checked="{{ $ad->is_enabled ? 'true' : 'false' }}">
                                    <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $ad->is_enabled ? 'translate-x-5' : '' }}"></span>
                                </button>
                            </div>

                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                <p>Hiển thị tại: {{ $ad->display_time }}%</p>
                                @if($ad->type === 'image')
                                    <p>Thời gian hiển thị: {{ $ad->duration }}s</p>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.movie-ads.edit', $ad) }}"
                                   class="p-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                <form action="{{ route('admin.movie-ads.destroy', $ad) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa quảng cáo này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-12">
                            <div class="p-3 rounded-full bg-gray-100 dark:bg-gray-700 inline-block">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>

                            <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">Chưa có quảng cáo nào</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Bắt đầu bằng cách tạo quảng cáo đầu tiên.</p>
                            <a href="{{ route('admin.movie-ads.create') }}"
                               class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tạo quảng cáo mới
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($ads->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $ads->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/modular/sortable.min.js"></script>
    <script>
		// Initialize sortable
		new Sortable(document.getElementById('sortable-ads'), {
			handle: '.drag-handle',
			animation: 150,
			ghostClass: 'opacity-50',
			onEnd: function() {
				updateOrder();
			}
		});

		// Update order via API
		function updateOrder() {
			const items = document.querySelectorAll('#sortable-ads > div');
			const orders = Array.from(items).map(item => item.dataset.id);

			fetch('{{ route("admin.movie-ads.update-order") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: JSON.stringify({ orders })
			});
		}

		// Toggle ad status
		function toggleAdStatus(id) {
			fetch(`/admin/movie-ads/${id}/toggle-status`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				}
			}).then(response => response.json())
				.then(data => {
					if (data.status === 'success') {
						const button = event.currentTarget;
						const span = button.querySelector('span');

						if (button.getAttribute('aria-checked') === 'true') {
							button.setAttribute('aria-checked', 'false');
							button.classList.remove('bg-green-500');
							button.classList.add('bg-gray-300');
							span.classList.remove('translate-x-5');
						} else {
							button.setAttribute('aria-checked', 'true');
							button.classList.remove('bg-gray-300');
							button.classList.add('bg-green-500');
							span.classList.add('translate-x-5');
						}
					}
				});
		}

		// Type filter functionality
		document.getElementById('type-filter').addEventListener('change', function(e) {
			const type = e.target.value;
			const url = new URL(window.location);

			if (type) {
				url.searchParams.set('type', type);
			} else {
				url.searchParams.delete('type');
			}

			window.location = url;
		});

		// Set initial filter value from URL
		document.addEventListener('DOMContentLoaded', function() {
			const urlParams = new URLSearchParams(window.location.search);
			const typeFilter = urlParams.get('type');

			if (typeFilter) {
				document.getElementById('type-filter').value = typeFilter;
			}
		});

		// Preview videos on hover
		document.querySelectorAll('video').forEach(video => {
			const container = video.closest('.aspect-video');

			container.addEventListener('mouseenter', () => {
				video.play();
				container.querySelector('.bg-black/50')?.classList.add('opacity-0');
			});

			container.addEventListener('mouseleave', () => {
				video.pause();
				video.currentTime = 0;
				container.querySelector('.bg-black/50')?.classList.remove('opacity-0');
			});
		});
    </script>
@endpush