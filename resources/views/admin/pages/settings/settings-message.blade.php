@extends('admin.base-admin')

@section('admin-section', 'settings-message')

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
        background: var(--card-bg, #ffffff); 
        border: 1px solid var(--border-color, rgba(0,0,0,0.04)); 
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
    .btn-outline-danger {
        border-radius: 12px; padding: 12px 24px; font-weight: 600; letter-spacing: 0.3px;
        background: white; border: 1px solid #fecaca; color: #ef4444; transition: all 0.2s ease; cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-outline-danger:hover {
        background: #fef2f2; border-color: #ef4444;
    }
    .btn-outline-primary {
        border-radius: 12px; padding: 12px 24px; font-weight: 600; letter-spacing: 0.3px;
        background: transparent; border: 1px solid var(--primary); color: var(--primary); transition: all 0.2s ease; cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-outline-primary:hover {
        background: var(--primary-light);
    }
    
    /* Storage Grid */
    .storage-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 24px;
    }
    @media (max-width: 600px) {
        .storage-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="content-section active" id="settings-message">
    <div class="general-dashboard-wrapper">
        
        <!-- Header Section -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 8px;">
            <div>
                <h2 style="font-weight: 800; color: var(--text-dark, #0f172a); font-size: 2rem; margin: 0 0 8px 0; letter-spacing: -0.02em;">
                    Global Message Settings
                </h2>
                <p style="color: var(--text-muted); font-size: 1.05rem; margin: 0;">Manage storage, notifications, and global message lifecycles.</p>
            </div>
        </div>

        <!-- Storage Breakdown Card -->
        <div class="hub-card">
            <div class="hub-card-header">
                <div class="hub-card-icon" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4f46e5;">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="hub-card-header-text">
                    <h4>Global System Storage Allocation</h4>
                    <p>Current media usage across the entire system.</p>
                </div>
            </div>
            
            <div style="background: var(--bg-subtle, #f8fafc); padding: 24px; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05);">
                @php
                    $imagesMB = round(($imagesSize ?? 0) / 1048576, 2);
                    $audioMB = round(($audioSize ?? 0) / 1048576, 2);
                    $totalMB = $imagesMB + $audioMB;
                    $limitMB = 2097152;
                    $percentUsed = min(100, ($totalMB / $limitMB) * 100);
                @endphp

                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 12px;">
                    <span style="font-weight: 700; color: {{ $percentUsed >= 90 ? '#ef4444' : 'var(--text-dark)' }};">Storage Usage</span>
                    <span style="font-size: 0.95rem; color: {{ $percentUsed >= 90 ? '#ef4444' : 'var(--text-muted)' }}; font-weight: 500;">
                        <span style="font-weight: 700; color: {{ $percentUsed >= 90 ? '#ef4444' : 'var(--primary)' }};">{{ number_format($totalMB, 2) }} MB</span> / {{ number_format($limitMB) }} MB (2 TB)
                    </span>
                </div>

                <!-- Progress Bar -->
                <div style="height: 12px; background: rgba(0,0,0,0.05); border-radius: 6px; overflow: hidden; margin-bottom: 24px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="height: 100%; border-radius: 6px; background: {{ $percentUsed >= 90 ? 'linear-gradient(90deg, #ef4444, #dc2626)' : 'linear-gradient(90deg, var(--primary), #4f46e5)' }}; width: {{ max(0.0001, $percentUsed) }}%; transition: width 1s ease;"></div>
                </div>

                <!-- Media Types Grid -->
                <div class="storage-grid">
                    <div style="background: var(--inner-card-bg, #ffffff); border: 1px solid var(--border-color, rgba(0,0,0,0.05)); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(99, 102, 241, 0.15); color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                            <i class="fas fa-image"></i>
                        </div>
                        <div>
                            <h5 style="margin: 0 0 4px 0; font-weight: 700; color: var(--text-dark);">Global Images</h5>
                            <div style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">{{ number_format($imagesMB, 2) }} MB Used</div>
                        </div>
                    </div>

                    <div style="background: var(--inner-card-bg, #ffffff); border: 1px solid var(--border-color, rgba(0,0,0,0.05)); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.15); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                            <i class="fas fa-music"></i>
                        </div>
                        <div>
                            <h5 style="margin: 0 0 4px 0; font-weight: 700; color: var(--text-dark);">Global Audio</h5>
                            <div style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">{{ number_format($audioMB, 2) }} MB Used</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="general-layout-grid">
            
            <!-- Left Column: Global Notifications -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706;">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Global Notifications</h4>
                            <p>Configure system-wide alerts and updates.</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.notificationSettings') }}">
                        @csrf
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); background: var(--bg-subtle, #f8fafc); cursor: pointer; transition: all 0.2s; margin-bottom: 16px;">
                            <input type="checkbox" name="notify_email_alerts" value="1" {{ ($systemSettings['notify_email_alerts'] ?? true) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--primary);">
                            <span style="font-weight: 600; color: var(--text-dark);">Email notifications for system alerts</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); background: var(--bg-subtle, #f8fafc); cursor: pointer; transition: all 0.2s; margin-bottom: 16px;">
                            <input type="checkbox" name="notify_push_critical" value="1" {{ ($systemSettings['notify_push_critical'] ?? true) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--primary);">
                            <span style="font-weight: 600; color: var(--text-dark);">Push notifications for critical issues</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); background: var(--bg-subtle, #f8fafc); cursor: pointer; transition: all 0.2s; margin-bottom: 24px;">
                            <input type="checkbox" name="notify_daily_summary" value="1" {{ ($systemSettings['notify_daily_summary'] ?? false) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--primary);">
                            <span style="font-weight: 600; color: var(--text-dark);">Daily summary reports</span>
                        </label>
                        
                        <button type="submit" class="btn-primary" style="width: 100%; background: linear-gradient(135deg, #f59e0b, #d97706); border-color: transparent;"><i class="fas fa-save" style="margin-right: 8px;"></i> Save Preferences</button>
                    </form>
                </div>
            </div>
            
            <!-- Right Column: Global Message Lifecycle -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669;">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Global Message Lifecycle</h4>
                            <p>Set default rules for auto-locking and archiving.</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.lifecycle') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label>Default Vault Auto-Lock <span style="text-transform: none; font-weight: normal; color: var(--text-muted);">(Minutes)</span></label>
                            <input type="number" name="global_vault_lock" placeholder="15" class="form-control">
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 8px 0 0 0;">Global timeout before requiring PIN again.</p>
                        </div>
                        
                        <div class="form-group mb-4" style="margin-bottom: 24px;">
                            <label>Global Auto-Archive After <span style="text-transform: none; font-weight: normal; color: var(--text-muted);">(Days)</span></label>
                            <input type="number" name="global_auto_archive" value="30" class="form-control">
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 8px 0 0 0;">Leave blank to disable global auto-archive.</p>
                        </div>
                        
                        <div style="display: flex; gap: 16px;">
                            <button type="submit" class="btn-primary" style="flex: 1; background: linear-gradient(135deg, #10b981, #059669); border-color: transparent;"><i class="fas fa-save" style="margin-right: 8px;"></i> Save Rules</button>
                            <button type="button" class="btn-outline-danger" style="flex: 1;" onclick="showAdminAlert('This will archive all qualifying messages across the system immediately.')"><i class="fas fa-archive" style="margin-right: 8px;"></i> Run Archive</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
