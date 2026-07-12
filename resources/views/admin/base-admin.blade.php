<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#6366f1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wisp Admin</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/admin-page.css') }}?v={{ filemtime(public_path('css/admin-page.css')) }}">
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: #f6f7ff !important;
            background-image:
                radial-gradient(circle at 10% 15%, rgba(166, 223, 255, 0.9), transparent 28%),
                radial-gradient(circle at 80% 22%, rgba(255, 181, 213, 0.78), transparent 34%),
                radial-gradient(circle at 60% 76%, rgba(255, 202, 230, 0.62), transparent 24%),
                linear-gradient(120deg, #edf4ff 0%, #f9f4ff 48%, #ffe8f2 100%) !important;
            background-attachment: fixed !important;
        }
        .nav-sub-menu { display: none; }
        .nav-dropdown.open .nav-sub-menu { display: block; }
        .nav-dropdown.open .dropdown-arrow { transform: rotate(90deg); }
        .dropdown-arrow { transition: transform 0.25s ease; }
        .sidebar-link:hover { transform: translateX(3px); }
        .sidebar-link { transition: all 0.18s ease; }
        .content-section { display: none; animation: fadeInUp 0.3s ease; }
        .content-section.active { display: block; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .modal { display: none; }
        .modal.active { display: flex; }
        .preview-popup { opacity: 0; visibility: hidden; transition: opacity 0.22s, visibility 0.22s; }
        .preview-popup.active { opacity: 1; visibility: visible; }
        .admin-header-bg { background: linear-gradient(90deg, #edf4ff 0%, #ffe8f2 100%) !important; }
        
        .notification-toast { 
            transform: translateX(420px); 
            transition: transform 0.32s cubic-bezier(0.4,0,0.2,1); 
            background: #ffffff;
            border-left: 4px solid #6366f1;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        .notification-toast.show { transform: translateX(0); }
        .notification-toast.success { border-left-color: #10b981; }
        .notification-toast.error { border-left-color: #ef4444; }
        .notification-toast.warning { border-left-color: #f59e0b; }
        .notification-toast.info { border-left-color: #3b82f6; }
        .notification-toast-icon { font-size: 1.25rem; }
        .notification-toast.success .notification-toast-icon { color: #10b981; }
        .notification-toast.error .notification-toast-icon { color: #ef4444; }
        .notification-toast.warning .notification-toast-icon { color: #f59e0b; }
        .notification-toast.info .notification-toast-icon { color: #3b82f6; }
        .notification-toast-content { flex: 1; }
        .notification-toast-content h4 { margin: 0 0 4px 0; font-size: 0.9rem; font-weight: 600; color: #1e293b; }
        .notification-toast-content p { margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.4; }
        .notification-toast-close { background: none; border: none; font-size: 1rem; color: #94a3b8; cursor: pointer; padding: 4px; }
        .notification-toast-close:hover { color: #ef4444; }

        /* Constrain modals to main content area to prevent blurring header/sidebar */
        .modal, .preview-popup, #messagesModal, #adModal, #addMethodModal {
            top: 70px !important;
            bottom: 0 !important;
            right: 0 !important;
            left: 0 !important;
        }
        @media (min-width: 1024px) {
            .modal, .preview-popup, #messagesModal, #adModal, #addMethodModal {
                left: 280px !important;
            }
        }

        /* Dark mode support */
        [data-theme="dark"] body { background: #0f172a !important; background-image: none !important; color: #f1f5f9; }
        [data-theme="dark"] .admin-header-bg { background: #1e293b !important; }
        [data-theme="dark"] .bg-white { background: #1e293b !important; }
        [data-theme="dark"] .bg-slate-50 { background: #1e293b !important; }
        [data-theme="dark"] .border-slate-200 { border-color: #334155 !important; }
        [data-theme="dark"] .text-slate-700, [data-theme="dark"] .text-slate-800, [data-theme="dark"] .text-slate-900 { color: #f1f5f9 !important; }
        [data-theme="dark"] .text-slate-500, [data-theme="dark"] .text-slate-600 { color: #94a3b8 !important; }
        [data-theme="dark"] input, [data-theme="dark"] select, [data-theme="dark"] textarea { background: #0f172a; color: #f1f5f9; border-color: #334155; }
        [data-theme="dark"] table thead { background: #334155 !important; }
        [data-theme="dark"] .notification-toast { background: #1e293b; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
        [data-theme="dark"] .notification-toast-content h4 { color: #f1f5f9; }
        [data-theme="dark"] .notification-toast-content p { color: #cbd5e1; }
        [data-theme="dark"] #globalSearchInput, [data-theme="dark"] #globalDateFilter { background: transparent !important; color: #334155 !important; }
    </style>
</head>

@php
    $adminSettings = auth()->check() ? (auth()->user()->settings ?? []) : [];
    $privacyEnabled = $adminSettings['privacy_blur_enabled'] ?? false;
    $activeTheme = $adminSettings['theme_preference'] ?? 'theme-default';
@endphp

<body data-active-section="@yield('admin-section', 'dashboard')"
    data-activity-page="{{ $activityPage ?? $actPage ?? 1 }}"
    class="{{ $activeTheme }} {{ $privacyEnabled ? 'privacy-blur-active' : '' }} min-h-screen">

    {{-- ===== LAYOUT SHELL ===== --}}
    <div class="flex flex-col min-h-screen">

        {{-- ===== HEADER ===== --}}
        <header class="sticky top-0 z-50 flex items-center justify-between px-4 lg:px-6 h-[70px] border-b border-slate-200 shadow-sm admin-header-bg">
            {{-- Left --}}
            <div class="flex items-center gap-3">
                <button id="menuToggle" class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl hover:bg-slate-100 text-slate-600 border-0 bg-transparent cursor-pointer">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <img src="{{ asset('img/logo.png') }}" alt="" class="w-9 h-9 object-contain">
                <h1 class="hidden sm:block text-lg font-bold text-slate-800 m-0">WISP Creator</h1>
            </div>

            {{-- Right --}}
            <div class="flex items-center gap-3">
                {{-- Global Search --}}
                <div class="flex items-center gap-2 h-10 px-3 rounded-full bg-slate-100 border border-slate-200 focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
                    <i class="fas fa-search text-slate-400 text-sm"></i>
                    <input type="text" id="globalSearchInput" placeholder="Search..." autocomplete="off"
                        class="border-0 bg-transparent outline-none text-sm text-slate-700 w-36 sm:w-48 placeholder-slate-400">
                    <div class="hidden sm:block w-px h-4 bg-slate-300"></div>
                    <input type="date" id="globalDateFilter"
                        class="hidden sm:block border-0 bg-transparent outline-none text-xs text-slate-400 cursor-pointer">
                </div>

                {{-- System status --}}
                @php $appLocked = $systemSettings['app_locked'] ?? false; @endphp
                <div id="systemStatus"
                    class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold cursor-pointer
                        {{ $appLocked ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">
                    <i class="fas fa-circle text-[8px]"></i>
                    {{ $appLocked ? 'Offline' : 'Online' }}
                </div>

                {{-- Theme toggle --}}
                <button id="themeToggleBtn" title="Toggle Dark/Light Mode"
                    class="w-10 h-10 flex items-center justify-center rounded-full border border-slate-200 bg-transparent text-slate-600 hover:bg-slate-100 hover:text-indigo-600 hover:border-indigo-300 cursor-pointer transition-all">
                    <i class="fas fa-moon"></i>
                </button>

                {{-- Admin profile --}}
                @php $admin = auth()->user(); @endphp
                <div class="flex items-center gap-2">
                    <img src="{{ route('profile.picture') }}" alt="Admin"
                        class="w-9 h-9 rounded-full object-cover border-2 border-slate-200"
                        onerror="this.src='{{ asset('img/logo.png') }}'">
                    <span class="hidden sm:block text-sm font-semibold text-slate-700">{{ $admin->username ?? 'Administrator' }}</span>
                </div>
            </div>
        </header>

        {{-- ===== TOAST CONTAINER ===== --}}
        <div id="notificationContainer" class="fixed top-20 right-5 z-[1100] flex flex-col gap-2 w-80"></div>

        @if(session('success') || session('error'))
            <div id="adminFlash" data-type="{{ session('success') ? 'success' : 'error' }}"
                data-message="{{ e(session('success') ?? session('error')) }}" aria-hidden="true" class="hidden"></div>
        @endif

        {{-- ===== BODY WRAPPER ===== --}}
        <div class="flex flex-1">

            {{-- Sidebar overlay (mobile) --}}
            <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden lg:hidden"></div>

            {{-- ===== SIDEBAR ===== --}}
            <aside id="sidebar"
                class="fixed lg:sticky top-0 lg:top-[70px] left-[-280px] lg:left-0 z-50 lg:z-auto
                       w-[280px] h-screen lg:h-[calc(100vh-70px)]
                       bg-white border-r border-slate-200 flex flex-col overflow-y-auto
                       transition-[left] duration-300 ease-in-out shadow-xl lg:shadow-none">

                {{-- Nav --}}
                <nav class="flex-1 py-4 px-2">
                    <ul class="list-none space-y-0.5">

                        <li>
                            <a href="{{ route('admin.page') }}"
                                class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline
                                    {{ request()->routeIs('admin.page') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                <i class="fas fa-tachometer-alt w-5 text-center"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        @if(auth()->user()->role === 'admin')
                        <li>
                            <a href="{{ route('user.page') }}"
                                class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline text-indigo-600 hover:bg-indigo-50">
                                <i class="fas fa-exchange-alt w-5 text-center"></i>
                                <span>Switch to User View</span>
                            </a>
                        </li>
                        @endif

                        <li class="nav-dropdown {{ request()->is('admin-page/users*', 'admin-page/subscribers*') ? 'open' : '' }}">
                            <a href="javascript:void(0)" class="sidebar-link dropdown-toggle flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer">
                                <i class="fas fa-users w-5 text-center"></i>
                                <span>User Management</span>
                                <i class="fas fa-chevron-right dropdown-arrow ml-auto text-xs"></i>
                            </a>
                            <ul class="nav-sub-menu list-none pl-4 pt-1 pb-1 space-y-0.5">
                                <li><a href="{{ route('admin.users.page') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.users.page') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-user w-4 text-center"></i> <span>Users</span></a></li>
                                <li><a href="{{ route('admin.subscribers.page') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.subscribers.page') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-envelope-open-text w-4 text-center"></i> <span>Subscribers</span></a></li>
                            </ul>
                        </li>

                        {{-- Message dropdown --}}
                        <li class="nav-dropdown {{ request()->is('admin-page/messages*', 'admin-page/view-activity*') ? 'open' : '' }}">
                            <a href="javascript:void(0)" class="sidebar-link dropdown-toggle flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer">
                                <i class="fas fa-envelope w-5 text-center"></i>
                                <span>Message Management</span>
                                <i class="fas fa-chevron-right dropdown-arrow ml-auto text-xs"></i>
                            </a>
                            <ul class="nav-sub-menu list-none pl-4 pt-1 pb-1 space-y-0.5">
                                <li><a href="{{ route('admin.messages.page') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.messages.page') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-envelope w-4 text-center"></i> <span>Messages</span></a></li>
                                <li><a href="{{ route('admin.view-activity.page') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.view-activity.page') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-eye w-4 text-center"></i> <span>View Activity</span></a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('admin.ads.page') }}"
                                class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline
                                    {{ request()->routeIs('admin.ads.page') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                <i class="fas fa-bullhorn w-5 text-center"></i>
                                <span>Advertisements</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.ai-usage.page') }}"
                                class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline
                                    {{ request()->routeIs('admin.ai-usage.page') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                <i class="fas fa-robot w-5 text-center"></i>
                                <span>AI Usage</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('database.manager') }}"
                                class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline
                                    {{ request()->routeIs('database.manager') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                <i class="fas fa-database w-5 text-center"></i>
                                <span>Database Manager</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.notifications.page') }}"
                                class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline
                                    {{ request()->routeIs('admin.notifications.page') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                <i class="fas fa-bell w-5 text-center"></i>
                                <span>Notifications</span>
                            </a>
                        </li>

                        {{-- Billing dropdown --}}
                        <li class="nav-dropdown {{ request()->is('admin-page/billing*') ? 'open' : '' }}">
                            <a href="javascript:void(0)" class="sidebar-link dropdown-toggle flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer">
                                <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
                                <span>Billing</span>
                                <i class="fas fa-chevron-right dropdown-arrow ml-auto text-xs"></i>
                            </a>
                            <ul class="nav-sub-menu list-none pl-4 pt-1 pb-1 space-y-0.5">
                                <li><a href="{{ route('admin.billing.users') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.billing.users') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-users w-4 text-center"></i> <span>Users</span></a></li>
                                <li><a href="{{ route('admin.billing.index') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.billing.index') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-list w-4 text-center"></i> <span>Subscriptions</span></a></li>
                            </ul>
                        </li>

                         {{-- Finances dropdown --}}
                        <li class="nav-dropdown {{ request()->is('admin-page/finances*') ? 'open' : '' }}">
                            <a href="javascript:void(0)" class="sidebar-link dropdown-toggle flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer">
                                <i class="fas fa-university w-5 text-center"></i>
                                <span>Finances</span>
                                <i class="fas fa-chevron-right dropdown-arrow ml-auto text-xs"></i>
                            </a>
                            <ul class="nav-sub-menu list-none pl-4 pt-1 pb-1 space-y-0.5">
                                <li><a href="{{ route('admin.finances.overview') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.finances.overview') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-chart-line w-4 text-center"></i> <span>Overview</span></a></li>
                                <li><a href="{{ route('admin.finances.transactions') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.finances.transactions') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-exchange-alt w-4 text-center"></i> <span>Transactions</span></a></li>
                            </ul>
                        </li>

                        {{-- Operations dropdown --}}
                        <li class="nav-dropdown {{ request()->is('admin-page/analytics*', 'admin-page/logs*', 'admin-page/system*', 'admin-page/maintenance*') ? 'open' : '' }}">
                            <a href="javascript:void(0)" class="sidebar-link dropdown-toggle flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer">
                                <i class="fas fa-tasks w-5 text-center"></i>
                                <span>Operations</span>
                                <i class="fas fa-chevron-right dropdown-arrow ml-auto text-xs"></i>
                            </a>
                            <ul class="nav-sub-menu list-none pl-4 pt-1 pb-1 space-y-0.5">
                                <li><a href="{{ route('admin.analytics.page') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.analytics.page') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-chart-bar w-4 text-center"></i> <span>Analytics</span></a></li>
                                <li><a href="{{ route('admin.logs.page') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.logs.page') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-clipboard-list w-4 text-center"></i> <span>Activity Logs</span></a></li>
                                <li><a href="{{ route('admin.system.page') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.system.page') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-cogs w-4 text-center"></i> <span>System Controls</span></a></li>
                                <li><a href="{{ route('admin.maintenance.page') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.maintenance.page') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <i class="fas fa-tools w-4 text-center"></i> <span>Maintenance</span></a></li>
                            </ul>
                        </li>

                        {{-- Settings dropdown --}}
                        <li class="nav-dropdown {{ request()->is('admin-page/settings*') ? 'open' : '' }}">
                            <a href="javascript:void(0)" class="sidebar-link dropdown-toggle flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium no-underline text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer">
                                <i class="fas fa-cog w-5 text-center"></i>
                                <span>Settings</span>
                                <i class="fas fa-chevron-right dropdown-arrow ml-auto text-xs"></i>
                            </a>
                            <ul class="nav-sub-menu list-none pl-4 pt-1 pb-1 space-y-0.5">
                                <li><a href="{{ route('admin.settings.message.page') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.settings.message.page') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <span>Message Settings</span></a></li>
                                <li><a href="{{ route('admin.settings.page.page') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.settings.page.page') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
                                    <span>Page Settings</span></a></li>
                                <li><a href="{{ route('admin.settings.user.page') }}"
                                    class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium no-underline
                                        {{ request()->routeIs('admin.settings.user.page') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">
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
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium
                                   text-slate-500 hover:bg-red-50 hover:text-red-600 cursor-pointer border-0 bg-transparent font-[inherit] transition-all">
                            <i class="fas fa-sign-out-alt w-5 text-center"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            {{-- ===== MAIN CONTENT ===== --}}
            <main class="flex-1 min-w-0 p-4 lg:p-6 overflow-auto">
                <div class="max-w-[1600px] w-full mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>



    {{-- Add User --}}
    <div id="addUserModal" class="modal fixed inset-0 z-[1000] items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl" style="animation:modalIn .2s ease">
            <div class="flex items-center justify-between p-5 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 m-0">Add New User</h3>
                <button type="button" id="closeAddUserModal" class="border-0 bg-transparent text-2xl cursor-pointer text-slate-400 hover:text-red-500 leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                    <input type="text" name="username" required minlength="3" pattern="[a-zA-Z0-9_.-]+"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role</label>
                    <select name="role" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                        <option value="user">User</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <button type="submit" id="createUserBtn"
                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl cursor-pointer border-0 transition-colors">
                    Create User
                </button>
            </form>
        </div>
    </div>

    {{-- Send Notification --}}
    <div id="sendNotificationModal" class="modal fixed inset-0 z-[1000] items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 m-0">Send Notification</h3>
                <button type="button" id="closeNotificationModal" class="border-0 bg-transparent text-2xl cursor-pointer text-slate-400 hover:text-red-500 leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.sendNotification') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Type</label>
                    <select name="type" id="notificationType" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                        <option value="info">Information</option>
                        <option value="success">Success</option>
                        <option value="warning">Warning</option>
                        <option value="error">Alert / Error</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Title</label>
                    <input type="text" name="title" id="notificationTitle" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Message</label>
                    <textarea name="message" id="notificationMessage" rows="4" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all resize-y"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Audience</label>
                    <select name="audience" id="notificationAudience" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                        <option value="all">All Users</option>
                        <option value="active">Active Users Only</option>
                        <option value="inactive">Inactive Users Only</option>
                    </select>
                </div>
                <button type="submit" id="sendNotificationActionBtn"
                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl cursor-pointer border-0 transition-colors">
                    Send Notification
                </button>
            </form>
        </div>
    </div>

    {{-- View User --}}
    <div id="viewUserModal" class="modal fixed inset-0 z-[1000] items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 m-0">User Details</h3>
                <button class="modal-close border-0 bg-transparent text-2xl cursor-pointer text-slate-400 hover:text-red-500 leading-none" id="closeViewUserModal">&times;</button>
            </div>
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl text-indigo-600">
                    <i class="fas fa-user"></i>
                </div>
                <h4 id="viewUserName" class="text-xl font-bold text-slate-800 mb-1">User Name</h4>
                <p id="viewUserDetails" class="text-sm text-slate-500 mb-5">User details</p>
                <div class="flex gap-2 justify-center">
                    <button id="editUserFromView" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl cursor-pointer border-0 transition-colors">Edit User</button>
                    <button id="closeViewModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl cursor-pointer border-0 transition-colors">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit User --}}
    <div id="editUserModal" class="modal fixed inset-0 z-[1000] items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 m-0">Edit User</h3>
                <button type="button" id="closeEditUserModal" class="border-0 bg-transparent text-2xl cursor-pointer text-slate-400 hover:text-red-500 leading-none">&times;</button>
            </div>
            <form id="editUserForm" method="POST" action="" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                    <input type="text" id="editUserName" name="username" required pattern="[a-zA-Z0-9_.-]+"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" id="editUserEmail" name="email" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                    <select id="editUserStatus" name="status" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                        <option value="active">Active</option>
                        <option value="blocked">Blocked</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" id="saveUserChanges" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl cursor-pointer border-0 transition-colors">Save Changes</button>
                    <button type="button" id="cancelEditUser" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl cursor-pointer border-0 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Block User --}}
    <div id="blockUserModal" class="modal fixed inset-0 z-[1000] items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 m-0">Block User</h3>
                <button type="button" id="closeBlockUserModal" class="border-0 bg-transparent text-2xl cursor-pointer text-slate-400 hover:text-red-500 leading-none">&times;</button>
            </div>
            <form id="blockUserForm" method="POST" action="" class="p-6 text-center space-y-4">
                @csrf
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto text-3xl text-amber-600">
                    <i class="fas fa-user-slash"></i>
                </div>
                <h4 id="blockUserName" class="text-xl font-bold text-slate-800 m-0">User Name</h4>
                <p class="text-sm text-slate-500">Are you sure you want to block this user? They will not be able to access the system.</p>
                <div class="flex gap-2 justify-center">
                    <button type="submit" id="confirmBlockUser" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl cursor-pointer border-0 transition-colors">Block User</button>
                    <button type="button" id="cancelBlockUser" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl cursor-pointer border-0 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete User --}}
    <div id="deleteUserModal" class="modal fixed inset-0 z-[1000] items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 m-0">Delete User</h3>
                <button type="button" id="closeDeleteUserModal" class="border-0 bg-transparent text-2xl cursor-pointer text-slate-400 hover:text-red-500 leading-none">&times;</button>
            </div>
            <form id="deleteUserForm" method="POST" action="" class="p-6 text-center space-y-4">
                @csrf @method('DELETE')
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto text-3xl text-red-600">
                    <i class="fas fa-trash"></i>
                </div>
                <h4 id="deleteUserName" class="text-xl font-bold text-slate-800 m-0">User Name</h4>
                <p class="text-sm text-slate-500">Are you sure you want to permanently delete this user? This action cannot be undone.</p>
                <div class="flex gap-2 justify-center">
                    <button type="submit" id="confirmDeleteUser" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl cursor-pointer border-0 transition-colors">Delete</button>
                    <button type="button" id="cancelDeleteUser" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl cursor-pointer border-0 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Message --}}
    <div id="editMessageModal" class="modal fixed inset-0 z-[1000] items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 m-0">Edit Message</h3>
                <button type="button" id="closeEditMessageModal" class="border-0 bg-transparent text-2xl cursor-pointer text-slate-400 hover:text-red-500 leading-none">&times;</button>
            </div>
            <form id="editMessageForm" method="POST" action="" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Title</label>
                    <input type="text" id="editMessageTitle" name="title" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Content</label>
                    <textarea id="editMessageContent" name="message" rows="5" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all resize-y"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                    <select id="editMessageStatus" name="is_published"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                        <option value="0">Draft</option>
                        <option value="1">Published</option>
                    </select>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" id="saveMessageChanges" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl cursor-pointer border-0 transition-colors">Save Changes</button>
                    <button type="button" id="cancelEditMessage" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl cursor-pointer border-0 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Message --}}
    <div id="deleteMessageModal" class="modal fixed inset-0 z-[1000] items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 m-0">Delete Message</h3>
                <button type="button" id="closeDeleteMessageModal" class="border-0 bg-transparent text-2xl cursor-pointer text-slate-400 hover:text-red-500 leading-none">&times;</button>
            </div>
            <form id="deleteMessageForm" method="POST" action="" class="p-6 text-center space-y-4">
                @csrf @method('DELETE')
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto text-3xl text-red-600">
                    <i class="fas fa-trash"></i>
                </div>
                <h4 id="deleteMessageTitle" class="text-xl font-bold text-slate-800 m-0">Message Title</h4>
                <p class="text-sm text-slate-500">Are you sure you want to delete this message? This action cannot be undone.</p>
                <div class="flex gap-2 justify-center">
                    <button type="submit" id="confirmDeleteMessage" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl cursor-pointer border-0 transition-colors">Delete</button>
                    <button type="button" id="cancelDeleteMessage" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl cursor-pointer border-0 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Message Preview Popup --}}
    <div id="preview-popup" class="preview-popup fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-black/55 backdrop-blur-sm">
        <div class="rounded-2xl overflow-hidden flex flex-col shadow-2xl" style="background: #f5f4f0; width: 95vw; height: 95vh; max-width: 100%; max-height: 100%;">
            <div class="flex items-center justify-between flex-shrink-0" style="padding: 6px 14px; background: #f5f4f0; border-bottom: 1px solid rgba(0, 0, 0, 0.08);">
                <div style="width: 28px;"></div>
                <h3 class="m-0 text-center" style="font-size: 0.875rem; font-weight: 600; color: #374151; flex: 1;">Preview</h3>
                <button type="button" id="close-popup" class="flex items-center justify-center border-0 bg-transparent cursor-pointer transition-colors" style="width: 28px; height: 28px; min-width: 28px; min-height: 28px; color: #6b7280; font-size: 0.95rem; border-radius: 6px;" onmouseover="this.style.background='rgba(0,0,0,0.06)'; this.style.color='#374151';" onmouseout="this.style.background='transparent'; this.style.color='#6b7280';" onclick="document.getElementById('preview-popup').classList.remove('active'); document.getElementById('preview-popup').style.display=''; document.body.style.overflow='';">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex-1 overflow-hidden flex flex-col min-h-0" style="padding: 0 0 10px 0; background: #f5f4f0;">
                <iframe id="admin-message-preview-iframe" style="width:100%;height:100%;min-height:60vh;border:0;" title="Message preview"></iframe>
            </div>
        </div>
    </div>

    {{-- Global Admin Confirm Modal --}}
    <div id="adminGlobalConfirmModal" class="modal fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden" style="display: none;">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl p-6 text-center transform transition-all scale-95 opacity-0 duration-200">
            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto text-3xl text-blue-600 mb-4">
                <i class="fas fa-question-circle"></i>
            </div>
            <h4 class="text-xl font-bold text-slate-800 m-0 mb-2">Confirm Action</h4>
            <p id="adminGlobalConfirmMessage" class="text-sm text-slate-500 mb-6">Are you sure?</p>
            <div class="flex gap-3 justify-center">
                <button type="button" id="adminGlobalConfirmBtn" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl cursor-pointer border-0 transition-colors">Confirm</button>
                <button type="button" id="adminGlobalConfirmCancelBtn" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl cursor-pointer border-0 transition-colors">Cancel</button>
            </div>
        </div>
    </div>

    {{-- Global Admin Alert Modal --}}
    <div id="adminGlobalAlertModal" class="modal fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden" style="display: none;">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl p-6 text-center transform transition-all scale-95 opacity-0 duration-200">
            <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto text-3xl text-amber-500 mb-4">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h4 class="text-xl font-bold text-slate-800 m-0 mb-2">Alert</h4>
            <p id="adminGlobalAlertMessage" class="text-sm text-slate-500 mb-6">Notice.</p>
            <div class="flex justify-center">
                <button type="button" id="adminGlobalAlertBtn" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl cursor-pointer border-0 transition-colors">OK</button>
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
            var box = modal.querySelector('div.bg-white');

            msgEl.textContent = message;
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            
            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 10);

            var cleanup = function() {
                box.classList.remove('scale-100', 'opacity-100');
                box.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                }, 200);
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
            var box = modal.querySelector('div.bg-white');

            msgEl.textContent = message;
            modal.style.display = 'flex';
            modal.classList.remove('hidden');

            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 10);

            var cleanup = function() {
                box.classList.remove('scale-100', 'opacity-100');
                box.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                }, 200);
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
                document.querySelectorAll('.nav-dropdown').forEach(function (dropdown) {
                    var toggle = dropdown.querySelector('.dropdown-toggle');
                    var submenu = dropdown.querySelector('.nav-sub-menu');
                    if (!toggle || !submenu) return;
                    submenu.style.display = dropdown.classList.contains('open') ? 'block' : 'none';
                    toggle.addEventListener('click', function (e) {
                        e.preventDefault(); e.stopPropagation();
                        var isOpen = dropdown.classList.contains('open');
                        document.querySelectorAll('.nav-dropdown').forEach(function (other) {
                            other.classList.remove('open');
                            var sm = other.querySelector('.nav-sub-menu');
                            if (sm) sm.style.display = 'none';
                        });
                        if (!isOpen) { dropdown.classList.add('open'); submenu.style.display = 'block'; }
                    });
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
                        }
                    }
                });
            }
        })();
    </script>
    
    @yield('scripts')
    
    <style>
        @keyframes modalIn { from { opacity:0; transform:scale(.96) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }
    </style>
</body>

</html>
