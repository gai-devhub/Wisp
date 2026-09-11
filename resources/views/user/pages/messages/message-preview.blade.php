@extends('user.base-user')

@section('user-section', 'message-preview')

@section('content')


<div class="preview-container">
    <div class="preview-header">
        <h2 class="preview-title">
            <i class="fas fa-eye text-primary"></i>
            Message Preview
        </h2>
        <div class="preview-flex-actions">
            <a href="{{ route('user.my-messages.page') }}" class="btn btn-action btn-primary btn-primary-shadow icon-btn-mobile">
                <i class="fas fa-envelope"></i> <span class="action-text">My Messages</span>
            </a>
        </div>
    </div>

    <div class="preview-card">
        <div class="preview-card-header">
            <i class="fas fa-info-circle"></i> General Details
        </div>
        <div class="preview-grid">
            <div>
                <div class="detail-group">
                    <div class="preview-label">Title</div>
                    <div class="preview-value preview-title-large">{{ $message->title }}</div>
                </div>

                <div class="detail-group">
                    <div class="preview-label">Recipient</div>
                    <div class="preview-value">
                        <div class="preview-recipient-badge">
                            <i class="fas fa-user-circle text-muted"></i> {{ $message->recipient_name }}
                        </div>
                    </div>
                </div>


            </div>
            <div>
                <div class="detail-group">
                    <div class="preview-label">Date Created</div>
                    <div class="preview-value">
                        <i class="far fa-clock text-muted icon-spacing"></i>
                        {{ $message->created_at->format('F j, Y, g:i a') }}
                    </div>
                </div>

                <div class="detail-group">
                    <div class="preview-label">Receiving Date</div>
                    <div class="preview-value">
                        @if($message->receiving_date)
                            <span class="text-emerald-bold">
                                <i class="far fa-calendar-check icon-spacing"></i>
                                {{ \Carbon\Carbon::parse($message->receiving_date)->format('F j, Y') }}
                            </span>
                        @else
                            <span class="text-slate-muted"><i class="far fa-calendar-times icon-spacing"></i> Not specified</span>
                        @endif
                    </div>
                </div>

                <div class="detail-group">
                    <div class="preview-label">Template Applied</div>
                    <div class="preview-value">
                        @if($message->template && !empty($message->template->template_name))
                            @php
                                $tName = $message->template->template_name;
                                if (preg_match('/^template\.([^.]+)\.template-(\d+)$/i', $tName, $matches)) {
                                    $displayTemplate = ucfirst($matches[1]) . ' — Template ' . $matches[2];
                                } elseif (preg_match('/^view-?(\d+)$/i', $tName, $matches)) {
                                    $displayTemplate = 'Template View ' . $matches[1];
                                } else {
                                    $displayTemplate = ucwords(str_replace(['-', '_', '.'], [' ', ' ', ' '], $tName));
                                }
                            @endphp
                            <span class="template-badge-custom">
                                <i class="fas fa-palette"></i> {{ $displayTemplate }}
                            </span>
                        @else
                            <span class="template-badge-default">
                                <i class="fas fa-file-alt"></i> Default Template
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <div class="detail-group">
                <div class="preview-label" style="margin-bottom: 12px;">Message Content</div>
                <div class="preview-content-box custom-scrollbar" style="max-height: 300px; overflow-y: auto; padding: 24px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; line-height: 1.7; color: #334155; white-space: pre-wrap;">{{ trim($message->message ?? $message->wish_message ?? '') }}</div>
            </div>
        </div>
    </div>

    <div class="preview-grid">
        <div class="preview-card message-preview-inline-1" >
            <div class="preview-card-header">
                <i class="fas fa-share-alt"></i> Sharing Status
            </div>
            
            @if($message->shareSends && $message->shareSends->count() > 0)
                @foreach($message->shareSends as $send)
                    <div class="share-item">
                        <div class="share-item-flex">
                            <div class="share-icon">
                                <i class="fas fa-{{ $send->channel === 'email' ? 'envelope' : ($send->channel === 'sms' ? 'sms' : 'share') }}"></i>
                            </div>
                            <div>
                                <div class="share-channel-title">{{ ucfirst($send->channel) }}</div>
                                <div class="share-channel-to">To: {{ $send->recipient_masked }}</div>
                                @if($send->scheduled_at)
                                    <div class="share-channel-time">
                                        <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($send->scheduled_at)->format('M j, Y g:i a') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            @if($send->status === 'scheduled')
                                <span class="status-badge scheduled"><i class="fas fa-hourglass-half"></i> Scheduled</span>
                            @elseif($send->status === 'sent')
                                <span class="status-badge sent"><i class="fas fa-check"></i> Sent</span>
                            @else
                                <span class="status-badge status-badge-custom">{{ ucfirst($send->status) }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="fas fa-paper-plane"></i>
                    <p>No automated sharing schedule<br>configured for this message.</p>
                </div>
            @endif
        </div>

        <div class="preview-card message-preview-inline-2" >
            <div class="preview-card-header">
                <i class="fas fa-photo-video"></i> Attached Media
            </div>
            
            <div class="media-container-flex">
                <!-- Image Section -->
                <div class="image-preview-wrapper">
                    @if($message->mediaFiles && $message->mediaFiles->recipient_image)
                        @php
                            $imgSrc = Str::startsWith($message->mediaFiles->recipient_image, ['http://', 'https://']) 
                                ? $message->mediaFiles->recipient_image 
                                : s3_url($message->mediaFiles->recipient_image);
                        @endphp
                        <img src="{{ $imgSrc }}" 
                             alt="Message Image" 
                             class="media-img"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="empty-image-placeholder" style="display: none;">
                            <i class="far fa-image empty-image-icon"></i>
                            <span class="empty-image-text">Image Unavailable</span>
                        </div>
                    @else
                        <div class="empty-image-placeholder">
                            <i class="far fa-image empty-image-icon"></i>
                            <span class="empty-image-text">No image attached</span>
                        </div>
                    @endif
                </div>

                <!-- Audio Section -->
                <div style="margin-top: 20px;">
                    @if($message->mediaFiles && $message->mediaFiles->background_music)
                        @php
                            $bgMusic = $message->mediaFiles->background_music;
                            $isSpotify = str_contains($bgMusic, 'spotify.com');
                        @endphp

                        @if($isSpotify)
                            <div class="spotify-link-container" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; margin-top: 10px;">
                                <div style="display: flex; align-items: center; gap: 12px; color: #334155; min-width: 0;">
                                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #e0e7ff; display: flex; align-items: center; justify-content: center; color: #6366f1; flex-shrink: 0;">
                                        <i class="fas fa-music"></i>
                                    </div>
                                    <div style="display: flex; flex-direction: column; line-height: 1.4; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                                        <strong style="font-size: 0.95rem; overflow: hidden; text-overflow: ellipsis;">Background Audio</strong>
                                        <span style="color: #64748b; font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis;">Spotify Link</span>
                                    </div>
                                </div>
                                <a href="{{ $bgMusic }}" target="_blank" class="btn btn-sm spotify-listen-btn" style="background: #6366f1; border: none; border-radius: 8px; padding: 8px 16px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; color: white; font-weight: 600; font-size: 0.9rem; flex-shrink: 0; margin-left: auto;">
                                    <span class="spotify-listen-text">Listen</span> <i class="fas fa-headphones"></i>
                                </a>
                            </div>
                        @else
                            <div class="preview-label mb-8"><i class="fas fa-music"></i> Background Music</div>
                            <audio controls class="audio-player-custom" style="width: 100%; margin-top: 10px;">
                                <source src="{{ Str::startsWith($bgMusic, ['http://', 'https://']) ? $bgMusic : s3_url($bgMusic) }}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        @endif
                    @else
                        <div class="empty-audio-placeholder">
                            <i class="fas fa-music empty-audio-icon"></i>
                            <span class="empty-audio-text">No background music</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
