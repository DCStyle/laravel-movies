@extends('layouts.admin')

@section('title', 'Quản lý danh mục')
@section('header', 'Quản lý danh mục')

@section('content')
    <div class="max-w-7xl mx-auto px-4 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <a href="{{ route('management.categories.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Thêm danh mục mới
            </a>

            <form method="GET" class="flex w-full md:w-auto">
                <div class="relative flex-1 md:w-80">
                    <input type="text"
                           name="search"
                           placeholder="Tìm kiếm danh mục..."
                           value="{{ request('search') }}"
                           class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tên</span>
                        </th>
                        <th class="px-6 py-3 text-left hidden md:table-cell">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Slug</span>
                        </th>
                        <th class="px-6 py-3 text-left hidden sm:table-cell">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Số lượng phim</span>
                        </th>
                        <th class="px-6 py-3 text-left hidden lg:table-cell">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ngày tạo</span>
                        </th>
                        <th class="px-6 py-3 text-right">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hành động</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $category->name }}
                                </div>
                                @if($category->description)
                                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                        {{ $category->description }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $category->slug }}</div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ number_format($category->movies_count) }} phim
                                    </span>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $category->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('management.categories.edit', $category) }}"
                                       class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('management.categories.destroy', $category) }}"
                                          method="POST"
                                          class="inline-block"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                    </svg>
                                    <p class="mt-4 text-gray-500 dark:text-gray-400">Không tìm thấy danh mục nào</p>
                                    <a href="{{ route('admin.categories.create') }}"
                                       class="mt-2 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                        Tạo danh mục mới ngay
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($categories->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection