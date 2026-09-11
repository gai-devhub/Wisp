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
                align-items: stretch;
            }
            @media (min-width: 768px) {
                .media-grid.two-cols {
                    grid-template-columns: 1fr 1fr;
                }
            }
            .media-grid-item {
                min-width: 0;
                display: flex;
                flex-direction: column;
            }
            .media-grid-item .card {
                height: 100%;
                margin-bottom: 0;
            }
            .btn-save-media-custom {
                padding: 12px 32px;
                font-size: 0.95rem;
                font-weight: 700;
                border-radius: 12px;
                background: linear-gradient(135deg, #E8674A, #F28C76);
                color: #ffffff !important;
                border: none;
                box-shadow: 0 4px 14px rgba(232, 103, 74, 0.35);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                cursor: pointer;
                transition: all 0.2s ease-in-out;
            }
            .btn-save-media-custom:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(232, 103, 74, 0.45);
                background: linear-gradient(135deg, #d95338, #E8674A);
                color: #ffffff !important;
            }
            .btn-save-media-custom:active {
                transform: translateY(0);
                box-shadow: 0 2px 8px rgba(232, 103, 74, 0.3);
            }
            .media-modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(15, 23, 42, 0.65);
                backdrop-filter: blur(4px);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .media-modal-card {
                background: #ffffff;
                border-radius: 16px;
                max-width: 550px;
                width: 100%;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                overflow: hidden;
                animation: mediaModalIn 0.2s ease-out;
            }
            @keyframes mediaModalIn {
                from { opacity: 0; transform: scale(0.95); }
                to { opacity: 1; transform: scale(1); }
            }
            .media-modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 20px;
                border-bottom: 1px solid #f1f5f9;
                background: #f8fafc;
            }
            .media-modal-header h4 {
                margin: 0;
                font-size: 1.05rem;
                font-weight: 700;
                color: #1e293b;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .media-modal-close-btn {
                background: transparent;
                border: none;
                font-size: 1.6rem;
                color: #64748b;
                cursor: pointer;
                line-height: 1;
                padding: 0;
            }
            .media-modal-close-btn:hover {
                color: #0f172a;
            }
            .media-modal-body {
                padding: 24px;
                text-align: center;
                max-height: 75vh;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
            }
            .media-modal-body img {
                max-width: 100%;
                max-height: 50vh;
                border-radius: 12px;
                object-fit: contain;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            .media-modal-details-card {
                margin-top: 20px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 16px;
                text-align: left;
                width: 100%;
                font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            }
            .media-modal-detail-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
            .media-modal-detail-row:not(:last-child) {
                margin-bottom: 12px;
                padding-bottom: 12px;
                border-bottom: 1px solid #e2e8f0;
            }
            @media (max-width: 480px) {
                .media-modal-detail-row {
                    grid-template-columns: 1fr;
                    gap: 10px;
                }
            }
            .media-modal-detail-item {
                display: flex;
                flex-direction: column;
                gap: 2px;
                min-width: 0;
            }
            .media-modal-detail-item .detail-label {
                font-size: 0.72rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: #64748b;
                display: flex;
                align-items: center;
                gap: 6px;
                font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
            .media-modal-detail-item .detail-val {
                font-size: 0.88rem;
                font-weight: 700;
                color: #1e293b;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
        </style>
        
        @if(!$selectedMessage)
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3>Message</h3>
            </div>
            <div class="card-body">
                <div class="form-group" style="margin-bottom: 0;">
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
        @else
        <input type="hidden" id="media-message-id" name="wish_message_id" value="{{ $selectedMessage->id }}" data-url="{{ route('media.show') }}">
        @endif

        <div class="media-grid two-cols">
            <!-- Recipient Image Card -->
            <div class="media-grid-item">
                <div class="card">
                    <div class="card-header">
                        <h3>Recipient Image</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group" style="margin-bottom: 0;">
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
                    </div>
                </div>
            </div>

            <!-- Background Music Card -->
            <div class="media-grid-item">
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
                        <div class="form-group" style="margin-bottom: 0;">
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
            </div>
        </div>

        <div class="form-group text-right" style="text-align: right; margin-top: 24px;">
            <button type="submit" class="btn btn-save-media-custom">
                <i class="fas fa-check-circle"></i> Save Media
            </button>
        </div>
    </form>
</div>

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="media-modal-overlay" style="display: none;" onclick="if(event.target===this) closeImagePreviewModal()">
    <div class="media-modal-card">
        <div class="media-modal-header">
            <h4><i class="fas fa-image text-primary"></i> Recipient Image Preview</h4>
            <button type="button" class="media-modal-close-btn" onclick="closeImagePreviewModal()">&times;</button>
        </div>
        <div class="media-modal-body">
            <img id="modal-preview-img" src="" alt="Image Preview">

            <div class="media-modal-details-card">
                <div class="media-modal-detail-row">
                    <div class="media-modal-detail-item">
                        <span class="detail-label"><i class="fas fa-file-alt text-muted"></i> File Name</span>
                        <strong class="detail-val" id="modal-file-name">-</strong>
                    </div>
                    <div class="media-modal-detail-item">
                        <span class="detail-label"><i class="fas fa-hdd text-muted"></i> File Size</span>
                        <strong class="detail-val" id="modal-file-size">-</strong>
                    </div>
                </div>
                <div class="media-modal-detail-row">
                    <div class="media-modal-detail-item">
                        <span class="detail-label"><i class="fas fa-envelope text-muted"></i> Message Title</span>
                        <strong class="detail-val" id="modal-msg-title">-</strong>
                    </div>
                    <div class="media-modal-detail-item">
                        <span class="detail-label"><i class="far fa-calendar-alt text-muted"></i> Assigned Date</span>
                        <strong class="detail-val" id="modal-assigned-date">-</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Music Preview Modal -->
<div id="musicPreviewModal" class="media-modal-overlay" style="display: none;" onclick="if(event.target===this) closeMusicPreviewModal()">
    <div class="media-modal-card">
        <div class="media-modal-header">
            <h4><i class="fas fa-music text-primary"></i> Background Music Preview</h4>
            <button type="button" class="media-modal-close-btn" onclick="closeMusicPreviewModal()">&times;</button>
        </div>
        <div class="media-modal-body">
            <div id="modal-music-player-container" style="width: 100%; margin-bottom: 12px;">
                <audio id="modal-music-audio" controls style="width: 100%; border-radius: 8px;">
                    <source id="modal-music-source" src="" type="audio/mpeg">
                    Your browser does not support audio preview.
                </audio>
                <div id="modal-music-spotify" style="display: none; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center;">
                    <i class="fab fa-spotify text-success" style="font-size: 2.5rem; margin-bottom: 8px; display: inline-block;"></i>
                    <h5 style="margin: 0 0 6px 0; font-weight: 700; color: #1e293b;">Spotify Background Track</h5>
                    <a id="modal-spotify-link" href="#" target="_blank" class="btn btn-sm btn-action btn-primary" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; margin-top: 6px; border-radius: 8px;">
                        Listen on Spotify <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>

            <div class="media-modal-details-card">
                <div class="media-modal-detail-row">
                    <div class="media-modal-detail-item">
                        <span class="detail-label"><i class="fas fa-file-audio text-muted"></i> File / Track</span>
                        <strong class="detail-val" id="modal-music-file-name">-</strong>
                    </div>
                    <div class="media-modal-detail-item">
                        <span class="detail-label"><i class="fas fa-hdd text-muted"></i> File Size</span>
                        <strong class="detail-val" id="modal-music-file-size">-</strong>
                    </div>
                </div>
                <div class="media-modal-detail-row">
                    <div class="media-modal-detail-item">
                        <span class="detail-label"><i class="fas fa-envelope text-muted"></i> Message Title</span>
                        <strong class="detail-val" id="modal-music-msg-title">-</strong>
                    </div>
                    <div class="media-modal-detail-item">
                        <span class="detail-label"><i class="far fa-calendar-alt text-muted"></i> Assigned Date</span>
                        <strong class="detail-val" id="modal-music-assigned-date">-</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
var currentImageSrc = null;
var currentImageMetaData = {
    fileName: '-',
    fileSize: '-',
    msgTitle: '-',
    assignedDate: '-'
};

var currentMusicSrc = null;
var currentMusicMetaData = {
    fileName: '-',
    fileSize: '-',
    msgTitle: '-',
    assignedDate: '-',
    isSpotify: false
};

function openImagePreviewModal() {
    if (!currentImageSrc) return;
    document.getElementById('modal-preview-img').src = currentImageSrc;
    document.getElementById('modal-file-name').textContent = currentImageMetaData.fileName || '-';
    document.getElementById('modal-file-name').title = currentImageMetaData.fileName || '-';
    document.getElementById('modal-file-size').textContent = currentImageMetaData.fileSize || '-';
    document.getElementById('modal-msg-title').textContent = currentImageMetaData.msgTitle || '-';
    document.getElementById('modal-msg-title').title = currentImageMetaData.msgTitle || '-';
    document.getElementById('modal-assigned-date').textContent = currentImageMetaData.assignedDate || '-';
    document.getElementById('imagePreviewModal').style.display = 'flex';
}

function closeImagePreviewModal() {
    document.getElementById('imagePreviewModal').style.display = 'none';
}

function openMusicPreviewModal() {
    if (!currentMusicSrc) return;
    const audioPlayer = document.getElementById('modal-music-audio');
    const audioSource = document.getElementById('modal-music-source');
    const spotifyBox = document.getElementById('modal-music-spotify');
    const spotifyLink = document.getElementById('modal-spotify-link');

    if (currentMusicMetaData.isSpotify) {
        audioPlayer.style.display = 'none';
        audioPlayer.pause();
        spotifyBox.style.display = 'block';
        spotifyLink.href = currentMusicSrc;
    } else {
        spotifyBox.style.display = 'none';
        audioPlayer.style.display = 'block';
        audioSource.src = currentMusicSrc;
        audioPlayer.load();
    }

    document.getElementById('modal-music-file-name').textContent = currentMusicMetaData.fileName || '-';
    document.getElementById('modal-music-file-name').title = currentMusicMetaData.fileName || '-';
    document.getElementById('modal-music-file-size').textContent = currentMusicMetaData.fileSize || '-';
    document.getElementById('modal-music-msg-title').textContent = currentMusicMetaData.msgTitle || '-';
    document.getElementById('modal-music-msg-title').title = currentMusicMetaData.msgTitle || '-';
    document.getElementById('modal-music-assigned-date').textContent = currentMusicMetaData.assignedDate || '-';

    document.getElementById('musicPreviewModal').style.display = 'flex';
}

function closeMusicPreviewModal() {
    const audioPlayer = document.getElementById('modal-music-audio');
    if (audioPlayer) audioPlayer.pause();
    document.getElementById('musicPreviewModal').style.display = 'none';
}

function setRecipientImageInfo(nameText, imageSrc) {
    currentImageSrc = imageSrc;
    var fileInfo = document.getElementById('recipient-image-info');
    if (!fileInfo) return;
    if (imageSrc && imageSrc !== 'https://via.placeholder.com/150') {
        fileInfo.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; width: 100%;">
                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600; color: #334155;">
                    <i class="fas fa-file-image text-primary" style="margin-right: 6px;"></i> ${escapeHtml(nameText)}
                </span>
                <button type="button" onclick="openImagePreviewModal()" title="Preview Image" style="background: transparent; border: none; color: #E8674A; font-size: 1.25rem; cursor: pointer; padding: 4px 8px; line-height: 1; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; transition: transform 0.15s ease, color 0.15s ease;" onmouseover="this.style.color='#d95338'; this.style.transform='scale(1.15)';" onmouseout="this.style.color='#E8674A'; this.style.transform='scale(1)';">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        `;
    } else {
        fileInfo.innerHTML = 'No file selected';
        fileInfo.style.color = 'var(--gray)';
    }
}

function setMusicFileInfo(nameText, musicSrc, isSpotify) {
    currentMusicSrc = musicSrc;
    var fileInfo = document.getElementById('music-file-info');
    if (!fileInfo) return;
    if (musicSrc) {
        var iconHtml = isSpotify 
            ? '<i class="fab fa-spotify text-success" style="font-size: 1.1em; margin-right: 6px;"></i>' 
            : '<i class="fas fa-file-audio text-primary" style="margin-right: 6px;"></i>';
        fileInfo.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; width: 100%;">
                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600; color: #334155;">
                    ${iconHtml} ${escapeHtml(nameText)}
                </span>
                <button type="button" onclick="openMusicPreviewModal()" title="Preview Background Music" style="background: transparent; border: none; color: #E8674A; font-size: 1.25rem; cursor: pointer; padding: 4px 8px; line-height: 1; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; transition: transform 0.15s ease, color 0.15s ease;" onmouseover="this.style.color='#d95338'; this.style.transform='scale(1.15)';" onmouseout="this.style.color='#E8674A'; this.style.transform='scale(1)';">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        `;
    } else {
        fileInfo.innerHTML = 'No file selected';
        fileInfo.style.color = 'var(--gray)';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const messageSelect = document.getElementById('media-message-id');
    const spotifyLink = document.getElementById('spotify-link');
    const baseUrl = spotifyLink ? spotifyLink.getAttribute('data-base-url') : '';

    function updateSpotifyLink() {
        if (!spotifyLink || !baseUrl) return;
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
                        window.location.href = targetUrl;
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

    if (messageSelect) {
        messageSelect.addEventListener('change', updateSpotifyLink);
        updateSpotifyLink();
    }

    @if($selectedMessage)
        @php
            $initImgName = '-';
            $initImgSize = 'N/A';
            $initImgUrl = null;
            if ($selectedMessage->mediaFiles && $selectedMessage->mediaFiles->recipient_image) {
                $initImgUrl = Str::startsWith($selectedMessage->mediaFiles->recipient_image, ['http://', 'https://']) 
                    ? $selectedMessage->mediaFiles->recipient_image 
                    : s3_url($selectedMessage->mediaFiles->recipient_image);
                $initImgName = basename($selectedMessage->mediaFiles->recipient_image);
                try {
                    $disk = config('filesystems.media_disk');
                    if (Storage::disk($disk)->exists($selectedMessage->mediaFiles->recipient_image)) {
                        $b = Storage::disk($disk)->size($selectedMessage->mediaFiles->recipient_image);
                        if ($b > 0) {
                            $units = ['B', 'KB', 'MB', 'GB'];
                            $i = (int) floor(log($b, 1024));
                            $initImgSize = round($b / pow(1024, $i), 2) . ' ' . $units[$i];
                        }
                    }
                } catch(\Throwable $e) {}
            }
            $initTitle = $selectedMessage->title;
            $initDate = $selectedMessage->receiving_date ? \Carbon\Carbon::parse($selectedMessage->receiving_date)->format('F j, Y') : ($selectedMessage->created_at ? $selectedMessage->created_at->format('F j, Y') : 'N/A');

            $initMusicName = '-';
            $initMusicSize = 'N/A';
            $initMusicUrl = null;
            $initIsSpotify = false;
            if ($selectedMessage->mediaFiles && $selectedMessage->mediaFiles->background_music) {
                $bgM = $selectedMessage->mediaFiles->background_music;
                if (Str::startsWith($bgM, ['http://', 'https://'])) {
                    $initMusicUrl = $bgM;
                    $initMusicName = request('spotify_name') ?? 'Spotify Track';
                    $initMusicSize = 'Spotify Link';
                    $initIsSpotify = true;
                } else {
                    $initMusicUrl = s3_url($bgM);
                    $initMusicName = basename($bgM);
                    try {
                        $disk = config('filesystems.media_disk');
                        if (Storage::disk($disk)->exists($bgM)) {
                            $b = Storage::disk($disk)->size($bgM);
                            if ($b > 0) {
                                $units = ['B', 'KB', 'MB', 'GB'];
                                $i = (int) floor(log($b, 1024));
                                $initMusicSize = round($b / pow(1024, $i), 2) . ' ' . $units[$i];
                            }
                        }
                    } catch(\Throwable $e) {}
                }
            }
        @endphp

        currentImageSrc = {!! json_encode($initImgUrl) !!};
        currentImageMetaData = {
            fileName: {!! json_encode($initImgName) !!},
            fileSize: {!! json_encode($initImgSize) !!},
            msgTitle: {!! json_encode($initTitle) !!},
            assignedDate: {!! json_encode($initDate) !!}
        };
        if (currentImageSrc) {
            setRecipientImageInfo({!! json_encode($initImgName) !!}, currentImageSrc);
        }

        if ({!! json_encode($initMusicUrl) !!}) {
            currentMusicSrc = {!! json_encode($initMusicUrl) !!};
            currentMusicMetaData = {
                fileName: {!! json_encode($initMusicName) !!},
                fileSize: {!! json_encode($initMusicSize) !!},
                msgTitle: {!! json_encode($initTitle) !!},
                assignedDate: {!! json_encode($initDate) !!},
                isSpotify: {!! json_encode($initIsSpotify) !!}
            };
            setMusicFileInfo({!! json_encode($initMusicName) !!}, currentMusicSrc, {!! json_encode($initIsSpotify) !!});
        }
    @endif
});

// Image file selection
var recipientImageInput = document.getElementById('recipient_image');
if (recipientImageInput) recipientImageInput.addEventListener('change', function (e) {
    var file = e.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function (ev) {
            var msgSelect = document.getElementById('media-message-id');
            var selectedText = '-';
            if (msgSelect && msgSelect.selectedIndex >= 0 && msgSelect.options[msgSelect.selectedIndex]) {
                selectedText = msgSelect.options[msgSelect.selectedIndex].text;
            }
            @if($selectedMessage)
                selectedText = {!! json_encode($selectedMessage->title) !!};
                var initDate = {!! json_encode($selectedMessage->receiving_date ? \Carbon\Carbon::parse($selectedMessage->receiving_date)->format('F j, Y') : ($selectedMessage->created_at ? $selectedMessage->created_at->format('F j, Y') : 'Today')) !!};
            @else
                var initDate = new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            @endif

            currentImageMetaData = {
                fileName: file.name,
                fileSize: formatFileSize(file.size),
                msgTitle: selectedText,
                assignedDate: initDate
            };
            setRecipientImageInfo(file.name + ' (' + formatFileSize(file.size) + ')', ev.target.result);
        };
        reader.readAsDataURL(file);
    } else {
        setRecipientImageInfo('', null);
    }
});

// Music file info
var backgroundMusicInput = document.getElementById('background_music');
if (backgroundMusicInput) backgroundMusicInput.addEventListener('change', function (e) {
    var file = e.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function (ev) {
            var msgSelect = document.getElementById('media-message-id');
            var selectedText = '-';
            if (msgSelect && msgSelect.selectedIndex >= 0 && msgSelect.options[msgSelect.selectedIndex]) {
                selectedText = msgSelect.options[msgSelect.selectedIndex].text;
            }
            @if($selectedMessage)
                selectedText = {!! json_encode($selectedMessage->title) !!};
                var initDate = {!! json_encode($selectedMessage->receiving_date ? \Carbon\Carbon::parse($selectedMessage->receiving_date)->format('F j, Y') : ($selectedMessage->created_at ? $selectedMessage->created_at->format('F j, Y') : 'Today')) !!};
            @else
                var initDate = new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            @endif

            currentMusicMetaData = {
                fileName: file.name,
                fileSize: formatFileSize(file.size),
                msgTitle: selectedText,
                assignedDate: initDate,
                isSpotify: false
            };
            setMusicFileInfo(file.name + ' (' + formatFileSize(file.size) + ')', ev.target.result, false);
        };
        reader.readAsDataURL(file);
    } else {
        setMusicFileInfo('', null, false);
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
        if (!messageId) {
            setRecipientImageInfo('', null);
            setMusicFileInfo('', null, false);
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
            var selectedOption = mediaMessageSelect.options[mediaMessageSelect.selectedIndex];
            var optText = selectedOption ? selectedOption.text : '-';

            if (data.recipient_image_url) {
                currentImageMetaData = {
                    fileName: data.recipient_image_name || 'Recipient Image',
                    fileSize: data.recipient_image_size || 'N/A',
                    msgTitle: data.message_title || optText,
                    assignedDate: data.receiving_date || 'N/A'
                };
                setRecipientImageInfo(data.recipient_image_name || 'Recipient Image', data.recipient_image_url);
            } else {
                setRecipientImageInfo('', null);
            }

            const urlParams = new URLSearchParams(window.location.search);
            const pendingSpotifyUrl = urlParams.get('spotify_url');
            const pendingSpotifyName = urlParams.get('spotify_name');
            const msgIdInUrl = urlParams.get('wish_message_id');

            if (pendingSpotifyUrl && msgIdInUrl === messageId) {
                currentMusicMetaData = {
                    fileName: pendingSpotifyName || 'Spotify Track',
                    fileSize: 'Spotify Link',
                    msgTitle: data.message_title || optText,
                    assignedDate: data.receiving_date || 'N/A',
                    isSpotify: true
                };
                setMusicFileInfo(pendingSpotifyName || 'Spotify Track', pendingSpotifyUrl, true);
                var spotifyHidden = document.getElementById('spotify_url');
                if (spotifyHidden) spotifyHidden.value = pendingSpotifyUrl;
            } else if (data.background_music_url) {
                var isSpot = data.background_music_url.indexOf('spotify.com') !== -1;
                currentMusicMetaData = {
                    fileName: data.background_music_name || (isSpot ? 'Spotify Track' : 'Audio File'),
                    fileSize: data.background_music_size || (isSpot ? 'Spotify Link' : 'N/A'),
                    msgTitle: data.message_title || optText,
                    assignedDate: data.receiving_date || 'N/A',
                    isSpotify: isSpot
                };
                setMusicFileInfo(data.background_music_name || (isSpot ? 'Spotify Track' : 'Audio File'), data.background_music_url, isSpot);
            } else {
                setMusicFileInfo('', null, false);
            }

            document.getElementById('recipient_image').value = '';
            document.getElementById('background_music').value = '';
        }).catch(function () {
            setRecipientImageInfo('', null);
            setMusicFileInfo('', null, false);
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
// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImagePreviewModal();
        closeMusicPreviewModal();
    }
});
</script>
@endpush
@endsection
