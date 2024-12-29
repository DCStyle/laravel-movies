@extends('layouts.admin')

@section('title', $isEdit ? 'Chỉnh sửa phim: ' . $movie->title : 'Thêm phim mới')
@section('header', $isEdit ? 'Chỉnh sửa phim: ' . $movie->title : 'Thêm phim mới')

@section('content')
    <div class="max-w-4xl mx-auto px-4">
        <form action="{{ $isEdit ? route('movies.management.update', $movie) : route('movies.management.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- Thông báo lỗi -->
            @if($errors->any())
                <div class="bg-red-100 dark:bg-red-800 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg" role="alert">
                    <strong class="font-semibold">Có lỗi xảy ra!</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Thông tin cơ bản -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h3 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Thông tin cơ bản</h3>
                <div class="grid gap-6">
                    <!-- Tiêu đề -->
                    <div>
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tiêu đề phim</label>
                        <input type="text"
                               name="title"
                               id="title"
                               value="{{ old('title', $isEdit ? $movie->title : '') }}"
                               class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                               placeholder="Nhập tiêu đề phim"
                               required>
                        @error('title')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Thể loại -->
                    <div>
                        <label for="genres" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Thể loại</label>
                        <select name="genres[]"
                                id="genres"
                                class="text-sm block w-full"
                                multiple
                                data-plugin-tomSelect
                                data-option-removeButton>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->id }}"
                                        {{ $isEdit && in_array($genre->id, old('genres', $movie->genres->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $genre->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('genres')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Danh mục -->
                    <div>
                        <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Danh mục</label>
                        <select name="category_id"
                                id="category_id"
                                class="text-sm rounded-lg block w-full"
                                data-plugin-tomSelect>
                            <option value="">Chọn danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        {{ $isEdit && old('category_id', $movie->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Layout 2 cột -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Năm phát hành -->
                        <div>
                            <label for="release_year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Năm phát hành</label>
                            <input type="number"
                                   name="release_year"
                                   id="release_year"
                                   min="1900"
                                   max="{{ date('Y') + 1 }}"
                                   value="{{ old('release_year', $isEdit ? $movie->release_year : '') }}"
                                   class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                   placeholder="VD: 2024">
                        </div>

                        <!-- Thời lượng -->
                        <div>
                            <label for="duration" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Thời lượng (phút)</label>
                            <input type="number"
                                   name="duration"
                                   id="duration"
                                   min="1"
                                   value="{{ old('duration', $isEdit ? $movie->duration : '') }}"
                                   class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                   placeholder="VD: 120">
                        </div>

                        <!-- Đánh giá -->
                        <div>
                            <label for="rating" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Đánh giá (0-10)</label>
                            <input type="number"
                                   name="rating"
                                   id="rating"
                                   min="0"
                                   max="10"
                                   step="0.1"
                                   value="{{ old('rating', $isEdit ? $movie->rating : '') }}"
                                   class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                   placeholder="VD: 8.5">
                        </div>

                        <!-- Độ tuổi -->
                        <div>
                            <label for="age_rating" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Độ tuổi</label>
                            <select name="age_rating"
                                    id="age_rating"
                                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    required>
                                <option value="">Chọn độ tuổi</option>
                                @foreach(App\Models\Movie::AGE_RATINGS as $rating)
                                    <option value="{{ $rating }}" {{ old('age_rating', $isEdit ? $movie->age_rating : '') == $rating ? 'selected' : '' }}>
                                        {{ $rating }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quốc gia -->
                        <div>
                            <label for="country" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quốc gia</label>
                            <select name="country"
                                    id="country"
                                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    required>
                                <option value="">Chọn quốc gia</option>
                                @foreach(App\Models\Movie::fetchCountries() as $code => $country)
                                    <option value="{{ $code }}" {{ old('country', $isEdit ? $movie->country : '') == $code ? 'selected' : '' }}>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Trạng thái -->
                        <div>
                            <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Trạng thái</label>
                            <select name="status"
                                    id="status"
                                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    required>
                                <option value="">Chọn trạng thái</option>
                                @foreach(App\Models\Movie::STATUSES as $status => $label)
                                    <option value="{{ $status }}" {{ old('status', $isEdit ? $movie->status : '') == $status ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Mô tả -->
                    <div>
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mô tả</label>
                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                  placeholder="Nhập mô tả phim">{{ old('description', $isEdit ? $movie->description : '') }}</textarea>
                    </div>

                    <!-- Ảnh thu nhỏ -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ảnh thu nhỏ</label>
                        <div class="flex items-center space-x-4">
                            <div id="thumbnail-preview" class="relative w-40 h-24 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                @if($isEdit && $movie->thumbnail)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($movie->thumbnail) }}"
                                         alt="Ảnh thu nhỏ"
                                         class="w-full h-full object-cover">
                                @endif
                            </div>
                            <label class="flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 dark:text-white dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tải ảnh lên
                                <input type="file"
                                       name="thumbnail"
                                       accept="image/*"
                                       class="hidden"
                                       onchange="previewImage(this)">
                            </label>
                        </div>
                    </div>

                    <!-- Banner -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Banner ảnh</label>
                        <div class="flex items-center space-x-4">
                            <div id="banner-preview" class="relative w-96 h-40 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                @if($isEdit && $movie->banner)
                                    <img src="{{ Storage::url($movie->banner) }}"
                                         alt="Banner ảnh"
                                         class="w-full h-full object-cover">
                                @endif
                            </div>
                            <label class="flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 dark:text-white dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tải banner
                                <input type="file"
                                       name="banner"
                                       accept="image/*"
                                       class="hidden"
                                       onchange="previewBanner(this)">
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nguồn video -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Nguồn phim</h3>
                    <button type="button"
                            onclick="{{ $isEdit ? 'addNewSource()' : 'addSource()' }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Thêm nguồn
                    </button>
                </div>

                <div id="{{ $isEdit ? 'existing-sources' : 'sources-container' }}" class="space-y-4">
                    @if($isEdit)
                        @foreach($movie->sources as $index => $source)
                            <div class="source-item bg-gray-50 dark:bg-gray-700 p-6 rounded-lg" id="source-{{ $index }}">
                                <div class="flex justify-between items-center mb-6">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">Source #{{ $index + 1 }}</h4>
                                    <button type="button" onclick="removeSource({{ $index }})"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Loại nguồn</label>
                                        <div class="relative">
                                            <select name="sources[{{ $index }}][source_type]"
                                                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 appearance-none"
                                                    required>
                                                <option value="">Chọn loại</option>
                                                <option value="youtube" {{ $source->source_type == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                                <option value="direct" {{ $source->source_type == 'direct' ? 'selected' : '' }}>Direct Upload</option>
                                                <option value="fshare" {{ $source->source_type == 'fshare' ? 'selected' : '' }}>FShare</option>
                                                <option value="gdrive" {{ $source->source_type == 'gdrive' ? 'selected' : '' }}>Google Drive</option>
                                                <option value="twitter" {{ $source->source_type == 'twitter' ? 'selected' : '' }}>Twitter</option>
                                                <option value="facebook" {{ $source->source_type == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                                <option value="tiktok" {{ $source->source_type == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Chất lượng</label>
                                        <div class="relative">
                                            <select name="sources[{{ $index }}][quality]"
                                                    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 appearance-none"
                                                    required>
                                                <option value="">Chọn chất lượng</option>
                                                <option value="360p" {{ $source->quality == '360p' ? 'selected' : '' }}>360p</option>
                                                <option value="480p" {{ $source->quality == '480p' ? 'selected' : '' }}>480p</option>
                                                <option value="720p" {{ $source->quality == '720p' ? 'selected' : '' }}>720p</option>
                                                <option value="1080p" {{ $source->quality == '1080p' ? 'selected' : '' }}>1080p</option>
                                                <option value="4k" {{ $source->quality == '4k' ? 'selected' : '' }}>4K</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">URL nguồn</label>
                                        <input type="text"
                                               name="sources[{{ $index }}][source_url]"
                                               value="{{ $source->source_url }}"
                                               class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5"
                                               placeholder="Nhập URL nguồn"
                                               required>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('movies.management.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                    Hủy
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
                    {{ $isEdit ? 'Cập nhật phim' : 'Tạo phim' }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
		document.addEventListener('DOMContentLoaded', function() {
			// Set initial source count based on existing sources
			let sourceCount = @json($isEdit ? $movie->sources->count() : 0);

			// Image preview functionality
			window.previewImage = function(input) {
				if (!input.files?.[0]) return;
				const previewContainer = document.querySelector('#thumbnail-preview');
				let previewImage = previewContainer.querySelector('img');

				if (!previewImage) {
					previewImage = document.createElement('img');
					previewImage.className = 'w-full h-full object-cover';
					previewContainer.appendChild(previewImage);
				}
				previewImage.src = URL.createObjectURL(input.files[0]);
			};

			window.previewBanner = function(input) {
				if (!input.files?.[0]) return;
				const previewContainer = document.querySelector('#banner-preview');
				let previewImage = previewContainer.querySelector('img');

				if (!previewImage) {
					previewImage = document.createElement('img');
					previewImage.className = 'w-full h-full object-cover';
					previewContainer.appendChild(previewImage);
				}
				previewImage.src = URL.createObjectURL(input.files[0]);
			};

			// Source template
			function getSourceTemplate(index) {
				return `
                    <div class="source-item bg-gray-50 dark:bg-gray-700 p-6 rounded-lg transition-all duration-200" id="source-${index}">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">Source #${index + 1}</h4>
                            <button type="button" onclick="removeSource(${index})"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Loại nguồn</label>
                                <div class="relative">
                                    <select name="sources[${index}][source_type]"
                                            class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 appearance-none"
                                            required>
                                        <option value="">Chọn loại</option>
                                        <option value="youtube">YouTube</option>
                                        <option value="direct">Direct Upload</option>
                                        <option value="fshare">FShare</option>
                                        <option value="gdrive">Google Drive</option>
                                        <option value="twitter">Twitter</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="tiktok">TikTok</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Chất lượng</label>
                                <div class="relative">
                                    <select name="sources[${index}][quality]"
                                            class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 appearance-none"
                                            required>
                                        <option value="">Chọn chất lượng</option>
                                        <option value="360p">360p</option>
                                        <option value="480p">480p</option>
                                        <option value="720p">720p</option>
                                        <option value="1080p">1080p</option>
                                        <option value="4k">4K</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">URL nguồn</label>
                                <input type="text"
                                       name="sources[${index}][source_url]"
                                       class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5"
                                       placeholder="Nhập URL nguồn"
                                       required>
                            </div>
                        </div>
                    </div>
                `;
			}

			// Add source to container with animation
			function addSourceToContainer(container) {
				const newSource = document.createElement('div');
				newSource.innerHTML = getSourceTemplate(sourceCount);
				const sourceElement = newSource.firstElementChild;

				sourceElement.style.opacity = '0';
				sourceElement.style.transform = 'translateY(20px)';

				container.appendChild(sourceElement);

				// Trigger animation
				requestAnimationFrame(() => {
					sourceElement.style.opacity = '1';
					sourceElement.style.transform = 'translateY(0)';
				});

				sourceCount++;
			}

			// Add source for new movie
			window.addSource = function() {
				const container = document.getElementById('sources-container');
				addSourceToContainer(container);
			};

			// Add source for existing movie
			window.addNewSource = function() {
				const container = document.getElementById('existing-sources');
				addSourceToContainer(container);
			};

			// Remove source with animation
			window.removeSource = function(sourceId) {
				const container = document.getElementById(`source-${sourceId}`);
				if (!container) return;

				container.style.opacity = '0';
				container.style.transform = 'translateY(20px)';
				setTimeout(() => container.remove(), 200);
			};

			// Initialize default source for Create mode
			if (document.getElementById('sources-container') && !sourceCount) {
				addSource();
			}

			// Initialize TomSelect for select elements
			document.querySelectorAll('[data-plugin-tomSelect]').forEach(select => {
				new TomSelect(select, {
					plugins: select.hasAttribute('data-option-removeButton') ? ['remove_button'] : [],
					create: select.hasAttribute('data-option-create')
				});
			});
		});
    </script>
@endpush