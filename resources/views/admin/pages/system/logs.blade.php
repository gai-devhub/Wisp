@extends('admin.base-admin')

@section('admin-section', 'logs')

@section('content')
<div class="content-section active" id="logs">
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <h2 class="flex items-center gap-3 text-2xl font-bold text-slate-800 m-0">
            <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-clipboard-list opacity-90"></i>
            </span>
            Activity Logs
        </h2>
    </div>

    <div class="db-card overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <p class="text-sm font-medium text-slate-500 mb-4">Complete audit trail of all administrative actions. Use the search and filters to find specific events.</p>
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px] max-w-sm">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Activity Type</label>
                    <select id="logFilterType" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all font-medium text-slate-700 bg-white cursor-pointer">
                        <option value="">All activities</option>
                        <option value="user created">User created</option>
                        <option value="user updated">User updated</option>
                        <option value="user blocked">User blocked</option>
                        <option value="user unblocked">User unblocked</option>
                        <option value="user deleted">User deleted</option>
                        <option value="message updated">Message updated</option>
                        <option value="message deleted">Message deleted</option>
                        <option value="notification sent">Notification sent</option>
                        <option value="backup">Backup</option>
                        <option value="system locked">System locked</option>
                        <option value="system unlocked">System unlocked</option>
                        <option value="database optimized">Database optimized</option>
                        <option value="admin account updated">Admin account updated</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse" id="logsTable">
                <thead class="bg-slate-50/50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Activity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/3">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activityLogs ?? [] as $log)
                        @php
                            $act = strtolower($log->activity ?? '');
                            $badgeClass = 'bg-slate-100 text-slate-600';
                            if (str_contains($act, 'deleted') || str_contains($act, 'locked')) $badgeClass = 'bg-rose-100 text-rose-700';
                            elseif (str_contains($act, 'blocked')) $badgeClass = 'bg-amber-100 text-amber-700';
                            elseif (str_contains($act, 'created') || str_contains($act, 'unblocked') || str_contains($act, 'backup')) $badgeClass = 'bg-emerald-100 text-emerald-700';
                            elseif (str_contains($act, 'updated') || str_contains($act, 'optimized')) $badgeClass = 'bg-sky-100 text-sky-700';
                            elseif (str_contains($act, 'notification')) $badgeClass = 'bg-indigo-100 text-indigo-700';
                        @endphp
                        <tr class="log-data-row hover:bg-slate-50 transition-colors"
                            data-activity="{{ $act }}"
                            data-date="{{ $log->created_at ? $log->created_at->format('Y-m-d') : '' }}"
                            data-user="{{ strtolower(e($log->user->username ?? 'system')) }}"
                            data-details="{{ strtolower(e($log->details ?? '')) }}">
                            <td class="px-6 py-4 text-sm text-slate-400 font-medium">{{ ($activityLogs->currentPage() - 1) * $activityLogs->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500 font-mono">{{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ $log->user ? $log->user->username : 'system' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide border border-white/50 {{ $badgeClass }}">{{ e($log->activity) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 leading-relaxed">{{ \Illuminate\Support\Str::limit(e($log->details ?? '-'), 100) }}</td>
                        </tr>
                    @empty
                        <tr class="log-row-empty"><td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium">No activity logs yet.</td></tr>
                    @endforelse
                    <tr class="log-row-no-results" style="display:none;"><td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium">No logs match your search or filters.</td></tr>
                </tbody>
            </table>
        </div>
        
        @if(isset($activityLogs) && method_exists($activityLogs, 'currentPage') && $activityLogs->lastPage() > 1)
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="text-sm font-medium text-slate-500">
                Showing page {{ $activityLogs->currentPage() }} of {{ $activityLogs->lastPage() }}
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" aria-label="Previous" @if($activityLogs->onFirstPage()) disabled @endif onclick="@if(!$activityLogs->onFirstPage()) location.href='{{ $activityLogs->previousPageUrl() }}'; @endif"><i class="fas fa-chevron-left mr-1"></i> Previous</button>
                <button type="button" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" aria-label="Next" @if(!$activityLogs->hasMorePages()) disabled @endif onclick="@if($activityLogs->hasMorePages()) location.href='{{ $activityLogs->nextPageUrl() }}'; @endif">Next <i class="fas fa-chevron-right ml-1"></i></button>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
