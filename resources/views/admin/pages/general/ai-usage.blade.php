@extends('admin.base-admin')

@section('admin-section', 'ai-usage')

@section('content')
<div class="content-section active" id="ai-usage">
    {{-- Page Header --}}
    <div class="admin-page-header">
        <h2 class="ai-page-title">
            <span class="ai-title-icon-box">
                <i class="fas fa-robot"></i>
            </span>
            AI Usage Logs
        </h2>
    </div>

    {{-- Card --}}
    <div class="db-card">
        <div class="table-responsive">
            <table class="data-table" id="aiUsageTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Context / Prompt</th>
                        <th>Response</th>
                        <th>Tokens</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aiUsage as $log)
                        <tr>
                            <td>
                                <div class="ai-user-wrap">
                                    <div class="ai-user-avatar">
                                        @if($log->user)
                                            {{ strtoupper(substr($log->user->username ?? $log->user->name, 0, 1)) }}
                                        @else
                                            <i class="fas fa-user-secret"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="ai-user-name">
                                            {{ $log->user->username ?? $log->user->name ?? 'Unauthenticated Guest' }}
                                        </div>
                                        @if($log->user)
                                            <div class="ai-user-email">{{ $log->user->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="ai-cell-prompt">
                                <div class="ai-prompt-box">
                                    <span class="admin-badge ai-badge-prompt">
                                        {{ $log->message_type ?? 'conversational' }}
                                    </span>
                                    <div class="ai-prompt-text">
                                        {{ Str::limit(is_array(json_decode($log->prompt, true)) ? 'JSON History' : $log->prompt, 100) }}
                                    </div>
                                </div>
                            </td>
                            <td class="ai-cell-response">
                                <div class="ai-response-text">
                                    {{ Str::limit($log->response, 150) }}
                                </div>
                            </td>
                            <td>
                                @if($log->tokens_used)
                                    <span class="ai-model-tag">
                                        {{ number_format($log->tokens_used) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="ai-timestamp-box">
                                    <div class="ai-user-name">{{ $log->created_at ? $log->created_at->format('Y-m-d') : '-' }}</div>
                                    <div class="ai-user-email">{{ $log->created_at ? $log->created_at->format('H:i:s') : '' }}</div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="ai-empty-cell">
                                <i class="fas fa-robot ai-empty-icon"></i>
                                <div class="ai-user-name">No AI usage logs found</div>
                                <div class="ai-user-email">No AI requests have been recorded yet.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($aiUsage, 'currentPage') && $aiUsage->lastPage() > 0)
            <div class="custom-pagination">
                <div class="pagination-info">
                    Showing page {{ $aiUsage->currentPage() }} of {{ $aiUsage->lastPage() }}
                </div>
                <div class="pagination-btns">
                    <button type="button" class="pagination-btn" aria-label="Previous" @if($aiUsage->onFirstPage()) disabled @endif onclick="@if(!$aiUsage->onFirstPage()) location.href='{!! $aiUsage->appends(request()->except('ai_page'))->previousPageUrl() !!}'; @endif"><i class="fas fa-chevron-left"></i> Previous</button>
                    <button type="button" class="pagination-btn" aria-label="Next" @if(!$aiUsage->hasMorePages()) disabled @endif onclick="@if($aiUsage->hasMorePages()) location.href='{!! $aiUsage->appends(request()->except('ai_page'))->nextPageUrl() !!}'; @endif">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
