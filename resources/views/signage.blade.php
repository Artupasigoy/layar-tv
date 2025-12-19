<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="robots" content="noindex, nofollow">
    <title>Layar TV - Digital Signage</title>

    <!-- Minimal inline CSS for performance -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #000;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .signage-container {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            background: #000;
            cursor: none;
        }

        .media-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .media-layer video,
        .media-layer img {
            max-width: 100%;
            max-height: 100%;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .media-layer.hidden {
            visibility: hidden;
            opacity: 0;
        }

        .media-layer.visible {
            visibility: visible;
            opacity: 1;
        }

        .fade-transition {
            transition: opacity 1s ease-in-out;
        }

        /* Ken Burns animation for images */
        @keyframes kenBurns {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.15);
            }
        }

        .ken-burns {
            animation: kenBurns 25s ease-out forwards;
        }

        /* Overlay: Logo */
        .logo-overlay {
            position: absolute;
            top: 24px;
            left: 24px;
            z-index: 50;
            pointer-events: none;
        }

        .logo-overlay img {
            height: 80px;
            width: auto;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.5));
        }

        /* Overlay: Clock */
        .clock-overlay {
            position: absolute;
            top: 24px;
            right: 24px;
            z-index: 50;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 16px 24px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            text-align: right;
        }

        .clock-time {
            font-size: 48px;
            font-weight: 700;
            letter-spacing: -1px;
        }

        .clock-date {
            font-size: 14px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        /* Start button overlay */
        .start-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .start-overlay.hidden {
            display: none;
        }

        .start-button {
            width: 100px;
            height: 100px;
            background: #4f46e5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        .start-button svg {
            width: 48px;
            height: 48px;
            fill: #fff;
        }

        .start-text {
            color: #fff;
            font-size: 24px;
            font-weight: 700;
            margin-top: 24px;
            letter-spacing: 2px;
        }

        .start-hint {
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
            margin-top: 8px;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        /* Loading state */
        .loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 90;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .loading-overlay.hidden {
            display: none;
        }

        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid rgba(255, 255, 255, 0.2);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .logo-overlay img {
                height: 48px;
            }

            .clock-overlay {
                padding: 10px 16px;
            }

            .clock-time {
                font-size: 28px;
            }

            .clock-date {
                font-size: 11px;
            }
        }
    </style>
</head>

<body>
    <div class="signage-container" id="signageContainer">
        <!-- Loading overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="spinner"></div>
        </div>

        <!-- Start button for audio activation -->
        <div class="start-overlay" id="startOverlay">
            <div class="start-button">
                <svg viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z" />
                </svg>
            </div>
            <div class="start-text">KLIK UNTUK MEMULAI</div>
            <div class="start-hint">Diperlukan untuk mengaktifkan suara</div>
        </div>

        <!-- Logo overlay -->
        <div class="logo-overlay" id="logoOverlay" style="display: none;">
            <img id="logoImage" src="" alt="Logo">
        </div>

        <!-- Clock overlay -->
        <div class="clock-overlay" id="clockOverlay" style="display: none;">
            <div class="clock-time" id="clockTime">00:00</div>
            <div class="clock-date" id="clockDate">-</div>
        </div>

        <!-- Video layer (single element, reused) -->
        <div class="media-layer hidden" id="videoLayer">
            <video id="videoPlayer" playsinline></video>
        </div>

        <!-- Image layer -->
        <div class="media-layer hidden" id="imageLayer">
            <img id="imagePlayer" src="" alt="">
        </div>
    </div>

    <!-- Vanilla JS Player - ~200 lines, no dependencies -->
    <script>
        (function () {
            'use strict';

            // Configuration
            const PLAYLIST_URL = '{{ $playlistUrl }}';
            const CHECK_INTERVAL = 0; // No polling! Zero network after load

            // State
            let playlist = null;
            let currentIndex = 0;
            let isStarted = false;
            let imageTimer = null;

            // DOM Elements
            const elements = {
                container: document.getElementById('signageContainer'),
                loading: document.getElementById('loadingOverlay'),
                start: document.getElementById('startOverlay'),
                logo: document.getElementById('logoOverlay'),
                logoImg: document.getElementById('logoImage'),
                clock: document.getElementById('clockOverlay'),
                clockTime: document.getElementById('clockTime'),
                clockDate: document.getElementById('clockDate'),
                videoLayer: document.getElementById('videoLayer'),
                video: document.getElementById('videoPlayer'),
                imageLayer: document.getElementById('imageLayer'),
                image: document.getElementById('imagePlayer')
            };

            // Initialize
            async function init() {
                try {
                    await loadPlaylist();
                    setupOverlays();
                    startClock();
                    setupAutoReload();
                    hideLoading();
                    preloadNext();
                } catch (error) {
                    console.error('Init failed:', error);
                    // Retry after 10 seconds
                    setTimeout(init, 10000);
                }
            }

            // Load playlist from static JSON (ONE request, no polling)
            async function loadPlaylist() {
                const response = await fetch(PLAYLIST_URL + '?t=' + Date.now());
                if (!response.ok) throw new Error('Failed to load playlist');
                playlist = await response.json();
                console.log('Playlist loaded:', playlist.media.length, 'items');
            }

            // Setup overlays based on settings
            function setupOverlays() {
                const settings = playlist.settings;

                // Logo
                if (settings.logo_path) {
                    elements.logoImg.src = settings.logo_path;
                    elements.logo.style.display = 'block';
                }

                // Clock
                if (settings.show_clock) {
                    elements.clock.style.display = 'block';
                }
            }

            // Clock update
            function startClock() {
                function update() {
                    const now = new Date();
                    elements.clockTime.textContent = now.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    elements.clockDate.textContent = now.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                }
                update();
                setInterval(update, 1000);
            }

            // Auto-reload for content updates (no polling, just full reload)
            function setupAutoReload() {
                const hours = playlist.settings.auto_reload_interval || 6;
                const ms = hours * 60 * 60 * 1000;
                console.log('Auto-reload scheduled in', hours, 'hours');
                setTimeout(() => location.reload(), ms);
            }

            // Hide loading overlay
            function hideLoading() {
                elements.loading.classList.add('hidden');
            }

            // Start signage (after user click for audio)
            function startSignage() {
                if (isStarted) return;
                isStarted = true;

                elements.start.classList.add('hidden');

                // Enable audio context
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (AudioContext) {
                        const ctx = new AudioContext();
                        ctx.resume();
                    }
                } catch (e) { }

                playCurrent();
            }

            // Play current media
            function playCurrent() {
                if (!playlist || playlist.media.length === 0) {
                    console.warn('No media to play');
                    return;
                }

                const item = playlist.media[currentIndex];
                clearTimeout(imageTimer);

                // Reset video
                elements.video.pause();
                elements.video.removeAttribute('src');
                elements.video.load();

                if (item.type === 'video') {
                    playVideo(item);
                } else {
                    playImage(item);
                }

                // Preload next
                preloadNext();
            }

            // Play video
            function playVideo(item) {
                elements.imageLayer.classList.remove('visible');
                elements.imageLayer.classList.add('hidden');

                elements.video.src = item.url;
                elements.video.muted = false; // Audio enabled per user request
                elements.video.onended = next;
                elements.video.onerror = function (e) {
                    console.error('Video error:', e);
                    next(); // Skip on error
                };

                elements.videoLayer.classList.remove('hidden');
                elements.videoLayer.classList.add('visible', 'fade-transition');

                elements.video.play().catch(e => {
                    console.error('Autoplay failed:', e);
                    // Try muted autoplay as fallback
                    elements.video.muted = true;
                    elements.video.play();
                });
            }

            // Play image
            function playImage(item) {
                elements.videoLayer.classList.remove('visible');
                elements.videoLayer.classList.add('hidden');

                elements.image.src = item.url;

                // Ken Burns effect - Force restart animation
                elements.image.classList.remove('ken-burns');
                if (playlist.settings.enable_animation) {
                    // Trigger reflow to restart animation
                    void elements.image.offsetWidth;
                    elements.image.classList.add('ken-burns');
                }

                elements.imageLayer.classList.remove('hidden');
                elements.imageLayer.classList.add('visible', 'fade-transition');

                // Timer for image duration
                const duration = (item.duration || playlist.settings.image_duration || 10) * 1000;
                imageTimer = setTimeout(next, duration);
            }

            // Next media
            function next() {
                currentIndex = (currentIndex + 1) % playlist.media.length;
                playCurrent();
            }

            // Preload next media
            function preloadNext() {
                if (!playlist || playlist.media.length < 2) return;

                const nextIndex = (currentIndex + 1) % playlist.media.length;
                const nextItem = playlist.media[nextIndex];

                if (nextItem.type === 'image') {
                    const img = new Image();
                    img.src = nextItem.url;
                }
                // Videos are preloaded by browser when possible
            }

            // Event listeners
            elements.start.addEventListener('click', startSignage);
            elements.container.addEventListener('click', startSignage);

            // Error recovery
            window.onerror = function (msg, url, line, col, error) {
                console.error('Global error:', msg);
                // Auto-reload on critical error
                setTimeout(() => location.reload(), 30000);
                return false;
            };

            // Start initialization
            init();
        })();
    </script>
</body>

</html>