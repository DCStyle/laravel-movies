@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.moderator')

@section('title', $category->exists ? 'Chỉnh sửa danh mục: ' . $category->name : 'Thêm danh mục mới')
@section('header', $category->exists ? 'Chỉnh sửa danh mục: ' . $category->name : 'Thêm danh mục mới')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form
                action="{{ $category->exists ? route('management.categories.update', $category) : route('management.categories.store') }}"
                method="POST"
                class="space-y-6"
        >
            @csrf
            @if($category->exists)
                @method('PUT')
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">Thông tin danh mục</h3>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 required">Tên</label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $category->name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SEO Section -->
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <h4 class="text-md font-medium text-gray-900">Thông tin SEO</h4>

                        <!-- Meta Title -->
                        <div class="mt-4">
                            <label for="meta_title" class="block text-sm font-medium text-gray-700">Tiêu đề SEO</label>
                            <input type="text"
                                   name="meta_title"
                                   id="meta_title"
                                   value="{{ old('meta_title', $category->meta_title) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">
                                Để trống nếu bạn muốn sử dụng tên danh mục làm tiêu đề SEO
                            </p>
                            @error('meta_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta Description -->
                        <div class="mt-4">
                            <label for="meta_description" class="block text-sm font-medium text-gray-700">Mô tả SEO</label>
                            <textarea name="meta_description"
                                      id="meta_description"
                                      rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('meta_description', $category->meta_description) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                Để trống nếu bạn muốn sử dụng mô tả danh mục làm mô tả SEO
                            </p>
                            @error('meta_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <x-slug-preview :nameId="'danh-muc'" :slugId="'slug-preview'" :initial-slug="$category->exists ? $category->slug : null"></x-slug-preview>

            <!-- Submit Buttons -->
            <div class="flex justify-between">
                <a href="{{ route('management.categories.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Hủy
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                    {{ $category->exists ? 'Cập nhật danh mục' : 'Thêm danh mục' }}
                </button>
            </div>
        </form>
    </div>
@endsection