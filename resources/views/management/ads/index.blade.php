@extends('layouts.admin')

@section('title', 'Quản lý quảng cáo')
@section('header', 'Quản lý quảng cáo')

@section('content')
    <div class="max-w-7xl mx-auto px-4 space-y-6">
        <!-- Header Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $ads->count() }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tổng số quảng cáo</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('management.ads.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Thêm quảng cáo mới
            </a>
        </div>

        <!-- Ads by Position -->
        @forelse(App\Models\Ad::POSITIONS as $position => $label)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $label }}</h3>
                </div>

                <div class="sortable-group" data-position="{{ $position }}">
                    @foreach($ads->where('position', $position)->sortBy('order') as $ad)
                        <div class="sortable-item relative" data-id="{{ $ad->id }}">
                            <div class="position-indicator"></div>
                            <div class="flex items-center p-4 group">
                                <div class="sortable-handle flex items-center px-2 -ml-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M4 8h16M4 16h16"/>
                                    </svg>
                                </div>

                                <div class="flex flex-1 items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $ad->name }}</h4>
                                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                               {{ $ad->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200' }}">
                                               {{ $ad->is_active ? 'Hoạt động' : 'Ẩn' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button type="button" onclick="previewAd({{ json_encode($ad->content) }})"
                                                class="p-1 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        <a href="{{ route('management.ads.edit', $ad) }}"
                                           class="p-1 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <form action="{{ route('management.ads.destroy', $ad) }}" method="POST" class="inline-block"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa quảng cáo này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="p-3 rounded-full bg-gray-100 dark:bg-gray-700 inline-block">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">Chưa có quảng cáo nào</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Bắt đầu bằng cách tạo quảng cáo đầu tiên.</p>
            </div>
        @endforelse
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl max-w-3xl w-full shadow-xl">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">Xem trước quảng cáo</h3>
                        <button onclick="closePreview()" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6" id="preview-content"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <script>
		function previewAd(content) {
			document.getElementById('preview-content').innerHTML = content;
			document.getElementById('preview-modal').classList.remove('hidden');
		}

		function closePreview() {
			document.getElementById('preview-modal').classList.add('hidden');
		}

		// Close modal when clicking outside
		document.getElementById('preview-modal').addEventListener('click', function(e) {
			if (e.target === this) closePreview();
		});

		document.addEventListener('DOMContentLoaded', function() {
			const groups = document.querySelectorAll('.sortable-group');

			groups.forEach(group => {
				new Sortable(group, {
					group: 'ads',
					animation: 150,
					handle: '.sortable-handle',
					ghostClass: 'sortable-ghost',
					dragClass: 'sortable-drag',
					forceFallback: true,
					fallbackClass: 'sortable-fallback',
					onStart: function(evt) {
						document.body.style.cursor = 'grabbing';
					},
					onEnd: function(evt) {
						document.body.style.cursor = '';
						const itemId = evt.item.dataset.id;
						const newPosition = evt.to.dataset.position;
						const items = evt.to.children;

						// Highlight animation
						evt.item.style.backgroundColor = '#f0f9ff';
						setTimeout(() => {
							evt.item.style.backgroundColor = '';
						}, 500);

						// Update server
						updateOrder(Array.from(items), newPosition);
					}
				});
			});
		});

		function updateOrder(items, position) {
			const orderData = items.map((item, index) => ({
				id: item.dataset.id,
				order: index,
				position: position
			}));

			fetch('{{ route("management.ads.reorder") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: JSON.stringify(orderData)
			});
		}
    </script>
@endpush

@push('styles')
    <style>
        .sortable-group {
            min-height: 50px;
        }

        .sortable-item {
            position: relative;
            background: white;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .sortable-item {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .sortable-drag {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .sortable-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            height: 20px;
            width: 3px;
            transform: translateY(-50%);
            background: #e5e7eb;
            border-radius: 2px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .sortable-item:hover::before {
            opacity: 1;
        }

        .sortable-item.sortable-ghost {
            background: #f3f4f6;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.05);
        }

        .sortable-item.sortable-drag {
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            transform: scale(1.02);
        }

        .sortable-handle {
            cursor: move;
            touch-action: none;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .sortable-item:hover .sortable-handle {
            opacity: 1;
        }

        .position-indicator {
            position: absolute;
            left: -2px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #3b82f6;
            opacity: 0;
            transform: scaleY(0);
            transition: transform 0.2s, opacity 0.2s;
        }

        .sortable-item:hover .position-indicator {
            opacity: 1;
            transform: scaleY(1);
        }
    </style>
@endpush