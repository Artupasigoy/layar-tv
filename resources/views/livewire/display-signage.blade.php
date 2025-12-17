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
            /* Emerald 500 */
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

    <!-- Background Loop (Active Item) -->
    <template x-for="(item, index) in media" :key="item.id">
        <div x-show="currentIndex === index" x-transition:enter="transition opacity duration-1000"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition opacity duration-1000" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="absolute inset-0 w-full h-full flex items-center justify-center">
            <template x-if="item.type === 'image'">
                <img :src="item.url" class="absolute inset-0 w-full h-full object-contain bg-black"
                    :class="{ 'animate-ken-burns': settings.enable_animation }">
            </template>

            <template x-if="item.type === 'video'">
                <video :src="item.url" playsinline :data-index="index"
                    class="absolute inset-0 w-full h-full object-contain bg-black" @ended="next()"></video>
            </template>

            <!-- Visual Equalizer (Only for Preview & Video when Muted) -->
            <template x-if="item.type === 'video' && isPreview && isMuted">
                <div
                    class="absolute bottom-4 right-4 bg-black/50 backdrop-blur px-3 py-2 rounded-lg flex items-end gap-1 z-[60]">
                    <div class="equalizer-bar h-3"></div>
                    <div class="equalizer-bar h-3"></div>
                    <div class="equalizer-bar h-3"></div>
                    <div class="equalizer-bar h-3"></div>
                    <div class="equalizer-bar h-3"></div>
                    <span class="text-[10px] text-white font-mono ml-1">AUDIO PLAYING (MUTED)</span>
                </div>
            </template>
        </div>
    </template>

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
                started: false, // Track if started
                isMuted: false,
                isPreview: false,

                init() {
                    // Check for muted query param
                    const urlParams = new URLSearchParams(window.location.search);
                    // If 'muted' param exists, we are in Preview Mode
                    if (urlParams.has('muted')) {
                        this.isPreview = true;
                        this.isMuted = true; // Default to muted for preview
                        this.started = false; // Do NOT auto-start, wait for user click
                    } else {
                        // Normal display mode
                        this.started = false; // Wait for interaction
                    }

                    // Start clock immediately
                    this.startClock();
                    // Listen for updates from Livewire
                    Livewire.on('content-updated', () => {
                        console.log('Content updated, reloading...');
                        window.location.reload();
                    });

                    // Start polling immediately for updates regardless of play state
                    setInterval(() => {
                        this.checkUpdates();
                    }, 5000);

                    // Note: We do NOT call playCurrent() here anymore for preview.
                    // It will be called by startSignage() when the user clicks the overlay.
                },

                toggleMute() {
                    this.isMuted = !this.isMuted;

                    // If playing a video, update its state immediately
                    const video = document.querySelector(`video[data-index="${this.currentIndex}"]`);
                    if (video) {
                        video.muted = this.isMuted;
                    }
                },

                startSignage() {
                    this.started = true;
                    // Try to unlock audio context if exists
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (AudioContext) {
                        const ctx = new AudioContext();
                        ctx.resume();
                    }
                    this.playCurrent();

                    // Start polling
                    setInterval(() => {
                        this.checkUpdates();
                    }, 5000);
                },

                checkUpdates() {
                    // This calls the PHP method
                    @this.checkForUpdates(this.hash);
                },

                playCurrent() {
                    if (this.media.length === 0) return;

                    const currentItem = this.media[this.currentIndex];

                    if (this.timer) clearTimeout(this.timer);

                    if (currentItem.type === 'image') {
                        // Settings duration is in seconds
                        const duration = (this.settings.image_duration || 10) * 1000;
                        this.timer = setTimeout(() => {
                            this.next();
                        }, duration);
                    } else if (currentItem.type === 'video') {
                        // Use querySelector to find the specific video element for this index
                        // Wait for Alpine to render/show it
                        this.$nextTick(() => {
                            const video = document.querySelector(`video[data-index="${this.currentIndex}"]`);
                            if (video) {
                                video.currentTime = 0;
                                // Respect isMuted flag
                                video.muted = this.isMuted;
                                video.play().catch(e => {
                                    console.error("Autoplay failed:", e);
                                });
                            }
                        });
                    }
                },

                next() {
                    this.currentIndex = (this.currentIndex + 1) % this.media.length;

                    // Allow UI to update before playing new item
                    this.$nextTick(() => {
                        this.playCurrent();
                    });
                },

                startClock() {
                    const updateTime = () => {
                        const now = new Date();
                        // Time: 14:30
                        this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        // Date: Senin, 16 Des 2024
                        this.date = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                    };

                    updateTime(); // Run immediately
                    setInterval(updateTime, 1000);
                }
            }));
        });
    </script>
</div>