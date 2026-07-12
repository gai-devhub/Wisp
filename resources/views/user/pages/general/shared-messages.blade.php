@extends('user.base-user')

@section('user-section', 'share-messages')

@section('content')
<div class="content-section active" id="share-messages">
    <!-- Header Section -->
    <div class="messages-header-toolbar">
        <h2 class="messages-header-title">
            <i class="fas fa-comment text-primary messages-header-icon"></i> 
            Shared Messages
        </h2>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h3>Shared Messages History</h3>
            </div>
            <div class="card-body">
                @forelse(($allShareSends ?? []) as $share)
                    @php $msg = $share->wishMessage; @endphp
                    <div class="generated-link-item links-history-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #f1f5f9;">
                        <div>
                            <strong style="color: #1e293b;">{{ $msg ? $msg->title : 'Deleted Message' }}</strong>
                            <div style="font-size: 0.85rem; color: #64748b; margin-top: 4px;">
                                To: <strong>{{ $share->recipient_masked ?? ($msg ? $msg->recipient_name : 'Unknown') }}</strong> via <span style="text-transform: capitalize;">{{ $share->channel }}</span>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            @php
                                $statusColor = '#64748b'; // default gray
                                if ($share->status === 'sent' || $share->status === 'success') {
                                    $statusColor = '#22c55e'; // green
                                } elseif ($share->status === 'failed') {
                                    $statusColor = '#ef4444'; // red
                                } elseif ($share->status === 'scheduled') {
                                    $statusColor = '#3b82f6'; // blue
                                }
                            @endphp
                            <span style="color: {{ $statusColor }}; font-weight: 700;">
                                {{ ucfirst($share->status ?? 'unknown') }}
                            </span>
                            @if($share->scheduled_at)
                                <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 4px;">
                                    {{ \Carbon\Carbon::parse($share->scheduled_at)->format('M j, g:i A') }}
                                </div>
                            @else
                                <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 4px;">
                                    {{ $share->created_at->format('M j, g:i A') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-table-cell" style="padding: 60px 20px;">
                        <div class="empty-table-icon-wrap">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <h4 class="empty-table-title" style="font-size: 0.95rem;">No Shared Messages</h4>
                        <p class="empty-table-desc" style="font-size: 0.85rem;">No messages have been shared or scheduled yet.</p>
                    </div>
                @endforelse
            </div>
            @if(isset($allShareSends) && $allShareSends->hasPages())
                <div class="custom-pagination">
                    <div class="pagination-btns">
                        <button type="button" class="pagination-btn" aria-label="Previous" @if($allShareSends->onFirstPage()) disabled @endif onclick="@if(!$allShareSends->onFirstPage()) location.href='{{ route('user.share-messages.page', ['shares_page' => $allShareSends->currentPage() - 1]) }}'; @endif"><i class="fas fa-chevron-left"></i> Previous</button>
                        <button type="button" class="pagination-btn" aria-label="Next" @if(!$allShareSends->hasMorePages()) disabled @endif onclick="@if($allShareSends->hasMorePages()) location.href='{{ route('user.share-messages.page', ['shares_page' => $allShareSends->currentPage() + 1]) }}'; @endif">Next <i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
