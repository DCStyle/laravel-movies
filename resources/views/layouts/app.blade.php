<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ \Illuminate\Support\Facades\Storage::url(setting('site_favicon')) }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ \Illuminate\Support\Facades\Storage::url(setting('site_favicon')) }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ \Illuminate\Support\Facades\Storage::url(setting('site_favicon')) }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    @yield('seo')
</head>
<body class="bg-[#424040] text-white antialiased">
<!-- Header -->
@include('includes.navbar')

<!-- Main Content -->
<div class="container min-h-screen mx-auto px-4 py-8 bg-[rgba(15,15,15,.9)]">
    @foreach(app(\App\Services\AdService::class)->getAds('header') as $ad)
        <div class="ad-container mb-4">
            {!! $ad->content !!}
        </div>
    @endforeach

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Column -->
        <div class="flex-1 min-w-0">
            @yield('content')
        </div>

        <!-- Sidebar -->
        <aside class="lg:w-[360px] shrink-0">
            <div class="lg:sticky lg:top-8 lg:h-[calc(100vh-4rem)] overflow-hidden">
                <div class="h-full overflow-y-auto custom-scrollbar space-y-4">
                    @include('includes.sidebar')

                    @foreach(app(\App\Services\AdService::class)->getAds('footer') as $ad)
                        <div class="ad-container mt-4">
                            {!! $ad->content !!}
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>

<!-- Footer -->
<footer class="container mx-auto bg-[rgba(15,15,15,.9)] shadow-2xl mb-12">
    @foreach(app(\App\Services\AdService::class)->getAds('footer') as $ad)
        <div class="ad-container mb-4">
            {!! $ad->content !!}
        </div>
    @endforeach

    @include('includes.footer')
</footer>

@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
	window.fbAsyncInit = function() {
		if (window.FB) {
			FB.init({
				xfbml: true,
				version: 'v3.2'
			});
		}
	};
</script>
<div id="fb-root"></div>
<script async defer src="https://connect.facebook.net/en_US/sdk.js"></script>
</body>
</html>