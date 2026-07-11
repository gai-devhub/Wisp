@extends('admin.base-admin')

@section('admin-section', 'ai-usage')

@section('content')
<div class="content-section active" id="ai-usage">
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <h2 class="flex items-center gap-3 text-2xl font-bold text-slate-800 m-0">
            <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-robot text-primary"></i>
            </span>
            AI Usage Logs
        </h2>
    </div>

    <div class="db-card overflow-hidden">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Context/Prompt</th>
                        <th>Response</th>
                        <th>Tokens</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aiUsage as $log)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; flex-shrink: 0;">
                                        @if($log->user)
                                            {{ strtoupper(substr($log->user->username ?? $log->user->name, 0, 1)) }}
                                        @else
                                            <i class="fas fa-user-secret"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <span style="font-weight: 600;">{{ $log->user->username ?? $log->user->name ?? 'Unauthenticated Guest' }}</span>
                                        @if($log->user)
                                            <div style="font-size: 0.8rem; color: #64748b;">{{ $log->user->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="max-width: 250px;">
                                <div style="font-size: 0.9rem; color: var(--text); overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-indigo-100 text-indigo-700 mb-1">{{ $log->message_type ?? 'conversational' }}</span><br>
                                    {{ Str::limit(is_array(json_decode($log->prompt, true)) ? 'JSON History' : $log->prompt, 100) }}
                                </div>
                            </td>
                            <td style="max-width: 300px;">
                                <div style="font-size: 0.9rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                    {{ Str::limit($log->response, 150) }}
                                </div>
                            </td>
                            <td>
                                @if($log->tokens_used)
                                    <span style="font-family: monospace; background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem;">{{ $log->tokens_used }}</span>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 0.8rem; color: #64748b;">
                                    <div style="font-weight: 500; color: #334155;">{{ $log->created_at->format('Y-m-d') }}</div>
                                    <div>{{ $log->created_at->format('H:i:s') }}</div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                <i class="fas fa-robot text-4xl mb-3 block opacity-30"></i>
                                No AI usage logs found.
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
