@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.moderator')

@section('title', 'Quản lý phim')
@section('header', 'Danh sách phim')

@section('content')
    <!-- Actions Section -->
    <div class="flex flex-wrap justify-between items-center mb-8">
        <!-- Add New Movie -->
        <a href="{{ route('management.movies.create') }}"
           class="flex items-center px-5 py-3 bg-gradient-to-r from-blue-500 to-emerald-600 text-white text-sm font-medium rounded-md shadow-md hover:from-blue-600 hover:to-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Thêm phim mới
        </a>

        <!-- Search Bar -->
        <div class="flex items-center space-x-4">
            <form method="GET" class="relative w-full max-w-sm">
                <input type="text"
                       name="search"
                       placeholder="Tìm kiếm phim..."
                       value="{{ request('search') }}"
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-full shadow-md focus:ring focus:ring-indigo-500 focus:ring-opacity-50 text-sm placeholder-gray-400">
                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
            </form>

            <select name="type"
                    class="rounded-lg border-gray-300 text-sm"
                    onchange="this.form.submit()">
                <option value="">Tất cả loại</option>
                <option value="single" {{ request('type') === 'single' ? 'selected' : '' }}>Phim lẻ</option>
                <option value="series" {{ request('type') === 'series' ? 'selected' : '' }}>Phim bộ</option>
            </select>
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-hidden bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tiêu đề</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Loại</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Danh mục</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Thể loại</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Năm phát hành</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Trạng thái</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Hành động</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @foreach($movies as $movie)
                <tr class="hover:bg-gray-50 transition-all duration-150">
                    <!-- Title and Thumbnail -->
                    <td class="px-6 py-4 flex items-center">
                        <div class="w-12 h-12 flex-shrink-0">
                            @if($movie->getThumbnail())
                                <img class="w-12 h-12 object-cover rounded-md shadow-md hover:scale-105 transition-transform duration-300"
                                     src="{{ $movie->getThumbnail() }}"
                                     alt="{{ $movie->title }}">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded-md shadow flex items-center justify-center text-gray-400">
                                    <i class="fas fa-film"></i>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $movie->title }}

                                @if($movie->title_en)
                                    <span class="inline-block ml-2 text-xs text-gray-500">({{ $movie->title_en }})</span>
                                @endif
                            </p>
                            @if($movie->type === 'series')
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $movie->total_seasons ?? $movie->seasons->count() }} Mùa
                                    • {{ $movie->total_episodes ?? $movie->seasons->sum(fn($s) => $s->episodes->count()) }} Tập
                                </p>
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-700">
                        @if($movie->type === 'series')
                            <span class="flex items-center px-3 py-1 text-xs font-medium text-purple-800 bg-purple-100 rounded-full">
                                <i class="fas fa-tv mr-1"></i> Series
                            </span>
                        @else
                            <span class="flex items-center px-3 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                                <i class="fas fa-film mr-1"></i> Phim lẻ
                            </span>
                        @endif
                    </td>

                    <!-- Category -->
                    <td class="px-6 py-4 text-sm text-gray-700">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-folder text-gray-400"></i>
                            <span>{{ $movie->category->name }}</span>
                        </div>
                    </td>

                    <!-- Genres -->
                    <td class="px-6 py-4 space-x-1">
                        @foreach($movie->genres as $genre)
                            <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full shadow-sm hover:bg-blue-200 transition-colors">
                                {{ $genre->name }}
                            </span>
                        @endforeach
                    </td>

                    <!-- Release Year -->
                    <td class="px-6 py-4 text-sm text-gray-700">
                        <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>{{ $movie->release_year }}
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4">
                        @if($movie->status == 'published')
                            <span class="flex items-center px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-md shadow-sm">
                                <i class="fas fa-check-circle mr-2"></i> Xuất bản
                            </span>
                        @else
                            <span class="flex items-center px-3 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-md shadow-sm">
                                <i class="fas fa-file-alt mr-2"></i> Nháp
                            </span>
                        @endif
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 text-right text-sm">
                        <div class="relative inline-block text-left">
                            <button data-dropdown-toggle="dropdown-movie-{{ $movie->id }}"
                                    class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="dropdown-movie-{{ $movie->id }}" class="mt-2 w-36 bg-white border border-gray-200 rounded-md shadow-lg z-50 hidden group-hover:block">
                                @if($movie->type === 'series')
                                    <a href="{{ route('management.seasons.index', $movie) }}"
                                       class="block px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-film mr-2"></i> Quản lý các mùa / tập
                                    </a>
                                @endif
                                <a href="{{ route('management.movies.edit', $movie) }}"
                                   class="block px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 hover:text-indigo-600">
                                    <i class="fas fa-edit mr-2"></i> Chỉnh sửa
                                </a>
                                @if(auth()->user()->hasRole('admin'))
                                    <form action="{{ route('management.movies.destroy', $movie) }}" method="POST" class="block w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-gray-100 hover:text-red-800 transition-colors"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa phim này?')">
                                            <i class="fas fa-trash-alt mr-2"></i> Xóa
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        {{ $movies->links('pagination::tailwind') }}
    </div>
@endsection
