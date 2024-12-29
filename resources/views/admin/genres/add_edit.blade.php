@extends('layouts.admin')

@section('title', $genre->exists ? 'Chỉnh sửa thể loại: ' . $genre->name : 'Thêm thể loại mới')
@section('header', $genre->exists ? 'Chỉnh sửa thể loại: ' . $genre->name : 'Thêm thể loại mới')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form
                action="{{ $genre->exists ? route('admin.genres.update', $genre) : route('admin.genres.store') }}"
                method="POST"
                class="space-y-6"
        >
            @csrf
            @if($genre->exists)
                @method('PUT')
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">Thông tin thể loại</h3>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 required">Tên</label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $genre->name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($genre->exists)
                        <!-- Slug (only for edit) -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                            <input type="text"
                                   id="slug"
                                   value="{{ $genre->slug }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50"
                                   disabled>
                            <p class="mt-1 text-xs text-gray-500">
                                Slug được tự động tạo từ tên
                            </p>
                        </div>
                    @endif

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $genre->description) }}</textarea>
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
                                   value="{{ old('meta_title', $genre->meta_title) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">
                                Để trống nếu bạn muốn sử dụng tên thể loại làm tiêu đề SEO
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
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('meta_description', $genre->meta_description) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                Để trống nếu bạn muốn sử dụng mô tả thể loại làm mô tả SEO
                            </p>
                            @error('meta_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @if($genre->exists)
                        <!-- Statistics (only for edit) -->
                        <div class="pt-4 mt-4 border-t border-gray-200">
                            <h4 class="text-md font-medium text-gray-900">Thống kê</h4>
                            <div class="mt-4 grid grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Tổng số phim</div>
                                    <div class="mt-1 text-2xl font-semibold">
                                        {{ $genre->movies()->count() }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Ngày tạo</div>
                                    <div class="mt-1">
                                        {{ $genre->created_at->format('d M, Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <x-slug-preview :nameId="'the-loai'" :slugId="'slug-preview'" :initial-slug="$genre->exists ? $genre->slug : null"></x-slug-preview>

            <!-- Submit Buttons -->
            <div class="flex {{ $genre->exists ? 'justify-between' : 'justify-end' }} space-x-4">
                @if($genre->exists)
                    <div>
                        <!-- View Movies Button -->
                        <a href="{{ route('movies.index', ['genre' => $genre->slug]) }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                           target="_blank">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Xem phim trong thể loại này
                        </a>
                    </div>
                @endif

                <div class="flex space-x-4">
                    <a href="{{ route('admin.genres.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Hủy
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                        {{ $genre->exists ? 'Cập nhật thể loại' : 'Tạo thể loại' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection