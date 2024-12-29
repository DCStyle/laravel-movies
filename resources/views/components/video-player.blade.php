@props(['source'])

<div class="w-full h-full">
    @if($source && $source->source_url)
        @switch(strtolower($source->source_type))
            @case('youtube')
                @php
                    // Extract YouTube video ID from URL
                    $videoId = '';
                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $source->source_url, $match)) {
                        $videoId = $match[1];
                    }
                    
                    // Construct embed URL with necessary parameters
                    $embedUrl = "https://www.youtube-nocookie.com/embed/{$videoId}?"
                        . http_build_query([
                            'autoplay' => 0,
                            'modestbranding' => 1,
                            'rel' => 0,
                            'showinfo' => 1,
                            'origin' => request()->getHttpHost()
                        ]);
                @endphp
                <div class="relative w-full h-full bg-black">
                    <iframe
                            src="{{ $embedUrl }}"
                            class="absolute top-0 left-0 w-full h-full"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                            frameborder="0"
                    ></iframe>
                </div>
                @break

            @case('direct upload')
                <video
                        class="w-full h-full"
                        controls
                        preload="auto"
                >
                    <source src="{{ $source->source_url }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                @break

            @case('fshare')
                <iframe
                        src="{{ $source->source_url }}"
                        class="w-full h-full"
                        allowfullscreen
                        frameborder="0"
                ></iframe>
                @break

            @case('google drive')
                @php
                    // Convert Google Drive link to embed format
                    $driveUrl = $source->source_url;
                    if (preg_match('/\/file\/d\/([^\/]+)/', $driveUrl, $matches)) {
                        $fileId = $matches[1];
                        $embedUrl = "https://drive.google.com/file/d/" . $fileId . "/preview";
                    } else {
                        $embedUrl = $driveUrl;
                    }
                @endphp
                <iframe
                        src="{{ $embedUrl }}"
                        class="w-full h-full"
                        allowfullscreen
                        frameborder="0"
                ></iframe>
                @break

            @case('twitter')
                <div class="w-full h-full" id="twitter-embed">
                    <script async src="https://platform.twitter.com/widgets.js"></script>
                    <script>
						document.addEventListener('DOMContentLoaded', function() {
							twttr.widgets.createVideo(
								'{{ $source->source_url }}',
								document.getElementById('twitter-embed')
							);
						});
                    </script>
                </div>
                @break

            @case('facebook')
                <div class="w-full h-full" id="fb-video-container-{{ $source->id }}">
                    <div
                            class="fb-video"
                            data-href="{{ $source->source_url }}"
                            data-width="auto"
                            data-allowfullscreen="true"
                            data-autoplay="false"
                            data-show-text="false"
                            data-show-captions="false">
                    </div>
                </div>
                <script>
					document.addEventListener('DOMContentLoaded', function() {
						if (window.FB) {
							FB.XFBML.parse(document.getElementById('fb-video-container-{{ $source->id }}'));
						}
					});
                </script>
                @break

            @case('tiktok')
                <div class="w-full h-full flex items-center justify-center">
                    <blockquote class="tiktok-embed" cite="{{ $source->source_url }}" data-video-id="{{ basename(parse_url($source->source_url, PHP_URL_PATH)) }}">
                    </blockquote>
                    <script async src="https://www.tiktok.com/embed.js"></script>
                </div>
                @break

            @default
                <div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-400">
                    <p>Unsupported video source type: {{ $source->source_type }}</p>
                </div>
        @endswitch
    @else
        <div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-400">
            <p>No video source available</p>
        </div>
    @endif
</div>

@push('scripts')
    <script>
		// Handle any player-specific error events
		document.addEventListener('DOMContentLoaded', function() {
			const playerContainer = document.getElementById('player-container');

			// Add error handling for iframe load failures
			const iframes = playerContainer.getElementsByTagName('iframe');
			for(let iframe of iframes) {
				iframe.onerror = function() {
					console.error('Failed to load iframe source');
				};
			}
		});
    </script>
@endpush