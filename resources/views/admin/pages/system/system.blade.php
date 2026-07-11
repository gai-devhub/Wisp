@extends('admin.base-admin')

@section('admin-section', 'system')

@section('content')
<div class="content-section active" id="system">
    <div class="flex items-center gap-4 mb-6">
        <h2 class="flex items-center gap-3 text-2xl font-bold text-slate-800 m-0">
            <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-cogs opacity-90"></i>
            </span>
            System Controls
        </h2>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 24px; margin-bottom: 24px;">
        {{-- System Lock --}}
        <div class="db-card overflow-hidden flex flex-col" style="flex: 1 1 300px;">
            <div class="px-6 py-5 border-b border-slate-200 flex items-center gap-2">
                <i class="fas fa-lock text-amber-500"></i>
                <h3 class="text-lg font-bold text-slate-800 m-0">System Lock</h3>
            </div>
            <div class="p-6 flex-1 flex flex-col">
                <p class="text-sm font-medium text-slate-500 mb-6">Lock the entire system to prevent user access during maintenance.</p>
                <form method="POST" action="{{ route('admin.toggleLock') }}" class="mt-auto m-0">
                    @csrf
                    @if($systemSettings['app_locked'] ?? false)
                        <div class="mb-4 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium">
                            <i class="fas fa-exclamation-triangle mr-1.5"></i> System is currently <strong>LOCKED</strong>.
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl cursor-pointer border-0 transition-all shadow-sm shadow-amber-200">Unlock System</button>
                    @else
                        <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                            <i class="fas fa-check-circle mr-1.5"></i> System is currently <strong>OPEN</strong>.
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-white border border-amber-200 text-amber-600 hover:bg-amber-50 font-bold rounded-xl cursor-pointer transition-colors shadow-sm">Lock System</button>
                    @endif
                </form>
            </div>
        </div>

        {{-- User Registration Control --}}
        <div class="db-card overflow-hidden flex flex-col" style="flex: 1 1 300px;">
            <div class="px-6 py-5 border-b border-slate-200 flex items-center gap-2">
                <i class="fas fa-user-plus text-indigo-500"></i>
                <h3 class="text-lg font-bold text-slate-800 m-0">Registration Control</h3>
            </div>
            <div class="p-6 flex-1 flex flex-col">
                <p class="text-sm font-medium text-slate-500 mb-6">Enable or disable new user registrations.</p>
                <form method="POST" action="{{ route('admin.toggleRegistration') }}" class="mt-auto m-0">
                    @csrf
                    @if($systemSettings['registration_disabled'] ?? false)
                        <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium">
                            <i class="fas fa-times-circle mr-1.5"></i> Registration is <strong>DISABLED</strong>.
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-indigo-200">Enable Registration</button>
                    @else
                        <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                            <i class="fas fa-check-circle mr-1.5"></i> Registration is <strong>OPEN</strong>.
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold rounded-xl cursor-pointer transition-colors shadow-sm">Disable Registration</button>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 24px; margin-bottom: 24px;">
        {{-- AI Logs Management --}}
        <div class="db-card overflow-hidden flex flex-col" style="flex: 1 1 300px;">
            <div class="px-6 py-5 border-b border-slate-200 flex items-center gap-2">
                <i class="fas fa-robot text-purple-500"></i>
                <h3 class="text-lg font-bold text-slate-800 m-0">AI Logs Management</h3>
            </div>
            <div class="p-6 flex-1 flex flex-col">
                <p class="text-sm font-medium text-slate-500 mb-6">Bulk delete old AI usage logs to save database space.</p>
                <div class="flex flex-col gap-3 mt-auto">
                    <form method="POST" action="{{ route('admin.aiLogs.delete') }}" class="w-full m-0">
                        @csrf
                        <input type="hidden" name="duration" value="week">
                        <button type="button" onclick="var form = this.closest('form'); showAdminConfirm('Are you sure you want to delete AI usage logs older than 1 week?', function() { form.submit(); });" class="w-full py-2.5 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold rounded-xl cursor-pointer transition-colors shadow-sm">
                            <i class="fas fa-trash-alt mr-1.5"></i> Delete > 1 Week Old
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.aiLogs.delete') }}" class="w-full m-0">
                        @csrf
                        <input type="hidden" name="duration" value="month">
                        <button type="button" onclick="var form = this.closest('form'); showAdminConfirm('Are you sure you want to delete AI usage logs older than 1 month?', function() { form.submit(); });" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-rose-200">
                            <i class="fas fa-trash-alt mr-1.5"></i> Delete > 1 Month Old
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Database Management --}}
        <div class="db-card overflow-hidden flex flex-col" style="flex: 1 1 300px;">
            <div class="px-6 py-5 border-b border-slate-200 flex items-center gap-2">
                <i class="fas fa-database text-sky-500"></i>
                <h3 class="text-lg font-bold text-slate-800 m-0">Database</h3>
            </div>
            <div class="p-6 flex-1 flex flex-col">
                <p class="text-sm font-medium text-slate-500 mb-6">Perform database operations and maintenance.</p>
                <div class="text-xs font-semibold text-slate-500 bg-slate-50 px-4 py-3 rounded-xl border border-slate-200 mb-4">
                    <span class="text-slate-700">Last Backup:</span> 
                    {{ $systemSettings['last_backup_at'] ? \Carbon\Carbon::parse($systemSettings['last_backup_at'])->format('M j, Y g:i A') : 'Never' }}
                </div>
                <div class="flex gap-3 mt-auto">
                    <form method="POST" action="{{ route('admin.backup') }}" class="flex-1 m-0">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-indigo-200">
                            <i class="fas fa-download mr-1.5"></i> Backup
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.optimizeDatabase') }}" class="flex-1 m-0">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-emerald-200">
                            <i class="fas fa-rocket mr-1.5"></i> Optimize
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
        <div class="db-card overflow-hidden flex flex-col" style="flex: 1 1 300px;">
            <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <i class="fas fa-sync-alt text-emerald-500"></i>
                    <h3 class="text-lg font-bold text-slate-800 m-0">System Updates</h3>
                </div>
                {{-- + / Pencil button --}}
                <button type="button" id="ghSettingsBtn" onclick="openGithubModal()"
                    title="{{ $ghConfigured ? 'Edit GitHub Settings' : 'Add GitHub Repository' }}"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300 transition-all cursor-pointer">
                    <i class="fas {{ $ghConfigured ? 'fa-pen' : 'fa-plus' }} text-sm"></i>
                </button>
            </div>
            <div class="p-6 flex-1 flex flex-col gap-4">
                <p class="text-sm font-medium text-slate-500 m-0">Check for and install system updates from GitHub.</p>

                {{-- Repo info badge --}}
                @if($ghConfigured)
                    <div class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono text-slate-600 flex items-center gap-2">
                        <i class="fab fa-github text-slate-400"></i>
                        <span id="ghRepoDisplay">{{ parse_url($ghRepoUrl, PHP_URL_PATH) }}</span>
                        <span class="ml-auto text-slate-400">{{ $ghBranch }}</span>
                    </div>
                @else
                    <div class="px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium">
                        <i class="fas fa-exclamation-triangle mr-1.5"></i> No GitHub repository configured yet.
                    </div>
                @endif

                {{-- Status result area (shown after Check) --}}
                <div id="ghCheckResult" class="hidden px-4 py-3 rounded-xl text-sm font-medium border"></div>

                {{-- Live log area (shown during/after Update) --}}
                <div id="ghLogArea" class="hidden mt-2 rounded-xl bg-slate-900 text-emerald-400 text-xs font-mono p-4 overflow-y-auto" style="max-height:200px; min-height:80px;"></div>

                <div class="flex gap-3 mt-auto flex-wrap">
                    <button type="button" id="ghCheckBtn" onclick="githubCheck()"
                        class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-blue-200 flex items-center justify-center gap-2">
                        <i class="fas fa-search" id="ghCheckIcon"></i> Check
                    </button>
                    <button type="button" id="ghUpdateBtn" onclick="githubUpdate()"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-indigo-200 flex items-center justify-center gap-2">
                        <i class="fas fa-sync-alt" id="ghUpdateIcon"></i> Update
                    </button>
                    <form method="POST" action="{{ route('admin.toggleUpdates') }}" class="flex-1 m-0">
                        @csrf
                        @if($systemSettings['enable_system_updates'] ?? false)
                            <button type="submit" class="w-full py-2.5 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold rounded-xl cursor-pointer transition-colors shadow-sm">Disable</button>
                        @else
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-emerald-200">Enable</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        {{-- Security Settings --}}
        <div class="db-card overflow-hidden flex flex-col" style="flex: 1 1 300px;">
        <div class="px-6 py-5 border-b border-slate-200 flex items-center gap-2">
            <i class="fas fa-shield-alt text-rose-500"></i>
            <h3 class="text-lg font-bold text-slate-800 m-0">Security Settings</h3>
        </div>
        <div class="p-6 flex-1 flex flex-col">
            <form method="POST" action="{{ route('admin.security') }}" class="m-0 space-y-6 flex-1 flex flex-col">
                @csrf
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label for="sessionTimeout" class="block text-sm font-bold text-slate-700 mb-1.5">Session Timeout (minutes)</label>
                        <p class="text-xs font-medium text-slate-500 mb-3">Automatically log out inactive administrators.</p>
                        <input type="number" name="session_timeout" id="sessionTimeout" value="{{ $systemSettings['session_timeout'] ?? 30 }}" min="5" max="120"
                            class="w-full max-w-[200px] px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all font-mono bg-white">
                    </div>
                    <div>
                        <label for="maxLoginAttempts" class="block text-sm font-bold text-slate-700 mb-1.5">Max Login Attempts (User)</label>
                        <p class="text-xs font-medium text-slate-500 mb-3">Number of failed logins before locking out.</p>
                        <input type="number" name="max_login_attempts" id="maxLoginAttempts" value="{{ $systemSettings['max_login_attempts'] ?? 5 }}" min="1" max="10"
                            class="w-full max-w-[200px] px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all font-mono bg-white">
                    </div>
                </div>
                <div class="pt-6 border-t border-slate-200 mt-auto">
                    <button type="submit" class="w-full flex justify-center items-center gap-2 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-indigo-200">
                        <i class="fas fa-save"></i> Save Security Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>

{{-- ===================== GITHUB SETTINGS MODAL ===================== --}}
<div id="githubModal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-slate-900 rounded-xl flex items-center justify-center">
                    <i class="fab fa-github text-white"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800 m-0">GitHub Repository Settings</h3>
                    <p class="text-xs text-slate-500 m-0">Connect your private repository for live updates</p>
                </div>
            </div>
            <button onclick="closeGithubModal()" class="w-8 h-8 flex items-center justify-center rounded-lg border-0 bg-transparent text-slate-400 hover:text-slate-700 hover:bg-slate-100 cursor-pointer transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        {{-- Modal Body --}}
        <div class="p-6 space-y-5">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">Repository URL <span class="text-rose-500">*</span></label>
                <p class="text-xs text-slate-500 mb-2">The GitHub page URL of your repo.</p>
                <input type="url" id="ghInputRepoUrl" placeholder="https://github.com/your-org/wisp"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"
                    value="{{ $ghRepoUrl }}">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">Clone URL <span class="text-rose-500">*</span></label>
                <p class="text-xs text-slate-500 mb-2">The HTTPS clone URL (used for <code class="bg-slate-100 px-1 rounded">git pull</code>).</p>
                <input type="url" id="ghInputCloneUrl" placeholder="https://github.com/your-org/wisp.git"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"
                    value="{{ $ghCloneUrl }}">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Branch</label>
                    <input type="text" id="ghInputBranch" placeholder="main"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"
                        value="{{ $ghBranch }}">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">
                        Personal Access Token
                        @if($ghHasPat) <span class="text-emerald-500 font-normal">(saved)</span> @endif
                    </label>
                    <input type="password" id="ghInputPat" placeholder="{{ $ghHasPat ? '••••••••••• (leave blank to keep)' : 'ghp_xxxxxxxxxxxx' }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
            </div>
            <div class="px-4 py-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-700">
                <i class="fas fa-info-circle mr-1"></i>
                <strong>Token tip:</strong> Create a token at <a href="https://github.com/settings/tokens" target="_blank" class="underline">github.com/settings/tokens</a> with <code class="bg-blue-100 px-1 rounded">repo</code> scope for private repos.
            </div>
            <div id="ghSaveError" class="hidden px-4 py-3 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-700"></div>
        </div>
        {{-- Modal Footer --}}
        <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
            <button onclick="closeGithubModal()" type="button"
                class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold text-sm hover:bg-slate-50 cursor-pointer transition-colors">
                Cancel
            </button>
            <button onclick="saveGithubSettings()" type="button" id="ghSaveBtn"
                class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-700 text-white font-semibold text-sm cursor-pointer border-0 transition-colors flex items-center gap-2">
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

