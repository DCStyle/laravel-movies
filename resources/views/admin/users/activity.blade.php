@extends('layouts.admin')

@section('title', 'Nhật ký hoạt động người dùng')
@section('header', 'Nhật ký hoạt động người dùng')

@section('content')
    <div class="space-y-6">
        <!-- Bộ lọc -->
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <form method="GET" class="flex flex-wrap gap-4">
                <!-- Bộ lọc người dùng -->
                <div class="flex-1 min-w-[200px]">
                    <select name="user" class="w-full rounded-md border-gray-300">
                        <option value="">Tất cả người dùng</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Bộ lọc loại hoạt động -->
                <div class="flex-1 min-w-[200px]">
                    <select name="type" class="w-full rounded-md border-gray-300">
                        <option value="">Tất cả hoạt động</option>

                        <option value="create_movie" {{ request('type') == 'create_movie' ? 'selected' : '' }}>Tạo phim</option>
                        <option value="update_movie" {{ request('type') == 'update_movie' ? 'selected' : '' }}>Cập nhật phim</option>
                        <option value="delete_movie" {{ request('type') == 'delete_movie' ? 'selected' : '' }}>Xóa phim</option>

                        <option value="create_user" {{ request('type') == 'create_movie' ? 'selected' : '' }}>Tạo người dùng</option>
                        <option value="update_user" {{ request('type') == 'update_movie' ? 'selected' : '' }}>Cập nhật người dùng</option>
                        <option value="delete_user" {{ request('type') == 'delete_movie' ? 'selected' : '' }}>Xóa người dùng</option>
                    </select>
                </div>

                <!-- Khoảng thời gian -->
                <div class="flex gap-2 flex-1 min-w-[300px]">
                    <input type="date"
                           name="from_date"
                           value="{{ request('from_date') }}"
                           class="rounded-md border-gray-300"
                           placeholder="Từ ngày">
                    <input type="date"
                           name="to_date"
                           value="{{ request('to_date') }}"
                           class="rounded-md border-gray-300"
                           placeholder="Đến ngày">
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    Lọc
                </button>
            </form>
        </div>

        <!-- Bảng hoạt động -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người dùng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hoạt động</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mô tả</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Địa chỉ IP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($activities as $activity)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $activity->user->name }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ str_replace('_', ' ', ucfirst($activity->type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $activity->description }}</div>
                            @if($activity->properties)
                                <button onclick="toggleProperties('{{ $activity->id }}')"
                                        class="text-xs text-blue-600 hover:text-blue-900">
                                    Xem chi tiết
                                </button>
                                <div id="properties-{{ $activity->id }}" class="hidden mt-2 text-xs text-gray-500">
                                    <pre class="whitespace-pre-wrap">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $activity->ip_address }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $activity->created_at->format('d/m/Y H:i:s') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- Phân trang -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
		function toggleProperties(id) {
			const element = document.getElementById(`properties-${id}`);
			element.classList.toggle('hidden');
		}
    </script>
@endpush
