@props(['source'])

<div class="w-full h-full">
	@if($source && $source->source_url && $source->source_type)
		<div id="player-{{ $source->id }}"
			 class="video-container relative w-full h-full bg-black"
			 data-source-type="{{ $source->source_type }}">

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
						<div class="youtube-embed w-full h-full"
							 data-plyr-provider="youtube"
							 data-plyr-embed-id="{{ $videoId }}">
						</div>
					@endif
					@break

				@case('direct')
					<video class="player-main w-full h-full"
						   playsinline
						   crossorigin="anonymous">
						<source src="{{ $source->source_url }}" type="video/mp4" />
					</video>
					@break

				@case('google drive')
					@php
						$embedUrl = $source->source_url;
                        if (preg_match('/\/file\/d\/([^\/]+)/', $source->source_url, $matches)) {
                            $fileId = $matches[1];
                            $embedUrl = "https://drive.google.com/file/d/" . e($fileId) . "/preview";
                        }
					@endphp
					<iframe src="{{ $embedUrl }}"
							class="w-full h-full"
							allowfullscreen
							allow="autoplay"
							loading="lazy"
							referrerpolicy="no-referrer"
							sandbox="allow-same-origin allow-scripts allow-popups allow-forms">
					</iframe>
					@break

				@case('facebook')
					<div class="fb-video w-full h-full"
						 data-href="{{ e($source->source_url) }}"
						 data-width="auto"
						 data-allowfullscreen="true"
						 data-autoplay="false"
						 data-show-text="false"
						 data-show-captions="false">
					</div>
					@break

				@default
					<div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-400">
						<p>Unsupported video source type: {{ e($source->source_type) }}</p>
					</div>
			@endswitch

			<!-- Overlay Container (for all source types) -->
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

			.video-content, .image-content {
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
					if (this.player.on) {
						// For Plyr players
						this.player.on('timeupdate', () => this.checkForOverlays());
						this.player.on('seeked', () => this.handleSeek());
					} else if (this.player instanceof HTMLVideoElement) {
						// For native video element
						this.player.addEventListener('timeupdate', () => this.checkForOverlays());
						this.player.addEventListener('seeked', () => this.handleSeek());
					} else if (this.player.getPlayerState) {
						// For YouTube iframe API
						setInterval(() => this.checkForOverlays(), 1000);
						this.player.addEventListener('onStateChange', (event) => {
							if (event.data === YT.PlayerState.SEEKING) {
								this.handleSeek();
							}
						});
					}

					if (this.skipButton) {
						this.skipButton.addEventListener('click', () => this.skipOverlay());
					}
				}

				handleSeek() {
					const currentTime = this.getCurrentTime();
					this.shownOverlays.forEach(overlayId => {
						const overlay = this.shownOverlays.get(overlayId);
						if (overlay && overlay.display_time > currentTime) {
							this.shownOverlays.delete(overlayId);
						}
					});
				}

				getCurrentTime() {
					if (this.player.currentTime !== undefined) {
						// Plyr or native video
						const duration = this.player.duration || 0;
						return duration ? (this.player.currentTime / duration) * 100 : 0;
					} else if (this.player.getCurrentTime) {
						// YouTube
						const duration = this.player.getDuration() || 0;
						return duration ? (this.player.getCurrentTime() / duration) * 100 : 0;
					}
					return 0;
				}

				isPlaying() {
					if (this.player.playing !== undefined) {
						return this.player.playing;
					} else if (this.player.getPlayerState) {
						return this.player.getPlayerState() === YT.PlayerState.PLAYING;
					} else if (this.player instanceof HTMLVideoElement) {
						return !this.player.paused;
					}
					return false;
				}

				pauseVideo() {
					if (this.player.pause) {
						this.player.pause();
					} else if (this.player.pauseVideo) {
						this.player.pauseVideo();
					}
				}

				playVideo() {
					if (this.player.play) {
						this.player.play();
					} else if (this.player.playVideo) {
						this.player.playVideo();
					}
				}

				async checkForOverlays() {
					if (this.currentOverlay || !this.isPlaying()) return;

					const currentTime = this.getCurrentTime();

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
					this.pauseVideo();
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

						const skipDuration = overlay.duration || 0;

						// Create timer container
						const timerContainer = document.createElement('div');
						timerContainer.className = 'absolute bottom-4 right-4 px-4 py-2 bg-black/70 rounded-lg text-white';
						this.overlayContent.appendChild(timerContainer);

						// Create skip button
						if (skipDuration > 0) {
							this.overlayControls.classList.remove('hidden');
							this.skipButton.disabled = true;
						}

						// Update timer and skip button on timeupdate
						video.addEventListener('timeupdate', () => {
							const currentTime = video.currentTime;
							const timeLeft = Math.ceil(video.duration - currentTime);

							if (skipDuration === 0) {
								timerContainer.textContent = `${timeLeft}s remaining`;
							} else {
								if (currentTime >= skipDuration) {
									this.skipButton.disabled = false;
									this.skipButton.classList.remove('opacity-50', 'cursor-not-allowed');
									this.skipButton.textContent = 'Skip Ad';
									timerContainer.textContent = `${timeLeft}s remaining`;
								} else {
									const skipTimeLeft = Math.ceil(skipDuration - currentTime);
									timerContainer.textContent = `Skip in ${skipTimeLeft}s`;
								}
							}
						});

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

						this.playVideo();
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
						'play-large', 'play', 'progress', 'current-time',
						'mute', 'volume', 'settings', 'fullscreen'
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
				let playerElement = null;

				switch (sourceType) {
					case 'youtube':
						playerElement = container.querySelector('.youtube-embed');
						break;
					case 'direct':
						playerElement = container.querySelector('video');
						break;
					case 'facebook':
						if (window.FB) {
							FB.XFBML.parse(container);
							// Facebook videos don't support our overlay system
							return;
						}
						break;
				}

				if (playerElement) {
					currentPlayer = new Plyr(playerElement, options);

					currentPlayer.on('ready', () => {
						container.classList.add('plyr--initialized');
					});

					currentPlayer.on('enterfullscreen', () => {
						container.classList.add('plyr--full');
					});

					currentPlayer.on('exitfullscreen', () => {
						container.classList.remove('plyr--full');
					});

					// Initialize overlay manager for all supported players
					new VideoOverlayManager(currentPlayer, container);
				}

				return currentPlayer;
			}

			// Initialize player on page load
			document.addEventListener('DOMContentLoaded', function() {
				const container = document.getElementById('player-{{ $source->id }}');
				if (container) {
					initializePlayer('player-{{ $source->id }}');
				}

				// Initialize Facebook SDK if needed
				if (container?.querySelector('.fb-video')) {
					if (!window.FB) {
						// Load Facebook SDK
						const script = document.createElement('script');
						script.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0";
						script.async = true;
						script.defer = true;
						script.crossOrigin = "anonymous";
						document.body.appendChild(script);
					}
				}

				// Load YouTube API if needed
				if (container?.querySelector('.youtube-embed')) {
					if (!window.YT) {
						const tag = document.createElement('script');
						tag.src = "https://www.youtube.com/iframe_api";
						const firstScriptTag = document.getElementsByTagName('script')[0];
						firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
					}
				}
			});

			// Function to handle source changes
			window.changeSource = async function(sourceId) {
				const playerContainer = document.getElementById('player-container');
				if (!playerContainer) return;

				try {
					// Show loading state
					playerContainer.innerHTML = `
                        <div class="w-full h-full flex items-center justify-center bg-black/90">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
                        </div>
                    `;

					// Fetch new player HTML
					const response = await fetch(`/api/movies/sources/${sourceId}`);
					if (!response.ok) throw new Error('Failed to fetch source');

					const data = await response.json();

					// Update player container
					playerContainer.innerHTML = data.player_html;

					// Initialize new player
					const newPlayerContainer = playerContainer.querySelector('[id^="player-"]');
					if (newPlayerContainer) {
						initializePlayer(newPlayerContainer.id);
					}

					// Update button states
					document.querySelectorAll('[data-source-id]').forEach(button => {
						const isActive = button.dataset.sourceId === sourceId;
						button.classList.toggle('bg-blue-500', isActive);
						button.classList.toggle('text-white', isActive);
						button.classList.toggle('bg-gray-700', !isActive);
						button.classList.toggle('text-gray-300', !isActive);
						button.setAttribute('aria-pressed', isActive.toString());
					});

				} catch (error) {
					console.error('Error changing source:', error);
					showErrorMessage(sourceId);
				}
			};

			function showErrorMessage(sourceId) {
				const playerContainer = document.getElementById('player-container');
				if (!playerContainer) return;

				playerContainer.innerHTML = `
                    <div class="w-full h-full flex items-center justify-center bg-black/90">
                        <div class="text-center p-6">
                            <div class="w-16 h-16 rounded-full bg-gray-800/50 mx-auto flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-gray-400 mb-4">Không thể tải nguồn phim</p>
                            <button onclick="changeSource('${sourceId}')"
                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200">
                                Thử lại
                            </button>
                        </div>
                    </div>
                `;
			}
		</script>
	@endpush
@endonce