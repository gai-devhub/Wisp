@props(['mediaFiles'])

@php
    $music = $mediaFiles->background_music ?? null;
    $appleMusicUrl = $mediaFiles->apple_music_url ?? null;
    $isSpotify = false;
    $spotifyUri = '';
    $spotifyTitle = '';

    if ($music && (str_contains($music, 'spotify.com') || str_contains($music, 'spotify:'))) {
        $isSpotify = true;
        if (preg_match('/(track|album|playlist)[\/|:]([a-zA-Z0-9]{22})/', $music, $matches)) {
            $spotifyUri = 'spotify:' . $matches[1] . ':' . $matches[2];
            try {
                $oembed = \Illuminate\Support\Facades\Http::timeout(4)
                    ->get('https://open.spotify.com/oembed', ['url' => $music]);
                if ($oembed->successful()) {
                    $spotifyTitle = $oembed->json('title', '');
                }
            } catch (\Throwable $e) {
                // Title optional
            }
        }
    }
@endphp

@if($music)
    @if($isSpotify && $spotifyUri)
        {{-- Spotify: UI + logic live only in this component --}}
        <div id="spotifyPlayer" class="spotify-audio-player">
            <div id="spotifyControls" class="spotify-controls">
                <button type="button" id="toggleSpotify" class="spotify-toggle-btn" aria-label="Play music" disabled>
                    <span class="spotify-icon spotify-icon-play" aria-hidden="true"></span>
                    <span class="spotify-icon spotify-icon-pause is-hidden" aria-hidden="true"></span>
                </button>
                <span id="spotifyTrackTitle" class="spotify-track-title" hidden>{{ $spotifyTitle }}</span>
            </div>
        </div>

        <div class="spotify-embed-host" aria-hidden="true">
            <div
                id="spotifyEmbedHost"
                data-spotify-uri="{{ $spotifyUri }}"
                data-music-url="{{ $music }}"
            ></div>
        </div>

        <script>
            // Store the API globally in case it loads before DOM ready
            let _spotifyIFrameAPI = null;
            let _spotifyInitController = null;

            window.onSpotifyIframeApiReady = function(IFrameAPI) {
                _spotifyIFrameAPI = IFrameAPI;
                if (_spotifyInitController) {
                    _spotifyInitController(IFrameAPI);
                }
            };
        </script>
        <script src="https://open.spotify.com/embed/iframe-api/v1" async></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toggleBtn = document.getElementById('toggleSpotify');
                const titleEl = document.getElementById('spotifyTrackTitle');
                const playerEl = document.getElementById('spotifyPlayer');
                const embedHost = document.getElementById('spotifyEmbedHost');
                if (!toggleBtn || !embedHost) return;

                const spotifyUri = embedHost.dataset.spotifyUri;
                const musicUrl = embedHost.dataset.musicUrl;
                let embedController = null;
                let isPlaying = false;

                function showTitle() {
                    if (!titleEl) return;
                    playerEl?.classList.add('is-expanded');
                    titleEl.hidden = false;
                }

                async function ensureTitle() {
                    if (!titleEl || titleEl.textContent.trim()) {
                        showTitle();
                        return;
                    }
                    try {
                        const res = await fetch(
                            'https://open.spotify.com/oembed?url=' + encodeURIComponent(musicUrl)
                        );
                        if (res.ok) {
                            const data = await res.json();
                            if (data.title) titleEl.textContent = data.title;
                        }
                    } catch (e) { /* ignore */ }
                    showTitle();
                }

                const iconPlay = toggleBtn.querySelector('.spotify-icon-play');
                const iconPause = toggleBtn.querySelector('.spotify-icon-pause');

                function setPlaying(playing) {
                    isPlaying = playing;
                    toggleBtn.classList.toggle('is-playing', playing);
                    toggleBtn.setAttribute('aria-label', playing ? 'Pause music' : 'Play music');
                    if (iconPlay) iconPlay.classList.toggle('is-hidden', playing);
                    if (iconPause) iconPause.classList.toggle('is-hidden', !playing);
                    localStorage.setItem('musicPlaying', playing ? 'true' : 'false');
                }

                let controllerReady = false;

                function initController(IFrameAPI) {
                    if (embedController) return;

                    IFrameAPI.createController(embedHost, {
                        uri: spotifyUri,
                        width: '280',
                        height: '80',
                    }, function (controller) {
                        embedController = controller;

                        controller.addListener('ready', function () {
                            controllerReady = true;
                            toggleBtn.disabled = false;

                            if (localStorage.getItem('musicPlaying') === 'true') {
                                setPlaying(true);
                                controller.play();
                            }
                        });

                        controller.addListener('playback_started', function () {
                            setPlaying(true);
                            ensureTitle();
                        });

                        controller.addListener('playback_paused', function () {
                            setPlaying(false);
                        });

                        controller.addListener('playback_stopped', function () {
                            setPlaying(false);
                        });
                    });
                }

                toggleBtn.addEventListener('click', function () {
                    if (!embedController || !controllerReady) return;

                    ensureTitle();

                    const willPlay = !isPlaying;
                    setPlaying(willPlay);

                    if (willPlay) {
                        embedController.play();
                    } else {
                        embedController.pause();
                    }
                });

                _spotifyInitController = initController;
                if (_spotifyIFrameAPI) {
                    _spotifyInitController(_spotifyIFrameAPI);
                } else if (window.SpotifyIframeApi) {
                    _spotifyInitController(window.SpotifyIframeApi);
                }
            });
        </script>

        <style>
            .spotify-audio-player {
                position: fixed;
                bottom: 25px;
                right: 25px;
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(5px);
                padding: 10px;
                border-radius: 50px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                z-index: 1000;
                transition: all 0.3s ease;
            }
            .spotify-controls {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .spotify-toggle-btn {
                background: #1DB954;
                color: white;
                border: none;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                transition: transform 0.2s, opacity 0.2s;
                padding: 0;
            }
            .spotify-toggle-btn:disabled {
                opacity: 0.6;
                cursor: wait;
            }
            .spotify-toggle-btn:not(:disabled):hover { transform: scale(1.1); }
            .spotify-icon {
                display: block;
                pointer-events: none;
            }
            .spotify-icon.is-hidden {
                display: none;
            }
            .spotify-icon-play {
                width: 0;
                height: 0;
                border-style: solid;
                border-width: 7px 0 7px 12px;
                border-color: transparent transparent transparent #fff;
                margin-left: 3px;
            }
            .spotify-icon-pause {
                width: 12px;
                height: 14px;
                position: relative;
            }
            .spotify-icon-pause::before,
            .spotify-icon-pause::after {
                content: '';
                position: absolute;
                top: 0;
                width: 4px;
                height: 14px;
                background: #fff;
                border-radius: 1px;
            }
            .spotify-icon-pause::before { left: 0; }
            .spotify-icon-pause::after { right: 0; }
            .spotify-track-title {
                font-size: 0.9rem;
                font-weight: 600;
                color: #191414;
                max-width: 0;
                opacity: 0;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                transition: max-width 0.35s ease, opacity 0.3s ease;
            }
            .spotify-audio-player.is-expanded .spotify-track-title {
                max-width: 220px;
                opacity: 1;
                margin-right: 4px;
            }
            .spotify-embed-host {
                position: fixed;
                width: 1px;
                height: 1px;
                bottom: 0;
                right: 0;
                overflow: hidden;
                opacity: 0;
                pointer-events: none;
                z-index: -1;
            }
            @media (max-width: 600px) {
                .spotify-audio-player { bottom: 15px; right: 15px; padding: 8px; }
                .spotify-toggle-btn { width: 35px; height: 35px; }
                .spotify-audio-player.is-expanded .spotify-track-title { max-width: 140px; }
            }
        </style>
    @else
        {{-- Local: template supplies player HTML/CSS via slot; this component supplies audio + JS --}}
        {{ $slot }}

        <audio id="bgMusic" loop>
            <source src="{{ asset('storage/' . $music) }}" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>

        @once('local-background-music-script')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const bgMusic = document.getElementById('bgMusic');
                    if (!bgMusic) return;

                    const iconState = document.getElementById('icon-state');
                    const playBtn = document.getElementById('playBtn');
                    const playBtnAlt = document.getElementById('play-btn');
                    const musicDisc = document.getElementById('music-disc');
                    const musicButton = document.getElementById('music-button');
                    const playBtnClass = document.querySelector('.play-btn:not(#toggleAudio)');

                    bgMusic.volume = 0.5;

                    function setPlayingUi(playing) {
                        const toggleAudio = document.getElementById('toggleAudio');
                        const musicTrigger = document.getElementById('music-trigger');
                        const musicToggle = document.getElementById('music-toggle');
                        const audioTrigger = document.getElementById('audio-trigger');

                        if (toggleAudio) {
                            toggleAudio.textContent = playing ? '❚❚' : (toggleAudio.dataset.idleLabel || '♪');
                            toggleAudio.classList.toggle('playing', playing);
                        }
                        if (musicTrigger) {
                            musicTrigger.textContent = playing ? 'PAUSE' : (musicTrigger.dataset.idleLabel || 'ENABLE AUDIO');
                        }
                        if (audioTrigger) {
                            audioTrigger.textContent = playing ? 'SUSPEND AUDIO' : (audioTrigger.dataset.idleLabel || 'INITIATE AUDIO');
                        }
                        if (iconState) {
                            iconState.textContent = playing ? '❚❚' : '♫';
                        } else if (musicToggle) {
                            musicToggle.textContent = playing ? 'PAUSE' : (musicToggle.dataset.idleLabel || musicToggle.textContent || 'PLAY');
                        }
                        if (playBtn) {
                            playBtn.textContent = playing ? '⏸ Pause' : (playBtn.dataset.idleLabel || '🎵 Play Music');
                        }
                        if (playBtnAlt) {
                            playBtnAlt.textContent = playing ? 'PAUSE ATMOSPHERE' : (playBtnAlt.dataset.idleLabel || 'Play');
                        }
                        if (musicDisc) {
                            musicDisc.classList.toggle('playing', playing);
                            musicDisc.textContent = playing ? '❚❚' : (musicDisc.dataset.idleLabel || '♪');
                        }
                        if (musicButton) {
                            musicButton.style.color = playing ? '#d63031' : (musicButton.dataset.idleColor || '#e5b0a3');
                        }
                        if (playBtnClass) {
                            playBtnClass.textContent = playing ? '❚❚' : (playBtnClass.dataset.idleLabel || '♪');
                        }
                        const playControl = document.getElementById('play-control');
                        if (playControl) {
                            playControl.textContent = playing ? 'PAUSE MUSIC' : (playControl.dataset.idleLabel || 'PLAY MUSIC');
                        }
                        const audioBtn = document.getElementById('audio-btn');
                        if (audioBtn) {
                            audioBtn.textContent = playing ? 'PAUSE' : (audioBtn.dataset.idleLabel || 'AUDIO');
                        }
                        const audioToggle = document.getElementById('audio-toggle');
                        if (audioToggle && audioToggle.id !== 'toggleAudio') {
                            audioToggle.textContent = playing ? '❚❚' : (audioToggle.dataset.idleLabel || '♪');
                        }
                        const musicBtn = document.getElementById('music-btn');
                        if (musicBtn) {
                            musicBtn.textContent = playing ? '❚❚' : (musicBtn.dataset.idleLabel || musicBtn.textContent.trim() || '♪');
                        }
                        const audioControl = document.getElementById('audio-control');
                        if (audioControl) {
                            audioControl.textContent = playing ? 'PAUSE AUDIO' : (audioControl.dataset.idleLabel || 'ACTIVATE AUDITORY ATMOSPHERE');
                        }
                    }

                    function togglePlayback() {
                        if (bgMusic.paused) {
                            bgMusic.play().catch(function () {});
                            setPlayingUi(true);
                            localStorage.setItem('musicPlaying', 'true');
                            window.dispatchEvent(new CustomEvent('wisp:music-play'));
                        } else {
                            bgMusic.pause();
                            setPlayingUi(false);
                            localStorage.setItem('musicPlaying', 'false');
                        }
                    }

                    window.togglePlay = togglePlayback;
                    window.toggleAudio = togglePlayback;

                    const playTriggers = new Set();
                    document.querySelectorAll(
                        '#toggleAudio, #audio-btn, #audio-toggle, #music-btn, #audio-control, #music-trigger, #music-toggle, #audio-trigger, #play-control, #playBtn, #play-btn, #music-disc, #music-button, .play-btn, .music-action-btn'
                    ).forEach(function (el) { playTriggers.add(el); });
                    playTriggers.forEach(function (el) {
                        el.removeAttribute('onclick');
                        if (el.id === 'music-toggle' || el.classList.contains('music-action-btn')) {
                            el.style.cursor = 'pointer';
                        }
                        el.addEventListener('click', togglePlayback);
                    });

                    bgMusic.addEventListener('play', function () { setPlayingUi(true); });
                    bgMusic.addEventListener('pause', function () { setPlayingUi(false); });

                    if (localStorage.getItem('musicPlaying') === 'true') {
                        bgMusic.play().catch(function () {});
                        setPlayingUi(true);
                    }
                });
            </script>
        @endonce
    @endif
@elseif($appleMusicUrl)
    @php
        $appleMusicPreviewUrl = null;
        $appleMusicTitle = '';
        $parsed = parse_url($appleMusicUrl);
        $lookupId = null;
        
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $appleQuery);
            if (isset($appleQuery['i'])) {
                $lookupId = $appleQuery['i'];
            }
        }
        
        if (!$lookupId && isset($parsed['path'])) {
            if (preg_match('/\/(\d+)$/', $parsed['path'], $matches)) {
                $lookupId = $matches[1];
            }
        }

        if ($lookupId) {
            try {
                $itunes = \Illuminate\Support\Facades\Http::timeout(4)->get('https://itunes.apple.com/lookup', [
                    'id' => $lookupId,
                    'entity' => 'song'
                ]);
                if ($itunes->successful()) {
                    $results = $itunes->json('results');
                    if (!empty($results)) {
                        foreach ($results as $result) {
                            if (!empty($result['previewUrl'])) {
                                $appleMusicPreviewUrl = $result['previewUrl'];
                                $appleMusicTitle = $result['trackName'] ?? 'Apple Music Track';
                                break;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }
    @endphp

    <div id="applePlayer" class="apple-audio-player">
        <div id="appleControls" class="apple-controls">
            <button type="button" id="toggleApple" class="apple-toggle-btn" aria-label="Play music">
                <span class="apple-icon apple-icon-play" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white">
                        <path d="M21 3v10.5a4.5 4.5 0 1 1-2-3.72V6.47L9 8.6v8.4a4.5 4.5 0 1 1-2-3.72V4l14-3v2z" />
                    </svg>
                </span>
                <span class="apple-icon apple-icon-pause is-hidden" aria-hidden="true"></span>
            </button>
            <span id="appleTrackTitle" class="apple-track-title" hidden>{{ $appleMusicTitle ?: 'Apple Music' }}</span>
        </div>
    </div>
    
    @if($appleMusicPreviewUrl)
        <audio id="appleBgMusic" loop>
            <source src="{{ $appleMusicPreviewUrl }}" type="audio/x-m4a">
        </audio>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggleApple');
            const titleEl = document.getElementById('appleTrackTitle');
            const playerEl = document.getElementById('applePlayer');
            const bgMusic = document.getElementById('appleBgMusic');
            if (!toggleBtn) return;

            const iconPlay = toggleBtn.querySelector('.apple-icon-play');
            const iconPause = toggleBtn.querySelector('.apple-icon-pause');
            let isPlaying = false;

            if (bgMusic) {
                bgMusic.volume = 0.5;
            }

            function showTitle() {
                if (!titleEl) return;
                playerEl?.classList.add('is-expanded');
                titleEl.hidden = false;
            }

            function setPlaying(playing) {
                isPlaying = playing;
                toggleBtn.classList.toggle('is-playing', playing);
                toggleBtn.setAttribute('aria-label', playing ? 'Pause music' : 'Play music');
                if (iconPlay) iconPlay.classList.toggle('is-hidden', playing);
                if (iconPause) iconPause.classList.toggle('is-hidden', !playing);
                localStorage.setItem('musicPlaying', playing ? 'true' : 'false');
            }

            toggleBtn.addEventListener('click', function () {
                showTitle();
                if (bgMusic) {
                    if (bgMusic.paused) {
                        bgMusic.play().catch(function(){});
                        setPlaying(true);
                    } else {
                        bgMusic.pause();
                        setPlaying(false);
                    }
                } else {
                    // Fallback if no preview URL
                    window.open("{{ $appleMusicUrl }}", "_blank");
                }
            });

            if (bgMusic && localStorage.getItem('musicPlaying') === 'true') {
                bgMusic.play().catch(function(){});
                setPlaying(true);
                showTitle();
            }
        });
    </script>
    
    <style>
        .apple-audio-player {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            padding: 10px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .apple-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .apple-toggle-btn {
            background: #FA243C; /* Apple Music Pink/Red */
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.2s, opacity 0.2s;
            padding: 0;
        }
        .apple-toggle-btn:hover { transform: scale(1.1); }
        .apple-icon { display: block; pointer-events: none; }
        .apple-icon.is-hidden { display: none; }
        .apple-icon-play {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .apple-icon-pause {
            width: 12px; height: 14px; position: relative;
        }
        .apple-icon-pause::before, .apple-icon-pause::after {
            content: ''; position: absolute; top: 0; width: 4px; height: 14px; background: #fff; border-radius: 1px;
        }
        .apple-icon-pause::before { left: 0; }
        .apple-icon-pause::after { right: 0; }
        .apple-track-title {
            font-size: 0.9rem; font-weight: 600; color: #191414;
            max-width: 0; opacity: 0; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;
            transition: max-width 0.35s ease, opacity 0.3s ease;
        }
        .apple-audio-player.is-expanded .apple-track-title {
            max-width: 220px; opacity: 1; margin-right: 4px;
        }
        @media (max-width: 600px) {
            .apple-audio-player { bottom: 15px; right: 15px; padding: 8px; }
            .apple-toggle-btn { width: 35px; height: 35px; }
            .apple-audio-player.is-expanded .apple-track-title { max-width: 140px; }
        }
    </style>
@endif
