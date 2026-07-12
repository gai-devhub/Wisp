@extends('user.base-user')

@section('user-section', 'notifications')

@section('content')
    <div class="content-section active" id="notifications">
        <!-- Header Section -->
        <div class="messages-header-toolbar">
            <div>
                <h2 class="messages-header-title">
                    <i class="fas fa-inbox text-primary messages-header-icon"></i>
                    Inbox
                </h2>
                <p class="inbox-header-subtitle">
                    You have <span id="inbox-unread-count" class="inbox-unread-count-text">{{ $notifications->where('read_at', null)->count() }}</span>
                    unread messages
                </p>
            </div>
            <div class="inbox-header-actions">
                <button class="btn btn-primary inbox-compose-btn" onclick="openComposeModal()">
                    <i class="fas fa-plus"></i> Compose
                </button>
            </div>
        </div>

        <!-- Main Inbox Card -->
        <div class="card inbox-card">
            <div class="inbox-layout">
                <!-- Left Pane: List -->
                <div class="inbox-list-pane">
                    <div class="inbox-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="notification-search" placeholder="Search messages...">
                    </div>
                    <div class="inbox-list" id="inbox-list">
                        @forelse($notifications as $n)
                            @php
                                $isMyMessage = $n->sender_id === auth()->id();
                                $isFromAdmin = ($n->context === 'admin_message' || $n->type === 'admin_alert') && !$isMyMessage;
                                $displaySender = $isMyMessage ? 'Admin' : ($n->sender_name ?? ($isFromAdmin ? 'Admin' : 'System'));
                                $initials = strtoupper(substr($displaySender, 0, 2));
                                $displayTitle = $isMyMessage && str_starts_with($n->title, 'Message from ') ? 'Message to Admin' : $n->title;
                            @endphp
                            <div class="inbox-item {{ $n->read_at ? '' : 'unread' }}" id="item-{{ $n->id }}"
                                onclick="openNotification('{{ $n->id }}')">
                                <div class="item-avatar {{ $isFromAdmin || $isMyMessage ? 'admin-avatar' : '' }}">
                                    @if($n->sender && $n->sender->profile_picture && !$isMyMessage)
                                        <img src="{{ s3_url($n->sender->profile_picture) }}" alt="Avatar" class="item-avatar-img">
                                    @else
                                        {{ $initials }}
                                    @endif
                                </div>
                                <div class="item-content">
                                    <div class="item-header">
                                        <span class="item-sender">{{ $displaySender }}</span>
                                        <span class="item-time">{{ $n->created_at->format('M j, g:i A') }}</span>
                                    </div>
                                    <h4 class="item-subject">{{ $displayTitle }}</h4>
                                    <div class="item-preview">{{ Str::limit($n->message, 60) }}</div>
                                </div>
                                <div class="item-actions">
                                    <button class="btn btn-sm theme-delete-btn"
                                        onclick="deleteNotification(event, '{{ $n->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="inbox-empty-list">
                                No messages found.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Right Pane: Reader -->
                <div class="inbox-reader-pane">
                    <button type="button" class="inbox-mobile-back-btn" onclick="closeInboxMobileDetail(event)"
                        aria-label="Back to inbox">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i> Inbox
                    </button>
                    <div id="inbox-reader-empty" class="inbox-reader-empty">
                        <i class="fas fa-envelope-open-text"></i>
                        <h4>Select a message</h4>
                        <p>Choose a message from the list to read it here.</p>
                    </div>

                    <div id="inbox-reader-content" class="inbox-reader-content notifications-inline-1" >
                        @foreach($notifications as $n)
                            @php
                                $isMyMessage = $n->sender_id === auth()->id();
                                $isFromAdmin = ($n->context === 'admin_message' || $n->type === 'admin_alert') && !$isMyMessage;
                                $displaySender = $isMyMessage ? 'Admin' : ($n->sender_name ?? ($isFromAdmin ? 'Admin' : 'System'));
                                $displayTitle = $isMyMessage && str_starts_with($n->title, 'Message from ') ? 'Message to Admin' : $n->title;
                            @endphp
                            <div class="notification-detail-block flex-column h-100" id="detail-{{ $n->id }}"
                                class="d-none">
                                <div class="inbox-reader-header">
                                    <button class="btn btn-sm theme-delete-btn inbox-reader-delete-btn"
                                        onclick="deleteNotification(event, '{{ $n->id }}')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                    <h3 class="reader-title">{{ $displayTitle }}</h3>
                                    <div class="inbox-reader-meta">
                                        <div class="reader-sender">
                                            <div class="sender-avatar">
                                                @if($n->sender && $n->sender->profile_picture && !$isMyMessage)
                                                    <img src="{{ s3_url($n->sender->profile_picture) }}" alt="Avatar" class="item-avatar-img">
                                                @else
                                                    {{ strtoupper(substr($displaySender, 0, 1)) }}
                                                @endif
                                            </div>
                                            <div class="sender-info">
                                                <h4>{{ $displaySender }}</h4>
                                                <p>{{ ($isFromAdmin || $isMyMessage) ? 'admin@wisp.test' : 'system@wisp.test' }}</p>
                                            </div>
                                        </div>
                                        <div class="reader-time">{{ $n->created_at->format('M j, Y \a\t g:i A') }}</div>
                                    </div>
                                </div>

                                <div class="inbox-reader-body chat-container">
                                    <!-- Root Message -->
                                    @php
                                        $isMyMessage = $n->sender_id === auth()->id();
                                        if ($n->sender_id === $n->user_id) {
                                            $isMyMessage = str_starts_with($n->title, 'Message from ') && $n->title !== 'Message from Admin';
                                        }
                                    @endphp
                                    <div class="chat-message {{ $isMyMessage ? 'chat-sent' : 'chat-received' }}">
                                        <div class="chat-bubble">
                                            {!! nl2br(e($n->message)) !!}
                                        </div>
                                        <div class="chat-time">{{ $n->created_at->format('g:i A') }}</div>
                                    </div>

                                    <!-- Replies -->
                                    @foreach($n->replies ?? [] as $reply)
                                        @php
                                            $isMyReply = $reply->sender_id === auth()->id();
                                            if ($reply->sender_id === $reply->user_id) {
                                                $isMyReply = ($reply->title !== 'Reply from Admin');
                                            }
                                        @endphp
                                        <div class="chat-message {{ $isMyReply ? 'chat-sent' : 'chat-received' }}">
                                            <div class="chat-bubble">
                                                {!! nl2br(e($reply->message)) !!}
                                            </div>
                                            <div class="chat-time">{{ $reply->created_at->format('g:i A') }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($n->context === 'admin_message' || $isFromAdmin || $isMyMessage)
                                    <div class="inbox-reader-reply">
                                        <textarea id="reply-message-{{ $n->id }}" placeholder="Type your reply..."></textarea>
                                        <div class="reply-actions">
                                            <button class="btn btn-outline-primary"><i class="fas fa-paperclip"></i> Attach</button>
                                            <button class="btn btn-primary" onclick="sendReply('{{ $n->id }}')"><i
                                                    class="fas fa-paper-plane"></i> Send Reply</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compose Modal -->
    <div id="compose-modal" class="custom-modal notifications-inline-2" >
        <div class="custom-modal-content notifications-inline-3" >
            <div class="custom-modal-header">
                <h3>Compose Message to Admin</h3>
                <button class="close-btn" onclick="closeComposeModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="custom-modal-body">
                <div class="form-group mb-3">
                    <label>Message</label>
                    <textarea id="compose-message-input" class="form-control" rows="5"
                        placeholder="Type your message here..."></textarea>
                </div>
            </div>
            <div class="custom-modal-footer">
                <button class="btn btn-light" onclick="closeComposeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="sendCompose()"><i class="fas fa-paper-plane"></i> Send</button>
            </div>
        </div>

        

        <script>
            let currentNotificationId = null;

            function isInboxMobileLayout() {
                return window.matchMedia('(max-width: 768px)').matches;
            }

            function closeInboxMobileDetail(e) {
                if (e) e.preventDefault();
                var layout = document.querySelector('.inbox-layout');
                if (layout) layout.classList.remove('inbox-mobile-show-detail');
            }

            document.getElementById('notification-search').addEventListener('input', function (e) {
                const query = e.target.value.toLowerCase();
                document.querySelectorAll('.inbox-item').forEach(item => {
                    const text = item.innerText.toLowerCase();
                    item.style.display = text.includes(query) ? 'flex' : 'none';
                });
            });

            function openNotification(id) {
                currentNotificationId = id;

                var layout = document.querySelector('.inbox-layout');
                if (layout && isInboxMobileLayout()) {
                    layout.classList.add('inbox-mobile-show-detail');
                }

                // UI State
                document.getElementById('inbox-reader-empty').style.display = 'none';
                document.getElementById('inbox-reader-content').style.display = 'block';

                // Toggle Blocks
                document.querySelectorAll('.notification-detail-block').forEach(el => {
                    el.style.display = 'none';
                    el.classList.remove('active');
                });
                document.getElementById('detail-' + id).classList.add('active');

                // Sidebar active state
                document.querySelectorAll('.inbox-item').forEach(el => el.classList.remove('active'));
                document.getElementById('item-' + id).classList.add('active');

                // Mark as read visually
                const item = document.getElementById('item-' + id);
                if (item.classList.contains('unread')) {
                    item.classList.remove('unread');
                    updateUnreadCount();

                    fetch(`/notifications/${id}/read`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                    });
                }
            }

            function updateUnreadCount() {
                const count = document.querySelectorAll('.inbox-item.unread').length;
                document.getElementById('inbox-unread-count').innerText = count;
            }

            function deleteNotification(event, id) {
                if (event) event.stopPropagation(); // prevent opening reader
                fetch(`/notifications/${id}/delete`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.ok) {
                            // Remove item from list
                            const item = document.getElementById('item-' + id);
                            if (item) item.remove();

                            // Hide reader if open
                            if (currentNotificationId == id) {
                                document.getElementById('inbox-reader-empty').style.display = 'flex';
                                document.getElementById('inbox-reader-content').style.display = 'none';
                                currentNotificationId = null;
                                closeInboxMobileDetail();
                            }

                            updateUnreadCount();
                        } else {
                            alert('Error: ' + (data.error || 'Failed to delete'));
                        }
                    });
            }

            window.addEventListener('resize', function () {
                if (!isInboxMobileLayout()) {
                    closeInboxMobileDetail();
                }
            });

            function sendReply(id) {
                const text = document.getElementById('reply-message-' + id).value;
                if (!text.trim()) return alert('Please enter a message to reply.');

                fetch(`/notifications/${id}/reply`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.ok) {
                            document.getElementById('reply-message-' + id).value = '';
                        } else {
                            alert('Error: ' + (data.error || 'Failed to send reply'));
                        }
                    });
            }

            function openComposeModal() {
                document.getElementById('compose-modal').style.display = 'flex';
                document.getElementById('compose-message-input').value = '';
            }

            function closeComposeModal() {
                document.getElementById('compose-modal').style.display = 'none';
            }

            function sendCompose() {
                const text = document.getElementById('compose-message-input').value;
                if (!text.trim()) return alert('Please enter a message.');

                fetch('{{ route('notifications.compose') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.ok) {
                            closeComposeModal();
                            window.location.reload();
                        } else {
                            alert('Error: ' + (data.error || 'Failed to send message'));
                        }
                    });
            }
        </script>
@endsection