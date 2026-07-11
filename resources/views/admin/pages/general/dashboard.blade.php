@extends('admin.base-admin')

@section('admin-section', 'dashboard')

@section('content')
<div class="content-section active" id="dashboard">
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-weight: 700; color: var(--text); font-size: 1.6rem; margin: 0; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-tachometer-alt text-primary" style="opacity: 0.9;"></i>
            Dashboard Overview
        </h2>
    </div>

    {{-- Stats Row --}}
    <div class="stats-container">
        {{-- Stat Card 1 --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div class="stat-info">
                <h4 style="font-size:1.5rem; margin:0; line-height:1;">{{ number_format($stats['total_users'] ?? 0) }}</h4>
                <p style="font-size:0.75rem; text-transform:uppercase; font-weight:700; margin-top:4px;">Total Users</p>
            </div>
        </div>
        {{-- Stat Card 2 --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="fas fa-envelope text-xl"></i>
            </div>
            <div class="stat-info">
                <h4 style="font-size:1.5rem; margin:0; line-height:1;">{{ number_format($stats['total_messages'] ?? 0) }}</h4>
                <p style="font-size:0.75rem; text-transform:uppercase; font-weight:700; margin-top:4px;">Messages Created</p>
            </div>
        </div>
        {{-- Stat Card 3 --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(217, 70, 239, 0.1); color: #d946ef;">
                <i class="fas fa-eye text-xl"></i>
            </div>
            <div class="stat-info">
                <h4 style="font-size:1.5rem; margin:0; line-height:1;">{{ number_format($stats['total_views'] ?? 0) }}</h4>
                <p style="font-size:0.75rem; text-transform:uppercase; font-weight:700; margin-top:4px;">Total Views</p>
            </div>
        </div>
        {{-- Stat Card 4 --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e;">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div class="stat-info">
                <h4 style="font-size:1.5rem; margin:0; line-height:1;">{{ number_format($stats['system_issues'] ?? 0) }}</h4>
                <p style="font-size:0.75rem; text-transform:uppercase; font-weight:700; margin-top:4px;">System Issues</p>
            </div>
        </div>
        {{-- Stat Card 5 --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(234, 179, 8, 0.1); color: #eab308;">
                <i class="fas fa-coins text-xl"></i>
            </div>
            <div class="stat-info">
                <h4 style="font-size:1.5rem; margin:0; line-height:1;">${{ number_format($stats['total_income'] ?? 0, 2) }}</h4>
                <p style="font-size:0.75rem; text-transform:uppercase; font-weight:700; margin-top:4px;">All Income</p>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="db-main-grid" style="grid-template-columns: 1fr 380px;">
        {{-- Left Col --}}
        <div class="db-col-left">
            {{-- Views Chart --}}
            <div class="db-card">
                <div class="db-card-header">
                    <h3 class="db-card-title">Views Activity</h3>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div class="db-chart-filters" style="display:flex; gap:4px; background:#f1f5f9; border:1px solid #e2e8f0; border-radius:10px; padding:3px;">
                            <button class="db-filter-btn" data-period="day" style="background:none; border:none; font-size:0.75rem; font-weight:600; color:#94a3b8; padding:4px 12px; border-radius:7px; cursor:pointer;">Day</button>
                            <button class="db-filter-btn active" data-period="week" style="background:#ffffff; border:none; font-size:0.75rem; font-weight:600; color:#1e293b; padding:4px 12px; border-radius:7px; cursor:pointer; box-shadow:0 1px 4px rgba(0,0,0,0.08);">Week</button>
                            <button class="db-filter-btn" data-period="month" style="background:none; border:none; font-size:0.75rem; font-weight:600; color:#94a3b8; padding:4px 12px; border-radius:7px; cursor:pointer;">Month</button>
                        </div>
                        <a href="{{ route('admin.page') }}" id="adminDashboardRefresh" style="color:var(--primary); transition:0.2s;" title="Refresh data">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="db-chart-area" style="height: 300px; padding: 20px;">
                    <canvas id="dashboardViewsChart"></canvas>
                </div>
            </div>

            {{-- Donut Row --}}
            <div class="db-donut-row">
                {{-- Message Status --}}
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">Messages Status</h3>
                    </div>
                    <div class="db-donut-body">
                        <div class="db-donut-canvas-wrap">
                            <canvas id="dashboardStatusPieChart"></canvas>
                            <div class="db-donut-center">
                                <span class="db-donut-num">{{ number_format(($stats['published_messages'] ?? 0) + ($stats['draft_messages'] ?? 0) + ($stats['expired_messages'] ?? 0)) }}</span>
                                <span class="db-donut-label">Total</span>
                            </div>
                        </div>
                        <div class="db-donut-legend">
                            <div class="db-legend-item">
                                <div class="db-legend-label-row">
                                    <span class="db-legend-dot" style="background:#10b981;"></span>
                                    <span class="db-legend-name">Published</span>
                                </div>
                                <div class="db-legend-val">{{ number_format($stats['published_messages'] ?? 0) }}</div>
                            </div>
                            <div class="db-legend-item">
                                <div class="db-legend-label-row">
                                    <span class="db-legend-dot" style="background:#94a3b8;"></span>
                                    <span class="db-legend-name">Draft</span>
                                </div>
                                <div class="db-legend-val">{{ number_format($stats['draft_messages'] ?? 0) }}</div>
                            </div>
                            <div class="db-legend-item">
                                <div class="db-legend-label-row">
                                    <span class="db-legend-dot" style="background:#f43f5e;"></span>
                                    <span class="db-legend-name">Expired</span>
                                </div>
                                <div class="db-legend-val">{{ number_format($stats['expired_messages'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Admin Usage --}}
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">Features Usage</h3>
                    </div>
                    <div class="db-donut-body">
                        <div class="db-donut-canvas-wrap">
                            <canvas id="dashboardFeaturesChart"></canvas>
                            <div class="db-donut-center">
                                <span class="db-donut-num">{{ number_format(($stats['total_media'] ?? 0) + ($stats['total_ai'] ?? 0)) }}</span>
                                <span class="db-donut-label">Features</span>
                            </div>
                        </div>
                        <div class="db-donut-legend">
                            <div class="db-legend-item">
                                <div class="db-legend-label-row">
                                    <span class="db-legend-dot" style="background:#334155;"></span>
                                    <span class="db-legend-name">Media Files</span>
                                </div>
                                <div class="db-legend-val">{{ number_format($stats['total_media'] ?? 0) }}</div>
                            </div>
                            <div class="db-legend-item">
                                <div class="db-legend-label-row">
                                    <span class="db-legend-dot" style="background:#94a3b8;"></span>
                                    <span class="db-legend-name">AI Chats</span>
                                </div>
                                <div class="db-legend-val">{{ number_format($stats['total_ai'] ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Col --}}
        <div class="db-col-right">
            {{-- Calendar --}}
            <div class="db-card" style="padding: 24px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                    <button id="cal-prev" style="width:32px; height:32px; border-radius:8px; border:1px solid var(--border); background:var(--bg); cursor:pointer;"><i class="fas fa-chevron-left text-xs"></i></button>
                    <span id="cal-month" style="font-weight:700; font-size:1rem; color:var(--text);">{{ date('F Y') }}</span>
                    <button id="cal-next" style="width:32px; height:32px; border-radius:8px; border:1px solid var(--border); background:var(--bg); cursor:pointer;"><i class="fas fa-chevron-right text-xs"></i></button>
                </div>
                
                <div style="display:grid; grid-template-columns:repeat(7,1fr); text-align:center; margin-bottom:8px;">
                    <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); padding:4px 0;">Mo</div>
                    <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); padding:4px 0;">Tu</div>
                    <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); padding:4px 0;">We</div>
                    <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); padding:4px 0;">Th</div>
                    <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); padding:4px 0;">Fr</div>
                    <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); padding:4px 0;">Sa</div>
                    <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); padding:4px 0;">Su</div>
                </div>

                <div id="calendar-days" style="display:grid; grid-template-columns:repeat(7,1fr); gap:4px;"></div>

                {{-- Messages Popup Modal --}}
                <div id="messagesModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
                    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden transform scale-95 transition-transform duration-300 relative">
                        <div class="flex items-center justify-between p-5 border-b border-slate-100">
                            <h3 id="messagesModalTitle" class="text-lg font-bold text-slate-800 m-0">Messages for Date</h3>
                            <button type="button" class="border-0 bg-transparent text-xl cursor-pointer text-slate-400 hover:text-red-500 leading-none" onclick="closeMessagesModal()">&times;</button>
                        </div>
                        <div id="messagesModalContent" class="p-5 max-h-[60vh] overflow-y-auto bg-slate-50">
                            <!-- Messages will be injected here -->
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="db-card">
                <div class="db-card-header" style="border-bottom:1px solid var(--border); padding-bottom:16px;">
                    <h3 class="db-card-title">Recent Activity</h3>
                </div>
                <div style="padding: 0;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <tbody>
                            @forelse(array_slice($recentActivity ?? [], 0, 5) as $a)
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 12px 20px;">
                                        <div style="font-weight: 600; font-size: 0.85rem; color: var(--text);">{{ $a['user'] ?? '-' }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">{{ $a['activity'] ?? '-' }}</div>
                                    </td>
                                    <td style="padding: 12px 20px; text-align: right; font-size: 0.75rem; color: var(--text-muted); white-space: nowrap;">
                                        {{ $a['time'] ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" style="padding: 24px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">No activity yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div style="padding: 16px; border-top: 1px solid var(--border); text-align: center;">
                    <a href="{{ route('admin.logs.page') }}" style="display:block; padding:8px 0; background:rgba(99,102,241,0.1); color:var(--primary); font-weight:600; font-size:0.85rem; border-radius:8px; text-decoration:none; transition:0.2s;">View All Logs</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Calendar Days custom classes */
        .cal-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            border: 1px solid transparent;
        }
        .cal-day:hover {
            background-color: var(--bg-subtle, #f1f5f9);
            border-color: var(--border);
        }
        .cal-day.today {
            background-color: var(--primary, #6366f1);
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(99,102,241,0.4);
        }
        .cal-day.selected {
            background-color: var(--text);
            color: var(--bg);
        }
        .db-event-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }
        .db-event-item:last-child {
            border-bottom: none;
        }
        .db-event-dot {
            width: 3px;
            background-color: var(--text);
            border-radius: 2px;
            flex-shrink: 0;
            align-self: stretch;
            min-height: 16px;
        }
        .db-event-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            line-height: 1.3;
        }
        .db-event-time {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 2px;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') return;

        // Render Features Chart
        var featuresEl = document.getElementById('dashboardFeaturesChart');
        if (featuresEl) {
            var mediaCount = Number({{ $stats['total_media'] ?? 0 }});
            var aiCount = Number({{ $stats['total_ai'] ?? 0 }});
            var total = mediaCount + aiCount;

            new Chart(featuresEl, {
                type: 'doughnut',
                data: {
                    labels: ['Media', 'AI'],
                    datasets: [{
                        data: [
                            mediaCount > 0 ? mediaCount : (total === 0 ? 0.001 : 0),
                            aiCount > 0 ? aiCount : (total === 0 ? 0.001 : 0),
                            total === 0 ? 1 : 0
                        ],
                        backgroundColor: ['#334155', '#94a3b8', '#e2e8f0'],
                        borderWidth: 0,
                        borderRadius: 6,
                        spacing: 4,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8,
                            filter: function(item) { return item.dataIndex < 2; }
                        }
                    }
                }
            });
        }

        // Render Views Chart
        var viewsChartInstance = null;
        function renderViewsChart(data) {
            var chartEl = document.getElementById('dashboardViewsChart');
            if (!chartEl) return;
            if (viewsChartInstance) {
                viewsChartInstance.destroy();
            }
            var labels = data.map(d => d.label);
            var counts = data.map(d => d.count);
            
            var ctx = chartEl.getContext('2d');
            var gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

            viewsChartInstance = new Chart(chartEl, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Views',
                        data: counts,
                        borderColor: '#6366f1',
                        backgroundColor: gradient,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        tension: 0.35,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 400 },
                    layout: { padding: { top: 12, bottom: 4, left: 6, right: 6 } },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.9)',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(ctx) {
                                    return ' ' + ctx.parsed.y + ' view' + (ctx.parsed.y !== 1 ? 's' : '');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: '10%',
                            ticks: { color: '#94a3b8' },
                            grid: { color: 'rgba(148,163,184,0.18)' },
                            border: { display: false }
                        },
                        x: {
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 10, weight: '600' },
                                maxRotation: 0
                            },
                            grid: { display: false },
                            border: { display: false }
                        }
                    }
                }
            });
        }

        function loadViewsChart(period) {
            fetch('{{ route("admin.api.views-chart-data") }}?period=' + period)
                .then(r => r.json())
                .then(data => renderViewsChart(data))
                .catch(e => {
                    renderViewsChart([]);
                });
        }
        
        loadViewsChart('week');

        document.querySelectorAll('.db-filter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.db-filter-btn').forEach(b => {
                    b.classList.remove('active');
                    b.style.background = 'none';
                    b.style.color = '#94a3b8';
                    b.style.boxShadow = 'none';
                });
                btn.classList.add('active');
                btn.style.background = '#ffffff';
                btn.style.color = '#1e293b';
                btn.style.boxShadow = '0 1px 4px rgba(0,0,0,0.08)';
                loadViewsChart(btn.dataset.period);
            });
        });

        // Render Status Pie Chart
        var statusEl = document.getElementById('dashboardStatusPieChart');
        if (statusEl) {
            var pub = Number({{ $stats['published_messages'] ?? 0 }});
            var draft = Number({{ $stats['draft_messages'] ?? 0 }});
            var exp = Number({{ $stats['expired_messages'] ?? 0 }});
            var totMsg = pub + draft + exp;

            new Chart(statusEl, {
                type: 'doughnut',
                data: {
                    labels: ['Published', 'Draft', 'Expired'],
                    datasets: [{
                        data: [
                            pub > 0 ? pub : (totMsg === 0 ? 0.001 : 0),
                            draft > 0 ? draft : (totMsg === 0 ? 0.001 : 0),
                            exp > 0 ? exp : (totMsg === 0 ? 0.001 : 0),
                            totMsg === 0 ? 1 : 0
                        ],
                        backgroundColor: ['#10b981', '#94a3b8', '#f43f5e', '#e2e8f0'],
                        borderWidth: 0,
                        borderRadius: 6,
                        spacing: 4,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8,
                            filter: function(item) { return item.dataIndex < 3; }
                        }
                    }
                }
            });
        }

        // Calendar Logic
        let currentDate = new Date();
        const calMonth = document.getElementById('cal-month');
        const calDays = document.getElementById('calendar-days');
        const prevBtn = document.getElementById('cal-prev');
        const nextBtn = document.getElementById('cal-next');

        if (calDays) {
            function renderCalendar() {
                calDays.innerHTML = '';
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                calMonth.innerText = monthNames[month] + ' ' + year;

                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const startOffset = firstDay === 0 ? 6 : firstDay - 1;

                const today = new Date();

                for (let i = 0; i < startOffset; i++) {
                    const emptyDiv = document.createElement('div');
                    calDays.appendChild(emptyDiv);
                }

                for (let i = 1; i <= daysInMonth; i++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.innerText = i;
                    dayDiv.classList.add('cal-day');

                    const isToday = (i === today.getDate() && month === today.getMonth() && year === today.getFullYear());
                    if (isToday) {
                        dayDiv.classList.add('today');
                    }

                    dayDiv.onclick = () => {
                        // Remove selected from all days, restore today highlight
                        document.querySelectorAll('#calendar-days .cal-day').forEach(d => {
                            d.classList.remove('selected');
                            const dayNum = parseInt(d.innerText);
                            if (year === today.getFullYear() && month === today.getMonth() && dayNum === today.getDate()) {
                                d.classList.add('today');
                            } else {
                                d.classList.remove('today');
                            }
                        });
                        // Mark this day as selected (today stays highlighted too)
                        dayDiv.classList.add('selected');
                        fetchMessagesForDate(year, month, i);
                    };

                    calDays.appendChild(dayDiv);
                }
            }

            window.closeMessagesModal = function() {
                const modal = document.getElementById('messagesModal');
                modal.classList.add('opacity-0');
                modal.children[0].classList.add('scale-95');
                setTimeout(() => modal.classList.add('hidden'), 300);
            };

            function fetchMessagesForDate(year, month, day) {
                const modal = document.getElementById('messagesModal');
                const title = document.getElementById('messagesModalTitle');
                const content = document.getElementById('messagesModalContent');
                
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                title.innerText = 'Messages on ' + monthNames[month] + ' ' + day + ', ' + year;
                
                content.innerHTML = '<div class="text-center text-slate-500 py-8"><i class="fas fa-spinner fa-spin text-2xl mb-3"></i><p>Loading messages...</p></div>';
                
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modal.children[0].classList.remove('scale-95');
                }, 10);
                
                const dateStr = year + '-' + (month+1).toString().padStart(2, '0') + '-' + day.toString().padStart(2, '0');
                fetch('{{ route("admin.api.messages_by_date") }}?date=' + dateStr)
                    .then(res => res.json())
                    .then(data => {
                        if (!data || data.length === 0) {
                            content.innerHTML = '<div class="text-center text-slate-500 py-8"><div class="text-4xl mb-3 opacity-50">📭</div><p>No messages created on this day.</p></div>';
                            return;
                        }

                        let html = '<div>';
                        data.forEach(msg => {
                            let statusColor = msg.status === 'active' || msg.status === 'published' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 
                                              (msg.status === 'draft' ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-slate-50 text-slate-600 border border-slate-200');
                                              
                            html += `
                                <div class="bg-white border border-slate-100 p-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 group relative mb-3 last:mb-0">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1 min-w-0 pr-4">
                                            <h4 class="font-bold text-slate-800 m-0 truncate text-base group-hover:text-primary transition-colors">${msg.title || 'Untitled'}</h4>
                                        </div>
                                        <span class="text-[0.65rem] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider flex-shrink-0 ${statusColor}">${msg.status}</span>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-slate-50 pt-3 mt-1">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center gap-1.5 text-xs font-medium text-slate-500 bg-slate-50 px-2 py-1 rounded-md">
                                                <i class="fas fa-user text-slate-400"></i> <span class="truncate max-w-[100px]">${msg.user}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-xs font-medium text-slate-500 bg-slate-50 px-2 py-1 rounded-md">
                                                <i class="fas fa-clock text-slate-400"></i> ${msg.time}
                                            </div>
                                        </div>
                                        <a href="${msg.url}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-700 px-3 py-1.5 rounded-lg transition-colors" style="text-decoration:none;">
                                            <i class="fas fa-external-link-alt"></i> View Message
                                        </a>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        content.innerHTML = html;
                    })
                    .catch(err => {
                        content.innerHTML = '<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-triangle text-2xl mb-3"></i><p>Error loading messages.</p></div>';
                    });
            }

            prevBtn.onclick = () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            };
            nextBtn.onclick = () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            };

            renderCalendar();
        }
    });
    </script>
</div>
@endsection
