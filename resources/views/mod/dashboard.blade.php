@extends('layouts.moderator')

@section('title', 'Bảng điều khiển')
@section('header', 'Bảng điều khiển Điều hành viên')

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total Movies -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/50">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_movies']) }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tổng số phim</p>
                    </div>
                </div>
            </div>

            <!-- Published Movies -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/50">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['published_movies']) }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Phim đã phát hành</p>
                    </div>
                </div>
            </div>

            <!-- Total Views -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-900/50">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_views']) }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Lượt xem</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mt-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Hành động nhanh</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('movies.management.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Thêm phim mới
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Movies -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mt-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Phim gần đây</h3>
                    <a href="{{ route('movies.management.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Xem tất cả</a>
                </div>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($latest_movies as $movie)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($movie->thumbnail)
                                    <img src="{{ $movie->thumbnail }}"
                                         alt="{{ $movie->title }}"
                                         class="w-16 h-10 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $movie->title }}</p>
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $movie->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                        {{ $movie->status === 'published' ? 'Đã phát hành' : 'Nháp' }}
                                    </span>
                                </div>
                                <div class="flex items-center mt-1 space-x-4">
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ number_format($movie->views_count) }} lượt xem
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="{{ route('mod.movies.edit', $movie) }}"
                                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Chỉnh sửa
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Chưa có phim nào</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Bắt đầu bằng cách thêm phim đầu tiên của bạn.</p>
                        <div class="mt-6">
                            <a href="{{ route('movies.management.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Thêm phim mới
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection