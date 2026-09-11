@extends('admin.base-admin')

@section('admin-section', 'maintenance')

@section('content')
<div class="content-section active" id="maintenance">
    <div class="flex items-center gap-4 mb-6">
        <h2 class="flex items-center gap-3 text-2xl font-bold text-slate-800 m-0">
            <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center shadow-sm">
                <i class="fas fa-tools opacity-90"></i>
            </span>
            Maintenance & Cleanup
        </h2>
    </div>

    <p class="text-sm font-medium text-slate-500 mb-8">Manage system storage, clear caches, and clean up expired data.</p>

    {{-- ROW 1: 2 CARDS (Database Stats & Storage Usage) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        {{-- Database Stats --}}
        <div class="db-card overflow-hidden flex flex-col shadow-sm border border-slate-200 bg-white" style="border-radius: 16px;">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-database text-sm"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 m-0">Database Stats</h3>
            </div>
            <div class="p-0 flex-1 bg-white">
                <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                    <span class="font-medium text-slate-500">Users</span>
                    <strong class="font-bold text-slate-800">{{ number_format($stats['total_users'] ?? 0) }}</strong>
                </div>
                <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                    <span class="font-medium text-slate-500">Messages</span>
                    <strong class="font-bold text-slate-800">{{ number_format($stats['total_messages'] ?? 0) }}</strong>
                </div>
                <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                    <span class="font-medium text-slate-500">Message Views</span>
                    <strong class="font-bold text-slate-800">{{ number_format($stats['total_views'] ?? 0) }}</strong>
                </div>
                <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                    <span class="font-medium text-slate-500">Published</span>
                    <strong class="font-bold text-slate-800">{{ number_format($stats['published_messages'] ?? 0) }}</strong>
                </div>
                <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                    <span class="font-medium text-slate-500">Expired Messages</span>
                    <strong class="font-bold text-slate-800">{{ number_format($stats['expired_messages'] ?? 0) }}</strong>
                </div>
                <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                    <span class="font-medium text-slate-500">Draft Messages</span>
                    <strong class="font-bold text-slate-800">{{ number_format($stats['draft_messages'] ?? 0) }}</strong>
                </div>
            </div>
        </div>

        {{-- Storage Usage --}}
        <div class="db-card overflow-hidden flex flex-col shadow-sm border border-slate-200 bg-white" style="border-radius: 16px;">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-hdd text-sm"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 m-0">Storage Usage</h3>
            </div>
            <div class="p-0 flex-1 bg-white flex flex-col">
                <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 hover:bg-slate-50 transition-colors">
                    <span class="font-medium text-slate-500">Uploads</span>
                    <strong class="font-semibold text-slate-700 font-mono text-[13px] bg-slate-100 border border-slate-200/60 px-2.5 py-1 rounded-md">{{ $storageStats['uploads_size'] ?? 'N/A' }}</strong>
                </div>
                <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 hover:bg-slate-50 transition-colors">
                    <span class="font-medium text-slate-500">Backups</span>
                    <strong class="font-semibold text-slate-700 font-mono text-[13px] bg-slate-100 border border-slate-200/60 px-2.5 py-1 rounded-md">{{ $storageStats['backups_size'] ?? 'N/A' }}</strong>
                </div>
                <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 hover:bg-slate-50 transition-colors">
                    <span class="font-medium text-slate-500">Logs</span>
                    <strong class="font-semibold text-slate-700 font-mono text-[13px] bg-slate-100 border border-slate-200/60 px-2.5 py-1 rounded-md">{{ $storageStats['logs_size'] ?? 'N/A' }}</strong>
                </div>
                <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 hover:bg-slate-50 transition-colors">
                    <span class="font-medium text-slate-500">System Files</span>
                    <strong class="font-semibold text-slate-700 font-mono text-[13px] bg-slate-100 border border-slate-200/60 px-2.5 py-1 rounded-md">{{ $storageStats['system_size'] ?? 'N/A' }}</strong>
                </div>
                <div class="px-6 py-4 flex justify-between items-center text-sm bg-indigo-50/40 mt-auto border-t-2 border-indigo-100">
                    <span class="font-bold text-indigo-900">Total Storage</span>
                    <strong class="font-bold text-indigo-700 font-mono text-[14px] bg-indigo-100/60 px-2.5 py-1 rounded-md">{{ $storageStats['total_size'] ?? 'N/A' }}</strong>
                </div>
                <div class="px-6 pb-5 pt-2 bg-indigo-50/40 flex flex-col gap-2 border-t border-indigo-50">
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
                    <div class="w-full bg-slate-200 rounded-full overflow-hidden flex" style="height: 14px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);">
                        @if($uploadsBytes > 0)
                            <div class="h-full transition-all duration-500 hover:opacity-80 cursor-help" 
                                 style="width: {{ max(1, $uploadsPct) }}%; background-color: #3b82f6;"
                                 title="Uploads: {{ $storageStats['uploads_size'] }} ({{ round($uploadsPct, 1) }}% of used)">
                            </div>
                        @endif
                        @if($backupsBytes > 0)
                            <div class="h-full transition-all duration-500 hover:opacity-80 cursor-help" 
                                 style="width: {{ max(1, $backupsPct) }}%; background-color: #10b981;"
                                 title="Backups: {{ $storageStats['backups_size'] }} ({{ round($backupsPct, 1) }}% of used)">
                            </div>
                        @endif
                        @if($logsBytes > 0)
                            <div class="h-full transition-all duration-500 hover:opacity-80 cursor-help" 
                                 style="width: {{ max(1, $logsPct) }}%; background-color: #f97316;"
                                 title="Logs: {{ $storageStats['logs_size'] }} ({{ round($logsPct, 1) }}% of used)">
                            </div>
                        @endif
                        @if($systemBytes > 0)
                            <div class="h-full transition-all duration-500 hover:opacity-80 cursor-help" 
                                 style="width: {{ max(1, $systemPct) }}%; background-color: #8b5cf6;"
                                 title="System Files: {{ $storageStats['system_size'] }} ({{ round($systemPct, 1) }}% of used)">
                            </div>
                        @endif
                    </div>
                    
                    <!-- Segmented Legend -->
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-[11px] font-semibold text-slate-500 justify-center">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #3b82f6;"></span>
                            <span>Uploads</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #10b981;"></span>
                            <span>Backups</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #f97316;"></span>
                            <span>Logs</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #8b5cf6;"></span>
                            <span>System</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-xs font-semibold mt-2" style="color: {{ $isAlmostFull ? '#ef4444' : 'var(--text-muted, #64748b)' }};">
                        <span>{{ number_format($overallPct, 4) }}% Used</span>
                        <span>2.0 TB Limit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: 4 CARDS (Cleanup Tools Grid: 4 cards on 1 row) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        
        {{-- Tool 1: Clean Expired Messages --}}
        <div class="db-card overflow-hidden flex flex-col p-6 bg-white shadow-sm border border-slate-200 justify-between" style="border-radius: 16px;">
            <div>
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mb-4">
                    <i class="fas fa-broom text-sm"></i>
                </div>
                <strong class="block text-base font-bold text-slate-800 mb-1.5">Clean Expired Messages</strong>
                <p class="text-xs font-medium text-slate-500 m-0 leading-relaxed mb-6">Mark expired messages and clean up their associated data.</p>
            </div>
            <form method="POST" action="{{ route('admin.maintenance.cleanExpired') }}" onsubmit="event.preventDefault(); showAdminConfirm('Clean all expired messages? This cannot be undone.', () => this.submit());" class="m-0">
                @csrf
                <button type="submit" class="w-full py-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 hover:bg-amber-100 font-bold text-xs transition-all shadow-sm cursor-pointer">Clean Now</button>
            </form>
        </div>

        {{-- Tool 2: Clear Application Cache --}}
        <div class="db-card overflow-hidden flex flex-col p-6 bg-white shadow-sm border border-slate-200 justify-between" style="border-radius: 16px;">
            <div>
                <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-4">
                    <i class="fas fa-bolt text-sm"></i>
                </div>
                <strong class="block text-base font-bold text-slate-800 mb-1.5">Clear Application Cache</strong>
                <p class="text-xs font-medium text-slate-500 m-0 leading-relaxed mb-6">Clear config, route, view, and application cache.</p>
            </div>
            <form method="POST" action="{{ route('admin.maintenance.clearCache') }}" class="m-0">
                @csrf
                <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 font-bold text-xs transition-all shadow-sm cursor-pointer">Clear Cache</button>
            </form>
        </div>

        {{-- Tool 3: Clear Old Activity Logs --}}
        <div class="db-card overflow-hidden flex flex-col p-6 bg-white shadow-sm border border-slate-200 justify-between" style="border-radius: 16px;">
            <div>
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                    <i class="fas fa-trash-alt text-sm"></i>
                </div>
                <strong class="block text-base font-bold text-slate-800 mb-1.5">Clear Old Activity Logs</strong>
                <p class="text-xs font-medium text-slate-500 m-0 leading-relaxed mb-6">Remove activity logs older than 90 days.</p>
            </div>
            <form method="POST" action="{{ route('admin.maintenance.cleanLogs') }}" onsubmit="event.preventDefault(); showAdminConfirm('Delete activity logs older than 90 days?', () => this.submit());" class="m-0">
                @csrf
                <button type="submit" class="w-full py-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 font-bold text-xs transition-all shadow-sm cursor-pointer">Clean Logs</button>
            </form>
        </div>

        {{-- Tool 4: Optimize Application --}}
        <div class="db-card overflow-hidden flex flex-col p-6 bg-white shadow-sm border border-slate-200 justify-between" style="border-radius: 16px;">
            <div>
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                    <i class="fas fa-rocket text-sm"></i>
                </div>
                <strong class="block text-base font-bold text-slate-800 mb-1.5">Optimize Application</strong>
                <p class="text-xs font-medium text-slate-500 m-0 leading-relaxed mb-6">Cache application bootstrap configuration, routes, and views.</p>
            </div>
            <form method="POST" action="{{ route('admin.maintenance.optimize') }}" class="m-0">
                @csrf
                <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-100 font-bold text-xs transition-all shadow-sm cursor-pointer">Optimize</button>
            </form>
        </div>
    </div>

    {{-- ROW 3: System Info --}}
    <div class="db-card overflow-hidden flex flex-col shadow-sm border border-slate-200 bg-white mb-6" style="border-radius: 16px;">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                <i class="fas fa-info-circle text-sm"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 m-0">System Info</h3>
        </div>
        <div class="p-0 flex-1 bg-white grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-0 gap-x-6">
            <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 transition-colors">
                <span class="font-medium text-slate-500">PHP Version</span>
                <strong class="font-semibold text-slate-700 font-mono bg-slate-100 border border-slate-200/60 px-2.5 py-1 rounded-md text-[12px]">{{ $systemInfo['php_version'] ?? phpversion() }}</strong>
            </div>
            <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 transition-colors">
                <span class="font-medium text-slate-500">Laravel Version</span>
                <strong class="font-semibold text-slate-700 font-mono bg-slate-100 border border-slate-200/60 px-2.5 py-1 rounded-md text-[12px]">{{ $systemInfo['laravel_version'] ?? app()->version() }}</strong>
            </div>
            <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 transition-colors">
                <span class="font-medium text-slate-500">Database</span>
                <strong class="font-semibold text-slate-700 bg-slate-100 border border-slate-200/60 px-2.5 py-1 rounded-md text-[12px] uppercase tracking-wider">{{ $systemInfo['db_driver'] ?? 'N/A' }}</strong>
            </div>
            <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 transition-colors">
                <span class="font-medium text-slate-500">Server Software</span>
                <strong class="font-semibold text-slate-700 text-[12px] font-mono bg-slate-100 border border-slate-200/60 px-2.5 py-1 rounded-md truncate max-w-[180px]" title="{{ $systemInfo['server_software'] ?? 'N/A' }}">{{ $systemInfo['server_software'] ?? 'N/A' }}</strong>
            </div>
            <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 transition-colors">
                <span class="font-medium text-slate-500">Timezone</span>
                <strong class="font-bold text-slate-800">{{ $systemSettings['timezone'] ?? 'UTC' }}</strong>
            </div>
            <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-100 transition-colors">
                <span class="font-medium text-slate-500">Last Backup</span>
                <strong class="font-bold text-slate-800">{{ $systemSettings['last_backup_at'] ? \Carbon\Carbon::parse($systemSettings['last_backup_at'])->diffForHumans() : 'Never' }}</strong>
            </div>
        </div>
    </div>

</div>
@endsection
