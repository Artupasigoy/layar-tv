<div x-data="signage({ 
        media: {{ json_encode($initialData['media']) }},
        settings: {{ json_encode($initialData['settings']) }},
        hash: '{{ $initialHash }}'
    })" x-init="init()" @click="enableSound()" class="fixed inset-0 w-full h-full bg-black overflow-hidden"
    :class="isPreview ? 'cursor-auto' : 'cursor-none'">

    <style>
        @keyframes kenBurns {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.2);
            }
        }

        .animate-ken-burns {
            animation: kenBurns 25s ease-out forwards;
        }

        /* Equalizer Animation */
        @keyframes equalizer {
            0% {
                height: 3px;
            }

            50% {
                height: 15px;
            }

            100% {
                height: 3px;
            }
        }

        .equalizer-bar {
            width: 4px;
            background-color: #10b981;
            animation: equalizer 1s infinite ease-in-out;
        }

        .equalizer-bar:nth-child(1) {
            animation-delay: 0.1s;
        }

        .equalizer-bar:nth-child(2) {
            animation-delay: 0.3s;
        }

        .equalizer-bar:nth-child(3) {
            animation-delay: 0.0s;
        }

        .equalizer-bar:nth-child(4) {
            animation-delay: 0.4s;
        }

        .equalizer-bar:nth-child(5) {
            animation-delay: 0.2s;
        }
    </style>

    <!-- Image Loop (Lazy Loading: Only render prev, current, next) -->
    <template x-for="item in visibleImages" :key="'img-' + item.id">
        <div x-show="currentIndex === item.originalIndex && item.type === 'image'"
            x-transition:enter="transition opacity duration-1000" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition opacity duration-1000"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="absolute inset-0 w-full h-full flex items-center justify-center">
            <img :src="item.url" class="absolute inset-0 w-full h-full object-contain bg-black"
                :class="{ 'animate-ken-burns': settings.enable_animation && currentIndex === item.originalIndex }">
        </div>
    </template>

    <!-- SINGLE Global Video Player (Smart TV Optimization) -->
    <!-- Only ONE video element exists. Source is swapped dynamically via JS. -->
    <div x-show="currentItem && currentItem.type === 'video'" x-transition:enter="transition opacity duration-500"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        class="absolute inset-0 w-full h-full bg-black flex items-center justify-center z-10">
        <video x-ref="videoPlayer" class="w-full h-full object-contain" playsinline></video>
    </div>

    <!-- Visual Equalizer (Only for Preview & Video when Muted) -->
    <div x-show="currentItem && currentItem.type === 'video' && isPreview && isMuted"
        class="absolute bottom-16 right-4 bg-black/50 backdrop-blur px-3 py-2 rounded-lg flex items-end gap-1 z-[60]">
        <div class="equalizer-bar h-3"></div>
        <div class="equalizer-bar h-3"></div>
        <div class="equalizer-bar h-3"></div>
        <div class="equalizer-bar h-3"></div>
        <div class="equalizer-bar h-3"></div>
        <span class="text-[10px] text-white font-mono ml-1">AUDIO PLAYING (MUTED)</span>
    </div>

    <!-- Overlay: Logo (Top Left) -->
    <div x-show="settings.logo_path" class="absolute top-3 left-3 sm:top-6 sm:left-6 z-50 pointer-events-none">
        <img :src="'/storage/' + settings.logo_path"
            class="h-10 sm:h-24 w-auto drop-shadow-lg filter transition-all duration-300">
    </div>

    <!-- Overlay: Aesthetic Clock (Top Right) -->
    <div x-show="settings.show_clock" class="absolute top-3 right-3 sm:top-6 sm:right-6 z-50">
        <div
            class="bg-black/40 backdrop-blur-md text-white px-3 py-1.5 sm:px-6 sm:py-3 rounded-xl sm:rounded-2xl border border-white/10 shadow-xl flex flex-col items-end transition-all duration-300">
            <div class="text-xl sm:text-5xl font-bold tracking-tight font-sans" x-text="time"></div>
            <div class="text-[10px] sm:text-sm font-medium text-gray-200 mt-0.5 sm:mt-1 uppercase tracking-wider"
                x-text="date"></div>
        </div>
    </div>

    <!-- Start Button Overlay (For Autoplay with Sound) -->
    <div x-show="!started"
        class="fixed inset-0 z-[100] bg-black/90 flex flex-col items-center justify-center cursor-pointer space-y-4 text-center px-4"
        @click="startSignage()">
        <div class="p-4 sm:p-6 rounded-full bg-indigo-600 animate-pulse">
            <svg class="w-10 h-10 sm:w-16 sm:h-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-white text-xl sm:text-3xl font-bold tracking-wider">KLIK UNTUK MEMULAI</h1>
        <p class="text-gray-400 text-xs sm:text-sm">Diperlukan interaksi untuk mengaktifkan suara</p>
    </div>

    <!-- Preview Mute Toggle (Only in Preview Mode) -->
    <div x-show="isPreview"
        class="absolute bottom-4 right-4 z-[70] flex items-center gap-2 bg-black/60 backdrop-blur px-3 py-1.5 rounded-full border border-white/10 shadow-lg hover:bg-black/70 transition-all">
        <span class="text-[10px] font-bold text-white uppercase tracking-wider"
            x-text="isMuted ? 'Muted' : 'Suara'"></span>
        <button @click="toggleMute()"
            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
            :class="!isMuted ? 'bg-green-500' : 'bg-gray-600'">
            <span class="sr-only">Use setting</span>
            <span aria-hidden="true"
                class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                :class="!isMuted ? 'translate-x-4' : 'translate-x-0'"></span>
        </button>
    </div>

    <!-- Alpine Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('signage', (initial) => ({
                media: initial.media,
                settings: initial.settings,
                hash: initial.hash,
                currentIndex: 0,
                time: '',
                date: '',
                timer: null,
                started: false,
                isMuted: false,
                isPreview: false,
                preloadedImages: {}, // Image cache for preloading

                // Computed: Get only visible images (prev, current, next) for lazy loading
                get visibleImages() {
                    if (this.media.length === 0) return [];
                    if (this.media.length <= 3) {
                        return this.media.map((m, i) => ({ ...m, originalIndex: i }));
                    }

                    const len = this.media.length;
                    const prev = (this.currentIndex - 1 + len) % len;
                    const next = (this.currentIndex + 1) % len;

                    // Return unique indices (handle edge cases)
                    const indices = [...new Set([prev, this.currentIndex, next])];
                    return indices.map(i => ({ ...this.media[i], originalIndex: i }));
                },

                // Computed-like property for current item
                get currentItem() {
                    return this.media[this.currentIndex] || null;
                },

                // Preload an image into cache
                preloadImage(url) {
                    if (!url || this.preloadedImages[url]) return;
                    const img = new Image();
                    img.src = url;
                    this.preloadedImages[url] = img;
                },

                // Preload the next image before transition
                preloadNext() {
                    if (this.media.length === 0) return;
                    const nextIndex = (this.currentIndex + 1) % this.media.length;
                    const nextItem = this.media[nextIndex];
                    if (nextItem && nextItem.type === 'image') {
                        this.preloadImage(nextItem.url);
                    }
                },

                // Cleanup old cached images to free memory
                cleanupOldMedia() {
                    const visible = this.visibleImages;
                    const visibleUrls = visible.map(m => m.url);
                    // Remove cached images that are no longer in visible range
                    Object.keys(this.preloadedImages).forEach(url => {
                        if (!visibleUrls.includes(url)) {
                            delete this.preloadedImages[url];
                        }
                    });
                },

                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('muted')) {
                        this.isPreview = true;
                        this.isMuted = true;
                        this.started = false;
                    } else {
                        this.started = false;
                    }

                    this.startClock();
                    Livewire.on('content-updated', () => {
                        console.log('Content updated, reloading...');
                        window.location.reload();
                    });

                    setInterval(() => {
                        this.checkUpdates();
                    }, 5000);
                },

                toggleMute() {
                    this.isMuted = !this.isMuted;
                    const video = this.$refs.videoPlayer;
                    if (video) {
                        video.muted = this.isMuted;
                    }
                },

                startSignage() {
                    this.started = true;
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (AudioContext) {
                        const ctx = new AudioContext();
                        ctx.resume();
                    }
                    this.playCurrent();

                    // Preload the next image immediately after starting
                    this.preloadNext();

                    setInterval(() => {
                        this.checkUpdates();
                    }, 5000);
                },

                checkUpdates() {
                    @this.checkForUpdates(this.hash);
                },

                playCurrent() {
                    if (this.media.length === 0) return;

                    const currentItem = this.media[this.currentIndex];
                    if (this.timer) clearTimeout(this.timer);

                    // Always pause and reset the single video player first
                    const video = this.$refs.videoPlayer;
                    if (video) {
                        video.pause();
                        video.removeAttribute('src');
                        video.load(); // Reset video element
                    }

                    if (currentItem.type === 'image') {
                        const duration = (this.settings.image_duration || 10) * 1000;
                        this.timer = setTimeout(() => {
                            this.next();
                        }, duration);
                    } else if (currentItem.type === 'video') {
                        this.$nextTick(() => {
                            const video = this.$refs.videoPlayer;
                            if (video) {
                                video.src = currentItem.url;
                                video.muted = this.isMuted;
                                video.onended = () => this.next();
                                video.onerror = (e) => {
                                    console.error("Video error:", e);
                                    this.next(); // Skip on error
                                };
                                video.play().catch(e => {
                                    console.error("Autoplay failed:", e);
                                });
                            }
                        });
                    }
                },

                next() {
                    // Preload the upcoming image before transition
                    this.preloadNext();

                    this.currentIndex = (this.currentIndex + 1) % this.media.length;
                    this.$nextTick(() => {
                        this.playCurrent();
                        // Cleanup old media from cache after transition
                        this.cleanupOldMedia();
                    });
                },

                startClock() {
                    const updateTime = () => {
                        const now = new Date();
                        this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        this.date = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                    };
                    updateTime();
                    setInterval(updateTime, 1000);
                }
            }));
        });
    </script>
</div>