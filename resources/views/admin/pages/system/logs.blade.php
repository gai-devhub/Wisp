@extends('admin.base-admin')

@section('admin-section', 'logs')

@section('content')
<div class="content-section active" id="logs">
    {{-- Page Header --}}
    <div class="admin-page-header">
        <h2 class="admin-page-title admin-page-title-box">
            <span class="admin-page-title-icon">
                <i class="fas fa-clipboard-list"></i>
            </span>
            Activity Logs
        </h2>
    </div>

    {{-- Main Card --}}
    <div class="db-card">
        
        {{-- Card Header & Filter Bar --}}
        <div class="admin-card-header-bar">
            <p class="admin-card-desc">
                Complete audit trail of all administrative actions. Use the search and filters to find specific events.
            </p>
            <div class="admin-filter-group">
                <div class="filter-col-sm">
                    <select id="logFilterType" class="admin-filter-select">
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
        
        {{-- Data Table --}}
        <div class="table-responsive">
            <table class="data-table" id="logsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Time</th>
                        <th>User</th>
                        <th>Activity</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activityLogs ?? [] as $log)
                        @php
                            $act = strtolower($log->activity ?? '');
                            $badgeClass = 'admin-badge-log admin-badge-log-default';
                            if (str_contains($act, 'deleted') || str_contains($act, 'locked')) {
                                $badgeClass = 'admin-badge-log admin-badge-log-danger';
                            } elseif (str_contains($act, 'blocked')) {
                                $badgeClass = 'admin-badge-log admin-badge-log-warning';
                            } elseif (str_contains($act, 'created') || str_contains($act, 'unblocked') || str_contains($act, 'backup') || str_contains($act, 'updated') || str_contains($act, 'optimized') || str_contains($act, 'notification')) {
                                $badgeClass = 'admin-badge-log admin-badge-log-primary';
                            }
                        @endphp
                        <tr class="log-data-row"
                            data-activity="{{ $act }}"
                            data-date="{{ $log->created_at ? $log->created_at->format('Y-m-d') : '' }}"
                            data-user="{{ strtolower(e($log->user->username ?? 'system')) }}"
                            data-details="{{ strtolower(e($log->details ?? '')) }}">
                            <td>{{ ($activityLogs->currentPage() - 1) * $activityLogs->perPage() + $loop->iteration }}</td>
                            <td>{{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                            <td>{{ $log->user ? $log->user->username : 'system' }}</td>
                            <td>
                                <span class="{{ $badgeClass }}">{{ e($log->activity) }}</span>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit(e($log->details ?? '-'), 100) }}</td>
                        </tr>
                    @empty
                        <tr class="log-row-empty"><td colspan="5" class="text-center">No activity logs yet.</td></tr>
                    @endforelse
                    <tr class="log-row-no-results hidden"><td colspan="5" class="text-center">No logs match your search or filters.</td></tr>
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(isset($activityLogs) && method_exists($activityLogs, 'currentPage') && $activityLogs->lastPage() > 0)
            <div class="custom-pagination">
                <div class="pagination-info">
                    Showing page {{ $activityLogs->currentPage() }} of {{ $activityLogs->lastPage() }}
                </div>
                <div class="pagination-btns">
                    <button type="button" class="pagination-btn" aria-label="Previous" @if($activityLogs->onFirstPage()) disabled @endif onclick="@if(!$activityLogs->onFirstPage()) location.href='{{ $activityLogs->previousPageUrl() }}'; @endif"><i class="fas fa-chevron-left"></i> Previous</button>
                    <button type="button" class="pagination-btn" aria-label="Next" @if(!$activityLogs->hasMorePages()) disabled @endif onclick="@if($activityLogs->hasMorePages()) location.href='{{ $activityLogs->nextPageUrl() }}'; @endif">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
