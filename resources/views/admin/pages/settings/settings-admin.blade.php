@extends('admin.base-admin')

@section('admin-section', 'settings-user')

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
    
    /* Subtle background accent */
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
    .profile-picture-settings {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }
    .profile-picture-current {
        position: relative;
        width: 64px;
        height: 64px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
        flex-shrink: 0;
    }
    .profile-picture-current img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .file-upload {
        position: relative;
        overflow: hidden;
        display: inline-block;
        flex: 1;
    }
    .file-upload-input {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        height: 100%;
        width: 100%;
    }
    .file-upload-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: transparent;
        border: 1px solid var(--primary);
        border-radius: 8px;
        color: var(--primary);
        font-weight: 500;
        cursor: pointer;
        transition: 0.2s;
    }
    .file-upload:hover .file-upload-label {
        background: var(--primary-light);
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="content-section active" id="settings-user">
    <div class="general-dashboard-wrapper">
        
        <!-- Header Section -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 8px;">
            <div>
                <h2 style="font-weight: 800; color: var(--text-dark, #0f172a); font-size: 2rem; margin: 0 0 8px 0; letter-spacing: -0.02em;">
                    Admin Settings
                </h2>
                <p style="color: var(--text-muted); font-size: 1.05rem; margin: 0;">Manage your admin account details and security preferences.</p>
            </div>
        </div>

        <div class="general-layout-grid">
            
            <!-- Left Column: Account Info -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                <!-- Account Information -->
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4f46e5;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Account Information</h4>
                            <p>Update your profile details and credentials.</p>
                        </div>
                    </div>
                    
                    @php $adminUser = auth()->user(); @endphp
                    <form method="POST" action="{{ route('admin.account') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="adminUsername">Username</label>
                            <input type="text" class="form-control" id="adminUsername" name="username" value="{{ old('username', $adminUser->username ?? '') }}">
                        </div>
                        <div class="form-group mb-4">
                            <label for="adminEmail">Email Address</label>
                            <input type="email" class="form-control" id="adminEmail" name="email" value="{{ old('email', $adminUser->email ?? '') }}">
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                            <div class="form-group mb-0">
                                <label for="adminPassword">New Password</label>
                                <input type="password" class="form-control" id="adminPassword" name="password" placeholder="Leave blank to keep">
                            </div>
                            <div class="form-group mb-0">
                                <label for="adminPasswordConfirmation">Confirm Password</label>
                                <input type="password" class="form-control" id="adminPasswordConfirmation" name="password_confirmation">
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="form-label">Profile Picture</label>
                            <div class="profile-picture-settings" style="background: var(--bg-subtle, #f8fafc); padding: 16px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05);">
                                <div class="profile-picture-current">
                                    <img id="adminAvatarPreview" src="{{ route('profile.picture') }}" alt="Current profile" data-fallback-src="{{ asset('img/logo.png') }}" onerror="if(this.dataset.fallbackSrc) this.src=this.dataset.fallbackSrc">
                                </div>
                                <div class="file-upload">
                                    <div class="file-upload-label btn-outline-primary" style="padding: 8px 16px; display: inline-flex;">
                                        <i class="fas fa-cloud-upload-alt"></i> Upload New Image
                                    </div>
                                    <input type="file" class="file-upload-input" name="profile_picture" accept="image/*" id="adminProfilePicture">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; background: linear-gradient(135deg, #6366f1, #4f46e5); border-color: transparent;"><i class="fas fa-save" style="margin-right: 8px;"></i> Update Account Details</button>
                    </form>
                </div>
            </div>
            
            <!-- Right Column: Security & Data -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                <!-- Security / 2FA -->
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>2FA Security Lock</h4>
                            <p>Add an extra layer of protection to your dashboard.</p>
                        </div>
                    </div>
                    
                    <div style="background: var(--bg-subtle, #f8fafc); padding: 24px; border-radius: 16px; border: 1px dashed var(--border-color, rgba(0,0,0,0.1)); text-align: center; margin-bottom: 8px;">
                        <div style="width: 64px; height: 64px; background: var(--inner-card-bg, #ffffff); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); color: {{ !empty($adminUser->settings->login_passcode) ? '#10b981' : '#cbd5e1' }}; font-size: 1.5rem;">
                            <i class="fas {{ !empty($adminUser->settings->login_passcode) ? 'fa-lock' : 'fa-lock-open' }}"></i>
                        </div>
                        <h5 style="font-weight: 700; margin-bottom: 8px; color: var(--text-dark);">{{ !empty($adminUser->settings->login_passcode) ? 'Dashboard is Protected' : 'Dashboard is Unlocked' }}</h5>
                        <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 24px;">A custom passcode ensures only you can access your private dashboard.</p>
                        
                        <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
                            <button type="button" class="btn-primary" onclick="openPasscodeModal()" style="background: linear-gradient(135deg, #f59e0b, #d97706); border-color: transparent;">
                                <i class="fas fa-key" style="margin-right: 6px;"></i>
                                {{ !empty($adminUser->settings->login_passcode) ? 'Update Passcode' : 'Set a Passcode' }}
                            </button>

                            @if(!empty($adminUser->settings->login_passcode))
                                <form method="POST" action="{{ route('admin.settings.passcode') }}" style="margin: 0;">
                                    @csrf
                                    <input type="hidden" name="login_passcode" value="">
                                    <input type="hidden" name="login_passcode_confirmation" value="">
                                    <button type="submit" class="btn-outline-danger">
                                        <i class="fas fa-lock-open" style="margin-right: 6px;"></i> Disable
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Passcode Modal Overlay -->
<div id="passcode-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card, #ffffff); width: 100%; max-width: 450px; border-radius: 24px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); position: relative; animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
        <button type="button" onclick="closePasscodeModal()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; color: var(--text-muted, #94a3b8); font-size: 1.2rem; cursor: pointer;">
            <i class="fas fa-times"></i>
        </button>

        <form id="passcode-form" method="POST" action="{{ route('admin.settings.passcode') }}">
            @csrf
            <!-- Step 1 -->
            <div id="passcode-step-1" style="text-align: center;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px;">
                    <i class="fas fa-lock"></i>
                </div>
                <h4 style="font-weight: 800; font-size: 1.5rem; color: var(--text-dark); margin-bottom: 12px;">Hello {{ explode(' ', $adminUser->name ?? $adminUser->username)[0] }},</h4>
                <p style="color: var(--text-muted); font-size: 1rem; line-height: 1.6; margin-bottom: 32px;">Please enter a secure passcode to lock your dashboard.</p>

                <input type="password" id="modal-passcode" name="login_passcode" class="form-control" placeholder="••••" minlength="4" required style="text-align: center; letter-spacing: 8px; font-size: 1.5rem; padding: 16px; border-radius: 12px; margin-bottom: 32px; background: var(--bg-subtle, #f8fafc);">
                
                <button type="button" class="btn-primary" onclick="nextPasscodeStep()" style="width: 100%; padding: 14px; font-size: 1.05rem;">Next <i class="fas fa-arrow-right" style="margin-left: 8px;"></i></button>
            </div>

            <!-- Step 2 -->
            <div id="passcode-step-2" style="display: none; text-align: center;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4 style="font-weight: 800; font-size: 1.5rem; color: var(--text-dark); margin-bottom: 12px;">Confirm Passcode</h4>
                <p style="color: var(--text-muted); font-size: 1rem; line-height: 1.6; margin-bottom: 32px;">Re-enter the passcode to verify it.</p>
                
                <input type="password" id="modal-passcode-confirm" name="login_passcode_confirmation" class="form-control" placeholder="••••" minlength="4" required style="text-align: center; letter-spacing: 8px; font-size: 1.5rem; padding: 16px; border-radius: 12px; margin-bottom: 32px; background: var(--bg-subtle, #f8fafc);">
                
                <div style="display: flex; gap: 16px;">
                    <button type="button" class="btn-outline-primary" onclick="prevPasscodeStep()" style="flex: 1; padding: 14px; font-size: 1rem;">Back</button>
                    <button type="submit" class="btn-primary" style="width: 100%; background: linear-gradient(135deg, #ec4899, #db2777); border-color: transparent;"><i class="fas fa-key" style="margin-right: 8px;"></i> Update Password</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('adminProfilePicture').addEventListener('change', function(e) {
            if(e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('adminAvatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    });

    function openPasscodeModal() {
        document.getElementById('passcode-modal').style.display = 'flex';
        document.getElementById('modal-passcode').value = '';
        document.getElementById('modal-passcode-confirm').value = '';
        document.getElementById('passcode-step-1').style.display = 'block';
        document.getElementById('passcode-step-2').style.display = 'none';
        setTimeout(() => document.getElementById('modal-passcode').focus(), 100);
    }

    function closePasscodeModal() {
        document.getElementById('passcode-modal').style.display = 'none';
    }

    function nextPasscodeStep() {
        const p1 = document.getElementById('modal-passcode');
        if(p1.value.length >= 4) {
            document.getElementById('passcode-step-1').style.display = 'none';
            document.getElementById('passcode-step-2').style.display = 'block';
            document.getElementById('modal-passcode-confirm').focus();
        } else {
            p1.reportValidity();
        }
    }

    function prevPasscodeStep() {
        document.getElementById('passcode-step-1').style.display = 'block';
        document.getElementById('passcode-step-2').style.display = 'none';
        document.getElementById('modal-passcode-confirm').value = '';
        document.getElementById('modal-passcode').focus();
    }

    document.getElementById('modal-passcode').addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            nextPasscodeStep();
        }
    });
</script>
@endsection
