@extends('layouts.admin')

@section('title', $movieAd->exists ? 'Chỉnh sửa quảng cáo: ' . $movieAd->name : 'Thêm quảng cáo mới')
@section('header', $movieAd->exists ? 'Chỉnh sửa quảng cáo: ' . $movieAd->name : 'Thêm quảng cáo mới')

@push('styles')
    <style>
        .preview-container {
            transition: transform 0.2s;
        }
        .preview-container:hover {
            transform: scale(1.02);
        }
    </style>
@endpush

@section('content')
    <div class="max-w-6xl mx-auto px-4">
        <form action="{{ $movieAd->exists ? route('admin.movie-ads.update', $movieAd) : route('admin.movie-ads.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-8">
            @csrf
            @if($movieAd->exists) @method('PUT') @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 space-y-6">
                            <!-- Name Input -->
                            <div>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       placeholder="Tên quảng cáo..."
                                       value="{{ old('name', $movieAd->name) }}"
                                       class="w-full px-4 py-3 text-xl border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500"
                                       required>
                                @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ad Type Selection -->
                            <div class="grid grid-cols-2 gap-4">
                                @foreach(App\Models\MovieAd::TYPES as $value => $label)
                                    <label class="relative flex cursor-pointer rounded-lg border-2 p-4 focus:outline-none transition-all duration-200
                                                    {{ old('type', $movieAd->type ?? 'image') === $value
                                                        ? 'border-blue-500 bg-blue-50'
                                                        : 'border-gray-200 hover:border-blue-200' }}"
                                    >
                                        <input type="radio"
                                               name="type"
                                               value="{{ $value }}"
                                               class="sr-only"
                                               onchange="handleTypeChange(this)"
                                                {{ old('type', $movieAd->type ?? 'image') === $value ? 'checked' : '' }}>
                                        <div class="flex items-center w-full">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center type-icon
                                                            {{ old('type', $movieAd->type ?? 'image') === $value
                                                                ? 'bg-blue-500 text-white'
                                                                : 'bg-gray-100 text-gray-500' }}"
                                                >
                                                    @if($value === 'image')
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-4 flex flex-col">
                                                <span class="block text-sm font-medium type-text
                                                    {{ old('type', $movieAd->type ?? 'image') === $value
                                                        ? 'text-blue-600'
                                                        : 'text-gray-900' }}">
                                                    {{ $label }}
                                                </span>
                                                <span class="mt-1 text-xs text-gray-500">
                                                            {{ $value === 'image' ? 'PNG, JPG, GIF' : 'MP4, WEBM' }}
                                                </span>
                                            </div>

                                            <!-- Selected indicator -->
                                            @if(old('type', $movieAd->type ?? 'image') === $value)
                                                <div class="absolute top-2 right-2 selected-indicator">
                                                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                              clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <!-- Content Upload -->
                            <div class="space-y-4">
                                <label class="block">
                                    <span class="text-sm font-medium text-gray-700">Nội dung quảng cáo</span>
                                    <div class="mt-2">
                                        <div class="flex items-center justify-center w-full">
                                            <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                                <div class="preview-container absolute inset-0 hidden">
                                                    <!-- Preview will be inserted here via JavaScript -->
                                                </div>
                                                <div class="content-upload flex flex-col items-center justify-center pt-5 pb-6">
                                                    <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mb-2 text-sm text-gray-500">
                                                        <span class="font-semibold">Click để tải lên</span> hoặc kéo thả file
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        PNG, JPG, GIF up to 10MB (Ảnh) hoặc MP4, WEBM up to 50MB (Video)
                                                    </p>
                                                </div>
                                                <input type="file"
                                                       name="content"
                                                       class="hidden"
                                                       accept="{{ old('type', $movieAd->type) === 'image' ? 'image/*' : 'video/*' }}"
                                                       onchange="handleFilePreview(this)">
                                            </label>
                                        </div>
                                    </div>
                                </label>
                                @error('content')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Duration Fields - Update this section in your template -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Thời điểm hiển thị (%)
                                    </label>
                                    <div class="mt-2">
                                        <div class="relative">
                                            <input type="range"
                                                   name="display_time"
                                                   min="0"
                                                   max="100"
                                                   step="1"
                                                   value="{{ old('display_time', $movieAd->display_time ?? 0) }}"
                                                   class="w-full h-2 rounded-lg appearance-none cursor-pointer bg-gray-200"
                                                   oninput="this.nextElementSibling.value = this.value + '%'">
                                            <output class="absolute -top-7 right-0 text-sm">{{ old('display_time', $movieAd->display_time ?? 0) }}%</output>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-700 duration-label">
                                        <!-- Label will be updated via JavaScript -->
                                        Thời gian hiển thị
                                    </label>
                                    <div class="mt-2">
                                        <input type="number"
                                               name="duration"
                                               min="0"
                                               value="{{ old('duration', $movieAd->duration ?? 5) }}"
                                               class="w-full rounded-lg border-gray-300">
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500 duration-help">
                                        <!-- Help text will be updated via JavaScript -->
                                    </p>
                                </div>
                            </div>

                            <!-- Click URL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Liên kết khi click (tuỳ chọn)
                                </label>
                                <div class="mt-2">
                                    <input type="url"
                                           name="click_url"
                                           placeholder="https://"
                                           value="{{ old('click_url', $movieAd->click_url) }}"
                                           class="w-full rounded-lg border-gray-300">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Publishing Options -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-medium text-gray-900">Xuất bản</h3>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                {{ $movieAd->exists ? 'Cập nhật' : 'Xuất bản' }}
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Status Toggle -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">Trạng thái</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           name="is_enabled"
                                           value="1"
                                           class="sr-only peer"
                                            {{ old('is_enabled', $movieAd->is_enabled) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Creation Date -->
                            @if($movieAd->exists)
                                <div class="pt-4 border-t border-gray-200">
                                    <span class="text-sm text-gray-600">
                                        Đã tạo: {{ $movieAd->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Preview Current Content -->
                    @if($movieAd->exists && $movieAd->content_url)
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="font-medium text-gray-900 mb-4">Nội dung hiện tại</h3>
                            <div class="relative rounded-lg overflow-hidden">
                                @if($movieAd->type === 'image')
                                    <img src="{{ $movieAd->content_url }}"
                                         alt="{{ $movieAd->name }}"
                                         class="w-full h-auto">
                                @else
                                    <video controls class="w-full h-auto">
                                        <source src="{{ $movieAd->content_url }}" type="video/mp4">
                                    </video>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
		function updateDurationField(type) {
			const durationLabel = document.querySelector('.duration-label');
			const durationHelp = document.querySelector('.duration-help');
			const durationInput = document.querySelector('input[name="duration"]');

			if (type === 'image') {
				durationLabel.textContent = 'Thời gian hiển thị (giây)';
				durationHelp.textContent = 'Thời gian hiển thị ảnh quảng cáo';
				durationInput.min = '1';
				durationInput.value = Math.max(1, durationInput.value);
			} else {
				durationLabel.textContent = 'Thời gian bỏ qua (giây)';
				durationHelp.textContent = 'Đặt 0 để bắt buộc xem hết video quảng cáo';
				durationInput.min = '0';
			}
		}

		function handleTypeChange(input) {
			const durationType = input.value;
			updateDurationField(durationType);
			const fileInput = document.querySelector('input[type="file"]');

			// Update file input accept attribute
			fileInput.setAttribute('accept', durationType === 'image' ? 'image/*' : 'video/*');

			// Update visual states for all type options
			document.querySelectorAll('[name="type"]').forEach(radio => {
				const label = radio.closest('label');
				const icon = label.querySelector('.type-icon');
				const textSpan = label.querySelector('.type-text');
				const isSelected = radio.value === durationType;

				// Update label classes
				label.classList.toggle('border-blue-500', isSelected);
				label.classList.toggle('bg-blue-50', isSelected);
				label.classList.toggle('border-gray-200', !isSelected);

				// Update icon classes
				icon.classList.toggle('bg-blue-500', isSelected);
				icon.classList.toggle('text-white', isSelected);
				icon.classList.toggle('bg-gray-100', !isSelected);
				icon.classList.toggle('text-gray-500', !isSelected);

				// Update text classes
				textSpan.classList.toggle('text-blue-600', isSelected);
				textSpan.classList.toggle('text-gray-900', !isSelected);

				// Handle selected indicator
				let indicator = label.querySelector('.selected-indicator');
				if (isSelected) {
					if (!indicator) {
						indicator = document.createElement('div');
						indicator.className = 'absolute top-2 right-2 selected-indicator';
						indicator.innerHTML = `
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        `;
						label.appendChild(indicator);
					}
				} else if (indicator) {
					indicator.remove();
				}
			});

			// Reset file input if type changes
			if (fileInput.value) {
				const previewContainer = fileInput.closest('label').querySelector('.preview-container');
				const uploadContainer = fileInput.closest('label').querySelector('.content-upload');

				fileInput.value = '';
				previewContainer.classList.add('hidden');
				previewContainer.innerHTML = '';
				uploadContainer.classList.remove('hidden');
			}
		}

		function handleFilePreview(input) {
			if (input.files && input.files[0]) {
				const file = input.files[0];
				const reader = new FileReader();
				const previewContainer = input.closest('label').querySelector('.preview-container');
				const uploadContainer = input.closest('label').querySelector('.content-upload');

				reader.onload = function(e) {
					previewContainer.innerHTML = '';

					if (file.type.startsWith('image/')) {
						const img = document.createElement('img');
						img.src = e.target.result;
						img.className = 'w-full h-full object-contain';
						previewContainer.appendChild(img);
					} else if (file.type.startsWith('video/')) {
						const video = document.createElement('video');
						video.src = e.target.result;
						video.className = 'w-full h-full object-contain';
						video.controls = true;
						previewContainer.appendChild(video);
					}

					previewContainer.classList.remove('hidden');
					uploadContainer.classList.add('hidden');

					const removeButton = document.createElement('button');
					removeButton.type = 'button';
					removeButton.className = 'absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2';
					removeButton.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    `;
					removeButton.onclick = function() {
						input.value = '';
						previewContainer.classList.add('hidden');
						previewContainer.innerHTML = '';
						uploadContainer.classList.remove('hidden');
					};
					previewContainer.appendChild(removeButton);
				}

				reader.readAsDataURL(file);
			}
		}

		// Initialize drag and drop handlers
		function initializeDragAndDrop() {
			const uploadLabel = document.querySelector('.content-upload').closest('label');

			uploadLabel.addEventListener('dragover', function(e) {
				e.preventDefault();
				this.classList.add('border-blue-500', 'bg-blue-50');
			});

			uploadLabel.addEventListener('dragleave', function(e) {
				e.preventDefault();
				this.classList.remove('border-blue-500', 'bg-blue-50');
			});

			uploadLabel.addEventListener('drop', function(e) {
				e.preventDefault();
				this.classList.remove('border-blue-500', 'bg-blue-50');

				const input = this.querySelector('input[type="file"]');
				const files = e.dataTransfer.files;

				if (files.length) {
					const file = files[0];
					const fileType = document.querySelector('input[name="type"]:checked').value;

					if ((fileType === 'image' && file.type.startsWith('image/')) ||
						(fileType === 'video' && file.type.startsWith('video/'))) {
						input.files = files;
						handleFilePreview(input);
					} else {
						showError(`Please upload ${fileType} files only`);
					}
				}
			});
		}

		function showError(message) {
			const errorDiv = document.createElement('div');
			errorDiv.className = 'fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg';
			errorDiv.textContent = message;
			document.body.appendChild(errorDiv);
			setTimeout(() => errorDiv.remove(), 3000);
		}

		// Initialize everything on page load
		document.addEventListener('DOMContentLoaded', function() {
			const existingContent = document.querySelector('#existing-content');
			if (existingContent) {
				const previewContainer = document.querySelector('.preview-container');
				const uploadContainer = document.querySelector('.content-upload');

				if (previewContainer && uploadContainer) {
					previewContainer.innerHTML = existingContent.innerHTML;
					previewContainer.classList.remove('hidden');
					uploadContainer.classList.add('hidden');
				}
			}

			// Initialize type selection and duration field
			const initialType = document.querySelector('input[name="type"]:checked')?.value || 'image';
			updateDurationField(initialType);

			initializeDragAndDrop();
		});
    </script>
@endpush