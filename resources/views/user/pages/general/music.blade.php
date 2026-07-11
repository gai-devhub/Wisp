@extends('user.base-user')

@section('user-section', 'music-library')

@section('content')
    

    <div class="content-section active" id="music-library">
        <!-- Top Header -->
        <div class="music-header">
            <div class="music-header-left">
                <h2><i class="fab fa-spotify music-inline-1" ></i> Spotify Library</h2>
                @if(request('wish_message_id'))
                    <p  class="music-inline-2">
                        Assigning music to:
                        <strong  class="music-inline-3">{{ request('message_title', 'Selected Message') }}</strong>
                    </p>
                @else
                    <p  class="music-inline-4">Find the perfect background music.
                    </p>
                @endif
            </div>

            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="spotify-search-input" class="search-input"
                    placeholder="Search for tracks or albums..." autocomplete="off">
            </div>
        </div>

        <!-- Hidden form for saving -->
        <form id="save-music-form" action="{{ route('user.music.save') }}" method="POST"  class="music-inline-5">
            @csrf
            <input type="hidden" name="track_url" id="selected-track-url" required>
            <input type="hidden" name="wish_message_id" id="hidden_message_id" value="{{ request('wish_message_id') }}">
        </form>

        <!-- Search View -->
        <div class="view-container" id="search-view">
            <div id="search-loader" class="loader">
                <i class="fas fa-circle-notch fa-spin fa-2x"></i>
            </div>

            <div id="search-results"  class="music-inline-6">
                <h3 class="section-title">Albums</h3>
                <div class="albums-wrapper">
                    <button class="scroll-btn scroll-left music-inline-7" onclick="scrollAlbums(-1)" id="btn-scroll-left"
                        ><i class="fas fa-chevron-left"></i></button>
                    <div class="grid-container" id="albums-grid"></div>
                    <button class="scroll-btn scroll-right music-inline-8" onclick="scrollAlbums(1)" id="btn-scroll-right"
                        ><i class="fas fa-chevron-right"></i></button>
                </div>

                <h3 class="section-title">Tracks</h3>
                <div class="track-list" id="tracks-list"></div>
            </div>

            <div id="empty-state"  class="music-inline-9">
                <i class="fas fa-music fa-3x music-inline-10" ></i>
                <p>Use the search bar at the top right to find songs or albums on Spotify...</p>
            </div>
        </div>

        <!-- Album Takeover View -->
        <div class="view-container" id="album-view">
            <button class="back-btn" onclick="closeAlbumView()">
                <i class="fas fa-arrow-left"></i> Back
            </button>

            <div id="album-loader" class="loader">
                <i class="fas fa-circle-notch fa-spin fa-2x"></i>
            </div>

            <div id="album-content"  class="music-inline-11">
                <div class="album-header-hero">
                    <img src="" id="album-hero-img" class="album-header-cover" alt="Album Cover">
                    <div>
                        <h1 id="album-hero-title">Album Title</h1>
                        <div id="album-hero-artist"  class="music-inline-12">Artist Name
                        </div>
                    </div>
                </div>

                <h3 class="section-title">Tracks</h3>
                <div class="track-list" id="album-tracks-list"></div>
            </div>
        </div>

        <!-- Bottom Player Bar -->
        <div class="player-bar">
            <!-- Spotify Iframe -->
            <div class="spotify-iframe-container" id="spotify-iframe-container">
                <div class="spotify-placeholder">
                    <i class="fab fa-spotify music-inline-13" ></i>
                    <span>Select a track from the list above to preview it here</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('spotify-search-input');
        const searchView = document.getElementById('search-view');
        const searchLoader = document.getElementById('search-loader');
        const searchResults = document.getElementById('search-results');
        const emptyState = document.getElementById('empty-state');

        const albumView = document.getElementById('album-view');
        const albumLoader = document.getElementById('album-loader');
        const albumContent = document.getElementById('album-content');
        const iframeContainer = document.getElementById('spotify-iframe-container');
        const albumsGrid = document.getElementById('albums-grid');

        let searchTimeout = null;

        // Search Logic
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            clearTimeout(searchTimeout);

            if (!query) {
                emptyState.style.display = 'block';
                searchResults.style.display = 'none';
                searchLoader.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 500);
        });

        async function performSearch(query) {
            emptyState.style.display = 'none';
            searchResults.style.display = 'none';
            searchLoader.style.display = 'block';

            try {
                const response = await fetch(`{{ route('user.music.spotify.search') }}?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (data.error) throw new Error(data.error);

                renderAlbums(data.albums);
                renderTracks(data.tracks, 'tracks-list');

                searchLoader.style.display = 'none';
                searchResults.style.display = 'block';
            } catch (error) {
                console.error(error);
                searchLoader.style.display = 'none';
                emptyState.innerHTML = '<p  class="music-inline-14">Failed to fetch results. Make sure Spotify is configured correctly.</p>';
                emptyState.style.display = 'block';
            }
        }

        function renderAlbums(albums) {
            albumsGrid.innerHTML = '';

            if (!albums || albums.length === 0) {
                albumsGrid.innerHTML = '<p  class="music-inline-15">No albums found.</p>';
                document.getElementById('btn-scroll-left').style.display = 'none';
                document.getElementById('btn-scroll-right').style.display = 'none';
                return;
            }

            albums.forEach(album => {
                const coverUrl = album.images && album.images.length > 0 ? album.images[0].url : 'https://via.placeholder.com/150';
                const artistName = album.artists && album.artists.length > 0 ? album.artists[0].name : 'Unknown Artist';

                const card = document.createElement('div');
                card.className = 'album-card';
                card.onclick = () => openAlbumView(album.id);
                card.innerHTML = `
                    <img src="${coverUrl}" alt="${album.name}">
                    <h4>${album.name}</h4>
                    <p>${artistName}</p>
                `;
                albumsGrid.appendChild(card);
            });

            // Show arrows if there are more than 6 albums
            if (albums.length > 6) {
                document.getElementById('btn-scroll-left').style.display = 'flex';
                document.getElementById('btn-scroll-right').style.display = 'flex';
            } else {
                document.getElementById('btn-scroll-left').style.display = 'none';
                document.getElementById('btn-scroll-right').style.display = 'none';
            }
        }

        function scrollAlbums(direction) {
            const scrollAmount = 450; // Scroll by roughly 3 cards
            albumsGrid.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
        }

        function renderTracks(tracks, containerId, parentAlbumCover = null) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';

            if (!tracks || tracks.length === 0) {
                container.innerHTML = '<p  class="music-inline-16">No tracks found.</p>';
                return;
            }

            tracks.forEach(track => {
                const coverUrl = parentAlbumCover || (track.album && track.album.images && track.album.images.length > 0 ? track.album.images[0].url : 'https://via.placeholder.com/50');
                const artistName = track.artists && track.artists.length > 0 ? track.artists[0].name : 'Unknown Artist';
                const saveUrl = track.external_urls ? track.external_urls.spotify : '';

                const row = document.createElement('div');
                row.className = 'track-row';
                row.setAttribute('data-track-id', track.id);
                row.setAttribute('data-save-url', saveUrl);

                // Single click handler for the whole row
                row.addEventListener('click', function (e) {
                    // If the save button or an icon inside it was clicked, don't trigger selectRow
                    if (e.target.closest('.row-save-btn')) return;
                    selectRow(this);
                });

                row.innerHTML = `
                    <img src="${coverUrl}" class="track-cover" alt="Cover">
                    <div class="track-info">
                        <h4 class="track-title">${track.name}</h4>
                        <p class="track-artist">${artistName}</p>
                    </div>
                    <div class="track-actions">
                        <input type="radio" name="music_selection" class="custom-radio" value="${saveUrl}" 
                               data-track-id="${track.id}">
                        <button type="button" class="row-save-btn" onclick="event.stopPropagation(); submitMusicRow(this, '${saveUrl}')" title="Save Track">
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);
            });
        }

        // Row Click Logic
        function selectRow(rowElement) {
            if (!rowElement) return;

            // Find the radio inside this row
            const radio = rowElement.querySelector('input[type="radio"]');
            if (!radio) return;

            // Visual selection
            document.querySelectorAll('.track-row').forEach(r => r.style.background = 'transparent');
            document.querySelectorAll('.track-row').forEach(r => r.style.borderColor = 'transparent');
            rowElement.style.background = 'var(--music-card-hover)';
            rowElement.style.borderColor = 'var(--music-border)';

            radio.checked = true;

            // Update hidden form
            const trackUrl = radio.value;
            const selectedUrlInput = document.getElementById('selected-track-url');
            if (selectedUrlInput) selectedUrlInput.value = trackUrl;

            // Load Iframe Preview
            const trackId = radio.getAttribute('data-track-id');
            const playerContainer = document.getElementById('spotify-iframe-container');

            if (trackId && playerContainer) {
                playerContainer.innerHTML = `
                    <iframe 
                         
                        src="https://open.spotify.com/embed/track/${trackId}?utm_source=generator" 
                        width="100%" 
                        height="80" 
                        frameBorder="0" 
                        allowfullscreen="" 
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" 
                        loading="lazy" class="music-inline-17">
                    </iframe>`;
            }
        }

        function submitMusicRow(btnElement, saveUrl) {
            let msgId = document.getElementById('hidden_message_id');

            if (!msgId || !msgId.value) {
                alert('Error: Message ID is missing. Please ensure you accessed this page from the Media Management page.');
                return;
            }

            const rowElement = btnElement.closest('.track-row');
            let trackName = "Unknown Track";
            if (rowElement) {
                const titleEl = rowElement.querySelector('.track-title');
                const artistEl = rowElement.querySelector('.track-artist');
                
                if (titleEl && artistEl) {
                    trackName = titleEl.textContent + " by " + artistEl.textContent;
                } else if (titleEl) {
                    trackName = titleEl.textContent;
                }
            }

            const params = new URLSearchParams({
                wish_message_id: msgId.value,
                spotify_url: saveUrl,
                spotify_name: trackName
            });
            window.location.href = "{{ url('/user-page/media') }}?" + params.toString();
        }

        // Album Takeover Logic
        async function openAlbumView(albumId) {
            searchView.style.display = 'none';
            albumView.style.display = 'block';
            albumContent.style.display = 'none';
            albumLoader.style.display = 'block';

            try {
                const response = await fetch(`{{ url('/user-page/music/spotify/album') }}/${albumId}`);
                const album = await response.json();

                if (album.error) throw new Error(album.error);

                const coverUrl = album.images && album.images.length > 0 ? album.images[0].url : 'https://via.placeholder.com/300';
                const artistName = album.artists && album.artists.length > 0 ? album.artists[0].name : 'Unknown Artist';

                document.getElementById('album-hero-img').src = coverUrl;
                document.getElementById('album-hero-title').textContent = album.name;
                document.getElementById('album-hero-artist').textContent = artistName;

                renderTracks(album.tracks.items, 'album-tracks-list', coverUrl);

                albumLoader.style.display = 'none';
                albumContent.style.display = 'block';
            } catch (error) {
                console.error(error);
                closeAlbumView();
            }
        }

        function closeAlbumView() {
            albumView.style.display = 'none';
            searchView.style.display = 'block';
        }
    </script>
@endsection