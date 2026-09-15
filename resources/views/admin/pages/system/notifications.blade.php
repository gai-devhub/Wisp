@extends('admin.base-admin')

@section('admin-section', 'notifications')

@section('content')
<div class="content-section active" id="notifications">
    <!-- Header Section -->
    <div class="admin-card-header-bar">
        <h2 class="admin-page-title">
            <i class="fas fa-bell text-primary"></i>
            Notifications & Alerts
        </h2>
        <div>
            <button class="admin-btn admin-btn-primary" id="sendNotificationBtn"><i class="fas fa-paper-plane"></i> Send Notification</button>
        </div>
    </div>

    <!-- Main Inbox Card -->
    <div class="card inbox-card">
        <div class="inbox-layout">
            
            <!-- Left Pane: List -->
            <div class="inbox-list-pane">
                <!-- Search Box first -->
                <div class="inbox-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="notification-search" placeholder="Search messages...">
                </div>

                <!-- Tabs Wrapper under the search input bar -->
                <div class="admin-notification-tabs-wrapper">
                    <div class="admin-notification-tabs-nav">
                        <button class="admin-tab-btn active" id="tab-broadcasts" onclick="switchAdminTab('broadcasts')">
                            <i class="fas fa-bullhorn"></i>
                            <span>Broadcasts</span>
                        </button>
                        <button class="admin-tab-btn" id="tab-inquiries" onclick="switchAdminTab('inquiries')">
                            <i class="fas fa-user-tag"></i>
                            <span>Inquiries</span>
                            <span class="admin-badge-count" id="inquiry-count">{{ $userInquiries ? $userInquiries->where('read_at', null)->count() : 0 }}</span>
                        </button>
                    </div>
                </div>

                <div class="border-b"></div>

                <!-- Broadcasts List -->
                <div class="inbox-list" id="inbox-list-broadcasts">
                    @forelse($notificationBroadcasts ?? collect() as $broadcast)
                        @php
                            $icons = ['info' => 'fa-info-circle', 'success' => 'fa-check-circle', 'warning' => 'fa-exclamation-triangle', 'error' => 'fa-exclamation-circle'];
                            $icon = $icons[$broadcast->type] ?? $icons['info'];
                        @endphp
                        <div class="inbox-item" id="item-b-{{ $broadcast->id }}" onclick="showBroadcastDetail('{{ $broadcast->id }}')">
                            <div class="item-avatar">
                                @if($broadcast->sender && $broadcast->sender->profile_picture)
                                    <img src="{{ s3_url($broadcast->sender->profile_picture) }}" alt="Avatar">
                                @else
                                    <i class="fas {{ $icon }}"></i>
                                @endif
                            </div>
                            <div class="item-content">
                                <div class="item-header">
                                    <span class="item-sender">WISP Admin</span>
                                    <span class="item-time">{{ $broadcast->created_at ? $broadcast->created_at->format('M j, g:i A') : '-' }}</span>
                                </div>
                                <h4 class="item-subject">{{ e($broadcast->title) }}</h4>
                                <div class="item-preview">{{ Str::limit(e($broadcast->message), 60) }}</div>
                            </div>
                            <div class="item-actions">
                                <button class="btn btn-sm theme-delete-btn" onclick="deleteNotification(event, 'b-{{ $broadcast->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="inbox-empty-list p-4 text-center text-muted">
                            No broadcasts sent yet.
                        </div>
                    @endforelse
                </div>

                <!-- Inquiries List -->
                <div class="inbox-list" id="inbox-list-inquiries">
                    @forelse($userInquiries ?? collect() as $inquiry)
                        <div class="inbox-item {{ $inquiry->read_at ? '' : 'unread' }}" id="item-i-{{ $inquiry->id }}" onclick="showInquiryDetail('{{ $inquiry->id }}')">
                            <div class="item-avatar">
                                @if($inquiry->sender && $inquiry->sender->profile_picture)
                                    <img src="{{ s3_url($inquiry->sender->profile_picture) }}" alt="Avatar">
                                @else
                                    {{ strtoupper(substr($inquiry->sender->username ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div class="item-content">
                                <div class="item-header">
                                    <span class="item-sender">{{ $inquiry->sender->username ?? 'User' }}</span>
                                    <span class="item-time">{{ $inquiry->created_at ? $inquiry->created_at->format('M j, g:i A') : '-' }}</span>
                                </div>
                                <h4 class="item-subject">{{ e($inquiry->title) }}</h4>
                                <div class="item-preview">{{ Str::limit(e($inquiry->message), 60) }}</div>
                            </div>
                            <div class="item-actions">
                                <button class="btn btn-sm theme-delete-btn" onclick="deleteNotification(event, 'i-{{ $inquiry->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="inbox-empty-list p-4 text-center text-muted">
                            No inquiries from users yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Pane: Reader -->
            <div class="inbox-reader-pane">
                <button type="button" class="inbox-mobile-back-btn" onclick="closeInboxMobileDetail(event)" aria-label="Back to list">
                    <i class="fas fa-chevron-left" aria-hidden="true"></i> Back
                </button>
                
                <div id="inbox-reader-empty" class="inbox-reader-empty">
                    <i class="fas fa-envelope-open-text"></i>
                    <h4>Select a message</h4>
                    <p>Choose a message from the list to read it here.</p>
                </div>

                <!-- Broadcast Detail Blocks -->
                <div id="inbox-reader-content-broadcasts" class="inbox-reader-content">
                    @foreach($notificationBroadcasts ?? collect() as $broadcast)
                        @php
                            $icons = ['info' => 'fa-info-circle', 'success' => 'fa-check-circle', 'warning' => 'fa-exclamation-triangle', 'error' => 'fa-exclamation-circle'];
                            $icon = $icons[$broadcast->type] ?? $icons['info'];
                            $readCount = $broadcast->userNotifications->whereNotNull('read_at')->count();
                            $recipientCount = $broadcast->userNotifications->count();
                        @endphp
                        <div class="notification-detail-block flex-column h-100" id="detail-b-{{ $broadcast->id }}">
                            <div class="inbox-reader-header">
                                <div class="inbox-reader-header-top">
                                    <h3 class="reader-title">{{ e($broadcast->title) }}</h3>
                                    <button class="reader-delete-btn" onclick="deleteNotification(event, 'b-{{ $broadcast->id }}')" title="Delete Broadcast">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                                <div class="inbox-reader-meta">
                                    <div class="reader-sender">
                                        <div class="sender-avatar">
                                            @if($broadcast->sender && $broadcast->sender->profile_picture)
                                                <img src="{{ s3_url($broadcast->sender->profile_picture) }}" alt="Avatar">
                                            @else
                                                <i class="fas {{ $icon }}"></i>
                                            @endif
                                        </div>
                                        <div class="sender-info">
                                            <h4>WISP Admin</h4>
                                            <p>Sent to all users ({{ $readCount }}/{{ $recipientCount }} read)</p>
                                        </div>
                                    </div>
                                    <div class="reader-time">{{ $broadcast->created_at ? $broadcast->created_at->format('M j, Y \a\t g:i A') : '-' }}</div>
                                </div>
                            </div>
                            
                            <div class="inbox-reader-body">
                                {{ e($broadcast->message) }}
                                
                                <hr class="my-4 border-t">
                                <h5 class="text-muted text-xs uppercase">Recipient Read Status</h5>
                                
                                <div class="admin-notification-recipient-list">
                                    @forelse($broadcast->userNotifications as $un)
                                        <div class="admin-notification-recipient">
                                            <div class="admin-notification-recipient-user">
                                                <span class="admin-notification-recipient-avatar">
                                                    @if($un->user && $un->user->profile_picture)
                                                        <img src="{{ s3_url($un->user->profile_picture) }}" alt="Avatar">
                                                    @else
                                                        {{ strtoupper(substr($un->user->username ?? $un->user->email ?? 'U', 0, 1)) }}
                                                    @endif
                                                </span>
                                                <span class="blur-sensitive">{{ e($un->user->username ?? $un->user->email ?? ('User #' . $un->user_id)) }}</span>
                                            </div>
                                            @if($un->read_at)
                                                <span class="badge-read">Read {{ $un->read_at->diffForHumans() }}</span>
                                            @else
                                                <span class="badge-unread">Unread</span>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-muted italic text-xs">No recipients yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Inquiry Detail Blocks -->
                <div id="inbox-reader-content-inquiries" class="inbox-reader-content">
                    @forelse($userInquiries ?? collect() as $inquiry)
                        <div class="notification-detail-block flex-column h-100" id="detail-i-{{ $inquiry->id }}">
                            <div class="inbox-reader-header">
                                <div class="inbox-reader-header-top">
                                    <h3 class="reader-title">{{ e($inquiry->title) }}</h3>
                                    <button class="reader-delete-btn" onclick="deleteNotification(event, 'i-{{ $inquiry->id }}')" title="Delete Inquiry">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                                <div class="inbox-reader-meta">
                                    <div class="reader-sender">
                                        <div class="sender-avatar">
                                            @if($inquiry->sender && $inquiry->sender->profile_picture)
                                                <img src="{{ s3_url($inquiry->sender->profile_picture) }}" alt="Avatar">
                                            @else
                                                {{ strtoupper(substr($inquiry->sender->username ?? 'U', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="sender-info">
                                            <h4>{{ e($inquiry->sender->username ?? 'User') }}</h4>
                                            <p>User Inquiry</p>
                                        </div>
                                    </div>
                                    <div class="reader-time">{{ $inquiry->created_at ? $inquiry->created_at->format('M j, Y \a\t g:i A') : '-' }}</div>
                                </div>
                            </div>
                            
                            <div class="inbox-reader-body chat-container">
                                <!-- Root Message (from user) -->
                                @php
                                    $isMyMessage = $inquiry->sender_id === auth()->id();
                                    if ($inquiry->sender_id === $inquiry->user_id) {
                                        $isMyMessage = str_starts_with($inquiry->title, 'Message from Admin'); // Admin sent broadcast
                                    }
                                @endphp
                                <div class="chat-message {{ $isMyMessage ? 'chat-sent' : 'chat-received' }}">
                                    <div class="chat-bubble">{!! nl2br(e(trim($inquiry->message))) !!}</div>
                                    <div class="chat-time">{{ $inquiry->created_at->format('g:i A') }}</div>
                                </div>

                                <!-- Replies -->
                                @foreach($inquiry->replies ?? [] as $reply)
                                    @php
                                        $isAdminReply = $reply->sender_id === auth()->id();
                                        if ($reply->sender_id === $reply->user_id) {
                                            $isAdminReply = ($reply->title === 'Reply from Admin');
                                        }
                                    @endphp
                                    <div class="chat-message {{ $isAdminReply ? 'chat-sent' : 'chat-received' }}">
                                        <div class="chat-bubble">{!! nl2br(e(trim($reply->message))) !!}</div>
                                        <div class="chat-time">{{ $reply->created_at->format('g:i A') }}</div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Reply Area -->
                            <div class="inbox-reader-reply">
                                <form action="{{ route('admin.notifications.reply', $inquiry->id) }}" method="POST">
                                    @csrf
                                    <textarea name="message" id="reply-message-{{ $inquiry->id }}" placeholder="Type your reply to the user..."></textarea>
                                    <div class="reply-actions">
                                        <span class="text-muted small mr-auto"><i class="fas fa-info-circle mr-1"></i> User will receive this as an in-app notification.</span>
                                        <button type="submit" class="admin-btn admin-btn-primary">
                                            <i class="fas fa-paper-plane"></i> Send Reply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchAdminTab(tab) {
    // Buttons
    var btnBroadcasts = document.getElementById("tab-broadcasts");
    var btnInquiries = document.getElementById("tab-inquiries");
    if (btnBroadcasts) btnBroadcasts.classList.toggle("active", tab === "broadcasts");
    if (btnInquiries) btnInquiries.classList.toggle("active", tab === "inquiries");

    // Lists
    var listBroadcasts = document.getElementById("inbox-list-broadcasts");
    var listInquiries = document.getElementById("inbox-list-inquiries");
    if (listBroadcasts) listBroadcasts.style.display = (tab === "broadcasts" ? "block" : "none");
    if (listInquiries) listInquiries.style.display = (tab === "inquiries" ? "block" : "none");

    // Reset layout view
    var layout = document.querySelector(".inbox-layout");
    if (layout) layout.classList.remove("detail-open");
    
    // Select first item depending on tab
    if (tab === "broadcasts") {
        var first = document.querySelector("#inbox-list-broadcasts .inbox-item");
        if (first) {
            first.click();
        } else {
            showEmptyReader();
        }
    } else {
        var first = document.querySelector("#inbox-list-inquiries .inbox-item");
        if (first) {
            first.click();
        } else {
            showEmptyReader();
        }
    }
}

function showEmptyReader() {
    var emptyEl = document.getElementById("inbox-reader-empty");
    if (emptyEl) emptyEl.style.display = "flex";
    
    var readerB = document.getElementById("inbox-reader-content-broadcasts");
    if (readerB) readerB.style.display = "none";
    
    var readerI = document.getElementById("inbox-reader-content-inquiries");
    if (readerI) readerI.style.display = "none";

    document.querySelectorAll(".notification-detail-block").forEach(el => el.style.display = "none");
    document.querySelectorAll(".inbox-item").forEach(el => el.classList.remove("active"));
}

function showBroadcastDetail(id) {
    var emptyEl = document.getElementById("inbox-reader-empty");
    if (emptyEl) emptyEl.style.display = "none";
    
    var readerB = document.getElementById("inbox-reader-content-broadcasts");
    if (readerB) readerB.style.display = "flex";
    
    var readerI = document.getElementById("inbox-reader-content-inquiries");
    if (readerI) readerI.style.display = "none";

    document.querySelectorAll(".notification-detail-block").forEach(el => el.style.display = "none");
    document.querySelectorAll(".inbox-item").forEach(el => el.classList.remove("active"));
    
    const detail = document.getElementById("detail-b-" + id);
    if (detail) detail.style.display = "flex";
    
    const thread = document.getElementById("item-b-" + id);
    if (thread) thread.classList.add("active");
    
    if (window.innerWidth <= 992) {
        var layout = document.querySelector(".inbox-layout");
        if (layout) layout.classList.add("detail-open");
    }
}

function showInquiryDetail(id) {
    var emptyEl = document.getElementById("inbox-reader-empty");
    if (emptyEl) emptyEl.style.display = "none";
    
    var readerB = document.getElementById("inbox-reader-content-broadcasts");
    if (readerB) readerB.style.display = "none";
    
    var readerI = document.getElementById("inbox-reader-content-inquiries");
    if (readerI) readerI.style.display = "flex";

    document.querySelectorAll(".notification-detail-block").forEach(el => el.style.display = "none");
    document.querySelectorAll(".inbox-item").forEach(el => el.classList.remove("active"));
    
    const detail = document.getElementById("detail-i-" + id);
    if (detail) detail.style.display = "flex";
    
    const thread = document.getElementById("item-i-" + id);
    if (thread) {
        thread.classList.add("active");
        
        if (thread.classList.contains('unread')) {
            thread.classList.remove('unread');
            
            // Call API to mark as read
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Content-Type': 'application/json' 
                }
            }).then(response => {
                if (response.ok) {
                    updateInquiryUnreadCount();
                }
            });
        }
    }
    
    if (window.innerWidth <= 992) {
        var layout = document.querySelector(".inbox-layout");
        if (layout) layout.classList.add("detail-open");
    }
}

function updateInquiryUnreadCount() {
    const count = document.querySelectorAll('#inbox-list-inquiries .inbox-item.unread').length;
    const badge = document.getElementById('inquiry-count');
    if (badge) {
        badge.innerText = count;
    }
}

function closeInboxMobileDetail(e) {
    if(e) e.stopPropagation();
    document.querySelector(".inbox-layout").classList.remove("detail-open");
}

document.addEventListener("DOMContentLoaded", function() {
    // Default view
    switchAdminTab("broadcasts");
    
    // Search filter
    document.getElementById("notification-search").addEventListener("keyup", function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll(".inbox-item").forEach(function(item) {
            const text = item.innerText.toLowerCase();
            if(text.includes(term)) {
                item.style.display = "flex";
            } else {
                item.style.display = "none";
            }
        });
    });
});

function deleteNotification(e, idStr) {
    e.stopPropagation();
    
    // Parse the type and ID
    const parts = idStr.split('-');
    const type = parts[0]; // 'b' or 'i'
    const id = parts[1]; // numeric ID
    
    let url = '';
    if (type === 'i') {
        url = `/notifications/${id}/delete`;
    } else if (type === 'b') {
        url = `/admin/notifications/broadcast/${id}`;
    } else {
        url = `/notifications/${idStr}/delete`; // fallback if no prefix
    }
    
    showAdminConfirm('Are you sure you want to delete this?', function() {
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                const item = document.getElementById('item-' + idStr);
                if (item) {
                    item.remove();
                    updateInquiryUnreadCount();
                }
                
                const detail = document.getElementById('detail-' + idStr);
                if (detail && detail.style.display !== 'none') {
                    showEmptyReader();
                }
            } else {
                showAdminAlert('Failed to delete message. Please try again.');
            }
        }).catch(err => {
            showAdminAlert('An error occurred. Please try again.');
        });
    });
}
</script>
@endsection