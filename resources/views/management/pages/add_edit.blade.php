@extends('layouts.admin')

@section('title', $page->exists ? 'Chỉnh sửa trang: ' . $page->title : 'Thêm trang mới')
@section('header', $page->exists ? 'Chỉnh sửa trang: ' . $page->title : 'Thêm trang mới')

@section('content')
    <div class="max-w-6xl mx-auto px-4">
        <form action="{{ $page->exists ? route('management.pages.update', $page) : route('management.pages.store') }}"
              method="POST"
              class="space-y-8">
            @csrf
            @if($page->exists) @method('PUT') @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title Input -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <input type="text"
                               name="title"
                               id="title"
                               placeholder="Tiêu đề trang..."
                               value="{{ old('title', $page->title) }}"
                               class="w-full px-6 py-4 text-xl border-0 focus:ring-0 placeholder-gray-400"
                               required>
                        @error('title')
                        <p class="px-6 py-2 text-sm text-red-600 bg-red-50">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content Editor -->
                    <div>
                        <x-head.tinymce-config/>
                        <textarea name="content"
                                  id="tinymce-editor"
                                  rows="20"
                                  placeholder="Viết nội dung trang của bạn tại đây..."
                                  class="w-full border-0 focus:ring-0 resize-none"
                        >{{ old('content', $page->content) }}</textarea>
                        @error('content')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Publishing Options -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-medium">Xuất bản</h3>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                {{ $page->exists ? 'Cập nhật' : 'Xuất bản' }}
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">Trạng thái</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           name="is_active"
                                           value="1"
                                           class="sr-only peer"
                                            {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            @if($page->exists)
                                <div class="py-4 border-t border-gray-100">
                                    <span class="text-sm text-gray-600">Đã tạo: {{ $page->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="font-medium mb-4">SEO</h3>
                        <div class="space-y-4">
                            <div>
                                <input type="text"
                                       name="meta_title"
                                       placeholder="Tiêu đề SEO"
                                       value="{{ old('meta_title', $page->meta_title) }}"
                                       class="w-full rounded-lg border-gray-300 text-sm">
                            </div>

                            <div>
                                <textarea name="meta_description"
                                          rows="3"
                                          placeholder="Mô tả SEO"
                                          class="w-full rounded-lg border-gray-300 text-sm">{{ old('meta_description', $page->meta_description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slug-preview :nameId="null" :slugId="'slug-preview'" :nameInput="'title'" :initial-slug="$page->exists ? $page->slug : null" />
        </form>
    </div>
@endsection