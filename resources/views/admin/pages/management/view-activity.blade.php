@extends('admin.base-admin')

@section('admin-section', 'view-activity')

@section('content')
<div class="content-section active" id="view-activity">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-weight: 700; color: var(--text-main); font-size: 1.6rem; margin: 0; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-eye text-primary" style="opacity: 0.9;"></i>
            Viewed messages &amp; IP addresses
        </h2>
        <span class="live-badge" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; font-size: 0.95rem; color: var(--text-main);">
            <i class="fas fa-circle" id="system-status-dot" style="color: #10b981; transition: color 0.3s ease; font-size: 0.85rem;"></i> 
            <span id="system-status-text">View activity</span>
        </span>
    </div>

    <div class="db-card overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50">
            <p class="text-sm font-medium text-slate-500 m-0">When a wish message is viewed, the viewer's IP and message details are listed here. Use the link to open or copy the message URL.</p>
        </div>
        <div class="table-responsive">
                <table class="data-table" id="viewedMessagesTable">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User (owner)</th>
                            <th>Message title</th>
                            <th>Recipient</th>
                            <th>IP address</th>
                            <th>Date viewed</th>
                            <th>Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messageViews ?? [] as $v)
                            @php /** @var \App\Models\MessageViews $v */ @endphp
                            @php
                                $msg = $v->wishMessage;
                                $messageUrl = $msg ? (url('/') . '/' . $msg->message_type . '/' . $msg->slug) : '#';
                            @endphp
                            <tr>
                                <td>{{ $v->viewed_at ? $v->viewed_at->format('Y-m-d H:i:s') : '-' }}</td>
                                <td>{{ $v->user ? $v->user->username : '-' }}</td>
                                <td>{{ $msg ? ($msg->title ?? '-') : '-' }}</td>
                                <td class="blur-sensitive">{{ $msg ? ($msg->recipient_name ?? $msg->recipient_special_name ?? '-') : '-' }}</td>
                                <td class="blur-sensitive"><code style="background: var(--bg-body); padding: 4px 8px; border-radius: 4px;">{{ $v->ip_address ?? '-' }}</code></td>
                                <td>{{ $v->viewed_at ? $v->viewed_at->format('Y-m-d H:i') : '-' }}</td>
                                <td class="link-cell">
                                    <div class="link-actions" style="display: flex; gap: 8px;">
                                        <button type="button" class="action-btn view btn-copy-link" data-copy="{{ $messageUrl }}"><i class="fas fa-copy"></i> Copy</button>
                                        <a href="{{ $messageUrl }}" target="_blank" rel="noopener" class="action-btn edit btn-open-link" style="text-decoration: none;"><i class="fas fa-external-link-alt"></i> Open</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <h4 class="text-base font-bold text-slate-700 m-0 mb-1">No Activity Found</h4>
                                    <p class="text-sm font-medium text-slate-500 m-0">No view activity has been recorded yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($messageViews) && method_exists($messageViews, 'currentPage') && $messageViews->lastPage() > 0)
            <div class="custom-pagination">
                <div class="pagination-info">
                    Showing page {{ $messageViews->currentPage() }} of {{ $messageViews->lastPage() }}
                </div>
                <div class="pagination-btns">
                    <button type="button" class="pagination-btn" aria-label="Previous" @if($messageViews->onFirstPage()) disabled @endif onclick="@if(!$messageViews->onFirstPage()) location.href='{{ $messageViews->previousPageUrl() }}'; @endif"><i class="fas fa-chevron-left"></i> Previous</button>
                    <button type="button" class="pagination-btn" aria-label="Next" @if($messageViews->hasMorePages()) disabled @endif onclick="@if($messageViews->hasMorePages()) location.href='{{ $messageViews->nextPageUrl() }}'; @endif">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dot = document.getElementById('system-status-dot');
    const text = document.getElementById('system-status-text');

    if (!dot || !text) return;

    function updateStatus(isOnline) {
        if (isOnline) {
            dot.style.color = '#10b981'; // green
            text.textContent = 'View activity';
        } else {
            dot.style.color = '#ef4444'; // red
            text.textContent = 'System offline';
        }
    }

    // Check navigator.onLine
    updateStatus(navigator.onLine);

    window.addEventListener('online', () => checkRealStatus());
    window.addEventListener('offline', () => updateStatus(false));

    function checkRealStatus() {
        if (!navigator.onLine) {
            updateStatus(false);
            return;
        }
        fetch(window.location.href, { method: 'HEAD', cache: 'no-store' })
            .then(response => {
                updateStatus(response.ok);
            })
            .catch(() => {
                updateStatus(false);
            });
    }

    // Ping every 10 seconds for real-time responsiveness
    setInterval(checkRealStatus, 10000);
    // Initial check
    checkRealStatus();
});
</script>
@endsection
