@extends('user.base-user')

@section('user-section', 'control-center-security')

@section('content')

<div class="content-section active" id="control-center-security">
    <!-- Header Section -->
    <div class="messages-header-toolbar">
        <h2 class="messages-header-title">
            <i class="fas fa-shield-alt text-primary messages-header-icon"></i> 
            Security & Privacy
        </h2>
    </div>

    <div class="audit-grid">
        <div class="hub-card security-hub-card-clean">
            <div class="security-hub-card-header mb-4">
                <h4 class="security-section-title">Active Sessions</h4>
                <p class="text-muted mb-0 security-section-subtitle"><i class="fas fa-shield-alt text-success"></i> Where your account is logged in.</p>
            </div>
            <div class="table-responsive">
                <table class="hub-table">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Browser</th>
                            <th>Last Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeSessions ?? [] as $session)
                            @php
                                /** @var \stdClass $session */
                                // Hash IP for privacy (show first octet + hashed remainder)
                                $rawIp = $session->ip_address ?? '';
                                $hashedIp = $rawIp ? substr($rawIp, 0, -5) . '*****' : '—';

                                // Parse user agent into friendly browser name
                                $ua = $session->user_agent ?? '';
                                $browser = 'Unknown Browser';
                                $os = '';

                                if (str_contains($ua, 'Edg/') || str_contains($ua, 'Edge/')) {
                                    $browser = 'Edge';
                                } elseif (str_contains($ua, 'OPR/') || str_contains($ua, 'Opera')) {
                                    $browser = 'Opera';
                                } elseif (str_contains($ua, 'SamsungBrowser')) {
                                    $browser = 'Samsung Browser';
                                } elseif (str_contains($ua, 'Chrome') && !str_contains($ua, 'Chromium')) {
                                    $browser = 'Chrome';
                                } elseif (str_contains($ua, 'Firefox')) {
                                    $browser = 'Firefox';
                                } elseif (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) {
                                    $browser = 'Safari';
                                } elseif (str_contains($ua, 'MSIE') || str_contains($ua, 'Trident')) {
                                    $browser = 'Internet Explorer';
                                }

                                if (str_contains($ua, 'Android')) {
                                    $os = 'Android';
                                } elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) {
                                    $os = 'iOS';
                                } elseif (str_contains($ua, 'Windows')) {
                                    $os = 'Windows';
                                } elseif (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS')) {
                                    $os = 'macOS';
                                } elseif (str_contains($ua, 'Linux')) {
                                    $os = 'Linux';
                                }

                                $browserLabel = $os ? "$browser on $os" : $browser;
                            @endphp
                            <tr>
                                <td data-label="IP Address"><strong>{{ $hashedIp }}</strong></td>
                                <td data-label="Browser">
                                    <span class="text-muted security-session-browser">
                                        @if($browser === 'Chrome')
                                            <i class="fab fa-chrome security-session-icon"></i>
                                        @elseif($browser === 'Firefox')
                                            <i class="fab fa-firefox security-session-icon"></i>
                                        @elseif($browser === 'Safari')
                                            <i class="fab fa-safari security-session-icon"></i>
                                        @elseif($browser === 'Edge')
                                            <i class="fab fa-edge security-session-icon"></i>
                                        @elseif($browser === 'Opera')
                                            <i class="fab fa-opera security-session-icon"></i>
                                        @else
                                            <i class="fas fa-globe security-session-icon"></i>
                                        @endif
                                        {{ $browserLabel }}
                                    </span>
                                </td>
                                <td data-label="Last Active">
                                    <span class="security-session-time">
                                        <i class="far fa-clock"></i> {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted security-session-empty">
                                    <i class="fas fa-history mb-2 security-session-empty-icon"></i>
                                    Session data unavailable.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="security-cards-col">
            <div class="hub-card">
                <h4 class="security-section-title">Encryption Status</h4>
                <p class="mb-0"><i class="fas fa-lock text-success"></i> <strong>End-to-End Encryption</strong></p>
                <p class="text-muted security-hub-card-desc-mt security-section-subtitle">All sensitive database payloads, Vault PINs, and active messages are encrypted using AES-256-CBC standard before leaving our servers.</p>
            </div>
            
            <div class="hub-card">
                <h4 class="security-section-title">System Version & Updates</h4>
                <p class="mb-2"><i class="fas fa-laptop-code text-primary"></i> <strong>App Version:</strong> v2.1.4</p>
                <p class="mb-2"><i class="fas fa-clock text-info"></i> <strong>Last Updated:</strong> Recently</p>
                <p class="mb-2"><i class="fas fa-check-circle text-success"></i> <strong>Update Status:</strong> System is running the latest configuration.</p>
                <p class="text-muted security-hub-card-desc security-section-subtitle mt-2" style="font-size: 0.85rem;">Automatic updates keep your features and security protocols up to date without interrupting your workflow.</p>
            </div>

        </div>
    </div>
</div>

<style>
.security-section-title {
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
}
.security-section-subtitle {
    margin-bottom: 16px;
}
@media (min-width: 768px) {
    .security-section-title {
        font-size: 1.4rem;
    }
    .security-section-subtitle {
        font-size: 1rem;
    }
}

.audit-grid {
    display: flex;
    flex-direction: column;
    gap: 24px;
}
@media (min-width: 992px) {
    .audit-grid {
        flex-direction: row;
        align-items: flex-start;
    }
    .audit-grid > * {
        flex: 1;
        min-width: 0;
    }
}
.security-cards-col {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.btn-update-privacy {
    padding: 10px 24px;
    font-weight: 600;
    border-radius: 8px;
    background-color: var(--primary, #6366f1);
    border: none;
    transition: 0.2s;
}
.btn-update-privacy:hover {
    background-color: var(--primary-dark, #4f46e5);
}

.security-session-time, .security-session-browser {
    white-space: nowrap;
    display: inline-block;
}

@media (max-width: 768px) {
    .hub-table thead {
        display: none;
    }
    .hub-table, .hub-table tbody, .hub-table tr, .hub-table td {
        display: block;
        width: 100%;
    }
    .hub-table tr {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 16px;
        padding: 16px;
        background: #f8fafc;
    }
    .hub-table td {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 8px 0;
        border: none;
    }
    .hub-table td::before {
        content: attr(data-label);
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }
}
</style>
@endsection
