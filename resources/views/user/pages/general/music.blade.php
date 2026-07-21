@extends('user.base-user')

@section('user-section', 'music-library')

@push('styles')
<style>
    #music-library {
        background: #fafafa;
        position: fixed;
        top: 90px;
        height: calc(100vh - 140px);
        left: calc(var(--sidebar-width, 240px) + 24px);
        right: 24px;
        margin: 0 auto;
        max-width: 1000px;
        display: flex;
        flex-direction: column;
        border-radius: 16px;
        overflow: hidden;
        font-family: 'Outfit', sans-serif;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        z-index: 50;
    }
    
    /* Fix global CSS overriding iframe height to auto */
    .spotify-iframe-container iframe {
        height: 80px !important;
    }

    .music-header {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 24px 40px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        z-index: 10;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    }

    .music-header-left h2 {
        font-weight: 700;
        font-size: 1.8rem;
        margin: 0 0 6px 0;
        color: #111;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: -0.5px;
    }

    .music-header-left h2 i {
        color: #1DB954;
        font-size: 2.2rem;
    }

    .music-header-left p {
        color: #666;
        font-size: 0.95rem;
        margin: 0;
        font-weight: 400;
    }

    .music-header-left p strong {
        color: #1DB954;
        font-weight: 600;
    }

    .search-container {
        position: relative;
        width: 100%;
        max-width: 400px;
    }

    .search-input {
        width: 100%;
        padding: 14px 20px 14px 48px;
        border-radius: 50px;
        border: 1px solid rgba(0,0,0,0.1);
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        color: #333;
    }

    .search-input::placeholder {
        color: #aaa;
    }

    .search-input:focus {
        outline: none;
        border-color: #1DB954;
        box-shadow: 0 0 0 4px rgba(29, 185, 84, 0.15);
    }

    .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 1.1rem;
        transition: color 0.3s;
    }

    .search-input:focus + .search-icon,
    .search-input:focus ~ .search-icon {
        color: #1DB954;
    }

    .view-container {
        flex: 1;
        overflow-y: auto;
        padding: 32px 40px;
        background: #fdfdfd;
    }

    #empty-state {
        text-align: center;
        padding: 100px 40px;
        color: #888;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 250px;
    }

    #empty-state i {
        font-size: 4rem;
        color: #1DB954;
        opacity: 0.2;
        margin-bottom: 24px;
    }

    #empty-state p {
        font-size: 1.1rem;
        font-weight: 500;
        max-width: 400px;
        line-height: 1.5;
        margin: 0;
    }

    .section-title {
        font-weight: 700;
        font-size: 1.3rem;
        margin: 0 0 20px 0;
        color: #222;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding-bottom: 12px;
        letter-spacing: -0.3px;
    }

    .album-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: left;
        flex: 0 0 160px;
        width: 160px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }

    .album-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        border-color: rgba(29, 185, 84, 0.3);
    }

    .album-card img {
        width: 128px;
        height: 128px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 16px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .album-card h4 {
        font-weight: 600;
        font-size: 0.85rem;
        color: #222;
        margin: 0 0 4px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .album-card p {
        font-size: 0.75rem;
        color: #777;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .track-row {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
    }

    .track-row:hover {
        background: #f8fcf9;
        border-color: rgba(29, 185, 84, 0.2);
        transform: translateX(4px);
    }

    .track-cover {
        width: 48px;
        height: 48px;
        border-radius: 6px;
        object-fit: cover;
        margin-right: 16px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .track-info {
        flex: 1;
        min-width: 0;
    }

    .track-title {
        font-weight: 600;
        font-size: 0.9rem;
        color: #222;
        margin: 0 0 4px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .track-artist {
        font-size: 0.8rem;
        color: #777;
        margin: 0;
    }

    .track-actions {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .custom-radio {
        accent-color: #1DB954;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .row-save-btn {
        background: #1DB954;
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(29, 185, 84, 0.3);
    }

    .row-save-btn:hover {
        background: #1ed760;
        transform: scale(1.1);
    }

    .player-bar {
        background: #ffffff;
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 24px 24px; /* Added breathing room back */
        box-shadow: 0 -4px 20px rgba(0,0,0,0.02);
        z-index: 10;
        height: auto; 
        flex-shrink: 0; 
    }

    @media (max-width: 768px) {
        .player-bar {
            padding: 20px;
        }
        
        #music-library {
            top: calc(60px + 16px); /* 60px header + 16px gap */
            height: calc(100vh - 60px - 32px); /* 100vh minus header minus top/bottom gaps */
            left: 16px;
            right: 16px;
            margin: 0;
            border-radius: 16px;
            max-width: none;
        }
        
        .view-container {
            padding: 20px;
        }
        
        .music-header {
            padding: 20px;
        }
        
        .album-card h4 {
            font-size: 0.75rem;
        }
        
        .album-card p {
            font-size: 0.55rem;
        }
        
        .track-title {
            font-size: 0.9rem;
        }
        
        .track-artist {
            font-size: 0.75rem;
        }
    }

    .spotify-iframe-container {
        border-radius: 12px;
        overflow: hidden;
        background: transparent; 
        height: auto; 
        min-height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .spotify-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 500;
        height: auto;
        min-height: 80px; /* Much neater than filling the whole space */
        width: 100%;
        max-width: 600px; /* Give it a nice max-width so it's a neat pill */
        margin: 0 auto;
        padding: 16px 24px;
        background: #fafafa;
        border-radius: 12px;
        border: 1px dashed rgba(0,0,0,0.1);
        gap: 12px;
    }

    .spotify-placeholder i {
        font-size: 1.5rem;
        color: #1DB954;
    }

    .album-header-hero {
        display: flex;
        align-items: flex-end;
        gap: 32px;
        margin-bottom: 40px;
        padding-bottom: 32px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .album-header-cover {
        width: 200px;
        height: 200px;
        border-radius: 12px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        object-fit: cover;
    }

    #album-hero-title {
        font-size: 3rem;
        font-weight: 800;
        letter-spacing: -1px;
        color: #111;
        margin: 0 0 12px 0;
        line-height: 1.1;
    }

    #album-hero-artist {
        font-size: 1.2rem;
        color: #666;
        font-weight: 500;
    }

    .back-btn {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.1);
        border-radius: 50px;
        padding: 8px 20px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #444;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }

    .back-btn:hover {
        background: #f9f9f9;
        transform: translateX(-4px);
    }

    .loader {
        display: none;
        text-align: center;
        padding: 60px;
        color: #1DB954;
    }

    .scroll-btn {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.1);
        border-radius: 50%;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: absolute;
        z-index: 10;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        color: #444;
        transition: all 0.2s;
    }

    .scroll-btn:hover {
        background: #1DB954;
        color: #ffffff;
        border-color: #1DB954;
    }

    .scroll-left { left: -22px; }
    .scroll-right { right: -22px; }
    
    .status-msg {
        color: #666;
        padding: 10px 0;
    }
    
    .status-error {
        color: #ef4444;
        padding: 10px 0;
    }
</style>
@endpush

@section('content')
    <div class="content-section active" id="music-library">
        <!-- Top Header -->
        <div class="music-header">
            <div class="music-header-left">
                <h2><i class="fab fa-spotify"></i> Spotify Library</h2>
                @if(request('wish_message_id'))
                    <p>
                        Assigning music to:
                        <strong>{{ request('message_title', 'Selected Message') }}</strong>
                    </p>
                @else
                    <p>Find the perfect background music.</p>
                @endif
            </div>

            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="spotify-search-input" class="search-input"
                    placeholder="Search for tracks or albums..." autocomplete="off">
            </div>
        </div>

        <!-- Hidden form for saving -->
        <form id="save-music-form" action="{{ route('user.music.save') }}" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="track_url" id="selected-track-url" required>
            <input type="hidden" name="wish_message_id" id="hidden_message_id" value="{{ request('wish_message_id') }}">
        </form>

        <!-- Search View -->
        <div class="view-container" id="search-view">
            <div id="search-loader" class="loader">
                <i class="fas fa-circle-notch fa-spin fa-3x"></i>
            </div>

            <div id="search-results" style="display:none;">
                <h3 class="section-title">Albums</h3>
                <div class="albums-wrapper">
                    <button class="scroll-btn scroll-left" onclick="scrollAlbums(-1)" id="btn-scroll-left" style="display:none;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="grid-container" id="albums-grid"></div>
                    <button class="scroll-btn scroll-right" onclick="scrollAlbums(1)" id="btn-scroll-right" style="display:none;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <h3 class="section-title">Tracks</h3>
                <div class="track-list" id="tracks-list"></div>
            </div>

            <div id="empty-state">
                <i class="fas fa-music"></i>
                <p>Use the search bar at the top right to find songs or albums on Spotify...</p>
            </div>
        </div>

        <!-- Album Takeover View -->
        <div class="view-container" id="album-view" style="display:none;">
            <button class="back-btn" onclick="closeAlbumView()">
                <i class="fas fa-arrow-left"></i> Back
            </button>

            <div id="album-loader" class="loader">
                <i class="fas fa-circle-notch fa-spin fa-3x"></i>
            </div>

            <div id="album-content" style="display:none;">
                <div class="album-header-hero">
                    <img src="" id="album-hero-img" class="album-header-cover" alt="Album Cover">
                    <div>
                        <h1 id="album-hero-title">Album Title</h1>
                        <div id="album-hero-artist">Artist Name</div>
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
                    <i class="fab fa-spotify"></i>
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
                emptyState.style.display = 'flex';
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
                emptyState.innerHTML = '<p class="status-error">Failed to fetch results. Make sure Spotify is configured correctly.</p>';
                emptyState.style.display = 'flex';
            }
        }

        function renderAlbums(albums) {
            albumsGrid.innerHTML = '';

            if (!albums || albums.length === 0) {
                albumsGrid.innerHTML = '<p class="status-msg">No albums found.</p>';
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
                container.innerHTML = '<p class="status-msg">No tracks found.</p>';
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
            document.querySelectorAll('.track-row').forEach(r => {
                r.style.background = '#ffffff';
                r.style.borderColor = 'rgba(0,0,0,0.05)';
            });
            rowElement.style.background = '#f8fcf9';
            rowElement.style.borderColor = 'rgba(29, 185, 84, 0.4)';

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
                        height="152" 
                        frameBorder="0" 
                        allowfullscreen="" 
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" 
                        loading="lazy" style="border-radius: 12px; border: none;">
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