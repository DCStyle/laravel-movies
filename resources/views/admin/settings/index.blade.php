@extends('layouts.admin')

@section('title', 'Cài đặt trang web')
@section('header', 'Cài đặt')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-800 border-b pb-2">Thông tin trang web</h3>

                    <!-- Site Name -->
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700">Tên trang web</label>
                        <input type="text"
                               name="site_name"
                               id="site_name"
                               value="{{ old('site_name', setting('site_name')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('site_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site H1 tag -->
                    <div>
                        <label for="site_h1_tag" class="block text-sm font-medium text-gray-700">Thẻ H1</label>
                        <input type="text"
                               name="site_h1_tag"
                               id="site_h1_tag"
                               value="{{ old('site_h1_tag', setting('site_h1_tag')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('site_h1_tag')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Description -->
                    <div>
                        <label for="site_description" class="block text-sm font-medium text-gray-700">Mô tả trang web</label>
                        <textarea name="site_description"
                                  id="site_description"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('site_description', setting('site_description')) }}</textarea>
                        @error('site_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Meta Keywords -->
                    <div>
                        <label for="site_meta_keywords" class="block text-sm font-medium text-gray-700">Từ khóa meta</label>
                        <input type="text"
                               name="site_meta_keywords"
                               id="site_meta_keywords"
                               value="{{ old('site_meta_keywords', setting('site_meta_keywords')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('site_meta_keywords')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Logo -->
                    <div>
                        <label for="site_logo" class="block text-sm font-medium text-gray-700">Logo trang web</label>
                        <input type="file"
                               name="site_logo"
                               id="site_logo"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               onchange="previewImage(this, 'site_logo_preview')">
                        <div id="site_logo_preview" class="mt-2">
                            @if(setting('site_logo'))
                                <img src="{{ Storage::url(setting('site_logo')) }}" alt="Site Logo" class="w-auto h-24 object-cover rounded-md">
                            @endif
                        </div>
                        @error('site_logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site Favicon -->
                    <div>
                        <label for="site_favicon" class="block text-sm font-medium text-gray-700">Favicon</label>
                        <input type="file"
                               name="site_favicon"
                               id="site_favicon"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               onchange="previewImage(this, 'site_favicon_preview')">
                        <div id="site_favicon_preview" class="mt-2">
                            @if(setting('site_favicon'))
                                <img src="{{ Storage::url(setting('site_favicon')) }}" alt="Site Favicon" class="w-8 h-8 object-cover rounded-md">
                            @endif
                        </div>
                        @error('site_favicon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site OG Image -->
                    <div>
                        <label for="site_og_image" class="block text-sm font-medium text-gray-700">Ảnh OG</label>
                        <input type="file"
                               name="site_og_image"
                               id="site_og_image"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               onchange="previewImage(this, 'site_og_image_preview')">
                        <div id="site_og_image_preview" class="mt-2">
                            @if(setting('site_og_image'))
                                <img src="{{ Storage::url(setting('site_og_image')) }}" alt="Site OG Image" class="w-48 h-48 object-cover rounded-md">
                            @endif
                        </div>
                        @error('site_og_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md shadow-sm hover:bg-blue-600">
                    Lưu cài đặt
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
		function previewImage(input, previewId) {
			const preview = document.getElementById(previewId);
			if (input.files && input.files[0]) {
				const reader = new FileReader();
				reader.onload = (e) => {
					preview.innerHTML = `<img src="${e.target.result}" class="w-auto h-24 object-cover rounded-md">`;
				};
				reader.readAsDataURL(input.files[0]);
			} else {
				preview.innerHTML = '';
			}
		}
    </script>
@endpush
