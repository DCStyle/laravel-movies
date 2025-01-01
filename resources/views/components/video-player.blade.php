@props(['source'])

<div class="w-full h-full">
	@if($source && $source->source_url && $source->source_type)
		@switch(strtolower($source->source_type))
			@case('youtube')
				@php
					$videoId = '';
                    $patterns = [
                        '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
                        '/^([^"&?\/\s]{11})$/'
                    ];

                    foreach ($patterns as $pattern) {
                        if (preg_match($pattern, $source->source_url, $match)) {
                            $videoId = $match[1];
                            break;
                        }
                    }
				@endphp

				@if($videoId)
					<div id="player-{{ $source->id }}"
						 data-plyr-provider="youtube"
						 data-plyr-embed-id="{{ $videoId }}"
						 data-source-type="youtube"
						 class="w-full h-full">
					</div>
				@endif
				@break

			@case('direct')
				<div id="player-{{ $source->id }}"
					 class="video-container relative w-full h-full bg-black"
					 data-source-type="direct">
					<video
							class="player-main w-full h-full"
							playsinline
							crossorigin="anonymous">
						<source src="{{ $source->source_url }}" type="video/mp4" />
						Your browser doesn't support HTML5 video.
					</video>

					<!-- Overlay Container (formerly ad container) -->
					<div class="overlay-container absolute inset-0 bg-black/90 hidden z-50">
						<div class="overlay-content relative w-full h-full flex items-center justify-center">
							<!-- Content will be dynamically inserted here -->
						</div>
						<div class="overlay-controls absolute bottom-4 right-4 hidden">
							<button type="button"
									class="skip-overlay-button px-4 py-2 bg-blue-600 text-white rounded-lg opacity-50 cursor-not-allowed"
									disabled>
								Continue in <span class="timer">5</span>
							</button>
						</div>
					</div>
				</div>
				@break

			@case('google drive')
				@php
					$embedUrl = $source->source_url;
                    if (preg_match('/\/file\/d\/([^\/]+)/', $source->source_url, $matches)) {
                        $fileId = $matches[1];
                        $embedUrl = "https://drive.google.com/file/d/" . e($fileId) . "/preview";
                    }
				@endphp
				<iframe
						src="{{ $embedUrl }}"
						class="w-full h-full"
						allowfullscreen
						allow="autoplay"
						loading="lazy"
						referrerpolicy="no-referrer"
						sandbox="allow-same-origin allow-scripts allow-popups allow-forms"
				></iframe>
				@break

			@case('facebook')
				<div class="w-full h-full" id="fb-video-container-{{ $source->id }}">
					<div
							class="fb-video"
							data-href="{{ e($source->source_url) }}"
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
						} else {
							console.warn('Facebook SDK not loaded');
						}
					});
				</script>
				@break

			@default
				<div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-400">
					<p>Unsupported video source type: {{ e($source->source_type) }}</p>
				</div>
		@endswitch
	@else
		<div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-400">
			<p>No video source available</p>
		</div>
	@endif
</div>

@once
	@push('styles')
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.7.8/plyr.css" />
		<style>
			.plyr--full {
				height: 100vh !important;
			}

			.plyr--video {
				height: 100%;
			}

			.plyr--video .plyr__control--overlaid {
				background: rgba(0, 0, 0, 0.75);
			}

			.plyr--video .plyr__control--overlaid:hover {
				background: rgb(0, 0, 0);
			}

			.plyr--full-ui input[type=range] {
				color: #3b82f6;
			}

			.plyr__control.plyr__tab-focus,
			.plyr__control:hover,
			.plyr__control[aria-expanded=true] {
				background: #3b82f6;
			}

			.plyr__menu__container .plyr__control[role=menuitemradio][aria-checked=true]::before {
				background: #3b82f6;
			}

			/* Overlay Styles */
			.overlay-container {
				transition: opacity 0.3s ease-in-out;
			}

			.overlay-container.hiding {
				opacity: 0;
			}

			.skip-overlay-button {
				transition: all 0.3s ease-in-out;
			}

			.skip-overlay-button:not(:disabled) {
				opacity: 1;
				cursor: pointer;
			}

			.skip-overlay-button:not(:disabled):hover {
				background-color: #2563eb;
			}

			/* Content Styles */
			.video-content {
				width: 100%;
				height: 100%;
				object-fit: contain;
			}

			.image-content {
				max-width: 100%;
				max-height: 100%;
				object-fit: contain;
			}

			.image-content.clickable {
				cursor: pointer;
			}
		</style>
	@endpush

	@push('scripts')
		<script src="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.7.8/plyr.polyfilled.js"></script>
		<script>
			let currentPlayer = null;

			class VideoOverlayManager {
				constructor(player, container) {
					this.player = player;
					this.container = container;
					this.overlayContainer = container.querySelector('.overlay-container');
					this.overlayContent = this.overlayContainer.querySelector('.overlay-content');
					this.overlayControls = this.overlayContainer.querySelector('.overlay-controls');
					this.skipButton = this.overlayControls?.querySelector('.skip-overlay-button');
					this.timerSpan = this.skipButton?.querySelector('.timer');
					this.currentOverlay = null;
					this.skipTimeout = null;
					this.countdownInterval = null;
					this.shownOverlays = new Set();

					this.setupEventListeners();
				}

				setupEventListeners() {
					this.player.on('timeupdate', () => this.checkForOverlays());

					this.player.on('seeked', () => {
						const currentTime = (this.player.currentTime / this.player.duration) * 100;
						this.shownOverlays.forEach(overlayId => {
							const overlay = this.shownOverlays.get(overlayId);
							if (overlay && overlay.display_time > currentTime) {
								this.shownOverlays.delete(overlayId);
							}
						});
					});

					if (this.skipButton) {
						this.skipButton.addEventListener('click', () => this.skipOverlay());
					}
				}

				async checkForOverlays() {
					if (this.currentOverlay || !this.player.playing) return;

					const currentTime = (this.player.currentTime / this.player.duration) * 100;

					try {
						const response = await fetch(`/api/movie-breaks/next?time=${currentTime}&shown=${Array.from(this.shownOverlays).join(',')}`);
						const data = await response.json();

						if (data.overlay && !this.shownOverlays.has(data.overlay.id)) {
							this.currentOverlay = data.overlay;
							this.showOverlay(data.overlay);
							this.shownOverlays.add(data.overlay.id);
						}
					} catch (error) {
						console.error('Error checking for overlays:', error);
					}
				}

				showOverlay(overlay) {
					this.player.pause();
					this.overlayContent.innerHTML = '';

					if (overlay.type === 'image') {
						const img = document.createElement('img');
						img.src = overlay.content_url;
						img.className = 'image-content ' + (overlay.click_url ? 'clickable' : '');

						if (overlay.click_url) {
							img.addEventListener('click', () => window.open(overlay.click_url, '_blank'));
						}

						this.overlayContent.appendChild(img);
						this.startSkipTimer(overlay.duration);
					} else if (overlay.type === 'video') {
						const video = document.createElement('video');
						video.src = overlay.content_url;
						video.className = 'video-content';
						video.controls = false;
						video.autoplay = true;

						video.addEventListener('ended', () => this.skipOverlay());
						if (overlay.click_url) {
							video.addEventListener('click', () => window.open(overlay.click_url, '_blank'));
							video.style.cursor = 'pointer';
						}

						this.overlayContent.appendChild(video);
					}

					this.overlayContainer.classList.remove('hidden');
				}

				startSkipTimer(duration) {
					let timeLeft = duration;

					this.overlayControls.classList.remove('hidden');

					if (this.skipButton) {
						this.skipButton.disabled = true;
						this.skipButton.classList.add('opacity-50', 'cursor-not-allowed');
						this.skipButton.classList.remove('hover:bg-blue-700');
					}

					this.countdownInterval = setInterval(() => {
						timeLeft--;
						if (this.timerSpan) {
							this.timerSpan.textContent = timeLeft;
						}

						if (timeLeft <= 0) {
							clearInterval(this.countdownInterval);
							if (this.skipButton) {
								this.skipButton.disabled = false;
								this.skipButton.classList.remove('opacity-50', 'cursor-not-allowed');
								this.skipButton.classList.add('hover:bg-blue-700');
								this.skipButton.textContent = 'Continue';
							}
						}
					}, 1000);
				}

				skipOverlay() {
					if (this.skipTimeout) clearTimeout(this.skipTimeout);
					if (this.countdownInterval) clearInterval(this.countdownInterval);

					this.overlayContainer.classList.add('hiding');

					setTimeout(() => {
						this.overlayContainer.classList.remove('hiding');
						this.overlayContainer.classList.add('hidden');
						this.overlayContent.innerHTML = '';
						this.overlayControls.classList.add('hidden');

						if (this.skipButton) {
							this.skipButton.textContent = 'Continue in <span class="timer">5</span>';
							this.timerSpan = this.skipButton.querySelector('.timer');
						}

						this.player.play();
					}, 300);

					this.currentOverlay = null;
				}
			}

			// Player initialization function
			function initializePlayer(containerId) {
				if (currentPlayer) {
					currentPlayer.destroy();
					currentPlayer = null;
				}

				const container = document.getElementById(containerId);
				if (!container) return;

				const options = {
					controls: [
						'play-large',
						'play',
						'progress',
						'current-time',
						'mute',
						'volume',
						'settings',
						'fullscreen'
					],
					youtube: {
						noCookie: true,
						rel: 0,
						showinfo: 0,
						iv_load_policy: 3,
						modestbranding: 1
					},
					quality: {
						default: 1080,
						options: [4320, 2880, 2160, 1440, 1080, 720, 576, 480, 360, 240]
					}
				};

				const sourceType = container.dataset.sourceType;
				if (sourceType === 'youtube') {
					currentPlayer = new Plyr(container, options);
				} else if (sourceType === 'direct') {
					const video = container.querySelector('video');
					if (video) {
						currentPlayer = new Plyr(video, options);
					}
				}

				if (currentPlayer) {
					currentPlayer.on('enterfullscreen', () => {
						container.classList.add('plyr--full');
					});

					currentPlayer.on('exitfullscreen', () => {
						container.classList.remove('plyr--full');
					});

					// Initialize overlay manager for direct videos
					if (sourceType === 'direct') {
						new VideoOverlayManager(currentPlayer, container);
					}
				}

				return currentPlayer;
			}

			// Initialize player on page load
			document.addEventListener('DOMContentLoaded', function() {
				const container = document.getElementById('player-{{ $source->id }}');
				if (container) {
					initializePlayer('player-{{ $source->id }}');
				}
			});
		</script>
	@endpush
@endonce