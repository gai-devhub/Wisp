<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#E8674A">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wisp Admin</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-page.css') }}?v={{ filemtime(public_path('css/admin-page.css')) }}">
</head>

@php
    $adminSettings = auth()->check() ? (auth()->user()->settings ?? []) : [];
    $privacyEnabled = $adminSettings['privacy_blur_enabled'] ?? false;
@endphp

<body data-active-section="@yield('admin-section', 'dashboard')"
    data-activity-page="{{ $activityPage ?? $actPage ?? 1 }}"
    class="{{ $privacyEnabled ? 'privacy-blur-active' : '' }} min-h-screen">

    {{-- ===== LAYOUT SHELL ===== --}}
    <div class="admin-shell">

        {{-- ===== HEADER ===== --}}
        <header class="admin-header">
            {{-- Left --}}
            <div class="admin-header-left">
                <button type="button" id="menuToggle" class="admin-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="{{ route('admin.page') }}" style="display: flex; align-items: center; gap: 0; text-decoration: none;">
                    <img src="{{ asset('img/logo.png') }}" alt="WISP" class="admin-logo">
                    <h2 style="color: var(--primary, #E8674A); margin: 0 0 0 4px; font-size: 1.3rem; font-weight: 700;">WISP</h2>
                </a>
            </div>

            {{-- Right --}}
            <div class="admin-header-right">
                {{-- Global Search --}}
                <form method="GET" action="{{ route('admin.search.page') }}" class="header-search-form" id="globalSearchForm">
                    <div class="header-search-wrap">
                        <i class="fas fa-search header-search-icon-inner"></i>
                        <input type="text" name="q" id="globalSearchInput" placeholder="Search messages and pages..." autocomplete="off" class="header-search-input" value="{{ request('q') ?? '' }}">
                        <input type="date" id="globalDateFilter" style="display: none;">
                    </div>
                </form>

                {{-- Notification Bell --}}
                @php
                    $adminUnreadCount = \App\Models\UserNotification::where('user_id', auth()->id())
                        ->whereNull('read_at')
                        ->where('deleted_by_user', false)
                        ->count();
                @endphp
                <a href="{{ route('admin.notifications.page') }}" class="notification-bell" title="Notifications">
                    <i class="fas fa-bell"></i>
                    @if($adminUnreadCount > 0)
                        <div class="notification-dot active"></div>
                    @endif
                </a>

                {{-- Hidden elements for JS compatibility --}}
                <button id="themeToggleBtn" style="display: none;"></button>
                <div id="systemStatus" style="display: none;"></div>

                {{-- Admin profile --}}
                @php $admin = auth()->user(); @endphp
                <a href="{{ route('admin.settings.user.page') }}" class="user-profile" style="text-decoration: none; color: inherit;">
                    <img src="{{ route('profile.picture') }}" alt="Admin" data-fallback-src="https://ui-avatars.com/api/?name={{ urlencode($admin->username ?? $admin->name ?? 'Admin') }}&color=E8674A&background=FDF6F0" onerror="if(this.dataset.fallbackSrc) this.src=this.dataset.fallbackSrc">
                    <span><b>{{ $admin->username ?? 'Administrator' }}</b></span>
                </a>
            </div>
        </header>

        {{-- ===== TOAST CONTAINER ===== --}}
        <div id="notificationContainer" class="notification-container"></div>

        @if(session('success') || session('error'))
            <div id="adminFlash" data-type="{{ session('success') ? 'success' : 'error' }}"
                data-message="{{ e(session('success') ?? session('error')) }}" aria-hidden="true" class="hidden"></div>
        @endif

        {{-- ===== BODY WRAPPER ===== --}}
        <div class="admin-body-wrapper">

            {{-- Sidebar overlay (mobile) --}}
            <div id="sidebarOverlay" class="admin-sidebar-overlay"></div>

            {{-- ===== SIDEBAR ===== --}}
            <aside id="sidebar" class="admin-sidebar">

                {{-- Nav --}}
                <nav class="admin-nav">
                    <ul class="admin-nav-list">

                        <li>
                            <a href="{{ route('admin.page') }}"
                                class="admin-nav-link {{ request()->routeIs('admin.page') || request()->is('admin.page') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt admin-nav-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        @if(auth()->user()->role === 'admin')
                        <li>
                            <a href="{{ route('user.page') }}"
                                class="admin-nav-link">
                                <i class="fas fa-exchange-alt admin-nav-icon"></i>
                                <span>Switch to User View</span>
                            </a>
                        </li>
                        @endif

                        <li class="admin-nav-item {{ request()->is('admin-page/users*', 'admin-page/subscribers*') ? 'open' : '' }}">
                            <a href="javascript:void(0)" class="admin-nav-link admin-nav-dropdown-toggle">
                                <i class="fas fa-users admin-nav-icon"></i>
                                <span>User Management</span>
                                <i class="fas fa-chevron-right dropdown-arrow ml-auto text-xs"></i>
                            </a>
                            <ul class="admin-nav-submenu">
                                <li><a href="{{ route('admin.users.page') }}"
                                    class="admin-nav-sublink {{ request()->routeIs('admin.users.page') || request()->is('admin.users.page') ? 'active' : '' }}">
                                    <i class="fas fa-user admin-nav-subicon"></i> <span>Users</span></a></li>
                                <li><a href="{{ route('admin.subscribers.page') }}"
                                    class="admin-nav-sublink {{ request()->routeIs('admin.subscribers.page') || request()->is('admin.subscribers.page') ? 'active' : '' }}">
                                    <i class="fas fa-envelope-open-text admin-nav-subicon"></i> <span>Subscribers</span></a></li>
                            </ul>
                        </li>

                        {{-- Message dropdown --}}
                        <li class="admin-nav-item {{ request()->is('admin-page/messages*', 'admin-page/view-activity*') ? 'open' : '' }}">
                            <a href="javascript:void(0)" class="admin-nav-link admin-nav-dropdown-toggle">
                                <i class="fas fa-envelope admin-nav-icon"></i>
                                <span>Message Management</span>
                                <i class="fas fa-chevron-right dropdown-arrow ml-auto text-xs"></i>
                            </a>
                            <ul class="admin-nav-submenu">
                                <li><a href="{{ route('admin.messages.page') }}"
                                    class="admin-nav-sublink {{ request()->routeIs('admin.messages.page') || request()->is('admin.messages.page') ? 'active' : '' }}">
                                    <i class="fas fa-envelope admin-nav-subicon"></i> <span>Messages</span></a></li>
                                <li><a href="{{ route('admin.view-activity.page') }}"
                                    class="admin-nav-sublink {{ request()->routeIs('admin.view-activity.page') || request()->is('admin.view-activity.page') ? 'active' : '' }}">
                                    <i class="fas fa-eye admin-nav-subicon"></i> <span>View Activity</span></a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('admin.ai-usage.page') }}"
                                class="admin-nav-link {{ request()->routeIs('admin.ai-usage.page') || request()->is('admin.ai-usage.page') ? 'active' : '' }}">
                                <i class="fas fa-robot admin-nav-icon"></i>
                                <span>AI Usage</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('database.manager') }}"
                                class="admin-nav-link {{ request()->routeIs('database.manager') || request()->routeIs('admin.database-manager.page') || request()->is('database-manager') || request()->is('admin-page/database-manager') ? 'active' : '' }}">
                                <i class="fas fa-database admin-nav-icon"></i>
                                <span>Database Manager</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.notifications.page') }}"
                                class="admin-nav-link {{ request()->routeIs('admin.notifications.page') || request()->is('admin.notifications.page') ? 'active' : '' }}">
                                <i class="fas fa-bell admin-nav-icon"></i>
                                <span>Notifications</span>
                            </a>
                        </li>


                        {{-- Operations dropdown --}}
                        <li class="admin-nav-item {{ request()->is('admin-page/analytics*', 'admin-page/logs*', 'admin-page/system*', 'admin-page/maintenance*') ? 'open' : '' }}">
                            <a href="javascript:void(0)" class="admin-nav-link admin-nav-dropdown-toggle">
                                <i class="fas fa-tasks admin-nav-icon"></i>
                                <span>Operations</span>
                                <i class="fas fa-chevron-right dropdown-arrow ml-auto text-xs"></i>
                            </a>
                            <ul class="admin-nav-submenu">
                                <li><a href="{{ route('admin.analytics.page') }}"
                                    class="admin-nav-sublink {{ request()->routeIs('admin.analytics.page') || request()->is('admin.analytics.page') ? 'active' : '' }}">
                                    <i class="fas fa-chart-bar admin-nav-subicon"></i> <span>Analytics</span></a></li>
                                <li><a href="{{ route('admin.logs.page') }}"
                                    class="admin-nav-sublink {{ request()->routeIs('admin.logs.page') || request()->is('admin.logs.page') ? 'active' : '' }}">
                                    <i class="fas fa-clipboard-list admin-nav-subicon"></i> <span>Activity Logs</span></a></li>
                                <li><a href="{{ route('admin.system.page') }}"
                                    class="admin-nav-sublink {{ request()->routeIs('admin.system.page') || request()->is('admin.system.page') ? 'active' : '' }}">
                                    <i class="fas fa-cogs admin-nav-subicon"></i> <span>System Controls</span></a></li>
                                <li><a href="{{ route('admin.maintenance.page') }}"
                                    class="admin-nav-sublink {{ request()->routeIs('admin.maintenance.page') || request()->is('admin.maintenance.page') ? 'active' : '' }}">
                                    <i class="fas fa-tools admin-nav-subicon"></i> <span>Maintenance</span></a></li>
                            </ul>
                        </li>

                        {{-- Settings dropdown --}}
                        <li class="admin-nav-item {{ request()->is('admin-page/settings*') ? 'open' : '' }}">
                            <a href="javascript:void(0)" class="admin-nav-link admin-nav-dropdown-toggle">
                                <i class="fas fa-cog admin-nav-icon"></i>
                                <span>Settings</span>
                                <i class="fas fa-chevron-right dropdown-arrow ml-auto text-xs"></i>
                            </a>
                            <ul class="admin-nav-submenu">
                                <li><a href="{{ route('admin.settings.message.page') }}"
                                    class="admin-nav-sublink {{ request()->routeIs('admin.settings.message.page') || request()->is('admin.settings.message.page') ? 'active' : '' }}">
                                    <span>Message Settings</span></a></li>
                                <li><a href="{{ route('admin.settings.user.page') }}"
                                    class="admin-nav-sublink {{ request()->routeIs('admin.settings.user.page') || request()->is('admin.settings.user.page') ? 'active' : '' }}">
                                    <span>Admin Settings</span></a></li>
                            </ul>
                        </li>

                    </ul>
                </nav>

                {{-- Logout --}}
                <div class="p-3 border-t border-slate-200">
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit"
                            class="admin-logout-btn">
                            <i class="fas fa-sign-out-alt admin-nav-icon"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            {{-- ===== MAIN CONTENT ===== --}}
            <main class="admin-main">
                <div class="admin-container">
                    <div class="admin-content-wrap">
                        @yield('content')
                    </div>
                    
                    {{-- Admin Page Footer --}}
                    <footer class="admin-page-footer">
                        <div class="admin-footer-content">
                            <div class="admin-footer-brand">
                                <img src="{{ asset('img/logo.png') }}" alt="WISP" class="admin-footer-logo">
                                <span>&copy; {{ date('Y') }} WISP. All rights reserved.</span>
                            </div>
                            <div class="admin-footer-links">
                                <a href="{{ route('help') }}">Documentation</a>
                                <span class="admin-footer-sep">&bull;</span>
                                <a href="{{ route('admin.settings.user.page') }}">Help &amp; Support</a>
                                <span class="admin-footer-sep">&bull;</span>
                                <a href="{{ route('admin.settings.message.page') }}">Settings</a>
                            </div>
                        </div>
                    </footer>
                </div>
            </main>
        </div>
    </div>



    {{-- Add User --}}
    <div id="addUserModal" class="admin-modal">
        <div class="admin-modal-content max-w-lg" style="animation:modalIn .2s ease">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Add New User</h3>
                <button type="button" id="closeAddUserModal" class="admin-modal-close">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" class="admin-modal-body space-y">
                @csrf
                <div>
                    <label class="admin-form-label">Username</label>
                    <input type="text" name="username" required minlength="3" pattern="[a-zA-Z0-9_.-]+"
                        class="admin-form-input">
                </div>
                <div>
                    <label class="admin-form-label">Email Address</label>
                    <input type="email" name="email" required
                        class="admin-form-input">
                </div>
                <div>
                    <label class="admin-form-label">Password</label>
                    <input type="password" name="password" required minlength="8"
                        class="admin-form-input">
                </div>
                <div>
                    <label class="admin-form-label">Role</label>
                    <select name="role" required
                        class="admin-form-input cursor-pointer">
                        <option value="user">User</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <button type="submit" id="createUserBtn"
                    class="admin-btn admin-btn-primary w-full">
                    Create User
                </button>
            </form>
        </div>
    </div>

    {{-- Send Notification --}}
    <div id="sendNotificationModal" class="admin-modal">
        <div class="admin-modal-content max-w-lg">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Send Notification</h3>
                <button type="button" id="closeNotificationModal" class="admin-modal-close">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.sendNotification') }}" class="admin-modal-body space-y">
                @csrf
                <div>
                    <label class="admin-form-label">Type</label>
                    <select name="type" id="notificationType" required
                        class="admin-form-input cursor-pointer">
                        <option value="info">Information</option>
                        <option value="success">Success</option>
                        <option value="warning">Warning</option>
                        <option value="error">Alert / Error</option>
                    </select>
                </div>
                <div>
                    <label class="admin-form-label">Title</label>
                    <input type="text" name="title" id="notificationTitle" required
                        class="admin-form-input">
                </div>
                <div>
                    <label class="admin-form-label">Message</label>
                    <textarea name="message" id="notificationMessage" rows="4" required
                        class="admin-form-input resize-y"></textarea>
                </div>
                <div>
                    <label class="admin-form-label">Audience</label>
                    <select name="audience" id="notificationAudience" required
                        class="admin-form-input cursor-pointer">
                        <option value="all">All Users</option>
                        <option value="active">Active Users Only</option>
                        <option value="inactive">Inactive Users Only</option>
                    </select>
                </div>
                <button type="submit" id="sendNotificationActionBtn"
                    class="admin-btn admin-btn-primary w-full">
                    Send Notification
                </button>
            </form>
        </div>
    </div>

    {{-- View User --}}
    <div id="viewUserModal" class="admin-modal">
        <div class="admin-modal-content max-w-md">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">User Details</h3>
                <button class="modal-close admin-modal-close" id="closeViewUserModal">&times;</button>
            </div>
            <div class="admin-modal-body text-center">
                <div class="admin-modal-icon-lg user">
                    <i class="fas fa-user"></i>
                </div>
                <h4 id="viewUserName" class="admin-modal-subtitle">User Name</h4>
                <p id="viewUserDetails" class="admin-modal-desc">User details</p>
                <div class="admin-form-actions justify-center">
                    <button id="editUserFromView" class="admin-btn admin-btn-primary btn-sm">Edit User</button>
                    <button id="closeViewModal" class="admin-btn admin-btn-secondary btn-sm">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit User --}}
    <div id="editUserModal" class="admin-modal">
        <div class="admin-modal-content max-w-md">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Edit User</h3>
                <button type="button" id="closeEditUserModal" class="admin-modal-close">&times;</button>
            </div>
            <form id="editUserForm" method="POST" action="" class="admin-modal-body space-y">
                @csrf @method('PUT')
                <div>
                    <label class="admin-form-label">Username</label>
                    <input type="text" id="editUserName" name="username" required pattern="[a-zA-Z0-9_.-]+"
                        class="admin-form-input">
                </div>
                <div>
                    <label class="admin-form-label">Email</label>
                    <input type="email" id="editUserEmail" name="email" required
                        class="admin-form-input">
                </div>
                <div>
                    <label class="admin-form-label">Status</label>
                    <select id="editUserStatus" name="status" required
                        class="admin-form-input cursor-pointer">
                        <option value="active">Active</option>
                        <option value="blocked">Blocked</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="admin-form-actions pt-2">
                    <button type="submit" id="saveUserChanges" class="admin-btn admin-btn-primary flex-1">Save Changes</button>
                    <button type="button" id="cancelEditUser" class="admin-btn admin-btn-secondary flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Block User --}}
    <div id="blockUserModal" class="admin-modal">
        <div class="admin-modal-content max-w-sm">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Block User</h3>
                <button type="button" id="closeBlockUserModal" class="admin-modal-close">&times;</button>
            </div>
            <form id="blockUserForm" method="POST" action="" class="admin-modal-body space-y text-center">
                @csrf
                <div class="admin-modal-icon-lg warning">
                    <i class="fas fa-user-slash"></i>
                </div>
                <h4 id="blockUserName" class="admin-modal-subtitle">User Name</h4>
                <p class="admin-modal-desc">Are you sure you want to block this user? They will not be able to access the system.</p>
                <div class="admin-form-actions justify-center">
                    <button type="submit" id="confirmBlockUser" class="admin-btn admin-btn-primary">Block User</button>
                    <button type="button" id="cancelBlockUser" class="admin-btn admin-btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete User --}}
    <div id="deleteUserModal" class="admin-modal">
        <div class="admin-modal-content max-w-sm">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Delete User</h3>
                <button type="button" id="closeDeleteUserModal" class="admin-modal-close">&times;</button>
            </div>
            <form id="deleteUserForm" method="POST" action="" class="admin-modal-body space-y text-center">
                @csrf @method('DELETE')
                <div class="admin-modal-icon-lg danger">
                    <i class="fas fa-trash"></i>
                </div>
                <h4 id="deleteUserName" class="admin-modal-subtitle">User Name</h4>
                <p class="admin-modal-desc">Are you sure you want to permanently delete this user? This action cannot be undone.</p>
                <div class="admin-form-actions justify-center">
                    <button type="submit" id="confirmDeleteUser" class="admin-btn admin-btn-primary">Delete</button>
                    <button type="button" id="cancelDeleteUser" class="admin-btn admin-btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Message --}}
    <div id="editMessageModal" class="admin-modal">
        <div class="admin-modal-content max-w-lg">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Edit Message</h3>
                <button type="button" id="closeEditMessageModal" class="admin-modal-close">&times;</button>
            </div>
            <form id="editMessageForm" method="POST" action="" class="admin-modal-body space-y">
                @csrf @method('PUT')
                <div>
                    <label class="admin-form-label">Title</label>
                    <input type="text" id="editMessageTitle" name="title" required
                        class="admin-form-input">
                </div>
                <div>
                    <label class="admin-form-label">Content</label>
                    <textarea id="editMessageContent" name="message" rows="5" required
                        class="admin-form-input resize-y"></textarea>
                </div>
                <div>
                    <label class="admin-form-label">Status</label>
                    <select id="editMessageStatus" name="is_published"
                        class="admin-form-input cursor-pointer">
                        <option value="0">Draft</option>
                        <option value="1">Published</option>
                    </select>
                </div>
                <div class="admin-form-actions pt-2">
                    <button type="submit" id="saveMessageChanges" class="admin-btn admin-btn-primary flex-1">Save Changes</button>
                    <button type="button" id="cancelEditMessage" class="admin-btn admin-btn-secondary flex-1">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Message --}}
    <div id="deleteMessageModal" class="admin-modal">
        <div class="admin-modal-content max-w-sm">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Delete Message</h3>
                <button type="button" id="closeDeleteMessageModal" class="admin-modal-close">&times;</button>
            </div>
            <form id="deleteMessageForm" method="POST" action="" class="admin-modal-body space-y text-center">
                @csrf @method('DELETE')
                <div class="admin-modal-icon-lg danger">
                    <i class="fas fa-trash"></i>
                </div>
                <h4 id="deleteMessageTitle" class="admin-modal-subtitle">Message Title</h4>
                <p class="admin-modal-desc">Are you sure you want to delete this message? This action cannot be undone.</p>
                <div class="admin-form-actions justify-center">
                    <button type="submit" id="confirmDeleteMessage" class="admin-btn admin-btn-primary">Delete</button>
                    <button type="button" id="cancelDeleteMessage" class="admin-btn admin-btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Message Preview Popup --}}
    <div id="preview-popup" class="preview-popup admin-modal">
        <div class="admin-modal-content preview-popup-content">
            <div class="preview-popup-header">
                <div style="width: 28px;"></div>
                <h3 class="preview-popup-title">Preview</h3>
                <button type="button" id="close-popup" class="preview-popup-close" onclick="document.getElementById('preview-popup').classList.remove('active'); document.getElementById('preview-popup').style.display=''; document.body.style.overflow='';">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="preview-popup-body">
                <iframe id="admin-message-preview-iframe" class="preview-iframe" title="Message preview"></iframe>
            </div>
        </div>
    </div>

    {{-- Global Admin Confirm Modal --}}
    <div id="adminGlobalConfirmModal" class="admin-modal" style="display: none;">
        <div class="admin-modal-content max-w-sm p-6 text-center">
            <div class="admin-modal-icon-lg user mb-4">
                <i class="fas fa-question-circle"></i>
            </div>
            <h4 class="admin-modal-subtitle">Confirm Action</h4>
            <p id="adminGlobalConfirmMessage" class="admin-modal-desc">Are you sure?</p>
            <div class="flex gap-3 justify-center">
                <button type="button" id="adminGlobalConfirmBtn" class="admin-btn admin-btn-primary flex-1">Confirm</button>
                <button type="button" id="adminGlobalConfirmCancelBtn" class="admin-btn admin-btn-secondary flex-1">Cancel</button>
            </div>
        </div>
    </div>

    {{-- Global Admin Alert Modal --}}
    <div id="adminGlobalAlertModal" class="admin-modal" style="display: none;">
        <div class="admin-modal-content max-w-sm p-6 text-center">
            <div class="admin-modal-icon-lg user mb-4">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h4 class="admin-modal-subtitle">Alert</h4>
            <p id="adminGlobalAlertMessage" class="admin-modal-desc">Notice.</p>
            <div class="flex justify-center">
                <button type="button" id="adminGlobalAlertBtn" class="admin-btn admin-btn-primary w-full">OK</button>
            </div>
        </div>
    </div>

    {{-- ===== SCRIPTS ===== --}}
    <script type="application/json" id="wisp-analytics-json">{!! json_encode(($analytics ?? null) ?: (object)[]) !!}</script>
    <script>
        window.showAdminConfirm = function(message, onConfirm) {
            var modal = document.getElementById('adminGlobalConfirmModal');
            var msgEl = document.getElementById('adminGlobalConfirmMessage');
            var confirmBtn = document.getElementById('adminGlobalConfirmBtn');
            var cancelBtn = document.getElementById('adminGlobalConfirmCancelBtn');
            var box = modal.querySelector('.admin-modal-content');

            msgEl.textContent = message;
            modal.style.display = 'flex';
            modal.classList.add('active');

            var cleanup = function() {
                modal.style.display = 'none';
                modal.classList.remove('active');
                confirmBtn.onclick = null;
                cancelBtn.onclick = null;
            };

            cancelBtn.onclick = function() {
                cleanup();
            };

            confirmBtn.onclick = function() {
                cleanup();
                if (onConfirm) onConfirm();
            };
        };

        window.showAdminAlert = function(message) {
            var modal = document.getElementById('adminGlobalAlertModal');
            var msgEl = document.getElementById('adminGlobalAlertMessage');
            var okBtn = document.getElementById('adminGlobalAlertBtn');

            msgEl.textContent = message;
            modal.style.display = 'flex';
            modal.classList.add('active');

            var cleanup = function() {
                modal.style.display = 'none';
                modal.classList.remove('active');
                okBtn.onclick = null;
            };

            okBtn.onclick = function() {
                cleanup();
            };
        };
    </script>
    <script>
        window.WISP_ROUTES = window.WISP_ROUTES || {};
        window.WISP_ROUTES.adminActivity  = "{{ route('admin.activity') }}";
        window.WISP_ROUTES.adminAnalytics = "{{ route('admin.analytics') }}";
        window.WISP_ACTIVITY_PAGE = parseInt(document.body.getAttribute('data-activity-page') || '1', 10);
        try {
            var raw = document.getElementById('wisp-analytics-json').textContent.trim();
            window.WISP_ANALYTICS = (raw && raw !== '[]' && raw !== '') ? JSON.parse(raw) : {};
        } catch(e) { window.WISP_ANALYTICS = {}; }
        window.WISP_DASHBOARD_STATS = <?php echo json_encode($stats ?? []); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.getElementById('adminDashboardRefresh');
            if (btn) btn.addEventListener('click', function(e) { e.preventDefault(); location.reload(); });
        });
    </script>
    <script src="{{ asset('js/admin-page.js') }}?v={{ filemtime(public_path('js/admin-page.js')) }}"></script>
    <script>
        // Sidebar dropdown toggle
        (function () {
            function initDropdowns() {
                document.querySelectorAll('.admin-nav-item').forEach(function (dropdown) {
                    var toggle = dropdown.querySelector('.admin-nav-dropdown-toggle');
                    
                    if (toggle) {
                        toggle.addEventListener('click', function (e) {
                            e.preventDefault();
                            var isOpen = dropdown.classList.contains('open');
                            
                            // Close other dropdowns
                            document.querySelectorAll('.admin-nav-item').forEach(function (other) {
                                other.classList.remove('open');
                            });
                            
                            // Toggle current dropdown
                            if (!isOpen) { 
                                dropdown.classList.add('open'); 
                            }
                        });
                    }
                });
            }
            document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', initDropdowns) : initDropdowns();
        })();

        // Mobile sidebar toggle
        (function () {
            var toggle = document.getElementById('menuToggle');
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            if (!toggle || !sidebar) return;
            toggle.addEventListener('click', function () {
                var open = sidebar.style.left === '0px';
                sidebar.style.left = open ? '-280px' : '0px';
                if (overlay) overlay.style.display = open ? 'none' : 'block';
            });
            if (overlay) overlay.addEventListener('click', function () {
                sidebar.style.left = '-280px';
                overlay.style.display = 'none';
            });
        })();

        // Global Search
        (function() {
            var searchInput = document.getElementById('globalSearchInput');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        var query = this.value.trim();
                        if (query.length > 0) {
                            window.location.href = "{{ route('admin.search.page') }}?q=" + encodeURIComponent(query);
                        } else {
                            window.location.href = "{{ route('admin.search.page') }}";
                        }
                    }
                });
            }
        })();
    </script>
    
    <script>
        // Global Fetch Interceptor to handle session timeouts and redirects
        (function() {
            const originalFetch = window.fetch;
            window.fetch = async function(...args) {
                try {
                    const response = await originalFetch(...args);
                    if (response.status === 401 || response.status === 419) {
                        window.location.href = "{{ route('auth.login') }}";
                    }
                    return response;
                } catch (error) {
                    throw error;
                }
            };
        })();
    </script>
    @yield('scripts')
</body>

</html>
