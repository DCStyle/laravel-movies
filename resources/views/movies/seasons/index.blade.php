@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.moderator')

@section('title', 'Danh sách các mùa - Phim ' . $movie->title)
@section('header', 'Danh sách các mùa')

@section('content')
    <div class="max-w-5xl mx-auto px-4">
        <!-- Movie Info Header -->
        <div class="mb-8 bg-white dark:bg-gray-800 rounded-xl p-6">
            <div class="flex items-start gap-6">
                <div class="w-32 flex-shrink-0">
                    <img src="{{ $movie->getThumbnail() ?? 'https://placeholder.co/350x200' }}"
                         alt="{{ $movie->title }}"
                         class="w-full rounded-lg shadow-lg">
                </div>
                <div class="flex-grow">
                    <h1 class="text-2xl font-bold mb-2">{{ $movie->title }}</h1>
                    <div class="text-gray-500 dark:text-gray-400 mb-4">
                        {{ $movie->total_seasons }} mùa • {{ $movie->total_episodes }} tập
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">{{ $movie->description }}</p>
                </div>
            </div>
        </div>

        <!-- Add Season Button -->
        <div class="mb-6">
            <button onclick="openAddSeasonModal()"
                    class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm mùa
            </button>
        </div>

        <!-- Seasons List -->
        <div class="space-y-6">
            @forelse($movie->seasons as $season)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold mb-2">
                                    Mùa {{ $season->number }}: {{ $season->title }}
                                </h3>
                                <div class="text-gray-500 dark:text-gray-400 mb-4">
                                    {{ $season->episodes->count() }} tập
                                    @if($season->release_date)
                                        • Phát hành {{ $season->release_date->format('M d, Y') }}
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('episodes.index', $season) }}"
                                   class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                    Quản lý các tập
                                </a>
                                <button onclick="openEditSeasonModal({{ $season->id }})"
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                                <button onclick="deleteSeason({{ $season->id }})"
                                        class="p-2 text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <p class="text-gray-500 dark:text-gray-400">Chưa có mùa nào</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Add/Edit Season Modal -->
    <div id="seasonModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
                <form id="seasonForm" method="POST" class="p-6">
                    @csrf
                    <h3 class="text-xl font-bold mb-6" id="modalTitle">Thêm mùa</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Mùa</label>
                            <input type="number" name="number" id="seasonNumber" required
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Tiêu đề</label>
                            <input type="text" name="title" id="seasonTitle" required
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Ngày phát hành</label>
                            <input type="date" name="release_date" id="seasonReleaseDate"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Mô tả</label>
                            <textarea name="description" id="seasonDescription" rows="3"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeSeasonModal()"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Huỷ
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Lưu mùa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
			const modal = document.getElementById('seasonModal');
			const form = document.getElementById('seasonForm');
			let isEditing = false;
			let currentSeasonId = null;

			function openAddSeasonModal() {
				isEditing = false;
				currentSeasonId = null;
				document.getElementById('modalTitle').textContent = 'Thêm mùa mới';
				form.reset();
				form.action = '/movies/{{ $movie->id }}/seasons';
				modal.classList.remove('hidden');
			}

			function openEditSeasonModal(seasonId) {
				isEditing = true;
				currentSeasonId = seasonId;
				document.getElementById('modalTitle').textContent = 'Cập nhật mùa phim';

				// Fetch season data and populate form
				fetch(`/api/seasons/${seasonId}`)
					.then(response => response.json())
					.then(season => {
						document.getElementById('seasonNumber').value = season.number;
						document.getElementById('seasonTitle').value = season.title;
						document.getElementById('seasonDescription').value = season.description;
						document.getElementById('seasonReleaseDate').value = season.release_date;
					});

				form.action = `/movies/{{ $movie->id }}/seasons/${seasonId}`;
				const methodInput = document.querySelector('input[name="_method"]');
				if (!methodInput) {
					const input = document.createElement('input');
					input.type = 'hidden';
					input.name = '_method';
					input.value = 'PUT';
					form.appendChild(input);
				}

				modal.classList.remove('hidden');
			}

			function closeSeasonModal() {
				modal.classList.add('hidden');
			}

			function deleteSeason(seasonId) {
				if (!confirm('Bạn có chắc muốn xoá?')) return;

				const form = document.createElement('form');
				form.method = 'POST';
				form.action = `/movies/{{ $movie->id }}/seasons/${seasonId}`;

				const methodInput = document.createElement('input');
				methodInput.type = 'hidden';
				methodInput.name = '_method';
				methodInput.value = 'DELETE';

				const csrfInput = document.createElement('input');
				csrfInput.type = 'hidden';
				csrfInput.name = '_token';
				csrfInput.value = '{{ csrf_token() }}';

				form.appendChild(methodInput);
				form.appendChild(csrfInput);
				document.body.appendChild(form);
				form.submit();
			}

			// Close modal when clicking outside
			modal.addEventListener('click', (e) => {
				if (e.target === modal) closeSeasonModal();
			});
        </script>
    @endpush
@endsection