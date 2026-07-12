@extends('user.base-user')

@section('user-section', 'settings-user')

@section('content')

<div class="content-section active" id="settings-user">
    <div class="settings-dashboard-wrapper">
        
        <!-- Header Section -->
        <div class="settings-header-flex">
            <div>
                <h2 class="settings-header-title">
                    User Settings
                </h2>
                <p class="settings-header-desc">Manage your account details, notifications, and security preferences.</p>
            </div>
        </div>

        <div class="settings-layout-grid">
            
            <!-- Left Column: Account Info & Notifications -->
            <div class="settings-column-flex">
                
                <!-- Account Information -->
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon icon-bg-indigo">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Account Information</h4>
                            <p>Update your profile details and credentials.</p>
                        </div>
                    </div>
                    
                    <form id="account-settings-form" method="POST" action="{{ route('settings.account') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username ?? '') }}">
                        </div>
                        <div class="form-group mb-4">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email ?? '') }}">
                        </div>
                        
                        <div class="settings-grid-2">
                            <div class="form-group mb-0">
                                <label for="password">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep">
                            </div>
                            <div class="form-group mb-0">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="form-label">Profile Picture</label>
                            <div class="profile-picture-container">
                                <div class="profile-picture-current user-settings-inline-1" >
                                    <img id="current-profile-picture" src="{{ route('profile.picture') }}" alt="Current profile" data-fallback-src="https://ui-avatars.com/api/?name={{ urlencode($user->username ?? 'User') }}&color=7F9CF5&background=EBF4FF" onerror="if(this.dataset.fallbackSrc) this.src=this.dataset.fallbackSrc" class="profile-picture-img">
                                </div>
                                <div class="file-upload user-settings-inline-2" >
                                    <div class="file-upload-label btn btn-outline-primary file-upload-btn">
                                        <i class="fas fa-cloud-upload-alt"></i> Upload New Image
                                    </div>
                                    <input type="file" class="file-upload-input user-settings-inline-3" name="profile_picture" accept="image/*" id="profile_picture_input" >
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save icon-margin-right"></i> Update Account</button>
                    </form>
                </div>
                
                <!-- Notification Preferences -->
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon icon-bg-emerald">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Notification Preferences</h4>
                            <p>Manage how you receive alerts and updates.</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('user.settings.general') }}">
                        @csrf
                        <div class="switch-container">
                            <input class="form-check-input" type="checkbox" name="page_view_alerts" id="pageViewAlerts" value="1" {{ ($userSettings->page_view_alerts ?? true) ? 'checked' : '' }}>
                            <div>
                                <label class="form-check-label switch-label" for="pageViewAlerts">Email Alerts for Page Views</label>
                                <span class="switch-desc">Receive an email when someone views your messages.</span>
                            </div>
                        </div>

                        <div class="switch-container mb-24">
                            <input class="form-check-input" type="checkbox" name="whatsapp_notifications" id="whatsappNotifs" value="1" {{ ($userSettings->whatsapp_notifications ?? false) ? 'checked' : '' }}>
                            <div>
                                <label class="form-check-label switch-label" for="whatsappNotifs">
                                    WhatsApp Notifications 
                                    <span class="badge bg-success badge-beta">BETA</span>
                                </label>
                                <span class="switch-desc">Get notified via WhatsApp when events occur.</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-check icon-margin-right"></i> Save Preferences</button>
                    </form>
                </div>
                
            </div>
            
            <!-- Right Column: Security & Data -->
            <div class="settings-column-flex">
                
                <!-- Security / 2FA -->
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon icon-bg-amber">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>2FA Security Lock</h4>
                            <p>Add an extra layer of protection to your dashboard.</p>
                        </div>
                    </div>
                    
                    <div class="security-status-box">
                        <div class="security-status-icon-wrapper" style="color: {{ !empty($user->settings->login_passcode) ? '#10b981' : '#cbd5e1' }};">
                            <i class="fas {{ !empty($user->settings->login_passcode) ? 'fa-lock' : 'fa-lock-open' }}"></i>
                        </div>
                        <h5 class="security-status-title">{{ !empty($user->settings->login_passcode) ? 'Dashboard is Protected' : 'Dashboard is Unlocked' }}</h5>
                        <p class="security-status-desc">A custom passcode ensures only you can access your private dashboard.</p>
                        
                        <div class="security-action-flex">
                            <button type="button" class="btn btn-primary" onclick="openPasscodeModal()">
                                <i class="fas fa-key icon-margin-right"></i>
                                {{ !empty($user->settings->login_passcode) ? 'Update Passcode' : 'Set a Passcode' }}
                            </button>

                            @if(!empty($user->settings->login_passcode))
                                <form method="POST" action="{{ route('user.settings.passcode') }}"  class="user-settings-inline-4">
                                    @csrf
                                    <input type="hidden" name="login_passcode" value="">
                                    <input type="hidden" name="login_passcode_confirmation" value="">
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="fas fa-lock-open icon-margin-right"></i> Disable
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Privacy Mode -->
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon icon-bg-indigo">
                            <i class="fas fa-eye-slash"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Privacy Mode</h4>
                            <p>Blur message previews in "My Messages" for public browsing.</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('settings.hub.privacy_theme') ?? '#' }}">
                        @csrf
                        <input type="hidden" name="theme_preference" value="{{ $userSettings->theme_preference ?? 'theme-default' }}">
                        <div class="form-check form-switch mt-2" style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                            <input class="form-check-input" type="checkbox" name="privacy_blur_enabled" id="privacyBlur" {{ ($userSettings->privacy_blur_enabled ?? false) ? 'checked' : '' }} style="width: 2.5em; height: 1.3em; cursor: pointer; flex-shrink: 0;">
                            <label class="form-check-label" for="privacyBlur" style="font-size: 0.95rem; font-weight: 500; color: #334155; cursor: pointer; margin: 0;">Enable Blur UI</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Privacy</button>
                    </form>
                </div>

                <!-- Appearance & Themes (moved from General Settings) -->
                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon icon-bg-pink">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Appearance &amp; Themes</h4>
                            <p>Personalize your dashboard experience across 3 exclusive palettes.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('settings.hub.privacy_theme') ?? '#' }}" id="user-settings-theme-form">
                        @csrf
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <input type="hidden" name="privacy_blur_enabled" value="{{ ($userSettings->privacy_blur_enabled ?? false) ? '1' : '0' }}">
                            <div class="form-check form-switch" style="display: flex; align-items: center; gap: 8px;">
                                <input class="form-check-input" type="checkbox" name="theme_bg_enabled" id="themeBgEnabled" value="1" {{ ($userSettings->theme_bg_enabled ?? true) ? 'checked' : '' }} style="cursor: pointer; width: 2.2em; height: 1.1em; margin: 0;">
                                <label class="form-check-label" for="themeBgEnabled" style="font-size: 0.85rem; color: var(--text-muted); cursor: pointer; margin: 0;">Apply to background</label>
                            </div>
                        </div>
                        <input type="hidden" name="theme_preference" id="us_theme_preference_input" value="{{ $userSettings->theme_preference ?? 'theme-default' }}">
                        <div class="theme-grid" style="display:flex;gap:10px;flex-wrap:wrap;margin:16px 0;">
                            <div class="theme-preview {{ ($userSettings->theme_preference ?? 'theme-default') === 'theme-default' ? 'active' : '' }}" data-theme="theme-default">Indigo</div>
                            <div class="theme-preview {{ ($userSettings->theme_preference ?? '') === 'theme-forest'  ? 'active' : '' }}" data-theme="theme-forest">Emerald</div>
                            <div class="theme-preview {{ ($userSettings->theme_preference ?? '') === 'theme-crimson' ? 'active' : '' }}" data-theme="theme-crimson">Crimson</div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paint-brush"></i> Apply Theme</button>
                    </form>
                </div>

                <!-- Account Data & Deletion -->

                <div class="hub-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon icon-bg-pink">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>Data & Privacy</h4>
                            <p>Download your personal data or request deletion.</p>
                        </div>
                    </div>
                    
                    <div class="data-box-flex">
                        <div class="data-row">
                            <div>
                                <strong class="data-row-title">Download Data Archive</strong>
                                <span class="data-row-desc">Get a copy of all your messages and settings.</span>
                            </div>
                            <a href="{{ route('user.settings.download-data') }}" class="btn btn-outline-primary user-settings-inline-5" >
                                <i class="fas fa-download icon-margin-right"></i> Download
                            </a>
                        </div>
                        
                        <div class="data-row-danger">
                            <div>
                                <strong class="data-row-danger-title">Delete Account</strong>
                                <span class="data-row-danger-desc">Permanently remove all your data.</span>
                            </div>
                            <button type="button" class="btn btn-outline-danger user-settings-inline-6" onclick="openDeleteAccountModal()" >
                                <i class="fas fa-trash-alt icon-margin-right"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- System Update (Conditional) -->
                @php
                    $enableUpdates = \Illuminate\Support\Facades\DB::table('system_settings')->where('key', 'enable_system_updates')->value('value') === '1';
                    $githubUrl = \Illuminate\Support\Facades\DB::table('system_settings')->where('key', 'github_update_url')->value('value');
                @endphp

                @if($enableUpdates)
                <div class="hub-card" id="system-update-card">
                    <div class="hub-card-header">
                        <div class="hub-card-icon icon-bg-blue">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="hub-card-header-text">
                            <h4>System Update</h4>
                            <p>An update is available from the repository.</p>
                        </div>
                    </div>
                    
                    <div class="data-box-flex">
                        <div class="data-row-info">
                            <div>
                                <strong class="data-row-info-title">Update Status: Available</strong>
                                <span class="data-row-info-desc">Fetch the current status of the update and install it.</span>
                            </div>
                            <button type="button" onclick="startSystemUpdate()" class="btn btn-primary btn-blue user-settings-inline-7" >
                                <i class="fas fa-cloud-download-alt icon-margin-right"></i> Update Page
                            </button>
                        </div>
                    </div>
                </div>
                @endif
                
            </div>
            
        </div>
    </div>
</div>

<!-- System Update Loading Overlay -->
<div id="update-loading-overlay"  class="user-settings-inline-8">
    
    <!-- Progress Circle -->
    <div  class="user-settings-inline-9">
        <svg viewBox="0 0 100 100"  class="user-settings-inline-10">
            <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(255, 255, 255, 0.1)" stroke-width="8"></circle>
            <circle id="update-progress-circle" cx="50" cy="50" r="45" fill="none" stroke="#3b82f6" stroke-width="8" stroke-linecap="round" stroke-dasharray="283" stroke-dashoffset="283"  class="user-settings-inline-11"></circle>
        </svg>
        <div id="update-progress-text"  class="user-settings-inline-12">
            0%
        </div>
    </div>

    <h2  class="user-settings-inline-13">Updating Wisp</h2>
    <p id="update-status-text"  class="user-settings-inline-14">Fetching update packages...</p>
</div>

<!-- Delete Account Modal -->
<div id="delete-account-modal"  class="user-settings-inline-15">
    <div  class="user-settings-inline-16">
        <button type="button" onclick="closeDeleteAccountModal()"  class="user-settings-inline-17">
            <i class="fas fa-times"></i>
        </button>

        <div  class="user-settings-inline-18">
            <div  class="user-settings-inline-19">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3  class="user-settings-inline-20">Delete Account?</h3>
            <p  class="user-settings-inline-21">This action is permanent and will delete all your messages, media, and settings. You cannot undo this.</p>
        </div>

        <form action="{{ route('user.settings.delete-account') }}" method="POST">
            @csrf
            <div class="form-group mb-4">
                <label  class="user-settings-inline-22">Confirm Username</label>
                <input type="text" name="confirm_username" class="form-control" placeholder="{{ \Auth::user()->username }}" required class="user-setting-input">
            </div>

            <div class="form-group mb-5">
                <label  class="user-settings-inline-23">Enter Passcode (2FA)</label>
                <input type="password" name="confirm_passcode" class="form-control user-settings-inline-24" placeholder="••••••" required >
            </div>

            <div  class="user-settings-inline-25">
                <button type="button" class="btn user-settings-inline-26" onclick="closeDeleteAccountModal()" >Cancel</button>
                <button type="submit" class="btn btn-danger user-settings-inline-27" >Delete</button>
            </div>
        </form>
    </div>
</div>

<!-- Passcode Modal Overlay -->
<div id="passcode-modal"  class="user-settings-inline-28">
    <div  class="user-settings-inline-29">
        <button type="button" onclick="closePasscodeModal()"  class="user-settings-inline-30">
            <i class="fas fa-times"></i>
        </button>

        <form id="passcode-form" method="POST" action="{{ route('user.settings.passcode') }}">
            @csrf
            <!-- Step 1 -->
            <div id="passcode-step-1"  class="user-settings-inline-31">
                <div  class="user-settings-inline-32">
                    <i class="fas fa-lock"></i>
                </div>
                <h4  class="user-settings-inline-33">Hello {{ explode(' ', $user->name ?? $user->username)[0] }},</h4>
                <p  class="user-settings-inline-34">Please enter a secure passcode to lock your dashboard.</p>

                <input type="password" id="modal-passcode" name="login_passcode" class="form-control user-settings-inline-35" placeholder="••••" minlength="4" required >
                
                <button type="button" class="btn btn-primary user-settings-inline-36" onclick="nextPasscodeStep()" >Next <i class="fas fa-arrow-right user-settings-inline-37" ></i></button>
            </div>

            <!-- Step 2 -->
            <div id="passcode-step-2"  class="user-settings-inline-38">
                <div  class="user-settings-inline-39">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4  class="user-settings-inline-40">Confirm Passcode</h4>
                <p  class="user-settings-inline-41">Re-enter the passcode to verify it.</p>
                
                <input type="password" id="modal-passcode-confirm" name="login_passcode_confirmation" class="form-control user-settings-inline-42" placeholder="••••" minlength="4" required >
                
                <div  class="user-settings-inline-43">
                    <button type="button" class="btn btn-outline-primary user-settings-inline-44" onclick="prevPasscodeStep()" >Back</button>
                    <button type="submit" class="btn btn-primary user-settings-inline-45" >Create Passcode</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function startSystemUpdate() {
        var overlay     = document.getElementById('update-loading-overlay');
        var progressCircle = document.getElementById('update-progress-circle');
        var progressText   = document.getElementById('update-progress-text');
        var statusText     = document.getElementById('update-status-text');
        var CSRF = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var UPDATE_URL = "{{ route('user.github.update') }}";

        overlay.style.display = 'flex';
        progressCircle.style.strokeDashoffset = '283';
        progressText.innerText = '0%';
        statusText.innerText = 'Connecting to GitHub...';

        // Animate progress while waiting for server response
        var progress = 0;
        var fakeInterval = setInterval(function () {
            if (progress < 85) {
                progress += Math.random() * 3;
                var offset = 283 - (283 * Math.min(progress, 85) / 100);
                progressCircle.style.strokeDashoffset = offset;
                progressText.innerText = Math.floor(Math.min(progress, 85)) + '%';
                if (progress > 20 && progress < 60) statusText.innerText = 'Pulling latest code...';
                else if (progress >= 60) statusText.innerText = 'Applying updates...';
            }
        }, 120);

        fetch(UPDATE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            clearInterval(fakeInterval);

            if (data.error) {
                progressCircle.style.strokeDashoffset = '283';
                progressText.innerText = '!';
                progressText.style.color = '#ef4444';
                statusText.innerText = 'Error: ' + data.error;
                statusText.style.color = '#ef4444';
                setTimeout(function () { overlay.style.display = 'none'; progressText.style.color = ''; statusText.style.color = ''; }, 4000);
                return;
            }

            // Success — fill to 100%
            progressCircle.style.strokeDashoffset = '0';
            progressText.innerText = '100%';
            statusText.innerText = data.message || 'Update complete!';

            setTimeout(function () {
                statusText.innerText = 'Reloading page...';
                setTimeout(function () {
                    overlay.style.display = 'none';
                    location.reload();
                }, 1000);
            }, 1500);
        })
        .catch(function () {
            clearInterval(fakeInterval);
            progressText.innerText = '!';
            statusText.innerText = 'Network error. Please try again.';
            setTimeout(function () { overlay.style.display = 'none'; }, 3000);
        });
    }

    // Handle Custom File Upload Click
    document.querySelector('.file-upload-label').addEventListener('click', function() {
        document.getElementById('profile_picture_input').click();
    });

    // Modals
    function openDeleteAccountModal() {
        document.getElementById('delete-account-modal').style.display = 'flex';
    }

    function closeDeleteAccountModal() {
        document.getElementById('delete-account-modal').style.display = 'none';
    }

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
        if (p1.value.length >= 4) {
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

    // Allow Enter key to trigger Next step
    document.getElementById('modal-passcode').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            nextPasscodeStep();
        }
    });

    // Profile Picture Preview
    var input = document.getElementById('profile_picture_input');
    var img = document.getElementById('current-profile-picture');
    if (input && img) {
        input.addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (file && file.type.indexOf('image/') === 0) {
                var r = new FileReader();
                r.onload = function () { img.src = r.result; };
                r.readAsDataURL(file);
            }
        });
    }
    // Theme Selection for User Settings
    const usThemePreviews = document.querySelectorAll('#user-settings-theme-form .theme-preview');
    const usThemeInput = document.getElementById('us_theme_preference_input');
    
    usThemePreviews.forEach(preview => {
        preview.addEventListener('click', () => {
            usThemePreviews.forEach(p => p.classList.remove('active'));
            preview.classList.add('active');
            let theme = preview.dataset.theme;
            if (usThemeInput) usThemeInput.value = theme;
            
            // Live Preview of theme
            document.body.classList.remove('theme-default', 'theme-forest', 'theme-crimson');
            document.body.classList.add(theme);
        });
    });
</script>
@endsection