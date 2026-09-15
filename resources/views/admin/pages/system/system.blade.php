@extends('admin.base-admin')

@section('admin-section', 'system')

@section('content')
<div class="content-section active" id="system">
    {{-- Page Header --}}
    <div class="admin-card-header-bar">
        <h2 class="admin-page-title">
            <span class="admin-title-icon-box">
                <i class="fas fa-cogs"></i>
            </span>
            System Controls
        </h2>
    </div>

    {{-- Grid Row 1 --}}
    <div class="admin-system-grid-2">
        {{-- System Lock --}}
        <div class="db-card admin-system-card">
            <div class="admin-card-header">
                <div class="admin-card-header-left">
                    <i class="fas fa-lock admin-card-header-icon"></i>
                    <h3 class="admin-card-header-title">System Lock</h3>
                </div>
            </div>
            <div class="admin-card-inner-body">
                <p class="admin-card-text">Lock the entire system to prevent user access during maintenance.</p>
                <form method="POST" action="{{ route('admin.toggleLock') }}">
                    @csrf
                    @if($systemSettings['app_locked'] ?? false)
                        <div class="admin-alert-box-danger">
                            <i class="fas fa-exclamation-triangle"></i> System is currently <strong>LOCKED</strong>.
                        </div>
                        <button type="submit" class="admin-btn-action-primary">
                            <i class="fas fa-lock-open"></i> Unlock System
                        </button>
                    @else
                        <div class="admin-alert-box-success">
                            <i class="fas fa-check-circle"></i> System is currently <strong>OPEN</strong>.
                        </div>
                        <button type="submit" class="admin-btn-action-primary">
                            <i class="fas fa-lock"></i> Lock System
                        </button>
                    @endif
                </form>
            </div>
        </div>

        {{-- User Registration Control --}}
        <div class="db-card admin-system-card">
            <div class="admin-card-header">
                <div class="admin-card-header-left">
                    <i class="fas fa-user-plus admin-card-header-icon"></i>
                    <h3 class="admin-card-header-title">Registration Control</h3>
                </div>
            </div>
            <div class="admin-card-inner-body">
                <p class="admin-card-text">Enable or disable new user registrations.</p>
                <form method="POST" action="{{ route('admin.toggleRegistration') }}">
                    @csrf
                    @if($systemSettings['registration_disabled'] ?? false)
                        <div class="admin-alert-box-danger">
                            <i class="fas fa-times-circle"></i> Registration is <strong>DISABLED</strong>.
                        </div>
                        <button type="submit" class="admin-btn-action-primary">
                            <i class="fas fa-user-check"></i> Enable Registration
                        </button>
                    @else
                        <div class="admin-alert-box-success">
                            <i class="fas fa-check-circle"></i> Registration is <strong>OPEN</strong>.
                        </div>
                        <button type="submit" class="admin-btn-action-secondary">
                            <i class="fas fa-user-slash"></i> Disable Registration
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Grid Row 2 --}}
    <div class="admin-system-grid-3">
        {{-- AI Logs Management --}}
        <div class="db-card admin-system-card">
            <div class="admin-card-header">
                <div class="admin-card-header-left">
                    <i class="fas fa-robot admin-card-header-icon"></i>
                    <h3 class="admin-card-header-title">AI Logs Management</h3>
                </div>
            </div>
            <div class="admin-card-inner-body">
                <p class="admin-card-text">Bulk delete old AI usage logs to save database space.</p>
                <div class="admin-card-actions-col">
                    <form method="POST" action="{{ route('admin.aiLogs.delete') }}">
                        @csrf
                        <input type="hidden" name="duration" value="week">
                        <button type="button" onclick="var form = this.closest('form'); showAdminConfirm('Are you sure you want to delete AI usage logs older than 1 week?', function() { form.submit(); });" 
                                class="admin-btn-action-secondary">
                            <i class="fas fa-trash-alt"></i> Delete > 1 Week Old
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.aiLogs.delete') }}">
                        @csrf
                        <input type="hidden" name="duration" value="month">
                        <button type="button" onclick="var form = this.closest('form'); showAdminConfirm('Are you sure you want to delete AI usage logs older than 1 month?', function() { form.submit(); });" 
                                class="admin-btn-action-primary">
                            <i class="fas fa-trash-alt"></i> Delete > 1 Month Old
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Database Management --}}
        <div class="db-card admin-system-card">
            <div class="admin-card-header">
                <div class="admin-card-header-left">
                    <i class="fas fa-database admin-card-header-icon"></i>
                    <h3 class="admin-card-header-title">Database</h3>
                </div>
            </div>
            <div class="admin-card-inner-body">
                <p class="admin-card-text">Perform database operations and maintenance.</p>
                <div class="admin-github-badge">
                    <span><strong>Last Backup:</strong></span> 
                    {{ $systemSettings['last_backup_at'] ? \Carbon\Carbon::parse($systemSettings['last_backup_at'])->format('M j, Y g:i A') : 'Never' }}
                </div>
                <div class="admin-card-actions-row">
                    <form method="POST" action="{{ route('admin.backup') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="admin-btn-action-secondary">
                            <i class="fas fa-download"></i> Backup
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.optimizeDatabase') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="admin-btn-action-primary">
                            <i class="fas fa-rocket"></i> Optimize
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- System Updates --}}
        @php
            $ghRepoUrl  = \Illuminate\Support\Facades\DB::table('system_settings')->where('key','github_repo_url')->value('value') ?? '';
            $ghCloneUrl = \Illuminate\Support\Facades\DB::table('system_settings')->where('key','github_clone_url')->value('value') ?? '';
            $ghBranch   = \Illuminate\Support\Facades\DB::table('system_settings')->where('key','github_branch')->value('value') ?: 'main';
            $ghHasPat   = (bool)(\Illuminate\Support\Facades\DB::table('system_settings')->where('key','github_pat')->value('value'));
            $ghConfigured = !empty($ghRepoUrl) && !empty($ghCloneUrl);
        @endphp
        <div class="db-card admin-system-card">
            <div class="admin-card-header">
                <div class="admin-card-header-left">
                    <i class="fas fa-sync-alt admin-card-header-icon"></i>
                    <h3 class="admin-card-header-title">System Updates</h3>
                </div>
                <button type="button" id="ghSettingsBtn" onclick="openGithubModal()"
                    title="{{ $ghConfigured ? 'Edit GitHub Settings' : 'Add GitHub Repository' }}"
                    class="admin-btn-icon-soft">
                    <i class="fas {{ $ghConfigured ? 'fa-pen' : 'fa-plus' }}"></i>
                </button>
            </div>
            <div class="admin-card-inner-body">
                <p class="admin-card-text">Check for and install system updates from GitHub.</p>

                @if($ghConfigured)
                    <div class="admin-github-badge">
                        <i class="fab fa-github admin-card-header-icon"></i>
                        <span id="ghRepoDisplay">{{ parse_url($ghRepoUrl, PHP_URL_PATH) }}</span>
                        <span class="ml-auto text-muted">{{ $ghBranch }}</span>
                    </div>
                @else
                    <div class="admin-alert-box-success">
                        <i class="fas fa-exclamation-triangle"></i> No GitHub repository configured yet.
                    </div>
                @endif

                <div id="ghCheckResult" class="hidden admin-alert-box-success"></div>

                <div id="ghLogArea" class="hidden admin-github-log"></div>

                <div class="admin-card-actions-row">
                    <button type="button" id="ghCheckBtn" onclick="githubCheck()" class="admin-btn-action-secondary">
                        <i class="fas fa-search" id="ghCheckIcon"></i> Check
                    </button>
                    <button type="button" id="ghUpdateBtn" onclick="githubUpdate()" class="admin-btn-action-secondary">
                        <i class="fas fa-sync-alt" id="ghUpdateIcon"></i> Update
                    </button>
                    <form method="POST" action="{{ route('admin.toggleUpdates') }}" class="flex-1">
                        @csrf
                        @if($systemSettings['enable_system_updates'] ?? false)
                            <button type="submit" class="admin-btn-action-secondary">Disable</button>
                        @else
                            <button type="submit" class="admin-btn-action-primary">Enable</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ===================== GITHUB SETTINGS MODAL ===================== --}}
<div id="githubModal" class="github-modal-backdrop">
    <div class="github-modal-box">
        {{-- Modal Header --}}
        <div class="github-modal-header">
            <div class="admin-card-header-left">
                <div class="github-modal-icon">
                    <i class="fab fa-github"></i>
                </div>
                <div>
                    <h3 class="admin-card-header-title">GitHub Repository Settings</h3>
                    <p class="admin-card-text margin-0">Connect your private repository for live updates</p>
                </div>
            </div>
            <button onclick="closeGithubModal()" class="github-modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        {{-- Modal Body --}}
        <div class="github-modal-body">
            <div>
                <label class="github-modal-label">Repository URL <span class="text-danger">*</span></label>
                <input type="url" id="ghInputRepoUrl" placeholder="https://github.com/your-org/wisp"
                    class="github-modal-input"
                    value="{{ $ghRepoUrl }}">
            </div>
            <div>
                <label class="github-modal-label">Clone URL <span class="text-danger">*</span></label>
                <input type="url" id="ghInputCloneUrl" placeholder="https://github.com/your-org/wisp.git"
                    class="github-modal-input"
                    value="{{ $ghCloneUrl }}">
            </div>
            <div class="admin-grid-2col">
                <div>
                    <label class="github-modal-label">Branch</label>
                    <input type="text" id="ghInputBranch" placeholder="main"
                        class="github-modal-input"
                        value="{{ $ghBranch }}">
                </div>
                <div>
                    <label class="github-modal-label">Access Token</label>
                    <input type="password" id="ghInputPat" placeholder="{{ $ghHasPat ? '•••••••••••' : 'ghp_xxxxxxxxxxxx' }}"
                        class="github-modal-input">
                </div>
            </div>
            <div id="ghSaveError" class="hidden admin-alert-box-danger"></div>
        </div>
        {{-- Modal Footer --}}
        <div class="github-modal-footer">
            <button onclick="closeGithubModal()" type="button" class="admin-btn-secondary">
                Cancel
            </button>
            <button onclick="saveGithubSettings()" type="button" id="ghSaveBtn" class="admin-btn-primary">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var CSRF = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var CHECK_URL  = "{{ route('admin.github.check') }}";
    var UPDATE_URL = "{{ route('admin.github.update') }}";
    var SAVE_URL   = "{{ route('admin.github.settings') }}";

    /* ---- Modal ---- */
    window.openGithubModal = function () {
        var m = document.getElementById('githubModal');
        m.style.display = 'flex';
        document.getElementById('ghSaveError').classList.add('hidden');
    };
    window.closeGithubModal = function () {
        document.getElementById('githubModal').style.display = 'none';
    };
    document.getElementById('githubModal').addEventListener('click', function (e) {
        if (e.target === this) closeGithubModal();
    });

    /* ---- Save Settings ---- */
    window.saveGithubSettings = function () {
        var btn   = document.getElementById('ghSaveBtn');
        var errEl = document.getElementById('ghSaveError');
        var repo  = document.getElementById('ghInputRepoUrl').value.trim();
        var clone = document.getElementById('ghInputCloneUrl').value.trim();
        var branch= document.getElementById('ghInputBranch').value.trim() || 'main';
        var pat   = document.getElementById('ghInputPat').value;

        errEl.classList.add('hidden');
        if (!repo || !clone) { showErr('Repository URL and Clone URL are required.'); return; }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        fetch(SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ repo_url: repo, clone_url: clone, branch: branch, pat: pat || undefined })
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { showErr(data.error); return; }
            closeGithubModal();
            // Update the pencil icon
            var settingsBtn = document.getElementById('ghSettingsBtn');
            if (settingsBtn) settingsBtn.querySelector('i').className = 'fas fa-pen text-sm';
            // Reload page to refresh displayed values
            location.reload();
        })
        .catch(() => showErr('Network error. Please try again.'))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Save Settings'; });

        function showErr(msg) {
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
        }
    };

    /* ---- Check for Updates ---- */
    window.githubCheck = function () {
        var btn     = document.getElementById('ghCheckBtn');
        var icon    = document.getElementById('ghCheckIcon');
        var result  = document.getElementById('ghCheckResult');

        btn.disabled = true;
        icon.className = 'fas fa-spinner fa-spin';
        result.className = 'px-4 py-3 rounded-xl text-sm font-medium border';

        fetch(CHECK_URL, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
        .then(r => r.json())
        .then(data => {
            result.classList.remove('hidden');
            if (data.error) {
                result.className += ' bg-rose-50 border-rose-200 text-rose-700';
                result.innerHTML = '<i class="fas fa-exclamation-circle mr-1.5"></i>' + data.error;
                return;
            }
            if (data.up_to_date) {
                result.className += ' bg-emerald-50 border-emerald-200 text-emerald-700';
                result.innerHTML = '<i class="fas fa-check-circle mr-1.5"></i><strong>Up to date!</strong> Current: <code class="bg-emerald-100 px-1 rounded">' + (data.local_sha || 'unknown') + '</code>';
            } else {
                result.className += ' bg-amber-50 border-amber-200 text-amber-700';
                result.innerHTML = '<i class="fas fa-exclamation-triangle mr-1.5"></i><strong>Updates available</strong> on <em>' + data.branch + '</em>. Latest: <code class="bg-amber-100 px-1 rounded">' + (data.latest_sha || '?') + '</code>'
                    + (data.latest_msg ? '<br><span class="text-xs mt-1 block opacity-75">' + data.latest_msg.split('\n')[0] + '</span>' : '');
            }
        })
        .catch(() => {
            result.classList.remove('hidden');
            result.className += ' bg-rose-50 border-rose-200 text-rose-700';
            result.innerHTML = '<i class="fas fa-exclamation-circle mr-1.5"></i>Network error. Check your connection.';
        })
        .finally(() => { btn.disabled = false; icon.className = 'fas fa-search'; });
    };

    /* ---- Run Update ---- */
    window.githubUpdate = function () {
        showAdminConfirm('This will run git pull on the server. Are you sure you want to update now?', function() {
            var btn    = document.getElementById('ghUpdateBtn');
            var icon   = document.getElementById('ghUpdateIcon');
            var logEl  = document.getElementById('ghLogArea');

            btn.disabled = true;
            icon.className = 'fas fa-spinner fa-spin';
            logEl.classList.remove('hidden');
            logEl.textContent = '⏳ Starting update...\n';

            fetch(UPDATE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(data => {
                var lines = data.log || [];
                logEl.textContent = lines.join('\n');
                logEl.scrollTop = logEl.scrollHeight;

                if (data.error) {
                    logEl.textContent += '\n\n❌ ERROR: ' + data.error;
                    logEl.style.color = '#f87171';
                } else {
                    logEl.style.color = '#34d399';
                    // Reload page after 2s to reflect new code
                    setTimeout(() => location.reload(), 2000);
                }
            })
            .catch(() => {
                logEl.textContent += '\n❌ Network error during update.';
                logEl.style.color = '#f87171';
            })
            .finally(() => { btn.disabled = false; icon.className = 'fas fa-download'; });
        });
    };
})();
</script>

@endsection

