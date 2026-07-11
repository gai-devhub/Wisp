@extends('admin.base-admin')
@php
    $adminSettings = auth()->check() ? (auth()->user()->settings ?? []) : [];
    $privacyEnabled = $adminSettings['privacy_blur_enabled'] ?? false;
@endphp

@section('admin-section', 'settings-page')

@section('content')
<style>
    /* Premium Dashboard Styles */
    .general-dashboard-wrapper {
        display: flex;
        flex-direction: column;
        gap: 24px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .general-layout-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        align-items: start;
    }
    @media (max-width: 992px) {
        .general-layout-grid { grid-template-columns: 1fr; }
    }

    .hub-card { 
        background: #ffffff; 
        border: 1px solid rgba(0,0,0,0.04); 
        border-radius: 20px; 
        padding: 28px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
        display: flex; 
        flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .hub-card:hover {
        box-shadow: 0 15px 40px rgba(0,0,0,0.06);
    }
    
    .hub-card::before {
        content: '';
        position: absolute;
        top: 0; right: 0; width: 150px; height: 150px;
        background: radial-gradient(circle, var(--primary-light) 0%, rgba(255,255,255,0) 70%);
        opacity: 0.3;
        border-radius: 50%;
        transform: translate(30%, -30%);
        pointer-events: none;
    }

    .hub-card-header {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
        position: relative;
        z-index: 1;
    }
    .hub-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary-light), rgba(var(--primary-rgb, 99, 102, 241), 0.1));
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(var(--primary-rgb, 99, 102, 241), 0.1);
    }
    .hub-card-header-text h4 {
        font-weight: 700;
        color: var(--text-dark, #1e293b);
        margin-bottom: 4px;
        font-size: 1.15rem;
        letter-spacing: -0.01em;
    }
    .hub-card-header-text p {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.5;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label { 
        font-weight: 600; 
        color: var(--text, #334155); 
        margin-bottom: 8px; 
        display: block; 
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-control { 
        border-radius: 12px; 
        padding: 12px 16px; 
        border: 1px solid rgba(0,0,0,0.08); 
        transition: all 0.2s ease; 
        background: var(--bg-subtle, #f8fafc);
        font-size: 0.95rem;
        color: var(--text-dark);
        width: 100%;
        box-sizing: border-box;
    }
    .form-control:focus { 
        border-color: var(--primary); 
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(var(--primary-rgb, 99, 102, 241), 0.15); 
        outline: none;
    }
    
    .btn-primary { 
        border-radius: 12px; 
        padding: 12px 24px; 
        font-weight: 600; 
        letter-spacing: 0.3px;
        background: linear-gradient(135deg, var(--primary), #4f46e5);
        border: none;
        box-shadow: 0 4px 12px rgba(var(--primary-rgb, 99, 102, 241), 0.3);
        transition: all 0.2s ease;
        color: white;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(var(--primary-rgb, 99, 102, 241), 0.4);
    }

    /* Theme Picker */
    .theme-grid { 
        display: grid; 
        grid-template-columns: repeat(3, 1fr); 
        gap: 16px; 
    }
    .theme-preview { 
        height: 90px; 
        border-radius: 16px; 
        border: 2px solid transparent; 
        cursor: pointer; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-weight: 700; 
        color: white; 
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .theme-preview::after {
        content: ''; position: absolute; inset: 0; background: rgba(0,0,0,0.1); opacity: 0; transition: opacity 0.2s;
    }
    .theme-preview:hover::after { opacity: 1; }
    .theme-preview.active { 
        border-color: var(--primary); 
        box-shadow: 0 0 0 4px rgba(var(--primary-rgb, 99, 102, 241), 0.2), 0 8px 20px rgba(0,0,0,0.15); 
        transform: translateY(-4px); 
    }
    .theme-preview.active::before {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 8px; right: 10px;
        background: white;
        color: #10b981;
        width: 20px; height: 20px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.7rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .hub-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .hub-table th { padding: 12px 16px; font-weight: 600; color: var(--text-muted); border-bottom: 2px solid var(--border); text-align: left; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .hub-table td { padding: 16px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }
    .hub-table tr:last-child td { border-bottom: none; }
    .hub-table tbody tr { transition: 0.2s; }
    .hub-table tbody tr:hover { background: var(--bg-subtle, #f8fafc); }
</style>

<div class="content-section active" id="settings-page">
    <div class="general-dashboard-wrapper">
        
        <!-- Header Section -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 8px;">
            <div>
                <h2 style="font-weight: 800; color: var(--text-dark, #0f172a); font-size: 2rem; margin: 0 0 8px 0; letter-spacing: -0.02em;">
                    Global Page Settings
                </h2>
                <p style="color: var(--text-muted); font-size: 1.05rem; margin: 0;">Manage global preferences, themes, and encryption statuses.</p>
            </div>
        </div>

        <div class="general-layout-grid">
            
            <!-- Left Column -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                {{-- System Preferences --}}
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4f46e5;">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>System Preferences</h4>
                            <p>Manage global system display settings.</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.preferences') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="timezone">Timezone</label>
                            <select name="timezone" id="timezone" class="form-control">
                                <option value="UTC-12:00" {{ ($systemSettings['timezone'] ?? '') == 'UTC-12:00' ? 'selected' : '' }}>UTC-12:00</option>
                                <option value="UTC-08:00" {{ ($systemSettings['timezone'] ?? '') == 'UTC-08:00' ? 'selected' : '' }}>UTC-08:00 (PST)</option>
                                <option value="UTC-05:00" {{ ($systemSettings['timezone'] ?? '') == 'UTC-05:00' ? 'selected' : '' }}>UTC-05:00 (EST)</option>
                                <option value="UTC" {{ ($systemSettings['timezone'] ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC+00:00 (GMT)</option>
                                <option value="UTC+01:00" {{ ($systemSettings['timezone'] ?? '') == 'UTC+01:00' ? 'selected' : '' }}>UTC+01:00 (CET)</option>
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label for="dateFormat">Date Format</label>
                            <select name="date_format" id="dateFormat" class="form-control">
                                <option value="m/d/Y" {{ ($systemSettings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                <option value="d/m/Y" {{ ($systemSettings['date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="Y-m-d" {{ ($systemSettings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label for="itemsPerPage">Items Per Page</label>
                            <select name="items_per_page" id="itemsPerPage" class="form-control">
                                <option value="10" {{ (int)($systemSettings['items_per_page'] ?? 25) === 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ (int)($systemSettings['items_per_page'] ?? 25) === 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ (int)($systemSettings['items_per_page'] ?? 25) === 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ (int)($systemSettings['items_per_page'] ?? 25) === 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; background: linear-gradient(135deg, #6366f1, #4f46e5); border-color: transparent;"><i class="fas fa-save" style="margin-right: 8px;"></i> Save Preferences</button>
                    </form>
                </div>

                {{-- Support Contact Settings --}}
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669;">
                            <i class="fas fa-address-book"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Support Contact Settings</h4>
                            <p>Contact options displayed to blocked users.</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.contactSettings') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="contact_email">Support Email</label>
                            <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $systemSettings['contact_email'] ?? '') }}" placeholder="admin@example.com" class="form-control">
                        </div>
                        <div class="form-group mb-4">
                            <label for="contact_phone">Support Phone</label>
                            <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $systemSettings['contact_phone'] ?? '') }}" placeholder="+1234567890" class="form-control">
                        </div>
                        <div class="form-group mb-4">
                            <label for="contact_whatsapp">WhatsApp Number</label>
                            <input type="text" name="contact_whatsapp" id="contact_whatsapp" value="{{ old('contact_whatsapp', $systemSettings['contact_whatsapp'] ?? '') }}" placeholder="+1234567890" class="form-control">
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; background: linear-gradient(135deg, #10b981, #059669);"><i class="fas fa-save" style="margin-right: 8px;"></i> Save Contact Info</button>
                    </form>
                </div>

            </div>
            
            <!-- Right Column -->
            <div style="display: flex; flex-direction: column; gap: 24px;">

                {{-- Admin Dashboard Themes --}}
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon" style="background: linear-gradient(135deg, #fce7f3, #fbcfe8); color: #db2777;">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Appearance & Themes</h4>
                            <p>Personalize your dashboard experience across 3 exclusive palettes.</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.privacy_theme') }}" id="theme-form">
                        @csrf
                        <input type="hidden" name="theme_preference" id="themeInput" value="{{ $adminSettings['theme_preference'] ?? 'theme-default' }}">
                        <div class="theme-grid" style="margin-bottom: 24px;">
                            @php $currentTheme = $adminSettings['theme_preference'] ?? 'theme-default'; @endphp
                            <div class="theme-preview {{ $currentTheme === 'theme-default' ? 'active' : '' }}" data-theme="theme-default" style="background: linear-gradient(135deg, #6366f1, #3b82f6);">
                                Indigo
                            </div>
                            <div class="theme-preview {{ $currentTheme === 'theme-forest' ? 'active' : '' }}" data-theme="theme-forest" style="background: linear-gradient(135deg, #10b981, #059669);">
                                Emerald
                            </div>
                            <div class="theme-preview {{ $currentTheme === 'theme-crimson' ? 'active' : '' }}" data-theme="theme-crimson" style="background: linear-gradient(135deg, #f43f5e, #e11d48);">
                                Crimson
                            </div>
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; background: linear-gradient(135deg, #ec4899, #db2777); border-color: transparent;"><i class="fas fa-paint-brush" style="margin-right: 8px;"></i> Apply Theme</button>
                    </form>
                </div>

                {{-- Privacy Mode --}}
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon" style="background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0284c7;">
                            <i class="fas fa-eye-slash"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Privacy Mode</h4>
                            <p>Blur message previews in "My Messages" for public browsing.</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.privacy_theme') }}" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="privacy_form_submitted" value="1">
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.08); background: var(--bg-subtle, #f8fafc); cursor: pointer; transition: all 0.2s; margin-bottom: 20px;">
                            <input type="checkbox" name="privacy_blur_enabled" id="privacyBlur" {{ $privacyEnabled ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--primary);">
                            <span style="font-weight: 600; color: var(--text-dark);">Enable Blur UI</span>
                        </label>
                        <button type="submit" class="btn-primary" style="width: 100%; background: linear-gradient(135deg, #0ea5e9, #0284c7); border-color: transparent;"><i class="fas fa-shield-alt" style="margin-right: 8px;"></i> Update Privacy</button>
                    </form>
                </div>

                {{-- Global Encryption Status --}}
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706;">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Encryption Status</h4>
                            <p>End-to-End Encryption enabled.</p>
                        </div>
                    </div>
                    
                    <div style="background: var(--bg-subtle, #f8fafc); padding: 24px; border-radius: 16px; border: 1px dashed rgba(0,0,0,0.1); text-align: center;">
                        <div style="width: 64px; height: 64px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); color: #10b981; font-size: 1.5rem;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h5 style="font-weight: 700; margin-bottom: 8px; color: var(--text-dark);">Fully Encrypted</h5>
                        <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 0;">All sensitive database payloads, Vault PINs, and active messages are encrypted using AES-256-CBC standard before leaving our servers.</p>
                    </div>
                </div>

            </div>
        </div>
        
        <!-- Bottom Full Width Row: Active Sessions -->
        <div style="margin-top: 24px;">
            <div class="hub-card">
                <div class="hub-card-header">
                    <div class="hub-card-icon" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4f46e5;">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="hub-card-header-text">
                        <h4>Admin Active Sessions</h4>
                        <p>Where your admin account is currently logged in.</p>
                    </div>
                </div>
                
                <div style="overflow-x: auto; border: 1px solid var(--border); border-radius: 12px;">
                    <table class="hub-table">
                        <thead style="background: var(--bg-subtle, #f8fafc);">
                            <tr>
                                <th>IP Address</th>
                                <th>Browser</th>
                                <th>Last Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeSessions ?? [] as $session)
                                @php /** @var \stdClass $session */ @endphp
                                <tr>
                                    <td style="font-weight: 700; color: var(--text-dark);" class="blur-sensitive">{{ $session->ip_address }}</td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-globe text-muted"></i> 
                                            {{ \Str::limit($session->user_agent, 36) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 8px; background: var(--bg-body); border: 1px solid var(--border); font-size: 0.85rem; font-weight: 600;">
                                            <i class="far fa-clock"></i> {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 40px 20px;">
                                        <i class="fas fa-history text-muted" style="font-size: 2rem; margin-bottom: 12px; display: block; opacity: 0.3;"></i>
                                        <span style="font-weight: 500; color: var(--text-muted);">Session data unavailable.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themePreviews = document.querySelectorAll('.theme-preview');
    const themeInput = document.getElementById('themeInput');
    
    themePreviews.forEach(preview => {
        preview.addEventListener('click', () => {
            themePreviews.forEach(p => p.classList.remove('active'));
            preview.classList.add('active');
            let theme = preview.dataset.theme;
            themeInput.value = theme;
        });
    });
});
</script>
@endsection
