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

					<!-- Ad Container -->
					<div class="ad-container absolute inset-0 bg-black/90 hidden z-50">
						<div class="ad-content relative w-full h-full flex items-center justify-center">
							<!-- Ad content will be dynamically inserted here -->
						</div>
						<div class="ad-controls absolute bottom-4 right-4 hidden">
							<button type="button"
									class="skip-ad-button px-4 py-2 bg-blue-600 text-white rounded-lg opacity-50 cursor-not-allowed"
									disabled>
								Skip Ad in <span class="countdown">5</span>
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

			/* Ad Container Styles */
			.ad-container {
				transition: opacity 0.3s ease-in-out;
			}

			.ad-container.hiding {
				opacity: 0;
			}

			.skip-ad-button {
				transition: all 0.3s ease-in-out;
			}

			.skip-ad-button:not(:disabled) {
				opacity: 1;
				cursor: pointer;
			}

			.skip-ad-button:not(:disabled):hover {
				background-color: #2563eb;
			}

			/* Video Ad Styles */
			.video-ad {
				width: 100%;
				height: 100%;
				object-fit: contain;
			}

			/* Image Ad Styles */
			.image-ad {
				max-width: 100%;
				max-height: 100%;
				object-fit: contain;
			}

			.image-ad.clickable {
				cursor: pointer;
			}
		</style>
	@endpush

	@push('scripts')
		<script src="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.7.8/plyr.polyfilled.js"></script>
		<script>
			class VideoAdManager {
				constructor(player, container) {
					this.player = player;
					this.container = container;
					this.adContainer = container.querySelector('.ad-container');
					this.adContent = this.adContainer.querySelector('.ad-content');
					this.adControls = this.adContainer.querySelector('.ad-controls');
					this.skipButton = this.adControls?.querySelector('.skip-ad-button');
					this.countdownSpan = this.skipButton?.querySelector('.countdown');
					this.currentAd = null;
					this.skipTimeout = null;
					this.countdownInterval = null;
					this.shownAds = new Set(); // Track shown ads for this session

					this.setupEventListeners();
				}

				setupEventListeners() {
					// Listen for timeupdate event
					this.player.on('timeupdate', () => this.checkForAds());

					// Reset shown ads when video is seeked
					this.player.on('seeked', () => {
						const currentTime = (this.player.currentTime / this.player.duration) * 100;
						// Only reset ads that should appear after the current position
						this.shownAds.forEach(adId => {
							const ad = this.shownAds.get(adId);
							if (ad && ad.display_time > currentTime) {
								this.shownAds.delete(adId);
							}
						});
					});

					// Handle skip button clicks
					if (this.skipButton) {
						this.skipButton.addEventListener('click', () => this.skipAd());
					}
				}

				async checkForAds() {
					if (this.currentAd || !this.player.playing) return;

					const currentTime = (this.player.currentTime / this.player.duration) * 100;

					try {
						const response = await fetch(`/api/movie-ads/next?time=${currentTime}&shown=${Array.from(this.shownAds).join(',')}`);
						const data = await response.json();

						if (data.ad && !this.shownAds.has(data.ad.id)) {
							this.currentAd = data.ad;
							this.showAd(data.ad);
							this.shownAds.add(data.ad.id); // Mark this ad as shown
						}
					} catch (error) {
						console.error('Error checking for ads:', error);
					}
				}

				showAd(ad) {
					// Pause the main video
					this.player.pause();

					// Clear previous content
					this.adContent.innerHTML = '';

					// Create ad element based on type
					if (ad.type === 'image') {
						const img = document.createElement('img');
						img.src = ad.content_url;
						img.className = 'image-ad ' + (ad.click_url ? 'clickable' : '');

						if (ad.click_url) {
							img.addEventListener('click', () => window.open(ad.click_url, '_blank'));
						}

						this.adContent.appendChild(img);

						// Show skip button after duration
						this.startSkipTimer(ad.duration);
					} else if (ad.type === 'video') {
						const video = document.createElement('video');
						video.src = ad.content_url;
						video.className = 'video-ad';
						video.controls = false;
						video.autoplay = true;

						video.addEventListener('ended', () => this.skipAd());
						if (ad.click_url) {
							video.addEventListener('click', () => window.open(ad.click_url, '_blank'));
							video.style.cursor = 'pointer';
						}

						this.adContent.appendChild(video);
					}

					// Show ad container
					this.adContainer.classList.remove('hidden');
				}

				startSkipTimer(duration) {
					let timeLeft = duration;

					// Show controls for image ads
					this.adControls.classList.remove('hidden');

					// Reset button state
					if (this.skipButton) {
						this.skipButton.disabled = true;
						this.skipButton.classList.add('opacity-50', 'cursor-not-allowed');
						this.skipButton.classList.remove('hover:bg-blue-700');
					}

					// Update countdown
					this.countdownInterval = setInterval(() => {
						timeLeft--;
						if (this.countdownSpan) {
							this.countdownSpan.textContent = timeLeft;
						}

						if (timeLeft <= 0) {
							clearInterval(this.countdownInterval);
							if (this.skipButton) {
								this.skipButton.disabled = false;
								this.skipButton.classList.remove('opacity-50', 'cursor-not-allowed');
								this.skipButton.classList.add('hover:bg-blue-700');
								this.skipButton.textContent = 'Skip Ad';
							}
						}
					}, 1000);
				}

				skipAd() {
					// Clear timers
					if (this.skipTimeout) clearTimeout(this.skipTimeout);
					if (this.countdownInterval) clearInterval(this.countdownInterval);

					// Hide ad container with animation
					this.adContainer.classList.add('hiding');

					setTimeout(() => {
						this.adContainer.classList.remove('hiding');
						this.adContainer.classList.add('hidden');
						this.adContent.innerHTML = '';
						this.adControls.classList.add('hidden');

						// Reset skip button
						if (this.skipButton) {
							this.skipButton.textContent = 'Skip Ad in <span class="countdown">5</span>';
							this.countdownSpan = this.skipButton.querySelector('.countdown');
						}

						// Resume main video
						this.player.play();
					}, 300);

					this.currentAd = null;
				}
			}

			// Global player instance
			let currentPlayer = null;

			// Player initialization function
			function initializePlayer(containerId) {
				// Destroy existing player if it exists
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

				// Initialize based on source type
				const sourceType = container.dataset.sourceType;
				if (sourceType === 'youtube') {
					currentPlayer = new Plyr(container, options);
				} else if (sourceType === 'direct') {
					const video = container.querySelector('video');
					if (video) {
						currentPlayer = new Plyr(video, options);
					}
				}

				// Handle fullscreen if player was initialized
				if (currentPlayer) {
					currentPlayer.on('enterfullscreen', () => {
						container.classList.add('plyr--full');
					});

					currentPlayer.on('exitfullscreen', () => {
						container.classList.remove('plyr--full');
					});
				}

				// Initialize ad manager if it's a direct video
				if (container?.dataset.sourceType === 'direct' && currentPlayer) {
					new VideoAdManager(currentPlayer, container);
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

					// Fetch new source
					const response = await fetch(`/api/movies/sources/${sourceId}`);
					if (!response.ok) throw new Error('Failed to fetch source');

					const data = await response.json();

					// Update container with new player HTML
					playerContainer.innerHTML = data.player_html;

					// Get the new player container
					const newPlayerContainer = playerContainer.querySelector('[id^="player-"]');
					if (newPlayerContainer) {
						// Initialize new player
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-gray-400 mb-4">Không thể tải nguồn phim</p>
                            <button
                                onclick="changeSource('${sourceId}')"
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200"
                            >
                                Thử lại
                            </button>
                        </div>
                    </div>
                `;
			}
		</script>
	@endpush
@endonce