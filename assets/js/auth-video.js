/**
 * GMIU Portal — High-Performance Auth Video Stream Manager
 * Features:
 * - HLS Segment Streaming (Loads lightweight ~100-200KB chunks instead of 9MB monolithic files)
 * - Sub-200ms Startup Time with Instant 0ms WebP Poster Rendering
 * - Native Safari / iOS HLS Support + Hls.js MSE Engine for Chrome, Edge, Firefox, Android
 * - Seamless Dynamic Stream Switching between Login and Sign Up
 * - Faststart Progressive MP4 Fallback for Offline / Degraded Network Conditions
 */
(function (window) {
  'use strict';

  let currentHls = null;
  let currentActiveMode = null;

  function initAuthVideo(initialMode) {
    const video = document.getElementById('showcaseVideo');
    if (!video) return;

    video.muted = true;

    // Helper to start playback smoothly
    function triggerPlay() {
      const p = video.play();
      if (p && typeof p.catch === 'function') {
        p.catch(() => {
          // Autoplay policy fallback: resume on user's first interaction
          window.addEventListener('click', () => video.play(), { once: true });
          window.addEventListener('touchstart', () => video.play(), { once: true });
        });
      }
    }

    function loadStream(mode) {
      if (currentActiveMode === mode && video.currentTime > 0) return;
      currentActiveMode = mode;

      const isSignup = mode === 'signup';
      const isOtp = mode === 'otp';

      // Pick URLs based on mode and data attributes
      let hlsUrl, mp4Url, posterUrl;

      if (isOtp) {
        hlsUrl = video.dataset.hlsOtp || (window.BASE_URL ? `${window.BASE_URL}assets/video/hls/otp/index.m3u8` : 'assets/video/hls/otp/index.m3u8');
        mp4Url = video.dataset.mp4Otp || (window.BASE_URL ? `${window.BASE_URL}assets/video/otp_security.mp4` : 'assets/video/otp_security.mp4');
        posterUrl = video.dataset.posterOtp || (window.BASE_URL ? `${window.BASE_URL}assets/video/posters/poster_otp.webp` : 'assets/video/posters/poster_otp.webp');
      } else if (isSignup) {
        hlsUrl = video.dataset.hlsSignup || (window.BASE_URL ? `${window.BASE_URL}assets/video/hls/signup/index.m3u8` : 'assets/video/hls/signup/index.m3u8');
        mp4Url = video.dataset.mp4Signup || (window.BASE_URL ? `${window.BASE_URL}assets/video/2.mp4` : 'assets/video/2.mp4');
        posterUrl = video.dataset.posterSignup || (window.BASE_URL ? `${window.BASE_URL}assets/video/posters/poster_signup.webp` : 'assets/video/posters/poster_signup.webp');
      } else {
        hlsUrl = video.dataset.hlsLogin || (window.BASE_URL ? `${window.BASE_URL}assets/video/hls/login/index.m3u8` : 'assets/video/hls/login/index.m3u8');
        mp4Url = video.dataset.mp4Login || (window.BASE_URL ? `${window.BASE_URL}assets/video/1.mp4` : 'assets/video/1.mp4');
        posterUrl = video.dataset.posterLogin || (window.BASE_URL ? `${window.BASE_URL}assets/video/posters/poster_login.webp` : 'assets/video/posters/poster_login.webp');
      }

      if (posterUrl && !video.poster.includes(posterUrl)) {
        video.poster = posterUrl;
      }

      // Cleanup existing HLS instance if switching streams
      if (currentHls) {
        currentHls.destroy();
        currentHls = null;
      }

      // 1. Try Hls.js (Chrome, Firefox, Edge, Android Opera)
      if (window.Hls && window.Hls.isSupported()) {
        const hls = new window.Hls({
          enableWorker: true,
          lowLatencyMode: false,
          backBufferLength: 4,
          maxBufferLength: 8,
          maxMaxBufferLength: 16,
          autoStartLoad: true
        });

        hls.loadSource(hlsUrl);
        hls.attachMedia(video);

        hls.on(window.Hls.Events.MANIFEST_PARSED, function () {
          triggerPlay();
        });

        hls.on(window.Hls.Events.ERROR, function (event, data) {
          if (data && data.fatal) {
            console.warn('[AuthVideo] HLS encountered fatal error, falling back to progressive MP4:', data.type);
            hls.destroy();
            currentHls = null;
            video.src = mp4Url;
            triggerPlay();
          }
        });

        currentHls = hls;
      }
      // 2. Try Native Safari / iOS HLS
      else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = hlsUrl;
        triggerPlay();
      }
      // 3. Fallback: Ultra-fast Faststart Progressive MP4
      else {
        video.src = mp4Url;
        triggerPlay();
      }
    }

    // Ensure seamless looping
    video.addEventListener('ended', function () {
      video.currentTime = 0;
      triggerPlay();
    });

    // Expose switch function globally
    window.switchAuthVideo = loadStream;

    // Load initial stream
    loadStream(initialMode || (video.dataset.currentMode || 'login'));
  }

  // Auto-init on DOMContentLoaded or immediate if ready
  window.initAuthVideo = initAuthVideo;
  if (document.readyState === 'interactive' || document.readyState === 'complete') {
    // Delay slightly to allow view scripts to define state if needed
    setTimeout(() => {
      const v = document.getElementById('showcaseVideo');
      if (v && !window._authVideoInitialized) {
        window._authVideoInitialized = true;
        initAuthVideo(v.dataset.currentMode || 'login');
      }
    }, 10);
  } else {
    document.addEventListener('DOMContentLoaded', () => {
      const v = document.getElementById('showcaseVideo');
      if (v && !window._authVideoInitialized) {
        window._authVideoInitialized = true;
        initAuthVideo(v.dataset.currentMode || 'login');
      }
    });
  }

})(window);

