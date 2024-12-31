@extends('layouts.admin')

@section('title', $ad->exists ? 'Chỉnh sửa quảng cáo: ' . $ad->name : 'Thêm quảng cáo mới')
@section('header', $ad->exists ? 'Chỉnh sửa quảng cáo: ' . $ad->name : 'Thêm quảng cáo mới')

@push('styles')
    <style>
        .preview-frame {
            background: #f9fafb url("data:image/svg+xml,%3Csvg width='6' height='6' viewBox='0 0 6 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23000000' fill-opacity='0.05' fill-rule='evenodd'%3E%3Cpath d='M5 0h1L0 6V5zM6 5v1H5z'/%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        <form action="{{ $ad->exists ? route('admin.ads.update', $ad) : route('admin.ads.store') }}"
              method="POST"
              class="space-y-6"
        >
            @csrf
            @if($ad->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="xl:col-span-2 space-y-6">
                    <!-- Basic Info -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên quảng cáo</label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name', $ad->name) }}"
                                       required
                                       placeholder="VD: Banner quảng cáo header"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Ad Content -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between border-b border-gray-200 p-4">
                            <h3 class="text-lg font-medium">Nội dung quảng cáo</h3>
                            <div class="flex items-center space-x-2">
                                <button type="button" class="inline-flex items-center px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50" onclick="previewAd()">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Xem trước
                                </button>
                            </div>
                        </div>

                        <div class="p-4">
                            <x-head.tinymce-config/>
                            <textarea id="tinymce-editor"
                                      name="content"
                                      rows="12"
                                      class="w-full">{{ old('content', $ad->content) }}</textarea>
                            @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Preview -->
                    <div id="preview-container" class="hidden bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium">Xem trước quảng cáo</h3>
                        </div>
                        <div class="preview-frame p-8">
                            <div id="preview-content" class="bg-white rounded-lg shadow p-4"></div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Publishing -->
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="font-medium">Xuất bản</h3>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                    </svg>
                                    {{ $ad->exists ? 'Cập nhật' : 'Xuất bản' }}
                                </button>
                            </div>
                        </div>

                        <div class="p-4 space-y-4">
                            <!-- Status -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700">Trạng thái</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           name="is_active"
                                           value="1"
                                           class="sr-only peer"
                                            {{ old('is_active', $ad->is_active) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Position -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí hiển thị</label>
                                <select name="position"
                                        required
                                        class="w-full rounded-lg border-gray-300">
                                    <option value="">Chọn vị trí</option>
                                    @foreach(App\Models\Ad::POSITIONS as $value => $label)
                                        <option value="{{ $value }}"
                                                {{ old('position', $ad->position) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự hiển thị</label>
                                <input type="number"
                                       name="order"
                                       value="{{ old('order', $ad->order) }}"
                                       min="0"
                                       class="w-full rounded-lg border-gray-300">
                                <p class="mt-1 text-xs text-gray-500">Số thứ tự càng nhỏ sẽ hiển thị càng cao</p>
                                @error('order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if($ad->exists)
                                <div class="pt-4 border-t border-gray-200">
                                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Đã tạo: {{ $ad->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
			function previewAd() {
				const content = tinymce.get('tinymce-editor').getContent();
				document.getElementById('preview-content').innerHTML = content;
				document.getElementById('preview-container').classList.remove('hidden');
			}
        </script>
    @endpush

@endsection