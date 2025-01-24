@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.moderator')

@section('title', 'Danh sách tập - Mùa ' . $season->number . ' - Phim ' . $movie->title)
@section('header', 'Danh sách tập phim')

@section('content')
    <div class="max-w-5xl mx-auto px-4">
        <!-- Navigation -->
        <nav class="mb-8 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('movies.management.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Phim</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('seasons.index', $movie) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $movie->title }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span>Mùa {{ $season->number }}</span>
        </nav>

        <!-- Season Info Header -->
        <div class="mb-8 bg-white dark:bg-gray-800 rounded-xl p-6">
            <h1 class="text-2xl font-bold mb-2">
                Mùa {{ $season->number }}: {{ $season->title }}
            </h1>
            <div class="text-gray-500 dark:text-gray-400 mb-4">
                {{ $season->episodes->count() }} tập
                @if($season->release_date)
                    • Phát hành {{ $season->release_date->format('M d, Y') }}
                @endif
            </div>
            @if($season->description)
                <p class="text-gray-600 dark:text-gray-300">{{ $season->description }}</p>
            @endif
        </div>

        <!-- Add Episode Button -->
        <div class="mb-6">
            <a href="{{ route('episodes.create', $season) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm tập
            </a>
        </div>

        <!-- Episodes List -->
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($season->episodes as $episode)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 md:p-6">
                        <div class="flex gap-6">
                            <!-- Episode Info -->
                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-bold mb-2">
                                            Tập {{ $episode->number }}: {{ $episode->title }}
                                        </h3>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                            @if($episode->duration)
                                                {{ $episode->duration }} phút
                                            @endif
                                            @if($episode->air_date)
                                                • Lên sóng {{ $episode->air_date->format('M d, Y') }}
                                            @endif
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                                            {{ Str::limit($episode->description, 150) }}
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('episodes.edit', [$season, $episode]) }}"
                                                class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>
                                        <button onclick="deleteEpisode({{ $episode->id }})"
                                                class="p-2 text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Sources -->
                                <div class="mt-4">
                                    <h4 class="text-sm font-semibold mb-2">Nguồn</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($episode->sources as $source)
                                            <span class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 rounded-full">
                                                {{ ucfirst($source->source_type) }} - {{ strtoupper($source->quality) }}
                                                @if($source->is_primary)
                                                    <span class="ml-1 text-blue-500">•</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <p class="text-gray-500 dark:text-gray-400">Chưa có tập nào mùa này</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function deleteEpisode(episodeId) {
			if (!confirm('Bạn có chắc muốn xoá?')) return;

			const form = document.createElement('form');
			form.method = 'POST';
			form.action = `/seasons/{{ $season->id }}/episodes/${episodeId}`;

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
			if (e.target === modal) closeEpisodeModal();
		});

		// Add initial source when adding new episode
		if (!isEditing) {
			addSource();
		}
    </script>
@endpush