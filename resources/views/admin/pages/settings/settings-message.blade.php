@extends('admin.base-admin')

@php
    $adminSettings = auth()->check() ? (auth()->user()->settings ?? []) : [];
    $privacyEnabled = $adminSettings['privacy_blur_enabled'] ?? false;
@endphp

@section('admin-section', 'settings-message')

@section('content')
<div class="content-section active" id="settings-message">
    <div class="general-dashboard-wrapper">
        
        <!-- Header Section -->
        <div class="settings-header-box">
            <div>
                <h2 class="settings-title">
                    Global Message &amp; Page Settings
                </h2>
                <p class="settings-subtitle">Manage storage, notifications, lifecycles, preferences, and session security.</p>
            </div>
        </div>

        <!-- Storage Breakdown Card -->
        <div class="hub-card">
            <div class="hub-card-header">
                <div class="hub-card-icon">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="hub-card-header-text">
                    <h4>Global System Storage Allocation</h4>
                    <p>Current media usage across the entire system.</p>
                </div>
            </div>
            
            <div class="storage-box-wrapper">
                @php
                    $imagesMB = round(($imagesSize ?? 0) / 1048576, 2);
                    $audioMB = round(($audioSize ?? 0) / 1048576, 2);
                    $totalMB = $imagesMB + $audioMB;
                    $limitMB = 2097152;
                    $percentUsed = min(100, ($totalMB / $limitMB) * 100);
                @endphp

                <div class="storage-header-row">
                    <span class="storage-usage-title {{ $percentUsed >= 90 ? 'danger' : '' }}">Storage Usage</span>
                    <span class="storage-usage-value">
                        <span class="font-bold {{ $percentUsed >= 90 ? 'text-danger' : 'text-primary' }}">{{ number_format($totalMB, 2) }} MB</span> / {{ number_format($limitMB) }} MB (2 TB)
                    </span>
                </div>

                <!-- Progress Bar -->
                <div class="storage-progress-bg">
                    <div class="storage-progress-bar {{ $percentUsed >= 90 ? 'danger' : '' }}" style="width: {{ max(0.0001, $percentUsed) }}%;"></div>
                </div>

                <!-- Media Types Grid -->
                <div class="storage-grid">
                    <div class="storage-item-card">
                        <div class="storage-item-icon">
                            <i class="fas fa-image"></i>
                        </div>
                        <div>
                            <h5 class="storage-item-title">Global Images</h5>
                            <div class="storage-item-value">{{ number_format($imagesMB, 2) }} MB Used</div>
                        </div>
                    </div>

                    <div class="storage-item-card">
                        <div class="storage-item-icon">
                            <i class="fas fa-music"></i>
                        </div>
                        <div>
                            <h5 class="storage-item-title">Global Audio</h5>
                            <div class="storage-item-value">{{ number_format($audioMB, 2) }} MB Used</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.saveAll') }}">
            @csrf
            <div class="general-layout-grid">
                
                <!-- Left Column -->
                <div class="flex-col-gap">
                    
                    {{-- Global Notifications --}}
                    <div class="hub-card">
                        <div class="hub-card-header">
                            <div class="hub-card-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="hub-card-header-text">
                                <h4>Global Notifications</h4>
                                <p>Configure system-wide alerts and updates.</p>
                            </div>
                        </div>
                        
                        <label class="check-card-label">
                            <input type="checkbox" name="notify_email_alerts" value="1" {{ ($systemSettings['notify_email_alerts'] ?? true) ? 'checked' : '' }} class="check-card-input">
                            <span class="check-card-text">Email notifications for system alerts</span>
                        </label>
                        
                        <label class="check-card-label">
                            <input type="checkbox" name="notify_push_critical" value="1" {{ ($systemSettings['notify_push_critical'] ?? true) ? 'checked' : '' }} class="check-card-input">
                            <span class="check-card-text">Push notifications for critical issues</span>
                        </label>
                        
                        <label class="check-card-label mb-0">
                            <input type="checkbox" name="notify_daily_summary" value="1" {{ ($systemSettings['notify_daily_summary'] ?? false) ? 'checked' : '' }} class="check-card-input">
                            <span class="check-card-text">Daily summary reports</span>
                        </label>
                    </div>

                    {{-- Support Contact Settings --}}
                    <div class="hub-card">
                        <div class="hub-card-header">
                            <div class="hub-card-icon">
                                <i class="fas fa-address-book"></i>
                            </div>
                            <div class="hub-card-header-text">
                                <h4>Support Contact Settings</h4>
                                <p>Contact options displayed to blocked users.</p>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="contact_email">Support Email</label>
                            <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $systemSettings['contact_email'] ?? '') }}" placeholder="admin@example.com" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="contact_phone">Support Phone</label>
                            <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $systemSettings['contact_phone'] ?? '') }}" placeholder="+1234567890" class="form-control">
                        </div>
                        <div class="form-group mb-0">
                            <label for="contact_whatsapp">WhatsApp Number</label>
                            <input type="text" name="contact_whatsapp" id="contact_whatsapp" value="{{ old('contact_whatsapp', $systemSettings['contact_whatsapp'] ?? '') }}" placeholder="+1234567890" class="form-control">
                        </div>
                    </div>

                </div>
                
                <!-- Right Column -->
                <div class="flex-col-gap">
                    
                    {{-- Global Message Lifecycle --}}
                    <div class="hub-card">
                        <div class="hub-card-header">
                            <div class="hub-card-icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <div class="hub-card-header-text">
                                <h4>Global Message Lifecycle</h4>
                                <p>Set default rules for auto-locking and archiving.</p>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Default Vault Auto-Lock <span class="text-muted text-normal">(Minutes)</span></label>
                            <input type="number" name="global_vault_lock" placeholder="15" value="{{ $systemSettings['global_vault_lock'] ?? '15' }}" class="form-control">
                            <p class="admin-form-help">Global timeout before requiring PIN again.</p>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Global Auto-Archive After <span class="text-muted text-normal">(Days)</span></label>
                            <input type="number" name="global_auto_archive" value="{{ $systemSettings['global_auto_archive'] ?? '30' }}" class="form-control">
                            <p class="admin-form-help">Leave blank to disable global auto-archive.</p>
                        </div>
                        
                        <button type="button" class="btn-soft-primary" onclick="showAdminAlert('This will archive all qualifying messages across the system immediately.')">
                            <i class="fas fa-archive mr-2"></i> Run Archive
                        </button>
                    </div>

                    {{-- Privacy Mode --}}
                    <div class="hub-card">
                        <div class="hub-card-header">
                            <div class="hub-card-icon">
                                <i class="fas fa-eye-slash"></i>
                            </div>
                            <div class="hub-card-header-text">
                                <h4>Privacy Mode</h4>
                                <p>Blur message previews in "My Messages" for public browsing.</p>
                            </div>
                        </div>
                        
                        <label class="check-card-label mb-0">
                            <input type="checkbox" name="privacy_blur_enabled" id="privacyBlur" {{ $privacyEnabled ? 'checked' : '' }} class="check-card-input">
                            <span class="check-card-text">Enable Blur UI</span>
                        </label>
                    </div>

                </div>
            </div>

            <!-- Consolidated Single Save Button -->
            <div class="form-footer-bar">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fas fa-save mr-2"></i> Save All Settings
                </button>
            </div>
        </form>
        
    </div>
</div>
@endsection
