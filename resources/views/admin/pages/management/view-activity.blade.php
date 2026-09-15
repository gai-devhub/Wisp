@extends('admin.base-admin')

@section('admin-section', 'view-activity')

@section('content')
<div class="content-section active" id="view-activity">
    <div class="admin-card-header-bar">
        <h2 class="admin-page-title">
            <i class="fas fa-eye text-primary"></i>
            Viewed messages &amp; IP addresses
        </h2>
    </div>

    <div class="db-card overflow-hidden">
        <div class="table-responsive">
                <table class="data-table" id="viewedMessagesTable">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User (owner)</th>
                            <th>Message title</th>
                            <th>Recipient</th>
                            <th>IP address</th>
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
                                <td class="blur-sensitive">
                                    <code class="storage-code-badge">{{ $v->ip_address ?? '-' }}</code>
                                    @if(isset($v->views_count) && $v->views_count > 1)
                                        <span class="admin-badge-count bg-primary text-white" title="{{ $v->views_count }} views from this IP">{{ $v->views_count }}</span>
                                    @endif
                                </td>
                                <td class="link-cell">
                                    <div class="action-buttons">
                                        <button type="button" class="action-btn view btn-copy-link" data-copy="{{ $messageUrl }}"><i class="fas fa-copy"></i> Copy</button>
                                        <a href="{{ $messageUrl }}" target="_blank" rel="noopener" class="action-btn edit btn-open-link"><i class="fas fa-external-link-alt"></i> Open</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="admin-table-empty">
                                    <div class="ai-icon-avatar margin-0-auto mb-4 text-2xl">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <h4 class="admin-table-empty-title">No Activity Found</h4>
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
@endsection
