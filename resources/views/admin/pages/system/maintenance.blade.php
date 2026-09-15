@extends('admin.base-admin')

@section('admin-section', 'maintenance')

@section('content')
<div class="content-section active" id="maintenance">
    {{-- Page Header --}}
    <div class="admin-card-header-bar margin-bottom-8">
        <h2 class="admin-page-title">
            <span class="admin-title-icon-box">
                <i class="fas fa-tools"></i>
            </span>
            Maintenance &amp; Cleanup
        </h2>
    </div>
    <p class="admin-card-text margin-bottom-24">
        Manage system storage, clear caches, and clean up expired data.
    </p>

    {{-- ROW 1: 2 CARDS (Database Stats & Storage Usage) --}}
    <div class="admin-system-grid-2">
        
        {{-- Database Stats --}}
        <div class="db-card admin-system-card">
            <div class="admin-card-header">
                <div class="admin-card-header-left">
                    <i class="fas fa-database admin-card-header-icon"></i>
                    <h3 class="admin-card-header-title">Database Stats</h3>
                </div>
            </div>
            <div class="p-0 flex-1 flex-col">
                <div class="storage-stat-row">
                    <span class="text-muted font-500">Users</span>
                    <strong class="text-dark font-700">{{ number_format($stats['total_users'] ?? 0) }}</strong>
                </div>
                <div class="storage-stat-row">
                    <span class="text-muted font-500">Messages</span>
                    <strong class="text-dark font-700">{{ number_format($stats['total_messages'] ?? 0) }}</strong>
                </div>
                <div class="storage-stat-row">
                    <span class="text-muted font-500">Message Views</span>
                    <strong class="text-dark font-700">{{ number_format($stats['total_views'] ?? 0) }}</strong>
                </div>
                <div class="storage-stat-row">
                    <span class="text-muted font-500">Published</span>
                    <strong class="text-dark font-700">{{ number_format($stats['published_messages'] ?? 0) }}</strong>
                </div>
                <div class="storage-stat-row">
                    <span class="text-muted font-500">Expired Messages</span>
                    <strong class="text-dark font-700">{{ number_format($stats['expired_messages'] ?? 0) }}</strong>
                </div>
                <div class="storage-stat-row border-none">
                    <span class="text-muted font-500">Draft Messages</span>
                    <strong class="text-dark font-700">{{ number_format($stats['draft_messages'] ?? 0) }}</strong>
                </div>
            </div>
        </div>

        {{-- Storage Usage --}}
        <div class="db-card admin-system-card">
            <div class="admin-card-header">
                <div class="admin-card-header-left">
                    <i class="fas fa-hdd admin-card-header-icon"></i>
                    <h3 class="admin-card-header-title">Storage Usage</h3>
                </div>
            </div>
            <div class="p-0 flex-1 flex-col">
                <div class="storage-stat-row">
                    <span class="text-muted font-500">Uploads</span>
                    <strong class="storage-code-badge">{{ $storageStats['uploads_size'] ?? 'N/A' }}</strong>
                </div>
                <div class="storage-stat-row">
                    <span class="text-muted font-500">Backups</span>
                    <strong class="storage-code-badge">{{ $storageStats['backups_size'] ?? 'N/A' }}</strong>
                </div>
                <div class="storage-stat-row">
                    <span class="text-muted font-500">Logs</span>
                    <strong class="storage-code-badge">{{ $storageStats['logs_size'] ?? 'N/A' }}</strong>
                </div>
                <div class="storage-stat-row">
                    <span class="text-muted font-500">System Files</span>
                    <strong class="storage-code-badge">{{ $storageStats['system_size'] ?? 'N/A' }}</strong>
                </div>
                <div class="storage-total-row">
                    <span class="font-700 text-dark">Total Storage</span>
                    <strong class="admin-github-badge">{{ $storageStats['total_size'] ?? 'N/A' }}</strong>
                </div>
                <div class="storage-bar-wrapper">
                    @php
                        $uploadsBytes = $storageStats['uploads_bytes'] ?? 0;
                        $backupsBytes = $storageStats['backups_bytes'] ?? 0;
                        $logsBytes = $storageStats['logs_bytes'] ?? 0;
                        $systemBytes = $storageStats['system_bytes'] ?? 0;
                        $totalBytes = $storageStats['total_bytes'] ?? 1;
                        
                        $uploadsPct = ($uploadsBytes / $totalBytes) * 100;
                        $backupsPct = ($backupsBytes / $totalBytes) * 100;
                        $logsPct = ($logsBytes / $totalBytes) * 100;
                        $systemPct = ($systemBytes / $totalBytes) * 100;
                        
                        $overallPct = $storageStats['storage_percentage'] ?? 0;
                        $isAlmostFull = $overallPct >= 90;
                    @endphp
                    <!-- Segmented Storage Bar -->
                    <div class="storage-segmented-bar">
                        @if($uploadsBytes > 0)
                            <div class="bg-primary h-full" style="width: {{ max(1, $uploadsPct) }}%;" title="Uploads: {{ $storageStats['uploads_size'] }}"></div>
                        @endif
                        @if($backupsBytes > 0)
                            <div class="bg-success h-full" style="width: {{ max(1, $backupsPct) }}%;" title="Backups: {{ $storageStats['backups_size'] }}"></div>
                        @endif
                        @if($logsBytes > 0)
                            <div class="bg-warning h-full" style="width: {{ max(1, $logsPct) }}%;" title="Logs: {{ $storageStats['logs_size'] }}"></div>
                        @endif
                        @if($systemBytes > 0)
                            <div class="bg-purple h-full" style="width: {{ max(1, $systemPct) }}%;" title="System Files: {{ $storageStats['system_size'] }}"></div>
                        @endif
                    </div>
                    
                    <!-- Segmented Legend -->
                    <div class="storage-legend-container">
                        <div class="storage-legend-item">
                            <span class="storage-legend-dot bg-primary"></span>
                            <span>Uploads</span>
                        </div>
                        <div class="storage-legend-item">
                            <span class="storage-legend-dot bg-success"></span>
                            <span>Backups</span>
                        </div>
                        <div class="storage-legend-item">
                            <span class="storage-legend-dot bg-warning"></span>
                            <span>Logs</span>
                        </div>
                        <div class="storage-legend-item">
                            <span class="storage-legend-dot bg-purple"></span>
                            <span>System</span>
                        </div>
                    </div>

                    <div class="storage-usage-footer {{ $isAlmostFull ? 'almost-full' : '' }}">
                        <span>{{ number_format($overallPct, 4) }}% Used</span>
                        <span>2.0 TB Limit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: 4 CARDS (Cleanup Tools Grid: 4 cards on 1 row) --}}
    <div class="cleanup-grid">
        
        {{-- Tool 1: Clean Expired Messages --}}
        <div class="db-card cleanup-card">
            <div>
                <div class="cleanup-icon-box">
                    <i class="fas fa-broom"></i>
                </div>
                <strong class="cleanup-card-title">Clean Expired Messages</strong>
                <p class="cleanup-card-desc">Mark expired messages and clean up their associated data.</p>
            </div>
            <form method="POST" action="{{ route('admin.maintenance.cleanExpired') }}" onsubmit="event.preventDefault(); showAdminConfirm('Clean all expired messages? This cannot be undone.', () => this.submit());">
                @csrf
                <button type="submit" class="admin-btn-action-primary">Clean Now</button>
            </form>
        </div>

        {{-- Tool 2: Clear Application Cache --}}
        <div class="db-card cleanup-card">
            <div>
                <div class="cleanup-icon-box">
                    <i class="fas fa-bolt"></i>
                </div>
                <strong class="cleanup-card-title">Clear Application Cache</strong>
                <p class="cleanup-card-desc">Clear config, route, view, and application cache.</p>
            </div>
            <form method="POST" action="{{ route('admin.maintenance.clearCache') }}">
                @csrf
                <button type="submit" class="admin-btn-action-primary">Clear Cache</button>
            </form>
        </div>

        {{-- Tool 3: Clear Old Activity Logs --}}
        <div class="db-card cleanup-card">
            <div>
                <div class="cleanup-icon-box">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <strong class="cleanup-card-title">Clear Old Activity Logs</strong>
                <p class="cleanup-card-desc">Remove activity logs older than 90 days.</p>
            </div>
            <form method="POST" action="{{ route('admin.maintenance.cleanLogs') }}" onsubmit="event.preventDefault(); showAdminConfirm('Delete activity logs older than 90 days?', () => this.submit());">
                @csrf
                <button type="submit" class="admin-btn-action-primary">Clean Logs</button>
            </form>
        </div>

        {{-- Tool 4: Optimize Application --}}
        <div class="db-card cleanup-card">
            <div>
                <div class="cleanup-icon-box">
                    <i class="fas fa-rocket"></i>
                </div>
                <strong class="cleanup-card-title">Optimize Application</strong>
                <p class="cleanup-card-desc">Cache application bootstrap configuration, routes, and views.</p>
            </div>
            <form method="POST" action="{{ route('admin.maintenance.optimize') }}">
                @csrf
                <button type="submit" class="admin-btn-action-primary">Optimize</button>
            </form>
        </div>
    </div>

    {{-- ROW 3: System Info --}}
    <div class="db-card margin-bottom-24">
        <div class="admin-card-header">
            <div class="admin-card-header-left">
                <i class="fas fa-info-circle admin-card-header-icon"></i>
                <h3 class="admin-card-header-title">System Info</h3>
            </div>
        </div>
        <div class="system-info-grid">
            <div class="system-info-item">
                <span class="text-muted font-500">PHP Version</span>
                <strong class="storage-code-badge">{{ $systemInfo['php_version'] ?? phpversion() }}</strong>
            </div>
            <div class="system-info-item">
                <span class="text-muted font-500">Laravel Version</span>
                <strong class="storage-code-badge">{{ $systemInfo['laravel_version'] ?? app()->version() }}</strong>
            </div>
            <div class="system-info-item">
                <span class="text-muted font-500">Database</span>
                <strong class="storage-code-badge uppercase">{{ $systemInfo['db_driver'] ?? 'N/A' }}</strong>
            </div>
            <div class="system-info-item">
                <span class="text-muted font-500">Server Software</span>
                <strong class="storage-code-badge text-ellipsis" title="{{ $systemInfo['server_software'] ?? 'N/A' }}">{{ $systemInfo['server_software'] ?? 'N/A' }}</strong>
            </div>
            <div class="system-info-item">
                <span class="text-muted font-500">Timezone</span>
                <strong class="text-dark font-700">{{ $systemSettings['timezone'] ?? 'UTC' }}</strong>
            </div>
            <div class="system-info-item border-none">
                <span class="text-muted font-500">Last Backup</span>
                <strong class="text-dark font-700">{{ $systemSettings['last_backup_at'] ? \Carbon\Carbon::parse($systemSettings['last_backup_at'])->diffForHumans() : 'Never' }}</strong>
            </div>
        </div>
    </div>

</div>
@endsection
