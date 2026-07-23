@extends('user.base-user')

@section('user-section', 'dashboard')

@section('content')
    <div class="content-section active" id="dashboard">
        <!-- Header Section -->
        <div class="messages-header-toolbar">
            <h2 class="messages-header-title">
                <i class="fas fa-home text-primary messages-header-icon"></i>
                Dashboard Overview
            </h2>
        </div>
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon dashboard-inline-1" >
                    <i class="fas fa-link"></i>
                </div>
                <div class="stat-info">
                    <h4>{{ number_format($dashboardStats['generated_links'] ?? 0) }}</h4>
                    <p>Generated Links</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon dashboard-inline-2" >
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-info">
                    <h4>{{ number_format($dashboardStats['total_views'] ?? 0) }}</h4>
                    <p>Total Views</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon dashboard-inline-3" >
                    <i class="fas fa-gift"></i>
                </div>
                <div class="stat-info">
                    <h4>{{ number_format($dashboardStats['active_messages'] ?? 0) }}</h4>
                    <p>Active Messages</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon dashboard-inline-4" >
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h4>{{ number_format($dashboardStats['expired_messages'] ?? 0) }}</h4>
                    <p>Expired Messages</p>
                </div>
            </div>
        </div>
        {{-- ===== MAIN DASHBOARD GRID ===== --}}
        <div class="db-main-grid">

            {{-- LEFT COLUMN --}}
            <div class="db-col-left">

                {{-- Views Activity --}}
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">Views Activity</h3>
                        <div class="db-chart-filters">
                            <button class="db-filter-btn" data-period="day">Day</button>
                            <button class="db-filter-btn active" data-period="week">Week</button>
                            <button class="db-filter-btn" data-period="month">Month</button>
                        </div>
                    </div>
                    <div class="db-chart-area">
                        <canvas id="dashboardViewsChart"></canvas>
                    </div>
                </div>

                {{-- Bottom two donut cards --}}
                <div class="db-donut-row">

                    {{-- Message Allocation --}}
                    <div class="db-card">
                        <div class="db-card-header">
                            <h3 class="db-card-title">Messages</h3>
                        </div>
                        <div class="db-donut-body">
                            <div class="db-donut-canvas-wrap">
                                <canvas id="dashboardMessagesPieChart"></canvas>
                                <div class="db-donut-center">
                                    <span class="db-donut-num">{{ number_format(($dashboardStats['active_messages'] ?? 0) + ($dashboardStats['expired_messages'] ?? 0)) }}</span>
                                    <span class="db-donut-label">Total</span>
                                </div>
                            </div>
                            <div class="db-donut-legend">
                                <div class="db-legend-item">
                                    <div class="db-legend-label-row">
                                        <span class="db-legend-name">Active</span>
                                        <span class="db-legend-dot dashboard-inline-5" ></span>
                                    </div>
                                    <div class="db-legend-val">{{ number_format($dashboardStats['active_messages'] ?? 0) }}</div>
                                </div>
                                <div class="db-legend-item">
                                    <div class="db-legend-label-row">
                                        <span class="db-legend-name">Expired</span>
                                        <span class="db-legend-dot dashboard-inline-6" ></span>
                                    </div>
                                    <div class="db-legend-val">{{ number_format($dashboardStats['expired_messages'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Links --}}
                    <div class="db-card">
                        <div class="db-card-header">
                            <h3 class="db-card-title">Total Links</h3>
                        </div>
                        <div class="db-donut-body">
                            <div class="db-donut-canvas-wrap">
                                <canvas id="dashboardLeadsChart"></canvas>
                                <div class="db-donut-center">
                                    <span class="db-donut-num">{{ number_format($dashboardStats['generated_links'] ?? 0) }}</span>
                                    <span class="db-donut-label">Links</span>
                                </div>
                            </div>
                            <div class="db-donut-legend">
                                <div class="db-legend-item">
                                    <div class="db-legend-label-row">
                                        <span class="db-legend-name">Generated</span>
                                        <span class="db-legend-dot dashboard-inline-7" ></span>
                                    </div>
                                    <div class="db-legend-val">{{ number_format($dashboardStats['generated_links'] ?? 0) }}</div>
                                </div>
                                <div class="db-legend-item">
                                    <div class="db-legend-label-row">
                                        <span class="db-legend-name">Views</span>
                                        <span class="db-legend-dot dashboard-inline-8" ></span>
                                    </div>
                                    <div class="db-legend-val">{{ number_format($dashboardStats['total_views'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /db-donut-row --}}
            </div>{{-- /db-col-left --}}

            {{-- RIGHT COLUMN --}}
            <div class="db-col-right">

                {{-- Calendar Card --}}
                <div class="db-card db-calendar-card">
                    {{-- Month Navigation --}}
                    <div class="db-cal-header">
                        <button id="cal-prev" class="db-cal-nav-btn"><i class="fas fa-chevron-left"></i></button>
                        <span id="cal-month" class="db-cal-month-label">{{ date('F Y') }}</span>
                        <button id="cal-next" class="db-cal-nav-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>

                    {{-- Day-of-week headers --}}
                    <div class="db-cal-dow-row">
                        <div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div><div>Su</div>
                    </div>

                    {{-- Day grid rendered by JS --}}
                    <div id="calendar-days" class="db-cal-days-grid"></div>


                </div>


            </div>{{-- /db-col-right --}}
        </div>{{-- /db-main-grid --}}

        {{-- Promo Banner Slot (Long span under dashboard) --}}
        <div class="db-promo-slot">
            @foreach($activeAds as $ad)
            <div class="dashboard-promo-banner" 
                 data-title="{{ $ad->title }}"
                 data-content="{{ $ad->content ?? 'Check out this latest update from WISP.' }}"
                 data-details="{{ $ad->details ?? '' }}"
                 data-image="{{ $ad->image_path ? s3_url($ad->image_path) : '' }}"
                 data-link="{{ $ad->link_url }}"
                 onclick="openDynamicAdModal(this)">
                <div class="promo-content">
                    <div class="promo-icon">
                        @if($ad->image_path)
                            <img src="{{ s3_url($ad->image_path) }}" alt="{{ $ad->title }}" class="dashboard-ad-img">
                        @else
                            <i class="fas fa-bullhorn"></i>
                        @endif
                    </div>
                    <div class="promo-text">
                        <h3>{{ $ad->title }}</h3>
                        <p>{!! nl2br(e($ad->content ?? 'Check out this latest update from WISP.')) !!}</p>
                    </div>
                </div>
                @if($ad->link_url)
                <div class="promo-cta"><span>Learn More <i class="fas fa-arrow-right"></i></span></div>
                @endif
            </div>
            @endforeach

            @if($activeAds->isEmpty())
            <!-- {{-- Premium Banner (commented out)
            <div class="dashboard-promo-banner" onclick="openPromoModal()">
                <div class="promo-content">
                    <div class="promo-icon"><i class="fas fa-rocket"></i></div>
                    <div class="promo-text">
                        <h3>Unlock WISP Premium</h3>
                        <p>Unlimited links, advanced analytics, and priority support.</p>
                    </div>
                </div>
                <div class="promo-cta"><span>Learn More <i class="fas fa-arrow-right"></i></span></div>
            </div>
            --}} -->
            <a href="{{ route('user.help-support.page') }}" class="dashboard-promo-banner" style="text-decoration: none; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                <div class="promo-content">
                    <div class="promo-icon"><i class="fas fa-comment-dots"></i></div>
                    <div class="promo-text">
                        <h3>Share Your Feedback</h3>
                        <p>Help us improve WISP — share your ideas, suggestions, or report an issue.</p>
                    </div>
                </div>
                <div class="promo-cta"><span>Give Feedback <i class="fas fa-arrow-right"></i></span></div>
            </a>
            @endif
        </div>

        


        @if($activeAds->isEmpty())
        <!-- Default Promo Modal -->
        <div id="promo-fullscreen-modal" class="promo-modal-overlay">
            <div class="promo-modal-content fullscreen-media">
                <button class="promo-close-btn" onclick="closePromoModal()"><i class="fas fa-times"></i></button>
                <div class="promo-modal-flex">
                    <div class="promo-media-col">
                        <img src="{{ asset('img/pro-image.jpeg') }}" alt="WISP Premium Promotional Flyer">
                    </div>
                    <div class="promo-text-col">
                        <div class="promo-modal-header text-left">
                            <h2>WISP Premium Experience</h2>
                            <p class="text-muted">Unlock the full potential of your messaging with high-end tools designed for creators and businesses.</p>
                        </div>
                        <div class="promo-modal-features compact">
                            <div class="promo-feature">
                                <div class="feature-icon"><i class="fas fa-check"></i></div>
                                <div><h4>Unlimited Links</h4><p>Create as many custom message links as you need.</p></div>
                            </div>
                            <div class="promo-feature">
                                <div class="feature-icon"><i class="fas fa-check"></i></div>
                                <div><h4>Advanced Analytics</h4><p>Real-time view tracking and audience insights.</p></div>
                            </div>
                        </div>
                        <div class="promo-modal-footer text-left mt-4">
                            <button class="btn btn-primary btn-lg dashboard-inline-10"  onclick="alert('Redirecting...')">
                                Upgrade Now <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Dynamic Ad Modal -->
        <div id="dynamic-ad-modal" class="promo-modal-overlay">
            <div class="promo-modal-content fullscreen-media">
                <button class="promo-close-btn" onclick="closeDynamicAdModal()"><i class="fas fa-times"></i></button>
                <div class="promo-modal-flex">
                    <div class="promo-media-col" id="dynamic-ad-media-col">
                        <img id="dynamic-ad-img" src="" alt="Ad Image"  class="dashboard-inline-11">
                        <div id="dynamic-ad-icon" class="promo-icon dashboard-inline-12" >
                            <i class="fas fa-bullhorn"></i>
                        </div>
                    </div>
                    <div class="promo-text-col">
                        <div class="promo-modal-header text-left">
                            <h2 id="dynamic-ad-title"></h2>
                            <p class="text-muted dashboard-inline-13" id="dynamic-ad-desc" ></p>
                        </div>
                        <div id="dynamic-ad-details"  class="dashboard-inline-14"></div>
                        <div class="promo-modal-footer text-left dashboard-inline-15" id="dynamic-ad-footer" >
                            <button id="dynamic-ad-btn" class="btn btn-primary btn-lg dashboard-inline-16" >
                                Learn More <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Move modals to body so they are not trapped by parent container CSS (transform, overflow, etc.)
            document.addEventListener('DOMContentLoaded', function() {
                var promoModal = document.getElementById('promo-fullscreen-modal');
                var dynamicModal = document.getElementById('dynamic-ad-modal');
                if (promoModal) document.body.appendChild(promoModal);
                if (dynamicModal) document.body.appendChild(dynamicModal);
            });

            function openPromoModal() {
                document.getElementById('promo-fullscreen-modal').classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closePromoModal() {
                document.getElementById('promo-fullscreen-modal').classList.remove('active');
                document.body.style.overflow = '';
            }

            function openDynamicAdModal(el) {
                const title = el.getAttribute('data-title');
                const content = el.getAttribute('data-content');
                const details = el.getAttribute('data-details');
                const image = el.getAttribute('data-image');
                const link = el.getAttribute('data-link');

                document.getElementById('dynamic-ad-title').textContent = title;
                document.getElementById('dynamic-ad-desc').textContent = content;

                const detailsEl = document.getElementById('dynamic-ad-details');
                if (details) {
                    detailsEl.textContent = details;
                    detailsEl.style.display = 'block';
                } else {
                    detailsEl.style.display = 'none';
                }

                const imgEl = document.getElementById('dynamic-ad-img');
                const iconEl = document.getElementById('dynamic-ad-icon');

                if (image) {
                    imgEl.src = image;
                    imgEl.style.display = 'block';
                    iconEl.style.display = 'none';
                } else {
                    imgEl.style.display = 'none';
                    iconEl.style.display = 'flex';
                }

                const footerEl = document.getElementById('dynamic-ad-footer');
                const btnEl = document.getElementById('dynamic-ad-btn');
                
                if (link) {
                    footerEl.style.display = 'block';
                    btnEl.onclick = function() {
                        window.open(link, '_blank');
                    };
                } else {
                    footerEl.style.display = 'none';
                }

                document.getElementById('dynamic-ad-modal').classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeDynamicAdModal() {
                document.getElementById('dynamic-ad-modal').classList.remove('active');
                document.body.style.overflow = '';
            }
        </script>
    </div>

<!-- Calendar Event Modal (Scheduled Messages) -->
<div id="calendar-event-modal" class="event-modal-overlay">
    <div class="event-modal-content">
        <button type="button" onclick="closeCalendarModal()" class="event-modal-close" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        <div class="event-modal-header">
            <div class="event-modal-icon"><i class="fas fa-calendar-day"></i></div>
            <div>
                <h3 id="modal-date-title">Events</h3>
                <p>Messages scheduled for this date</p>
            </div>
        </div>
        <div id="modal-events-list" class="event-modal-list"></div>
    </div>
</div>

<style>
.db-cal-event-dot {
    display: block;
    width: 5px;
    height: 5px;
    background-color: var(--primary, #6366f1);
    border-radius: 50%;
    margin-top: 2px;
}
.cal-day { display: flex; flex-direction: column; align-items: center; justify-content: center; }
.cal-day.has-wisp-event { cursor: pointer; }
.cal-day.has-wisp-event:hover { background: #f1f5f9; border-radius: 6px; }

/* Modal Overlay */
.event-modal-overlay {
    position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(15, 23, 42, 0.55);
    z-index: 9999;
    display: none; align-items: center; justify-content: center;
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
}
/* Modal Box */
.event-modal-content {
    background: #fff;
    width: 90%;
    max-width: 420px;
    border-radius: 24px;
    padding: 28px 28px 24px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 4px 16px rgba(0,0,0,0.08);
    animation: modalSlideIn 0.2s ease;
}
@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(12px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
/* Close Button */
.event-modal-close {
    position: absolute; top: 20px; right: 20px;
    background: #f1f5f9;
    border: none;
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem;
    color: #64748b; cursor: pointer; transition: 0.2s;
}
.event-modal-close:hover { background: #e2e8f0; color: #0f172a; }
/* Header */
.event-modal-header {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #f1f5f9;
}
.event-modal-icon {
    width: 42px; height: 42px; border-radius: 12px;
    background: linear-gradient(135deg, #6366f1, #818cf8);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1rem; flex-shrink: 0;
}
.event-modal-header h3 {
    margin: 0 0 3px 0;
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
}
.event-modal-header p { margin: 0; color: #94a3b8; font-size: 0.8rem; }
/* List */
.event-modal-list { max-height: 280px; overflow-y: auto; }
.event-modal-item {
    display: flex; align-items: center; gap: 14px;
    padding: 12px 10px;
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.15s;
}
.event-modal-item:hover { background: #f8fafc; }
.event-modal-item-details { flex: 1; }
.event-modal-item-details strong {
    display: block; font-size: 0.93rem; color: #1e293b;
    font-weight: 600; margin-bottom: 2px;
    line-height: 1.3;
}
.event-modal-item-details span {
    display: flex; align-items: center; gap: 5px;
    font-size: 0.82rem; color: #64748b;
}
</style>

@push('scripts')
    <script>
        (function () {
            const scheduledSends = @json($scheduledSends ?? []);

            function ensureChartJsLoaded() {
                return new Promise(function (resolve, reject) {
                    if (typeof window.Chart !== 'undefined') {
                        resolve(window.Chart);
                        return;
                    }
                    var existing = document.querySelector('script[data-chartjs="1"]');
                    if (existing) {
                        existing.addEventListener('load', function () { resolve(window.Chart); }, { once: true });
                        existing.addEventListener('error', reject, { once: true });
                        return;
                    }
                    var script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
                    script.async = true;
                    script.defer = true;
                    script.setAttribute('data-chartjs', '1');
                    script.onload = function () { resolve(window.Chart); };
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            }

            function renderDashboardCharts() {
                var dashboardStats = window.WISP_DASHBOARD_STATS || {};

                // Multi-color palette for bars
                var BAR_COLORS = [
                    '#4361ee', '#7c3aed', '#2563eb', '#0891b2', '#059669',
                    '#16a34a', '#ca8a04', '#ea580c', '#dc2626', '#9333ea',
                    '#0284c7', '#0d9488', '#65a30d', '#d97706', '#c026d3',
                    '#e11d48', '#7c3aed', '#4f46e5', '#0369a1', '#047857',
                    '#b45309', '#c2410c', '#be123c', '#6d28d9', '#1d4ed8'
                ];

                var viewsChartInstance = null;

                function buildBarColors(count) {
                    var colors = [];
                    for (var i = 0; i < count; i++) {
                        colors.push(BAR_COLORS[i % BAR_COLORS.length]);
                    }
                    return colors;
                }

                function renderViewsChart(data) {
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
                    fetch('{{ route("user.views-chart-data") }}?period=' + period)
                        .then(function(r) { return r.json(); })
                        .then(function(data) { renderViewsChart(data); })
                        .catch(function() {
                            // Fall back to stored 7-day data
                            renderViewsChart(window.WISP_VIEWS_LAST_7_DAYS || []);
                        });
                }

                // Initial load (week)
                loadViewsChart('week');

                // Filter button listeners
                document.querySelectorAll('.db-filter-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.db-filter-btn').forEach(function(b) { b.classList.remove('active'); });
                        btn.classList.add('active');
                        loadViewsChart(btn.dataset.period);
                    });
                });
                
                // Message Allocation Donut — data-driven
                var pieEl = document.getElementById('dashboardMessagesPieChart');
                if (pieEl && typeof Chart !== 'undefined') {
                    var activeMessages  = Number(dashboardStats.active_messages  || 0);
                    var expiredMessages = Number(dashboardStats.expired_messages || 0);
                    var totalMsg = activeMessages + expiredMessages;

                    // Background track fills ring to the total so proportions are correct
                    new Chart(pieEl, {
                        type: 'doughnut',
                        data: {
                            labels: ['Active', 'Expired'],
                            datasets: [{
                                data: [
                                    activeMessages  > 0 ? activeMessages  : (totalMsg === 0 ? 0.001 : 0),
                                    expiredMessages > 0 ? expiredMessages : (totalMsg === 0 ? 0.001 : 0),
                                    totalMsg === 0 ? 1 : 0  // ghost track when no data at all
                                ],
                                backgroundColor: ['#22c55e', '#f97316', '#e2e8f0'],
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

                // Total Links Donut — data-driven
                var leadsEl = document.getElementById('dashboardLeadsChart');
                if (leadsEl && typeof Chart !== 'undefined') {
                    var generatedLinks = Number(dashboardStats.generated_links || 0);
                    var totalViews     = Number(dashboardStats.total_views     || 0);
                    var totalLinks = generatedLinks + totalViews;

                    new Chart(leadsEl, {
                        type: 'doughnut',
                        data: {
                            labels: ['Generated', 'Views'],
                            datasets: [{
                                data: [
                                    generatedLinks > 0 ? generatedLinks : (totalLinks === 0 ? 0.001 : 0),
                                    totalViews     > 0 ? totalViews     : (totalLinks === 0 ? 0.001 : 0),
                                    totalLinks === 0 ? 1 : 0  // ghost track when no data at all
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
            }

            function initCalendarAndFocus() {
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

                        // Build map of scheduled messages by date (YYYY-MM-DD)
                        const eventsByDate = {};
                        scheduledSends.forEach(send => {
                            if (send.scheduled_at) {
                                const datePart = (send.scheduled_at.split('T')[0] || send.scheduled_at.split(' ')[0]);
                                if (!eventsByDate[datePart]) eventsByDate[datePart] = [];
                                eventsByDate[datePart].push(send);
                            }
                        });

                        for (let i = 0; i < startOffset; i++) {
                            calDays.appendChild(document.createElement('div'));
                        }

                        for (let i = 1; i <= daysInMonth; i++) {
                            const dayDiv = document.createElement('div');
                            dayDiv.setAttribute('data-day', i);
                            dayDiv.classList.add('cal-day');

                            const numSpan = document.createElement('span');
                            numSpan.textContent = i;
                            dayDiv.appendChild(numSpan);

                            if (year === today.getFullYear() && month === today.getMonth() && i === today.getDate()) {
                                dayDiv.classList.add('today');
                            }

                            // Check for scheduled messages on this day
                            const mStr = String(month + 1).padStart(2, '0');
                            const dStr = String(i).padStart(2, '0');
                            const dateString = `${year}-${mStr}-${dStr}`;
                            const dayEvents = eventsByDate[dateString];

                            if (dayEvents && dayEvents.length > 0) {
                                dayDiv.classList.add('has-wisp-event');
                                const dot = document.createElement('span');
                                dot.className = 'db-cal-event-dot';
                                dayDiv.appendChild(dot);
                            }

                            dayDiv.onclick = () => {
                                document.querySelectorAll('#calendar-days .cal-day').forEach(d => {
                                    d.classList.remove('selected', 'today');
                                    if (year === today.getFullYear() && month === today.getMonth() && parseInt(d.getAttribute('data-day')) === today.getDate()) {
                                        d.classList.add('today');
                                    }
                                });
                                dayDiv.classList.add('selected');
                                dayDiv.classList.remove('today');
                                // Removed Google Events trigger
                                if (dayEvents && dayEvents.length > 0) {
                                    openCalendarModal(dateString, dayEvents);
                                }
                            };

                            calDays.appendChild(dayDiv);
                        }
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
                    
                    // Trigger fetch for today
                    const today = new Date();
                    // Removed initial fetch
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                initCalendarAndFocus();
                
                var chartWrap = document.querySelector('.db-main-grid, .dashboard-chart-wrap');
                if (chartWrap) {
                    ensureChartJsLoaded()
                        .then(renderDashboardCharts)
                        .catch(function () { });
                }
                document.querySelectorAll('.activity-pagination-prev[data-href], .activity-pagination-next[data-href]').forEach(function (btn) {
                    var href = btn.getAttribute('data-href');
                    if (href) btn.addEventListener('click', function () { location.href = href; });
                });
            });
        })();

        function openCalendarModal(dateString, events) {
            const modal = document.getElementById('calendar-event-modal');
            const title = document.getElementById('modal-date-title');
            const list = document.getElementById('modal-events-list');
            const dateObj = new Date(dateString + 'T00:00:00');
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            title.textContent = dateObj.toLocaleDateString(undefined, options);
            list.innerHTML = '';
            events.forEach(event => {
                const item = document.createElement('div');
                item.className = 'event-modal-item';
                if (event.wish_message_id) {
                    item.onclick = () => window.location.href = `/user-page/message-preview/${event.wish_message_id}`;
                }
                const msgTitle = event.wish_message ? event.wish_message.title : 'Unknown Message';
                const recipient = event.recipient_masked || (event.wish_message ? event.wish_message.recipient_name : 'Unknown');
                item.innerHTML = `
                    <div class="event-modal-item-details">
                        <strong>${msgTitle}</strong>
                        <span><i class="fas fa-user-circle"></i> To: ${recipient}</span>
                    </div>
                    <div style="margin-left:auto;color:var(--primary,#6366f1);font-size:1.25rem;">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                `;
                list.appendChild(item);
            });
            modal.style.display = 'flex';
        }

        function closeCalendarModal() {
            document.getElementById('calendar-event-modal').style.display = 'none';
        }
    </script>
@endpush
@endsection
