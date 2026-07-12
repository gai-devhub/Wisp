@extends('admin.base-admin')

@section('admin-section', 'notifications')

@section('content')
<div class="content-section active" id="notifications">
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-weight: 700; color: var(--text-main); font-size: 1.6rem; margin: 0; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-bell text-primary" style="opacity: 0.9;"></i>
            Notifications & Alerts
        </h2>
        <div>
            <button class="btn btn-primary" id="sendNotificationBtn" style="margin-bottom: 0; border-radius: 50px;"><i class="fas fa-paper-plane"></i> Send Notification</button>
        </div>
    </div>

    <!-- Main Inbox Card (matching user style exactly) -->
    <div class="card inbox-card" style="background: var(--admin-bg-soft); border: 1px solid var(--admin-border); height: 700px; max-width: 1200px; margin: 0 auto;">
        <div class="inbox-layout">
            
            <!-- Left Pane: List -->
            <div class="inbox-list-pane" style="background: var(--admin-bg-main); border-right: 1px solid var(--admin-border);">
                <!-- Tabs Wrapper inside the card -->
                <div class="admin-notification-tabs-wrapper" style="margin: 16px 16px 0 16px; width: calc(100% - 32px); display: flex; justify-content: stretch;">
                    <div class="admin-notification-tabs-nav" style="width: 100%; display: flex;">
                        <button class="admin-tab-btn active" id="tab-broadcasts" onclick="switchAdminTab('broadcasts')" style="flex: 1; justify-content: center;">
                            <i class="fas fa-bullhorn"></i>
                            <span>Broadcasts</span>
                        </button>
                        <button class="admin-tab-btn" id="tab-inquiries" onclick="switchAdminTab('inquiries')" style="flex: 1; justify-content: center;">
                            <i class="fas fa-user-tag"></i>
                            <span>Inquiries</span>
                            <span class="admin-badge-count" id="inquiry-count">{{ count($userInquiries ?? []) }}</span>
                        </button>
                    </div>
                </div>

                <div class="inbox-search" style="border-bottom: 1px solid var(--admin-border);">
                    <i class="fas fa-search" style="color: var(--admin-text-muted);"></i>
                    <input type="text" id="notification-search" placeholder="Search messages..." style="background: var(--admin-bg-soft); border: 1px solid var(--admin-border); color: var(--admin-text-main);">
                </div>

                <!-- Broadcasts List -->
                <div class="inbox-list" id="inbox-list-broadcasts">
                    @forelse($notificationBroadcasts ?? collect() as $broadcast)
                        @php
                            $icons = ['info' => 'fa-info-circle', 'success' => 'fa-check-circle', 'warning' => 'fa-exclamation-triangle', 'error' => 'fa-exclamation-circle'];
                            $icon = $icons[$broadcast->type] ?? $icons['info'];
                        @endphp
                        <div class="inbox-item" id="item-b-{{ $broadcast->id }}" onclick="showBroadcastDetail('{{ $broadcast->id }}')" style="border-bottom: 1px solid var(--admin-border);">
                            <div class="item-avatar" style="background: var(--admin-bg-soft); color: var(--admin-primary);">
                                @if($broadcast->sender && $broadcast->sender->profile_picture)
                                    <img src="{{ s3_url($broadcast->sender->profile_picture) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                @else
                                    <i class="fas {{ $icon }}"></i>
                                @endif
                            </div>
                            <div class="item-content">
                                <div class="item-header">
                                    <span class="item-sender" style="color: var(--admin-text-main);">WISP Admin</span>
                                    <span class="item-time" style="color: var(--admin-text-muted);">{{ $broadcast->created_at ? $broadcast->created_at->format('M j, g:i A') : '-' }}</span>
                                </div>
                                <h4 class="item-subject" style="color: var(--admin-text-main);">{{ e($broadcast->title) }}</h4>
                                <div class="item-preview" style="color: var(--admin-text-muted);">{{ Str::limit(e($broadcast->message), 60) }}</div>
                            </div>
                            <div class="item-actions">
                                <button class="btn btn-sm theme-delete-btn" onclick="deleteNotification(event, 'b-{{ $broadcast->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="inbox-empty-list" style="padding: 20px; text-align: center; color: var(--admin-text-muted);">
                            No broadcasts sent yet.
                        </div>
                    @endforelse
                </div>

                <!-- Inquiries List -->
                <div class="inbox-list" id="inbox-list-inquiries" style="display: none;">
                    @forelse($userInquiries ?? collect() as $inquiry)
                        <div class="inbox-item" id="item-i-{{ $inquiry->id }}" onclick="showInquiryDetail('{{ $inquiry->id }}')" style="border-bottom: 1px solid var(--admin-border);">
                            <div class="item-avatar" style="background: var(--admin-bg-soft); color: var(--admin-primary);">
                                @if($inquiry->sender && $inquiry->sender->profile_picture)
                                    <img src="{{ s3_url($inquiry->sender->profile_picture) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                @else
                                    {{ strtoupper(substr($inquiry->sender->username ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div class="item-content">
                                <div class="item-header">
                                    <span class="item-sender" style="color: var(--admin-text-main);">{{ $inquiry->sender->username ?? 'User' }}</span>
                                    <span class="item-time" style="color: var(--admin-text-muted);">{{ $inquiry->created_at ? $inquiry->created_at->format('M j, g:i A') : '-' }}</span>
                                </div>
                                <h4 class="item-subject" style="color: var(--admin-text-main);">{{ e($inquiry->title) }}</h4>
                                <div class="item-preview" style="color: var(--admin-text-muted);">{{ Str::limit(e($inquiry->message), 60) }}</div>
                            </div>
                            <div class="item-actions">
                                <button class="btn btn-sm theme-delete-btn" onclick="deleteNotification(event, 'i-{{ $inquiry->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="inbox-empty-list" style="padding: 20px; text-align: center; color: var(--admin-text-muted);">
                            No inquiries from users yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Pane: Reader -->
            <div class="inbox-reader-pane" style="background: var(--admin-bg-main);">
                <button type="button" class="inbox-mobile-back-btn" onclick="closeInboxMobileDetail(event)" aria-label="Back to list">
                    <i class="fas fa-chevron-left" aria-hidden="true"></i> Back
                </button>
                
                <div id="inbox-reader-empty" class="inbox-reader-empty" style="color: var(--admin-text-muted);">
                    <i class="fas fa-envelope-open-text"></i>
                    <h4>Select a message</h4>
                    <p>Choose a message from the list to read it here.</p>
                </div>

                <!-- Broadcast Detail Blocks -->
                <div id="inbox-reader-content-broadcasts" class="inbox-reader-content" style="display: none;">
                    @foreach($notificationBroadcasts ?? collect() as $broadcast)
                        @php
                            $icons = ['info' => 'fa-info-circle', 'success' => 'fa-check-circle', 'warning' => 'fa-exclamation-triangle', 'error' => 'fa-exclamation-circle'];
                            $icon = $icons[$broadcast->type] ?? $icons['info'];
                            $readCount = $broadcast->userNotifications->whereNotNull('read_at')->count();
                            $recipientCount = $broadcast->userNotifications->count();
                        @endphp
                        <div class="notification-detail-block flex-column h-100" id="detail-b-{{ $broadcast->id }}" style="display: none;">
                            <div class="inbox-reader-header" style="border-bottom: 1px solid var(--admin-border); position: relative;">
                                <button class="btn btn-sm theme-delete-btn" style="position: absolute; top: 24px; right: 32px;" onclick="deleteNotification(event, 'b-{{ $broadcast->id }}')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                                <h3 class="reader-title" style="color: var(--admin-text-main); padding-right: 80px;">{{ e($broadcast->title) }}</h3>
                                <div class="inbox-reader-meta">
                                    <div class="reader-sender">
                                        <div class="sender-avatar" style="background: var(--admin-bg-soft); color: var(--admin-primary);">
                                            @if($broadcast->sender && $broadcast->sender->profile_picture)
                                                <img src="{{ s3_url($broadcast->sender->profile_picture) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                            @else
                                                <i class="fas {{ $icon }}"></i>
                                            @endif
                                        </div>
                                        <div class="sender-info">
                                            <h4 style="color: var(--admin-text-main);">WISP Admin</h4>
                                            <p style="color: var(--admin-text-muted);">Sent to all users ({{ $readCount }}/{{ $recipientCount }} read)</p>
                                        </div>
                                    </div>
                                    <div class="reader-time" style="color: var(--admin-text-muted);">{{ $broadcast->created_at ? $broadcast->created_at->format('M j, Y \a\t g:i A') : '-' }}</div>
                                </div>
                            </div>
                            
                            <!-- Notice how broadcasts are one-way, so we don't use chat layout, just the text -->
                            <div class="inbox-reader-body" style="padding: 24px; color: var(--admin-text-main); font-size: 0.95rem; line-height: 1.6; white-space: pre-line; overflow-y: auto;">
                                {{ e($broadcast->message) }}
                                
                                <hr style="margin: 24px 0; border-top: 1px solid var(--admin-border);">
                                <h5 style="color: var(--admin-text-muted); font-size: 0.85rem; text-transform: uppercase;">Recipient Read Status</h5>
                                
                                <div class="admin-notification-recipient-list" style="margin-top: 12px;">
                                    @forelse($broadcast->userNotifications as $un)
                                        <div class="admin-notification-recipient" style="display: flex; justify-content: space-between; padding: 12px; border-bottom: 1px solid var(--admin-border);">
                                            <div class="admin-notification-recipient-user" style="display: flex; align-items: center; gap: 12px; font-weight: 600;">
                                                <span class="admin-notification-recipient-avatar" style="width: 32px; height: 32px; border-radius: 50%; background: var(--admin-bg-soft); display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                                    @if($un->user && $un->user->profile_picture)
                                                        <img src="{{ s3_url($un->user->profile_picture) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                                    @else
                                                        {{ strtoupper(substr($un->user->username ?? $un->user->email ?? 'U', 0, 1)) }}
                                                    @endif
                                                </span>
                                                <span class="blur-sensitive" style="color: var(--admin-text-main);">{{ e($un->user->username ?? $un->user->email ?? ('User #' . $un->user_id)) }}</span>
                                            </div>
                                            @if($un->read_at)
                                                <span class="badge" style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem;">Read {{ $un->read_at->diffForHumans() }}</span>
                                            @else
                                                <span class="badge" style="background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem;">Unread</span>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-muted" style="font-style: italic; font-size: 0.85rem; color: var(--admin-text-muted);">No recipients yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Inquiry Detail Blocks -->
                <div id="inbox-reader-content-inquiries" class="inbox-reader-content" style="display: none;">
                    @forelse($userInquiries ?? collect() as $inquiry)
                        <div class="notification-detail-block flex-column h-100" id="detail-i-{{ $inquiry->id }}" style="display: none;">
                            <div class="inbox-reader-header" style="border-bottom: 1px solid var(--admin-border); position: relative;">
                                <button class="btn btn-sm theme-delete-btn" style="position: absolute; top: 24px; right: 32px;" onclick="deleteNotification(event, 'i-{{ $inquiry->id }}')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                                <h3 class="reader-title" style="color: var(--admin-text-main); padding-right: 80px;">{{ e($inquiry->title) }}</h3>
                                <div class="inbox-reader-meta">
                                    <div class="reader-sender">
                                        <div class="sender-avatar" style="background: var(--admin-bg-soft); color: var(--admin-primary);">
                                            @if($inquiry->sender && $inquiry->sender->profile_picture)
                                                <img src="{{ s3_url($inquiry->sender->profile_picture) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                            @else
                                                {{ strtoupper(substr($inquiry->sender->username ?? 'U', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="sender-info">
                                            <h4 style="color: var(--admin-text-main);">{{ e($inquiry->sender->username ?? 'User') }}</h4>
                                            <p style="color: var(--admin-text-muted);">User Inquiry</p>
                                        </div>
                                    </div>
                                    <div class="reader-time" style="color: var(--admin-text-muted);">{{ $inquiry->created_at ? $inquiry->created_at->format('M j, Y \a\t g:i A') : '-' }}</div>
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
                                    <div class="chat-bubble">
                                        {!! nl2br(e($inquiry->message)) !!}
                                    </div>
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
                                        <div class="chat-bubble">
                                            {!! nl2br(e($reply->message)) !!}
                                        </div>
                                        <div class="chat-time">{{ $reply->created_at->format('g:i A') }}</div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Reply Area -->
                            <div class="inbox-reader-reply" style="border-top: 1px solid var(--admin-border); background: var(--admin-bg-soft);">
                                <form action="{{ route('admin.notifications.reply', $inquiry->id) }}" method="POST">
                                    @csrf
                                    <textarea name="message" id="reply-message-{{ $inquiry->id }}" placeholder="Type your reply to the user..." style="background: var(--admin-bg-main); border: 1px solid var(--admin-border); color: var(--admin-text-main);"></textarea>
                                    <div class="reply-actions">
                                        <span class="text-muted small" style="margin-right: auto; font-size: 0.8rem; color: var(--admin-text-muted);"><i class="fas fa-info-circle mr-1"></i> User will receive this as an in-app notification.</span>
                                        <button type="submit" class="btn btn-primary" style="border-radius: 50px;">
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
    document.getElementById("tab-broadcasts").classList.remove("active");
    document.getElementById("tab-inquiries").classList.remove("active");
    document.getElementById("tab-" + tab).classList.add("active");

    // Lists
    document.getElementById("inbox-list-broadcasts").style.display = (tab === "broadcasts" ? "block" : "none");
    document.getElementById("inbox-list-inquiries").style.display = (tab === "inquiries" ? "block" : "none");

    // Readers
    document.getElementById("inbox-reader-content-broadcasts").style.display = (tab === "broadcasts" ? "flex" : "none");
    document.getElementById("inbox-reader-content-inquiries").style.display = (tab === "inquiries" ? "flex" : "none");

    // Reset layout view
    document.querySelector(".inbox-layout").classList.remove("detail-open");
    
    // Select first item depending on tab
    if (tab === "broadcasts") {
        const first = document.querySelector("#inbox-list-broadcasts .inbox-item");
        if (first) {
            first.click();
        } else {
            showEmptyReader();
        }
    } else {
        const first = document.querySelector("#inbox-list-inquiries .inbox-item");
        if (first) {
            first.click();
        } else {
            showEmptyReader();
        }
    }
}

function showEmptyReader() {
    document.getElementById("inbox-reader-empty").style.display = "flex";
    document.querySelectorAll(".notification-detail-block").forEach(el => el.style.display = "none");
    document.querySelectorAll(".inbox-item").forEach(el => el.classList.remove("active"));
}

function showBroadcastDetail(id) {
    document.getElementById("inbox-reader-empty").style.display = "none";
    document.querySelectorAll(".notification-detail-block").forEach(el => el.style.display = "none");
    document.querySelectorAll(".inbox-item").forEach(el => el.classList.remove("active"));
    
    const detail = document.getElementById("detail-b-" + id);
    if (detail) detail.style.display = "flex";
    
    const thread = document.getElementById("item-b-" + id);
    if (thread) thread.classList.add("active");
    
    if (window.innerWidth <= 992) {
        document.querySelector(".inbox-layout").classList.add("detail-open");
    }
}

function showInquiryDetail(id) {
    document.getElementById("inbox-reader-empty").style.display = "none";
    document.querySelectorAll(".notification-detail-block").forEach(el => el.style.display = "none");
    document.querySelectorAll(".inbox-item").forEach(el => el.classList.remove("active"));
    
    const detail = document.getElementById("detail-i-" + id);
    if (detail) detail.style.display = "flex";
    
    const thread = document.getElementById("item-i-" + id);
    if (thread) thread.classList.add("active");
    
    if (window.innerWidth <= 992) {
        document.querySelector(".inbox-layout").classList.add("detail-open");
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

function deleteNotification(e, id) {
    e.stopPropagation();
    showAdminConfirm('Are you sure you want to delete this?', function() {
        // Here you would make an AJAX call to delete the notification.
        // For now, simply hide it.
        const item = document.getElementById('item-' + id);
        if(item) item.remove();
        
        const detail = document.getElementById('detail-' + id);
        if(detail && detail.style.display !== 'none') {
            showEmptyReader();
        }
    });
}
</script>

<style>
/* Modern Admin Notification Styles mapped to Inbox UI */
:root {
    --admin-primary: #6366f1;
    --admin-primary-light: #818cf8;
    --admin-secondary: #0ea5e9;
    --admin-bg-main: #ffffff;
    --admin-bg-soft: #f8fafc;
    --admin-border: #e2e8f0;
    --admin-text-main: #1e293b;
    --admin-text-muted: #64748b;
    --admin-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --admin-transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

[data-theme="dark"] {
    --admin-bg-main: #1e293b;
    --admin-bg-soft: #0f172a;
    --admin-border: #334155;
    --admin-text-main: #f1f5f9;
    --admin-text-muted: #94a3b8;
}

.admin-notification-tabs-wrapper {
    background: var(--admin-bg-main);
    padding: 6px;
    border-radius: 12px;
    display: inline-block;
    border: 1px solid var(--admin-border);
}
.admin-notification-tabs-nav {
    display: flex;
    gap: 4px;
}
.admin-tab-btn {
    border: none;
    background: transparent;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    color: var(--admin-text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
    transition: var(--admin-transition);
    cursor: pointer;
}
.admin-tab-btn i { font-size: 0.9rem; }
.admin-tab-btn:hover {
    color: var(--admin-primary);
    background: var(--admin-bg-soft);
}
.admin-tab-btn.active {
    background: var(--admin-primary);
    color: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.admin-tab-btn.active .admin-badge-count {
    background: var(--admin-bg-main);
    color: var(--admin-primary);
}
.admin-badge-count {
    background: var(--admin-bg-soft);
    color: var(--admin-text-muted);
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 20px;
    margin-left: 4px;
    font-weight: 700;
}

/* Inbox Layout exactly matching User side */
.inbox-card {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    border-radius: 16px;
    box-shadow: var(--admin-shadow);
}
.inbox-layout {
    display: flex;
    height: 100%;
    width: 100%;
}
.inbox-list-pane {
    width: 340px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    z-index: 10;
}
.inbox-search {
    padding: 20px;
    position: relative;
}
.inbox-search i {
    position: absolute;
    left: 36px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.5;
}
.inbox-search input {
    width: 100%;
    padding: 12px 16px 12px 40px;
    border-radius: 20px;
    outline: none;
    transition: all 0.2s ease;
}
.inbox-search input:focus {
    border-color: var(--admin-primary) !important;
}
.inbox-list {
    flex: 1;
    overflow-y: auto;
}
.inbox-item {
    display: flex;
    align-items: flex-start;
    padding: 16px 20px;
    cursor: pointer;
    transition: all 0.2s ease;
    gap: 16px;
}
.inbox-item:hover {
    background: var(--admin-bg-soft);
}
.inbox-item.active {
    background: var(--admin-bg-soft);
    border-left: 3px solid var(--admin-primary);
}
.inbox-item.unread .item-subject {
    font-weight: 700;
}
.item-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.item-content {
    flex: 1;
    min-width: 0;
}
.item-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 4px;
}
.item-sender {
    font-weight: 600;
    font-size: 0.95rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.item-time {
    font-size: 0.8rem;
}
.item-subject {
    font-size: 0.95rem;
    margin: 0 0 4px 0;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.item-preview {
    font-size: 0.85rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
}
.item-actions {
    display: flex;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.2s ease;
}
.inbox-item:hover .item-actions {
    opacity: 1;
}
.theme-delete-btn {
    background: transparent;
    color: #ef4444;
    border: 1px solid #ef4444;
    border-radius: 6px;
    transition: all 0.2s ease;
}
.theme-delete-btn:hover {
    background: #ef4444;
    color: #fff;
}

/* Reader Pane */
.inbox-reader-pane {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    position: relative;
}
.inbox-reader-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    text-align: center;
    padding: 40px;
}
.inbox-reader-empty i {
    font-size: 48px;
    opacity: 0.3;
    margin-bottom: 20px;
}
.inbox-reader-empty h4 {
    font-size: 1.2rem;
    margin: 0 0 10px 0;
}
.inbox-reader-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}
.notification-detail-block {
    display: flex;
    flex-direction: column;
    height: 100%;
}
.inbox-reader-header {
    padding: 24px 32px;
}
.reader-title {
    margin: 0 0 16px 0;
    font-size: 1.4rem;
    font-weight: 700;
}
.inbox-reader-meta {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.reader-sender {
    display: flex;
    align-items: center;
    gap: 12px;
}
.sender-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.2rem;
}
.sender-info h4 {
    margin: 0 0 4px 0;
    font-size: 1rem;
}
.sender-info p {
    margin: 0;
    font-size: 0.85rem;
}
.reader-time {
    font-size: 0.9rem;
}

/* Chat Layout Styles */
.inbox-reader-body.chat-container {
    flex: 1;
    overflow-y: auto;
    padding: 32px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.chat-message {
    display: flex;
    flex-direction: column;
    max-width: 80%;
}
.chat-sent {
    align-self: flex-end;
    align-items: flex-end;
}
.chat-received {
    align-self: flex-start;
    align-items: flex-start;
}
.chat-bubble {
    padding: 14px 20px;
    border-radius: 20px;
    font-size: 0.95rem;
    line-height: 1.5;
    word-break: break-word;
}
.chat-sent .chat-bubble {
    background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
    color: white;
    border-bottom-right-radius: 4px;
}
.chat-received .chat-bubble {
    background: var(--admin-bg-soft);
    color: var(--admin-text-main);
    border-bottom-left-radius: 4px;
    border: 1px solid var(--admin-border);
}
.chat-time {
    font-size: 0.75rem;
    color: var(--admin-text-muted);
    margin-top: 6px;
    padding: 0 6px;
}

/* Reply Area */
.inbox-reader-reply {
    padding: 24px 32px;
    flex-shrink: 0;
}
.inbox-reader-reply form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.inbox-reader-reply textarea {
    width: 100%;
    min-height: 100px;
    border-radius: 12px;
    padding: 16px;
    resize: none;
    outline: none;
    transition: all 0.2s ease;
    font-family: inherit;
    font-size: 1rem;
}
.inbox-reader-reply textarea:focus {
    border-color: var(--admin-primary) !important;
}
.reply-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
}

.inbox-mobile-back-btn {
    display: none;
}

/* Animations and Responsive */
@media (max-width: 992px) {
    .inbox-card {
        height: 600px;
    }
    .inbox-layout {
        position: relative;
    }
    .inbox-list-pane {
        width: 100%;
        transition: transform 0.3s ease;
    }
    .inbox-reader-pane {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        z-index: 20;
    }
    .inbox-layout.detail-open .inbox-list-pane {
        transform: translateX(-30%);
    }
    .inbox-layout.detail-open .inbox-reader-pane {
        transform: translateX(0);
    }
    .inbox-mobile-back-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        background: none;
        border: none;
        padding: 16px 20px;
        border-bottom: 1px solid var(--admin-border);
        font-weight: 600;
        color: var(--admin-primary);
        cursor: pointer;
    }
}
</style>
@endsection