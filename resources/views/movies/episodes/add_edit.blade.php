@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.moderator')

@section('title', ($isEdit ? 'Sửa thông tin tập - ' . $episode->title : 'Thêm tập mới'))
@section('header', $isEdit ? 'Sửa thông tin tập' : 'Thêm tập mới')

@section('content')
    <div class="max-w-4xl mx-auto px-4">
        <form action="{{ $isEdit ? route('episodes.update', [$season, $episode]) : route('episodes.store', $season) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- Basic Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6">
                <h2 class="text-xl font-semibold mb-6">Thông tin tập</h2>

                <div class="grid grid-cols-2 gap-6 mb-4">
                    <div class="inline-flex items-center space-x-2">
                        <label class="block text-sm font-medium mb-2">Phim:</label>
                        <strong class="block text-lg text-primary font-semibold mb-2">{{ $movie->title }}</strong>
                    </div>

                    <div class="inline-flex items-center space-x-2">
                        <label class="block text-sm font-medium mb-2">Mùa:</label>
                        <strong class="block text-lg text-primary font-semibold mb-2">Mùa {{ $season->number }}</strong>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2">Tập</label>
                        <input type="number" name="number"
                               value="{{ old('number', $isEdit ? $episode->number : $season->episodes->count() + 1) }}"
                               required min="1"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Thời lượng (phút)</label>
                        <input type="number" name="duration"
                               value="{{ old('duration', $isEdit ? $episode->duration : '') }}"
                               min="1"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-2">Tiêu đề</label>
                        <input type="text" name="title"
                               value="{{ old('title', $isEdit ? $episode->title : '') }}"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-2">Lên sóng</label>
                        <input type="date" name="air_date"
                               value="{{ old('air_date', $isEdit ? $episode->air_date?->format('Y-m-d') : '') }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-2">Mô tả</label>
                        <textarea name="description" rows="3"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600">{{ old('description', $isEdit ? $episode->description : '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Sources -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Nguồn</h2>
                    <button type="button" onclick="addSource()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Thêm Nguồn
                    </button>
                </div>

                <div id="sources-container" class="space-y-4">
                    @if($isEdit)
                        @foreach($episode->sources as $index => $source)
                            <div class="source-item bg-gray-50 dark:bg-gray-700 p-4 rounded-lg" data-index="{{ $index }}">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-medium">Nguồn #{{ $index + 1 }}</h4>
                                    <button type="button" onclick="removeSource({{ $index }})"
                                            class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Source Type</label>
                                        <select name="sources[{{ $index }}][source_type]" required
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                                            <option value="">Select Type</option>
                                            <option value="youtube" {{ $source->source_type === 'youtube' ? 'selected' : '' }}>YouTube</option>
                                            <option value="direct" {{ $source->source_type === 'direct' ? 'selected' : '' }}>Direct Upload</option>
                                            <option value="fshare" {{ $source->source_type === 'fshare' ? 'selected' : '' }}>FShare</option>
                                            <option value="gdrive" {{ $source->source_type === 'gdrive' ? 'selected' : '' }}>Google Drive</option>
                                            <option value="twitter" {{ $source->source_type === 'twitter' ? 'selected' : '' }}>Twitter</option>
                                            <option value="facebook" {{ $source->source_type === 'facebook' ? 'selected' : '' }}>Facebook</option>
                                            <option value="tiktok" {{ $source->source_type === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Chất lượng</label>
                                        <select name="sources[{{ $index }}][quality]" required
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                                            <option value="">Select Quality</option>
                                            <option value="360p" {{ $source->quality === '360p' ? 'selected' : '' }}>360p</option>
                                            <option value="480p" {{ $source->quality === '480p' ? 'selected' : '' }}>480p</option>
                                            <option value="720p" {{ $source->quality === '720p' ? 'selected' : '' }}>720p</option>
                                            <option value="1080p" {{ $source->quality === '1080p' ? 'selected' : '' }}>1080p</option>
                                            <option value="4k" {{ $source->quality === '4k' ? 'selected' : '' }}>4K</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium mb-2">URL</label>
                                        <input type="url" name="sources[{{ $index }}][source_url]"
                                               value="{{ $source->source_url }}" required
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('episodes.index', $season) }}"
                   class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Huỷ
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    {{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
			let sourceIndex = {{ $isEdit ? count($episode->sources) : 0 }};

			function getSourceTemplate(index) {
				return `
                    <div class="source-item bg-gray-50 dark:bg-gray-700 p-4 rounded-lg" data-index="${index}">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-medium">Nguồn #${index + 1}</h4>
                            <button type="button" onclick="removeSource(${index})"
                                    class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Source Type</label>
                                <select name="sources[${index}][source_type]" required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                                    <option value="">Select Type</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="direct">Direct Upload</option>
                                    <option value="fshare">FShare</option>
                                    <option value="gdrive">Google Drive</option>
                                    <option value="twitter">Twitter</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="tiktok">TikTok</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Chất lượng</label>
                                <select name="sources[${index}][quality]" required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600">
                                    <option value="">Select Quality</option>
                                    <option value="360p">360p</option>
                                    <option value="480p">480p</option>
                                    <option value="720p">720p</option>
                                    <option value="1080p">1080p</option>
                                    <option value="4k">4K</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium mb-2">URL</label>
                                <input type="url" name="sources[${index}][source_url]" required
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600"
                                       placeholder="Enter source URL">
                            </div>
                        </div>
                    </div>
                `;
			}

			function addSource() {
				document.getElementById('sources-container').insertAdjacentHTML('beforeend', getSourceTemplate(sourceIndex));
				sourceIndex++;
			}

			function removeSource(index) {
				const sourceItem = document.querySelector(`.source-item[data-index="${index}"]`);
				if (sourceItem) {
					sourceItem.remove();
				}
			}

			// Preview thumbnail when selected
			document.querySelector('input[name="thumbnail"]')?.addEventListener('change', function(e) {
				if (this.files && this.files[0]) {
					const reader = new FileReader();
					reader.onload = function(e) {
						const preview = document.getElementById('thumbnail-preview');
						preview.innerHTML = `
                            <img src="${e.target.result}"
                                 class="w-full aspect-video object-cover rounded-lg"
                                 alt="Thumbnail preview">
                        `;
					}
					reader.readAsDataURL(this.files[0]);
				}
			});

			// Add initial source for new episodes
			if (!{{ $isEdit ? 'true' : 'false' }}) {
				addSource();
			}
        </script>
    @endpush
@endsection