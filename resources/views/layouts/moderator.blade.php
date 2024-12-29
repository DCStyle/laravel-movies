<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Moderator Panel</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex flex-col md:flex-row">
    <!-- Sidebar -->
    <div class="w-full md:w-64 bg-gray-800 text-white flex flex-col">
        <!-- Sidebar Header -->
        <div class="p-4 bg-gray-900 flex justify-between items-center">
            <h1 class="text-lg font-bold">Admin Panel</h1>
            <button id="menu-toggle" class="md:hidden text-white">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <nav id="menu" class="hidden md:block mt-4">
            <a href="{{ route('mod.dashboard') }}"
               class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('mod.dashboard') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-tachometer-alt mr-2"></i> Tổng quan
            </a>
            <a href="{{ route('movies.management.index') }}"
               class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('movies.management.*') ? 'bg-gray-700' : '' }}">
                <i class="fas fa-film mr-2"></i> Phim
            </a>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}" class="mt-4 px-4">
                @csrf
                <button type="submit" class="w-full text-left py-2 hover:bg-gray-700">
                    <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Top Navigation -->
        <header class="bg-white shadow">
            <div class="flex items-center justify-between px-4 md:px-6 py-4">
                <h2 class="text-xl font-semibold text-gray-800">@yield('header')</h2>
                <div class="flex items-center">
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 md:p-6 flex-1">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')

<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
<script>
	// Sidebar toggle for mobile
	document.getElementById('menu-toggle').addEventListener('click', function () {
		const menu = document.getElementById('menu');
		menu.classList.toggle('hidden');
	});
</script>
</body>
</html>