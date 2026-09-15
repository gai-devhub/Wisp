@extends('admin.base-admin')

@section('admin-section', 'dashboard')

@section('content')
<div class="content-section active" id="dashboard">
    <!-- Header Section -->
<div class="content-section active" id="dashboard">
    <!-- Header Section -->
    <div class="admin-card-header-bar">
        <h2 class="admin-page-title">
            <i class="fas fa-tachometer-alt text-primary"></i>
            Dashboard Overview
        </h2>
    </div>

    {{-- Stats Row --}}
    <div class="stats-container">
        {{-- Stat Card 1 --}}
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft text-primary">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div class="stat-info">
                <h4>{{ number_format($stats['total_users'] ?? 0) }}</h4>
                <p>Total Users</p>
            </div>
        </div>
        {{-- Stat Card 2 --}}
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft text-primary">
                <i class="fas fa-envelope text-xl"></i>
            </div>
            <div class="stat-info">
                <h4>{{ number_format($stats['total_messages'] ?? 0) }}</h4>
                <p>Messages Created</p>
            </div>
        </div>
        {{-- Stat Card 3 --}}
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft text-primary">
                <i class="fas fa-eye text-xl"></i>
            </div>
            <div class="stat-info">
                <h4>{{ number_format($stats['total_views'] ?? 0) }}</h4>
                <p>Total Views</p>
            </div>
        </div>
        {{-- Stat Card 4 --}}
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft text-primary">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div class="stat-info">
                <h4>{{ number_format($stats['system_issues'] ?? 0) }}</h4>
                <p>System Issues</p>
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="db-main-grid">
        {{-- Left Col --}}
        <div class="db-col-left">
            {{-- Views Chart --}}
            <div class="db-card">
                <div class="db-card-header">
                    <h3 class="db-card-title">Views Activity</h3>
                    <div class="db-card-header-actions">
                        <div class="db-chart-filters">
                            <button class="db-filter-btn" data-period="day">Day</button>
                            <button class="db-filter-btn active" data-period="week">Week</button>
                            <button class="db-filter-btn" data-period="month">Month</button>
                        </div>
                        <a href="{{ route('admin.page') }}" id="adminDashboardRefresh" class="text-primary transition-200" title="Refresh data">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="db-chart-area">
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
                                    <span class="db-legend-dot bg-primary"></span>
                                    <span class="db-legend-name">Published</span>
                                </div>
                                <div class="db-legend-val">{{ number_format($stats['published_messages'] ?? 0) }}</div>
                            </div>
                            <div class="db-legend-item">
                                <div class="db-legend-label-row">
                                    <span class="db-legend-dot bg-primary-soft"></span>
                                    <span class="db-legend-name">Draft</span>
                                </div>
                                <div class="db-legend-val">{{ number_format($stats['draft_messages'] ?? 0) }}</div>
                            </div>
                            <div class="db-legend-item">
                                <div class="db-legend-label-row">
                                    <span class="db-legend-dot bg-warning"></span>
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
                                    <span class="db-legend-dot bg-primary"></span>
                                    <span class="db-legend-name">Media Files</span>
                                </div>
                                <div class="db-legend-val">{{ number_format($stats['total_media'] ?? 0) }}</div>
                            </div>
                            <div class="db-legend-item">
                                <div class="db-legend-label-row">
                                    <span class="db-legend-dot bg-primary-soft"></span>
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
            <div class="db-card calendar-card">
                <div class="calendar-header-bar">
                    <button id="cal-prev" class="cal-nav-btn" type="button"><i class="fas fa-chevron-left"></i></button>
                    <span id="cal-month" class="cal-month-title">{{ date('F Y') }}</span>
                    <button id="cal-next" class="cal-nav-btn" type="button"><i class="fas fa-chevron-right"></i></button>
                </div>
                
                <div class="calendar-week-header">
                    <div class="calendar-week-day">MO</div>
                    <div class="calendar-week-day">TU</div>
                    <div class="calendar-week-day">WE</div>
                    <div class="calendar-week-day">TH</div>
                    <div class="calendar-week-day">FR</div>
                    <div class="calendar-week-day">SA</div>
                    <div class="calendar-week-day">SU</div>
                </div>

                <div id="calendar-days" class="calendar-days-container"></div>

                {{-- Messages Popup Modal --}}
                <div id="messagesModal" class="admin-modal transition-all">
                    <div class="admin-modal-content max-w-lg transition-all">
                        <div class="admin-modal-header">
                            <h3 id="messagesModalTitle" class="admin-modal-title">Messages for Date</h3>
                            <button type="button" class="admin-modal-close" onclick="closeMessagesModal()">&times;</button>
                        </div>
                        <div id="messagesModalContent" class="admin-modal-body bg-subtle">
                            <!-- Messages will be injected here -->
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="db-card recent-activity-card">
                <div class="db-card-header">
                    <h3 class="db-card-title">Recent Activity</h3>
                </div>
                <div class="recent-activity-body">
                    <table class="recent-activity-table">
                        <tbody>
                            @forelse(array_slice($recentActivity ?? [], 0, 5) as $a)
                                <tr>
                                    <td>
                                        <div class="ra-user-name">{{ $a['user'] ?? '-' }}</div>
                                        <div class="ra-activity-desc">{{ strtoupper($a['activity'] ?? '-') }}</div>
                                    </td>
                                    <td class="ra-time-col">
                                        {{ $a['time'] ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="admin-table-empty">No activity yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="recent-activity-footer">
                    <a href="{{ route('admin.logs.page') }}" class="ra-view-btn">View All Logs</a>
                </div>
            </div>
        </div>
    </div>

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
                        backgroundColor: ['rgba(232, 103, 74, 0.8)', 'rgba(232, 103, 74, 0.4)', '#e2e8f0'],
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

        // Render Views Chart (matching user dashboard style)
        var BAR_COLORS = [
            '#F8D2C8', '#F4B9A9', '#F0A08B', '#EC876D', 
            '#F28C76', '#D15D43', '#BA523B', '#A24834'
        ];

        function buildBarColors(count) {
            var colors = [];
            for (var i = 0; i < count; i++) {
                colors.push(BAR_COLORS[i % BAR_COLORS.length]);
            }
            return colors;
        }

        var viewsChartInstance = null;
        function renderViewsChart(data) {
            if (data && data.data) { data = data.data; }
            var chartEl = document.getElementById('dashboardViewsChart');
            if (!chartEl || typeof Chart === 'undefined') return;

            if (viewsChartInstance) {
                viewsChartInstance.destroy();
                viewsChartInstance = null;
            }

            var labels = data.map(function(d) { return d.label; });
            var counts = data.map(function(d) { return d.count; });
            var colors = buildBarColors(counts.length);
            var maxVal = Math.max.apply(null, counts) || 1;

            var bgColors = counts.map(function(c, i) {
                return c >= maxVal ? colors[i] : colors[i] + 'bb';
            });

            viewsChartInstance = new Chart(chartEl, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        backgroundColor: bgColors,
                        borderRadius: { topLeft: 6, topRight: 6 },
                        borderSkipped: false,
                        minBarLength: 6,
                        barPercentage: 0.6,
                        categoryPercentage: 0.75
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 400 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#94a3b8',
                            bodyColor: '#ffffff',
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
                            ticks: { display: false },
                            grid: { display: false },
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
                        backgroundColor: ['rgba(232, 103, 74, 1.0)', 'rgba(232, 103, 74, 0.4)', 'rgba(232, 103, 74, 0.7)', '#e2e8f0'],
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
        let selectedDayNum = null;
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

                fetch('{{ route("admin.api.month_message_dates") }}?year=' + year + '&month=' + (month + 1))
                    .then(res => res.json())
                    .then(activeDays => {
                        buildDaysGrid(activeDays || []);
                    })
                    .catch(() => {
                        buildDaysGrid([]);
                    });

                function buildDaysGrid(activeDays) {
                    calDays.innerHTML = '';

                    for (let i = 0; i < startOffset; i++) {
                        const emptyDiv = document.createElement('div');
                        emptyDiv.classList.add('cal-day-empty');
                        calDays.appendChild(emptyDiv);
                    }

                    for (let i = 1; i <= daysInMonth; i++) {
                        const dayDiv = document.createElement('div');
                        dayDiv.classList.add('cal-day');

                        const isToday = (i === today.getDate() && month === today.getMonth() && year === today.getFullYear());
                        const isSelected = (selectedDayNum !== null && i === selectedDayNum && month === currentDate.getMonth() && year === currentDate.getFullYear());
                        const hasMessage = Array.isArray(activeDays) && activeDays.includes(i);

                        if (isToday) {
                            dayDiv.classList.add('today');
                        }

                        if (isSelected && !isToday) {
                            dayDiv.classList.add('selected');
                        }

                        const numSpan = document.createElement('span');
                        numSpan.innerText = i;
                        dayDiv.appendChild(numSpan);

                        if (hasMessage) {
                            const dotSpan = document.createElement('span');
                            dotSpan.classList.add('cal-day-dot');
                            dayDiv.appendChild(dotSpan);
                        }

                        dayDiv.onclick = () => {
                            selectedDayNum = i;
                            document.querySelectorAll('#calendar-days .cal-day').forEach(d => {
                                d.classList.remove('selected');
                            });
                            if (!dayDiv.classList.contains('today')) {
                                dayDiv.classList.add('selected');
                            }
                            fetchMessagesForDate(year, month, i);
                        };

                        calDays.appendChild(dayDiv);
                    }
                }
            }

            window.closeMessagesModal = function() {
                const modal = document.getElementById('messagesModal');
                modal.style.opacity = '0';
                modal.children[0].style.transform = 'scale(0.95)';
                setTimeout(() => modal.classList.remove('active'), 300);
            };

            function fetchMessagesForDate(year, month, day) {
                const modal = document.getElementById('messagesModal');
                const title = document.getElementById('messagesModalTitle');
                const content = document.getElementById('messagesModalContent');
                
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                title.innerText = 'Messages on ' + monthNames[month] + ' ' + day + ', ' + year;
                
                content.innerHTML = '<div class="modal-loading-box"><i class="fas fa-spinner fa-spin modal-loading-spinner"></i><p style="margin:0;">Loading messages...</p></div>';
                
                modal.classList.add('active');
                modal.style.opacity = '0';
                setTimeout(() => {
                    modal.style.opacity = '1';
                    modal.children[0].style.transform = 'scale(1)';
                }, 10);
                
                const dateStr = year + '-' + (month+1).toString().padStart(2, '0') + '-' + day.toString().padStart(2, '0');
                fetch('{{ route("admin.api.messages_by_date") }}?date=' + dateStr)
                    .then(res => res.json())
                    .then(data => {
                        if (!data || data.length === 0) {
                            content.innerHTML = '<div class="modal-empty-box"><div class="modal-empty-icon">📭</div><p style="margin:0;">No messages created on this day.</p></div>';
                            return;
                        }

                        let html = '<div>';
                        data.forEach(msg => {
                            let statusClass = msg.status === 'active' || msg.status === 'published' ? 'modal-status-published' : 
                                              (msg.status === 'draft' ? 'modal-status-draft' : 'modal-status-default');
                                              
                            html += `
                                <div class="modal-msg-item">
                                    <div class="modal-msg-header">
                                        <div style="flex:1; min-width:0; padding-right:16px;">
                                            <h4 class="modal-msg-title">${msg.title || 'Untitled'}</h4>
                                        </div>
                                        <span class="modal-msg-badge ${statusClass}">${msg.status}</span>
                                    </div>
                                    <div class="modal-msg-footer">
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            <div class="modal-msg-meta">
                                                <i class="fas fa-user text-muted"></i> <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;">${msg.user}</span>
                                            </div>
                                            <div class="modal-msg-meta">
                                                <i class="fas fa-clock text-muted"></i> ${msg.time}
                                            </div>
                                        </div>
                                        <a href="${msg.url}" target="_blank" class="modal-msg-link">
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
                        content.innerHTML = '<div class="modal-loading-box text-danger"><i class="fas fa-exclamation-triangle modal-loading-spinner"></i><p style="margin:0;">Error loading messages.</p></div>';
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
