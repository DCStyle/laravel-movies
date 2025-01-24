@extends('layouts.admin')

@section('title', 'Bảng điều khiển')
@section('header', 'Bảng điều khiển')

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/50">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_movies']) }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tổng số phim</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/50">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['published_movies']) }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Phim đã phát hành</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-900/50">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_views']) }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Lượt xem</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-yellow-50 dark:bg-yellow-900/50">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_mods']) }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Điều hành viên</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Movie Lists -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Latest Movies -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Phim mới nhất</h3>
                        <a href="{{ route('management.movies.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Xem tất cả</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($latest_movies as $movie)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                                        @if($movie->getThumbnail())
                                            <img src="{{ $movie->getThumbnail() }}"
                                                 alt="{{ $movie->title }}"
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $movie->title }}
                                        </p>
                                        <div class="flex items-center">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium {{ $movie->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }} rounded-full">
                                                {{ $movie->status === 'published' ? 'Đã phát hành' : 'Nháp' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $movie->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('management.movies.edit', $movie) }}"
                                       class="inline-flex items-center p-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Most Viewed Movies -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Phim xem nhiều nhất</h3>
                        <a href="{{ route('management.movies.index', ['sort' => 'views']) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Xem tất cả</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($top_movies as $movie)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                                        @if($movie->getThumbnail())
                                            <img src="{{ $movie->getThumbnail() }}"
                                                 alt="{{ $movie->title }}"
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $movie->title }}
                                    </p>
                                    <div class="flex items-center mt-1">
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
                                    <a href="{{ route('management.movies.edit', $movie) }}"
                                       class="inline-flex items-center p-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection