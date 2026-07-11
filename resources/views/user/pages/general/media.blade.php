@extends('user.base-user')

@section('user-section', 'media')

@section('content')
@php
    $selectedMsgId = old('wish_message_id') ?? request('wish_message_id') ?? request('message_id');
    $selectedMessage = null;
    if ($selectedMsgId) {
        $selectedMessage = collect($allMessagesForSelect ?? [])->firstWhere('id', $selectedMsgId);
    }
@endphp

<div class="content-section active" id="media">
    <!-- Header Section -->
    <div class="messages-header-toolbar" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: flex-start; gap: 0.5px;">
            <i class="fas fa-photo-video text-primary messages-header-icon" style="font-size: 1.4rem; margin-top: 4px;"></i> 
            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: flex-start;">
                <h2 class="messages-header-title" style="margin: 0 0 2px 0; padding: 0; display: block;">Manage Media</h2>
                @if($selectedMessage)
                    <div style="font-size: 0.85rem; color: #64748b; margin: 0; padding: 0;">
                        Selecting for: <strong style="font-weight: 800; color: #334155;">{{ $selectedMessage->title }}</strong>, {{ $selectedMessage->recipient_name }}!
                    </div>
                @endif
            </div>
        </div>
    </div>
    <form id="media-form" method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data">
        @csrf
        <style>
            .media-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 24px;
                margin-bottom: 24px;
                align-items: start;
            }
            @media (min-width: 768px) {
                .media-grid.two-cols {
                    grid-template-columns: 1fr 1fr;
                }
            }
            .media-grid-item {
                min-width: 0;
            }
        </style>
        
        @if(!$selectedMessage)
        <div class="media-grid two-cols">
            <div class="media-grid-item">
                <div class="card">
                    <div class="card-header">
                        <h3>Message</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="media-message-id">Select Message</label>
                            <select class="form-control" id="media-message-id" name="wish_message_id" required data-url="{{ route('media.show') }}">
                                <option value="">-- Select a message --</option>
                                @foreach(($allMessagesForSelect ?? []) as $msg)
                                    <option value="{{ $msg->id }}">{{ $msg->title }} - {{ $msg->recipient_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="media-grid-item">
        @else
        <input type="hidden" id="media-message-id" name="wish_message_id" value="{{ $selectedMessage->id }}" data-url="{{ route('media.show') }}">
        <div class="media-grid">
            <div class="media-grid-item">
        @endif
                <div class="card">
                    <div class="card-header">
                        <h3>Recipient Image</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Upload Picture</label>
                            <div class="file-upload">
                                <div class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i> Choose an image
                                </div>
                                <input type="file" class="file-upload-input" name="recipient_image" accept="image/*" id="recipient_image">
                            </div>
                            <div class="file-info" id="recipient-image-info">
                                No file selected
                            </div>
                        </div>
                        <div class="media-preview-container">
                            <img id="recipient-preview" src="https://via.placeholder.com/150" class="media-preview-image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card media-background-music-card">
            <div class="card-header media-music-card-header">
                <h3 class="media-music-card-title">Background Music</h3>
                <a href="{{ route('user.music.page') }}" id="spotify-link" title="Select Music from Spotify"
                   data-base-url="{{ route('user.music.page') }}" class="media-spotify-link">
                    <i class="fab fa-spotify media-spotify-icon" aria-hidden="true"></i>
                </a>
            </div>
            <div class="card-body">
                <input type="hidden" name="spotify_url" id="spotify_url" value="{{ request('spotify_url') }}">
                <div class="form-group">
                    <label class="form-label">Upload Music File</label>
                    <div class="file-upload">
                        <div class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i> Choose an audio file
                        </div>
                        <input type="file" class="file-upload-input" name="background_music" accept="audio/*" id="background_music">
                    </div>
                    <div class="file-info" id="music-file-info">
                        @if(request('spotify_url') && request('spotify_name'))
                            <i class="fab fa-spotify text-success" style="font-size: 1.1em;"></i> Spotify: <strong>{{ request('spotify_name') }}</strong>
                        @else
                            No file selected
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-right" style="text-align: right;">
            <button type="submit" class="btn btn-primary" style="width: auto; min-width: 150px;">Save Media</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageSelect = document.getElementById('media-message-id');
    const spotifyLink = document.getElementById('spotify-link');
    const baseUrl = spotifyLink.getAttribute('data-base-url');

    function updateSpotifyLink() {
        const val = messageSelect.value;
        const selectedOption = messageSelect.options ? messageSelect.options[messageSelect.selectedIndex] : null;
        const msgText = selectedOption ? selectedOption.text : '{!! $selectedMessage ? addslashes($selectedMessage->title . " - " . $selectedMessage->recipient_name) : "" !!}';

        if (val) {
            const params = new URLSearchParams({
                wish_message_id: val,
                message_title: msgText
            });
            const targetUrl = baseUrl + '?' + params.toString();
            spotifyLink.href = targetUrl;
            spotifyLink.onclick = function(e) {
                const imgInput = document.getElementById('recipient_image');
                const musicInput = document.getElementById('background_music');
                if ((imgInput && imgInput.files.length > 0) || (musicInput && musicInput.files.length > 0)) {
                    e.preventDefault();
                    const btn = this;
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="color:#1db954;"></i>';
                    btn.style.pointerEvents = 'none';
                    
                    const form = document.getElementById('media-form');
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    }).then(() => {
                        window.location.href = targetUrl;
                    }).catch(() => {
                        window.location.href = targetUrl; // Proceed anyway on failure
                    });
                }
            };
        } else {
            spotifyLink.href = '#';
            spotifyLink.onclick = function(e) {
                e.preventDefault();
                if (typeof showNotification === 'function') {
                    showNotification('Selection Required', 'Please select a message from the dropdown at the top of the page before picking music.', 'warning', 4000);
                } else {
                    alert('Please select a message from the dropdown at the top of the page before picking music.');
                }
            };
        }
    }

    messageSelect.addEventListener('change', updateSpotifyLink);
    // Call once on load in case a message is pre-selected
    updateSpotifyLink();
});

// Image preview
var recipientImageInput = document.getElementById('recipient_image');
if (recipientImageInput) recipientImageInput.addEventListener('change', function (e) {
    var file = e.target.files[0];
    var fileInfo = document.getElementById('recipient-image-info');
    if (!fileInfo) return;
    if (file) {
        var reader = new FileReader();
        reader.onload = function (ev) {
            var preview = document.getElementById('recipient-preview');
            if (preview) preview.src = ev.target.result;
        };
        reader.readAsDataURL(file);
        fileInfo.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
        fileInfo.style.color = 'var(--primary)';
    } else {
        fileInfo.textContent = 'No file selected';
        fileInfo.style.color = 'var(--gray)';
    }
});

// Music file info
var backgroundMusicInput = document.getElementById('background_music');
if (backgroundMusicInput) backgroundMusicInput.addEventListener('change', function (e) {
    var file = e.target.files[0];
    var fileInfo = document.getElementById('music-file-info');
    if (!fileInfo) return;
    if (file) {
        fileInfo.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
        fileInfo.style.color = 'var(--primary)';
    } else {
        fileInfo.textContent = 'No file selected';
        fileInfo.style.color = 'var(--gray)';
    }
});

// When message selection changes in Media section, load that message's media
var mediaMessageSelect = document.getElementById('media-message-id');
if (mediaMessageSelect) {
    function loadMediaForSelectedMessage() {
        if (mediaMessageSelect.value) mediaMessageSelect.dispatchEvent(new Event('change'));
    }
    mediaMessageSelect.addEventListener('change', function () {
        var messageId = this.value;
        var previewImg = document.getElementById('recipient-preview');
        var recipientImageInfo = document.getElementById('recipient-image-info');
        var musicFileInfo = document.getElementById('music-file-info');
        if (!messageId) {
            previewImg.src = 'https://via.placeholder.com/150';
            recipientImageInfo.textContent = 'No file selected';
            musicFileInfo.textContent = 'No file selected';
            recipientImageInfo.style.color = musicFileInfo.style.color = 'var(--gray)';
            document.getElementById('recipient_image').value = '';
            document.getElementById('background_music').value = '';
            return;
        }
        var baseUrl = mediaMessageSelect.getAttribute('data-url');
        var url = baseUrl ? baseUrl + '?message_id=' + encodeURIComponent(messageId) : '';
        if (!url) return;
        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) { return r.json(); }).then(function (data) {
            previewImg.src = (data.recipient_image_url || 'https://via.placeholder.com/150');
            
            if (data.recipient_image_name) {
                recipientImageInfo.innerHTML = '<i class="fas fa-file-image"></i> Attached: <strong>' + escapeHtml(data.recipient_image_name) + '</strong>';
                recipientImageInfo.style.color = 'var(--primary)';
            } else {
                recipientImageInfo.textContent = 'No file selected';
                recipientImageInfo.style.color = 'var(--gray)';
            }

            const urlParams = new URLSearchParams(window.location.search);
            const pendingSpotifyUrl = urlParams.get('spotify_url');
            const pendingSpotifyName = urlParams.get('spotify_name');
            const msgIdInUrl = urlParams.get('wish_message_id');

            if (pendingSpotifyUrl && msgIdInUrl === messageId) {
                musicFileInfo.innerHTML = '<i class="fab fa-spotify text-success" style="font-size: 1.1em;"></i> Spotify: <strong>' + escapeHtml(pendingSpotifyName) + '</strong> <small>(Unsaved)</small>';
                musicFileInfo.style.color = 'var(--primary)';
                var spotifyHidden = document.getElementById('spotify_url');
                if (spotifyHidden) spotifyHidden.value = pendingSpotifyUrl;
            } else if (data.background_music_url) {
                if (data.background_music_url.indexOf('spotify.com') !== -1) {
                    musicFileInfo.innerHTML = '<i class="fab fa-spotify text-success" style="font-size: 1.1em;"></i> Attached: <strong>Spotify Track</strong>';
                } else {
                    musicFileInfo.innerHTML = '<i class="fas fa-file-audio"></i> Attached: <strong>' + escapeHtml(data.background_music_name || 'Audio File') + '</strong>';
                }
                musicFileInfo.style.color = 'var(--primary)';
            } else {
                musicFileInfo.textContent = 'No file selected';
                musicFileInfo.style.color = 'var(--gray)';
            }

            document.getElementById('recipient_image').value = '';
            document.getElementById('background_music').value = '';
        }).catch(function () {
            previewImg.src = 'https://via.placeholder.com/150';
            recipientImageInfo.textContent = 'No file selected';
            musicFileInfo.textContent = 'No file selected';
        });
    });
    // Load media when opening Media section with a message already selected
    document.addEventListener('DOMContentLoaded', loadMediaForSelectedMessage);
}

// Helper functions
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
</script>
@endpush
@endsection
