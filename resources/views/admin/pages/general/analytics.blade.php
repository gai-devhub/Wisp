@extends('admin.base-admin')

@section('admin-section', 'analytics')

@section('content')
@php
    // Safe fallback data
    $kpis = $analytics['kpis'] ?? [];
    
    // 1. Usage Statistics
    $pv_actual = $kpis['page_views'] ?? 0;
    $uv = $kpis['unique_visitors'] ?? 0;
    $repeat = max($pv_actual - $uv, 0);
    $pv_calc = max($pv_actual, 1);
    $uvPct = round(($uv / $pv_calc) * 100);
    $repeatPct = 100 - $uvPct;

    // 2. User Growth
    $totU_actual = $kpis['total_users'] ?? 0;
    $newU = $kpis['new_users'] ?? 0;
    $actU = $kpis['active_users_7d'] ?? 0;
    $inactU = max($totU_actual - $newU - $actU, 0);
    $totU_calc = max($totU_actual, 1);
    $newUPct = round(($newU / $totU_calc) * 100);
    $actUPct = round(($actU / $totU_calc) * 100);
    $inactUPct = max(100 - $newUPct - $actUPct, 0);

    // 3. Message Statistics
    $pubM = $kpis['messages_published'] ?? 0;
    $expM = $kpis['messages_expired'] ?? 0;
    $draftM = $kpis['messages_draft'] ?? 0;
    $totM_actual = $kpis['total_messages'] ?? ($pubM + $expM + $draftM);
    $totM_calc = max($totM_actual, 1);
    $pubMPct = round(($pubM / $totM_calc) * 100);
    $expMPct = round(($expM / $totM_calc) * 100);
    $draftMPct = max(100 - $pubMPct - $expMPct, 0);

    // 4. Message Views
    $totV_actual = $kpis['total_views'] ?? 0;
    $tdyV = $kpis['views_today'] ?? 0;
    $pstV = max($totV_actual - $tdyV, 0);
    $totV_calc = max($totV_actual, 1);
    $tdyVPct = round(($tdyV / $totV_calc) * 100);
    $pstVPct = 100 - $tdyVPct;
@endphp

<div class="content-section active" id="analytics">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-weight: 700; color: var(--text); font-size: 1.6rem; margin: 0; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-chart-bar text-primary" style="opacity: 0.9;"></i>
            System Analytics
            <span style="display: inline-flex; items-center; gap: 6px; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; background-color: rgba(244, 63, 94, 0.1); color: #f43f5e; margin-left: 8px;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #f43f5e; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span> Live
            </span>
        </h2>
    </div>

    <!-- 2 Bar Charts At Top -->
    <div class="db-main-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 24px; gap: 24px;">
        <!-- Bar Chart 1 -->
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">Daily Activity</h3>
            </div>
            <div class="db-chart-area" style="height: 250px; padding: 20px;">
                <canvas id="adminBarChart1"></canvas>
            </div>
        </div>

        <!-- Bar Chart 2 -->
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">Engagement Metrics</h3>
            </div>
            <div class="db-chart-area" style="height: 250px; padding: 20px;">
                <canvas id="adminBarChart2"></canvas>
            </div>
        </div>
    </div>

    <!-- 4 Pie Charts Underneath -->
    <div class="db-main-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin-bottom: 40px; gap: 24px;">
        
        <!-- Pie Chart 1: Usage Statistics -->
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">Usage Statistics</h3>
            </div>
            <div class="db-donut-body" style="flex-direction: column; padding-bottom: 24px;">
                <div class="db-donut-canvas-wrap" style="width: 150px; height: 150px; margin-bottom: 16px;">
                    <canvas id="adminPieUsage"></canvas>
                    <div class="db-donut-center">
                        <span class="db-donut-num" style="font-size: 1.1rem;">{{ number_format($kpis['page_views'] ?? 0) }}</span>
                        <span class="db-donut-label">Views</span>
                    </div>
                </div>
                <div class="db-donut-legend" style="width: 100%; align-items: flex-start; text-align: left; gap: 8px;">
                    <div class="db-legend-item" style="align-items: center; flex-direction: row; justify-content: space-between; width: 100%;">
                        <div class="db-legend-label-row">
                            <span class="db-legend-dot" style="background:#f43f5e;"></span>
                            <span class="db-legend-name">Unique Visitors</span>
                        </div>
                        <div class="db-legend-val" style="font-size: 1.1rem;">{{ number_format($uv) }}</div>
                    </div>
                    <div class="db-legend-item" style="align-items: center; flex-direction: row; justify-content: space-between; width: 100%;">
                        <div class="db-legend-label-row">
                            <span class="db-legend-dot" style="background:#10b981;"></span>
                            <span class="db-legend-name">Repeat Views</span>
                        </div>
                        <div class="db-legend-val" style="font-size: 1.1rem;">{{ number_format($repeat) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart 2: User Growth -->
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">User Growth</h3>
            </div>
            <div class="db-donut-body" style="flex-direction: column; padding-bottom: 24px;">
                <div class="db-donut-canvas-wrap" style="width: 150px; height: 150px; margin-bottom: 16px;">
                    <canvas id="adminPieUsers"></canvas>
                    <div class="db-donut-center">
                        <span class="db-donut-num" style="font-size: 1.1rem;">{{ number_format($kpis['total_users'] ?? 0) }}</span>
                        <span class="db-donut-label">Users</span>
                    </div>
                </div>
                <div class="db-donut-legend" style="width: 100%; align-items: flex-start; text-align: left; gap: 8px;">
                    <div class="db-legend-item" style="align-items: center; flex-direction: row; justify-content: space-between; width: 100%;">
                        <div class="db-legend-label-row">
                            <span class="db-legend-dot" style="background:#10b981;"></span>
                            <span class="db-legend-name">New (30d)</span>
                        </div>
                        <div class="db-legend-val" style="font-size: 1.1rem;">{{ number_format($newU) }}</div>
                    </div>
                    <div class="db-legend-item" style="align-items: center; flex-direction: row; justify-content: space-between; width: 100%;">
                        <div class="db-legend-label-row">
                            <span class="db-legend-dot" style="background:#f43f5e;"></span>
                            <span class="db-legend-name">Active (7d)</span>
                        </div>
                        <div class="db-legend-val" style="font-size: 1.1rem;">{{ number_format($actU) }}</div>
                    </div>
                    <div class="db-legend-item" style="align-items: center; flex-direction: row; justify-content: space-between; width: 100%;">
                        <div class="db-legend-label-row">
                            <span class="db-legend-dot" style="background:#64748b;"></span>
                            <span class="db-legend-name">Inactive</span>
                        </div>
                        <div class="db-legend-val" style="font-size: 1.1rem;">{{ number_format($inactU) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart 3: Message Statistics -->
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">Message Stats</h3>
            </div>
            <div class="db-donut-body" style="flex-direction: column; padding-bottom: 24px;">
                <div class="db-donut-canvas-wrap" style="width: 150px; height: 150px; margin-bottom: 16px;">
                    <canvas id="adminPieMessages"></canvas>
                    <div class="db-donut-center">
                        <span class="db-donut-num" style="font-size: 1.1rem;">{{ number_format($totM_actual) }}</span>
                        <span class="db-donut-label">Msgs</span>
                    </div>
                </div>
                <div class="db-donut-legend" style="width: 100%; align-items: flex-start; text-align: left; gap: 8px;">
                    <div class="db-legend-item" style="align-items: center; flex-direction: row; justify-content: space-between; width: 100%;">
                        <div class="db-legend-label-row">
                            <span class="db-legend-dot" style="background:#10b981;"></span>
                            <span class="db-legend-name">Published</span>
                        </div>
                        <div class="db-legend-val" style="font-size: 1.1rem;">{{ number_format($pubM) }}</div>
                    </div>
                    <div class="db-legend-item" style="align-items: center; flex-direction: row; justify-content: space-between; width: 100%;">
                        <div class="db-legend-label-row">
                            <span class="db-legend-dot" style="background:#f43f5e;"></span>
                            <span class="db-legend-name">Expired</span>
                        </div>
                        <div class="db-legend-val" style="font-size: 1.1rem;">{{ number_format($expM) }}</div>
                    </div>
                    <div class="db-legend-item" style="align-items: center; flex-direction: row; justify-content: space-between; width: 100%;">
                        <div class="db-legend-label-row">
                            <span class="db-legend-dot" style="background:#94a3b8;"></span>
                            <span class="db-legend-name">Drafts</span>
                        </div>
                        <div class="db-legend-val" style="font-size: 1.1rem;">{{ number_format($draftM) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart 4: Message Views -->
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">Message Views</h3>
            </div>
            <div class="db-donut-body" style="flex-direction: column; padding-bottom: 24px;">
                <div class="db-donut-canvas-wrap" style="width: 150px; height: 150px; margin-bottom: 16px;">
                    <canvas id="adminPieViews"></canvas>
                    <div class="db-donut-center">
                        <span class="db-donut-num" style="font-size: 1.1rem;">{{ number_format($totV_actual) }}</span>
                        <span class="db-donut-label">Total</span>
                    </div>
                </div>
                <div class="db-donut-legend" style="width: 100%; align-items: flex-start; text-align: left; gap: 8px;">
                    <div class="db-legend-item" style="align-items: center; flex-direction: row; justify-content: space-between; width: 100%;">
                        <div class="db-legend-label-row">
                            <span class="db-legend-dot" style="background:#3b82f6;"></span>
                            <span class="db-legend-name">Today</span>
                        </div>
                        <div class="db-legend-val" style="font-size: 1.1rem;">{{ number_format($tdyV) }}</div>
                    </div>
                    <div class="db-legend-item" style="align-items: center; flex-direction: row; justify-content: space-between; width: 100%;">
                        <div class="db-legend-label-row">
                            <span class="db-legend-dot" style="background:#94a3b8;"></span>
                            <span class="db-legend-name">Past</span>
                        </div>
                        <div class="db-legend-val" style="font-size: 1.1rem;">{{ number_format($pstV) }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    // Data for Bar Chart 1
    var bars1 = {!! json_encode($analytics['usage'] ?? []) !!};
    var labels1 = {!! json_encode($analytics['labels'] ?? []) !!};
    
    var ctx1 = document.getElementById('adminBarChart1');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: labels1,
                datasets: [{
                    label: 'Activity',
                    data: bars1,
                    backgroundColor: '#6366f1',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, display: false },
                    x: { 
                        display: true,
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Data for Bar Chart 2
    var bars2 = {!! json_encode($analytics['user_growth'] ?? []) !!};
    var labels2 = {!! json_encode($analytics['labels'] ?? []) !!};
    
    var ctx2 = document.getElementById('adminBarChart2');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labels2,
                datasets: [{
                    label: 'Engagement',
                    data: bars2,
                    backgroundColor: '#4cc9f0',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, display: false },
                    x: { 
                        display: true,
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Helper to check theme and return empty color
    function getEmptyColor() {
        return document.documentElement.getAttribute('data-theme') === 'dark' ? '#334155' : '#e2e8f0';
    }
    function getBorderColor() {
        return document.documentElement.getAttribute('data-theme') === 'dark' ? '#1e293b' : '#ffffff';
    }

    // Pie Chart 1: Usage
    var ctxUsage = document.getElementById('adminPieUsage');
    if (ctxUsage) {
        var uvVal = {{ $uv }};
        var repeatVal = {{ $repeat }};
        var isEmpty = (uvVal === 0 && repeatVal === 0);
        new Chart(ctxUsage, {
            type: 'doughnut',
            data: {
                labels: isEmpty ? ['No Data'] : ['Unique', 'Repeat'],
                datasets: [{
                    data: isEmpty ? [1] : [uvVal, repeatVal],
                    backgroundColor: isEmpty ? [getEmptyColor()] : ['#f43f5e', '#10b981'],
                    borderWidth: 6,
                    borderColor: getBorderColor(),
                    borderRadius: isEmpty ? 0 : 20,
                    cutout: '65%'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    // Pie Chart 2: Users
    var ctxUsers = document.getElementById('adminPieUsers');
    if (ctxUsers) {
        var newUVal = {{ $newU }};
        var actUVal = {{ $actU }};
        var inactUVal = {{ $inactU }};
        var isEmpty = (newUVal === 0 && actUVal === 0 && inactUVal === 0);
        new Chart(ctxUsers, {
            type: 'doughnut',
            data: {
                labels: isEmpty ? ['No Data'] : ['New', 'Active', 'Inactive'],
                datasets: [{
                    data: isEmpty ? [1] : [newUVal, actUVal, inactUVal],
                    backgroundColor: isEmpty ? [getEmptyColor()] : ['#10b981', '#f43f5e', '#64748b'],
                    borderWidth: 6,
                    borderColor: getBorderColor(),
                    borderRadius: isEmpty ? 0 : 20,
                    cutout: '65%'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    // Pie Chart 3: Messages
    var ctxMsgs = document.getElementById('adminPieMessages');
    if (ctxMsgs) {
        var pubMVal = {{ $pubM }};
        var expMVal = {{ $expM }};
        var draftMVal = {{ $draftM }};
        var isEmpty = (pubMVal === 0 && expMVal === 0 && draftMVal === 0);
        new Chart(ctxMsgs, {
            type: 'doughnut',
            data: {
                labels: isEmpty ? ['No Data'] : ['Published', 'Expired', 'Drafts'],
                datasets: [{
                    data: isEmpty ? [1] : [pubMVal, expMVal, draftMVal],
                    backgroundColor: isEmpty ? [getEmptyColor()] : ['#10b981', '#f43f5e', '#94a3b8'],
                    borderWidth: 6,
                    borderColor: getBorderColor(),
                    borderRadius: isEmpty ? 0 : 20,
                    cutout: '65%'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    // Pie Chart 4: Views
    var ctxViews = document.getElementById('adminPieViews');
    if (ctxViews) {
        var tdyVVal = {{ $tdyV }};
        var pstVVal = {{ $pstV }};
        var isEmpty = (tdyVVal === 0 && pstVVal === 0);
        new Chart(ctxViews, {
            type: 'doughnut',
            data: {
                labels: isEmpty ? ['No Data'] : ['Today', 'Past'],
                datasets: [{
                    data: isEmpty ? [1] : [tdyVVal, pstVVal],
                    backgroundColor: isEmpty ? [getEmptyColor()] : ['#3b82f6', '#94a3b8'],
                    borderWidth: 6,
                    borderColor: getBorderColor(),
                    borderRadius: isEmpty ? 0 : 20,
                    cutout: '65%'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
});
</script>
@endsection
