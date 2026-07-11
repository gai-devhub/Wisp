<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="theme-color" content="#6366f1">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wisp User</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/user-page.css') }}?v={{ filemtime(public_path('css/user-page.css')) }}">

    @php
        $activeTheme = $userSettings->theme_preference ?? 'theme-default';
        $privacyEnabled = $userSettings->privacy_blur_enabled ?? false;
        $isUserSettingsNav = request()->routeIs(
            'user.settings.messages.page',
            'user.control-center.security.page',
            'user.settings.user.page',
        );
    @endphp
    
    <style>
    /* ===== HEADER SEARCH ===== */
    .header-search-form {
        display: block;
        max-width: 380px;
        margin: 0;
    }
    .header-search-wrap {
        display: flex;
        align-items: center;
        background: var(--card-bg, #f8fafc);
        border: 1.5px solid var(--border, #e2e8f0);
        border-radius: 12px;
        padding: 0 12px;
        gap: 8px;
        transition: border-color 0.2s, box-shadow 0.2s;
        height: 40px;
    }
    .header-search-wrap:focus-within {
        border-color: var(--primary, #6366f1);
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    .header-search-icon-inner { color: #94a3b8; font-size: 0.85rem; flex-shrink: 0; }
    .header-search-input {
        flex: 1;
        border: none;
        background: transparent;
        outline: none;
        font-size: 0.88rem;
        color: var(--text, #1e293b);
        min-width: 0;
    }
    .header-search-input::placeholder { color: #94a3b8; }
    .header-search-clear {
        color: #94a3b8;
        font-size: 0.8rem;
        text-decoration: none;
        transition: color 0.2s;
        flex-shrink: 0;
    }
    .header-search-clear:hover { color: #ef4444; }

    /* Mobile: icon only, form hidden by default */
    .header-search-mob-btn {
        display: none;
        width: 32px;
        height: 32px;
        align-items: center;
        justify-content: center;
        border: none;
        background: none;
        border-radius: var(--radius-sm, 8px);
        cursor: pointer;
        color: #94a3b8;
        transition: var(--transition, all 0.2s);
        -webkit-tap-highlight-color: transparent;
        padding: 0;
        margin: 0;
    }
    .header-search-mob-btn:hover {
        background: var(--bg-subtle, #f1f5f9);
        color: var(--primary, #6366f1);
    }
    .header-search-mob-btn i {
        font-size: 1.05rem;
    }

    @media (max-width: 768px) {
        .header-search-form {
            display: none;
            position: absolute;
            top: 100%;
            left: 0; right: 0;
            max-width: 100%;
            margin: 0;
            padding: 8px 16px;
            background: var(--card-bg, #fff);
            border-bottom: 1px solid var(--border, #e2e8f0);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            z-index: 100001;
            animation: searchSlideDown 0.2s ease;
        }
        .header-search-form.mob-open { display: block; }
        .header-search-mob-btn { display: flex; align-items: center; justify-content: center; }
        @keyframes searchSlideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    }
    </style>
</head>

<body data-active-section="@yield('user-section', 'dashboard')"
    class="{{ $activeTheme }} {{ $privacyEnabled ? 'privacy-blur-active' : '' }}">
    <div class="notification-container" id="notificationContainer"></div>

    @php $activeSection = View::yieldContent('user-section'); @endphp
    @if(in_array($activeSection, ['my-messages', 'create', 'edit']))
        <a href="{{ route('ai.page', ['from' => 'user']) }}" class="btn-ai-float"
            title="AI Assistant - get help writing your message" aria-label="Open AI Assistant">
            <img src="{{ asset('img/logo.png') }}" alt="AI">
            <i class="fas fa-robot ai-float-fallback" aria-hidden="true"></i>
        </a>
    @endif

    <div class="dashboard-container">
        <div class="header">
            <div class="header-left">
                <div class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </div>
                <a href="{{ route('user.page') }}" style="display: flex; align-items: center; gap: 0; text-decoration: none;">
                    <img src="{{ asset('img/logo.png') }}" alt="" class="header-logo">
                    <h2 style="color: var(--primary); margin: 0 0 0 4px;">WISP</h2>
                </a>
            </div>

            <div class="header-right">
                {{-- Global Search (desktop: full, mobile: icon → slide) --}}
                <form method="GET" action="{{ route('user.search.page') }}" class="header-search-form" id="headerSearchForm">
                    <div class="header-search-wrap" id="headerSearchWrap">
                        <i class="fas fa-search header-search-icon-inner"></i>
                        <input type="text" name="q" id="headerSearchInput"
                               value="{{ request('q') ?? '' }}"
                               placeholder="Search messages, snippets..."
                               class="header-search-input" autocomplete="off">
                        @if(request('q'))
                            <a href="{{ route('user.search.page') }}" class="header-search-clear" title="Clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>

                {{-- Group search and notifications closely --}}
                <div style="display: flex; align-items: center; gap: 4px;">
                    {{-- Mobile search icon --}}
                    <button type="button" class="header-search-mob-btn" id="headerSearchMobBtn" aria-label="Search">
                        <i class="fas fa-search"></i>
                    </button>
                    @php
                        $unreadCount = \App\Models\UserNotification::where('user_id', auth()->id())
                            ->whereNull('read_at')
                            ->count();
                    @endphp
                    <a href="{{ route('user.notifications.page') }}" class="notification-bell" id="notificationBell"
                        aria-label="Open notifications">
                        <i class="fas fa-bell"></i>
                        @if($unreadCount > 0)
                            <div class="notification-dot active" id="notificationDot"></div>
                        @endif
                    </a>
                </div>
                <a href="{{ route('user.settings.user.page') }}" class="user-profile" style="text-decoration: none; color: inherit;">
                    <img src="{{ route('profile.picture') }}" alt="{{ $user->username ?? 'User' }}"
                        data-fallback-src="https://ui-avatars.com/api/?name={{ urlencode($user->username ?? 'User') }}&color=7F9CF5&background=EBF4FF"
                        onerror="if(this.dataset.fallbackSrc) this.src=this.dataset.fallbackSrc">
                    <span><b>{{ $user->username ?? $user->username ?? 'User' }}</b></span>
                </a>
            </div>
        </div>

        <div class="main-wrapper">
            <div class="sidebar-overlay" id="sidebarOverlay"></div>

            <div class="sidebar" id="sidebar">
                <div class="sidebar-menu">
                    <ul>
                        @if(auth()->user()->role === 'admin')
                            <li><a href="{{ route('admin.page') }}"
                                    class="nav-link {{ request()->routeIs('admin.page') ? 'active' : '' }}"
                                    data-section="admin"><i class="fas fa-crown"></i> <span>Admin Dashboard</span></a>
                            </li>
                        @endif

                        <li><a href="{{ route('user.page') }}"
                                class="nav-link {{ request()->routeIs('user.page') ? 'active' : '' }}"
                                data-section="dashboard"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                        <li><a href="{{ route('user.my-messages.page') }}"
                                class="nav-link {{ request()->routeIs('user.my-messages.page') ? 'active' : '' }}"
                                data-section="my-messages"><i class="fas fa-envelope"></i> <span>My Messages</span></a>
                        </li>
                        <!-- <li><a href="{{ route('user.create.page') }}" class="nav-link {{ request()->routeIs('user.create.page') ? 'active' : '' }}" data-section="create"><i class="fas fa-edit"></i> <span>Create Message</span></a></li> -->
                        <!-- <li><a href="{{ route('user.media.page') }}"
                                class="nav-link {{ request()->routeIs('user.media.page') ? 'active' : '' }}"
                                data-section="media"><i class="fas fa-image"></i> <span>Media</span></a></li> -->
                        <!-- <li><a href="{{ route('user.template.page') }}"
                                class="nav-link {{ request()->routeIs('user.template.page') ? 'active' : '' }}"
                                data-section="template"><i class="fas fa-palette"></i> <span>Template</span></a></li> -->
                        <li><a href="{{ route('user.links.page') }}"
                                class="nav-link {{ request()->routeIs('user.links.page') ? 'active' : '' }}"
                                data-section="links"><i class="fas fa-link"></i> <span>Generated Links</span></a></li>
                        <li><a href="{{ route('user.share-messages.page') }}"
                                class="nav-link {{ request()->routeIs('user.share-messages.page') ? 'active' : '' }}"
                                data-section="share-messages"><i class="fas fa-comment"></i> <span>Shared
                                    Messages</span></a></li>
                        <li><a href="{{ route('user.notifications.page') }}"
                                class="nav-link {{ request()->routeIs('user.notifications.page') ? 'active' : '' }}"
                                data-section="notifications"><i class="fas fa-inbox"></i> <span>Notifications</span></a>
                        </li>
                        @php
                            $paymentEnabled = \Illuminate\Support\Facades\DB::table('system_settings')->where('key', 'payment_system_enabled')->value('value') === '1';
                            $hasActiveSubscription = false;
                            if ($paymentEnabled) {
                                $hasActiveSubscription = \App\Models\Subscription::where('user_id', auth()->id())
                                    ->where('status', 'active')
                                    ->where('expires_at', '>', now())
                                    ->exists();
                            }
                        @endphp
                        @if($paymentEnabled && !$hasActiveSubscription)
                        <li><a href="{{ route('user.billing.index') }}"
                                class="nav-link {{ request()->routeIs('user.billing.*') ? 'active' : '' }}"
                                data-section="billing"><i class="fas fa-credit-card"></i> <span>Billing</span></a>
                        </li>
                        @endif

                        <li class="has-dropdown {{ $isUserSettingsNav ? 'open' : '' }}">
                            <a href="#" class="dropdown-toggle {{ $isUserSettingsNav ? 'active' : '' }}">
                                <div><i class="fas fa-layer-group"></i> <span>Control Center</span></div>
                                <i class="fas fa-chevron-down mt-1"></i>
                            </a>
                            <ul class="sidebar-submenu">
                                <li><a href="{{ route('user.settings.messages.page') }}"
                                        class="nav-link {{ request()->routeIs('user.settings.messages.page') ? 'active' : '' }}"
                                        data-section="settings-messages"><i class="fas fa-envelope-open-text fa-fw text-muted me-2" style="font-size: 0.9em; opacity: 0.7;"></i> <span>Message Settings</span></a></li>
                                <li><a href="{{ route('user.control-center.security.page') }}"
                                        class="nav-link {{ request()->routeIs('user.control-center.security.page') ? 'active' : '' }}"
                                        data-section="control-center-security"><i class="fas fa-shield-alt fa-fw text-muted me-2" style="font-size: 0.9em; opacity: 0.7;"></i> <span>Security & Privacy</span></a></li>
                                <li><a href="{{ route('user.settings.user.page') }}"
                                        class="nav-link {{ request()->routeIs('user.settings.user.page') ? 'active' : '' }}"
                                        data-section="settings-user"><i class="fas fa-user-cog fa-fw text-muted me-2" style="font-size: 0.9em; opacity: 0.7;"></i> <span>User Settings</span></a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="sidebar-footer">
                    <form method="POST" action="{{ route('auth.logout') }}" class="sidebar-logout-form">
                        @csrf
                        <button type="submit" class="sidebar-logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="main-content">
                <div class="content-area">
                    @if(Auth::user()->isStorageFull())
                        <div class="alert alert-danger mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Storage Full!</strong> You have reached your 1GB limit. Please delete some messages or
                            media to free up space.
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <div class="preview-popup" id="preview-popup" data-preview-base-url="{{ route('templates.preview') }}">
        <div class="preview-popup-content">
            <div class="preview-popup-header">
                <div class="preview-header-left">
                    <button type="button" class="carousel-control carousel-control-header" id="prev-template"
                        aria-label="Previous template"><i class="fas fa-chevron-left"></i></button>
                </div>
                <div class="preview-header-center">
                    <h3>Preview Templates</h3>
                    <span class="preview-template-counter" id="preview-template-counter"></span>
                </div>
                <div class="preview-header-right">
                    <button type="button" class="carousel-control carousel-control-header" id="next-template"
                        aria-label="Next template"><i class="fas fa-chevron-right"></i></button>
                    <button type="button" class="close-popup" id="close-popup" aria-label="Close"><i
                            class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="preview-popup-body">
                <form id="template-select-form" method="POST" action="{{ route('templates.select') }}"
                    style="display:none;">
                    @csrf
                    <input type="hidden" name="message_id" id="template-message-id" />
                    <input type="hidden" name="template_name" id="template-name" value="view-1" />
                </form>
                <div class="preview-carousel-wrap" id="preview-carousel-wrap">
                    <div class="preview-carousel" id="preview-carousel-touch">
                        <div class="carousel-inner" id="carousel-inner">
                            @foreach(($templateList ?? []) as $idx => $tplKey)
                                <div class="carousel-item" data-template-index="{{ $idx }}"><iframe
                                        id="tpl-frame-{{ $idx + 1 }}" data-template-key="{{ $tplKey }}"
                                        style="width:100%;height:100%;border:0;"></iframe></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Typed Confirm Modal -->
    <div class="custom-modal" id="typed-confirm-modal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
        <div class="custom-modal-content"
            style="background: var(--card-bg, #ffffff); max-width: 400px; width: 90%; padding: 24px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <div class="custom-modal-header"
                style="border-bottom: none; padding: 0 0 16px 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 id="typed-confirm-title"
                    style="color: var(--danger); font-size: 1.2rem; font-weight: 700; margin: 0;">Confirm Action</h3>
                <button class="close-btn" onclick="closeTypedConfirmModal()"
                    style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted); padding: 0;"><i
                        class="fas fa-times"></i></button>
            </div>
            <div class="custom-modal-body" style="padding: 0;">
                <p id="typed-confirm-message" style="margin-bottom: 16px; font-size: 0.95rem; color: var(--text);"></p>
                <div class="form-group mb-0">
                    <label
                        style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted); display: block; margin-bottom: 8px;">
                        Type <span id="typed-confirm-expected-word"
                            style="font-weight: 800; color: var(--danger);">confirm</span> to proceed:
                    </label>
                    <input type="text" id="typed-confirm-input" class="form-control" autocomplete="off"
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px;">
                </div>
            </div>
            <div class="custom-modal-footer"
                style="border-top: none; padding: 24px 0 0 0; display: flex; justify-content: flex-end; gap: 12px;">
                <button class="btn btn-light" onclick="closeTypedConfirmModal()"
                    style="padding: 8px 16px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg); cursor: pointer; font-weight: 500;">Cancel</button>
                <button class="btn btn-danger" id="typed-confirm-btn" disabled
                    style="padding: 8px 16px; border-radius: 8px; border: none; background: var(--danger); color: white; cursor: pointer; font-weight: 500; opacity: 0.5;">Proceed</button>
            </div>
        </div>
    </div>

    <script>
        let wispConfirmCallback = null;
        let wispExpectedWord = 'confirm';

        function showTypedConfirmModal(message, callback, expectedWord = 'confirm') {
            document.getElementById('typed-confirm-message').innerText = message;
            document.getElementById('typed-confirm-expected-word').innerText = expectedWord;
            document.getElementById('typed-confirm-input').value = '';
            document.getElementById('typed-confirm-input').placeholder = 'Type "' + expectedWord + '"';
            document.getElementById('typed-confirm-btn').disabled = true;
            document.getElementById('typed-confirm-btn').style.opacity = '0.5';

            wispConfirmCallback = callback;
            wispExpectedWord = expectedWord.toLowerCase();

            document.getElementById('typed-confirm-modal').style.display = 'flex';
            setTimeout(() => { document.getElementById('typed-confirm-input').focus(); }, 50);
        }

        function closeTypedConfirmModal() {
            document.getElementById('typed-confirm-modal').style.display = 'none';
            wispConfirmCallback = null;
        }

        document.getElementById('typed-confirm-input').addEventListener('input', function (e) {
            const val = e.target.value.trim().toLowerCase();
            const btn = document.getElementById('typed-confirm-btn');
            if (val === wispExpectedWord) {
                btn.disabled = false;
                btn.style.opacity = '1';
            } else {
                btn.disabled = true;
                btn.style.opacity = '0.5';
            }
        });

        document.getElementById('typed-confirm-btn').addEventListener('click', function () {
            if (!this.disabled && typeof wispConfirmCallback === 'function') {
                const cb = wispConfirmCallback;
                closeTypedConfirmModal();
                cb();
            }
        });

        function confirmFormSubmit(event, formElement, message, expectedWord = 'confirm') {
            event.preventDefault();
            showTypedConfirmModal(message, function () {
                formElement.submit();
            }, expectedWord);
            return false;
        }
    </script>

    <script>
        window.WISP_ROUTES = {
            templatesPreview: "{{ route('templates.preview') }}",
            templatesSelect: "{{ route('templates.select') }}",
            messagesStore: "{{ route('messages.store') }}",
            shareMessagesSendEmail: "{{ route('share-messages.sendEmail') }}",
            shareMessagesSendSms: "{{ route('share-messages.sendSms') }}",
            notificationsIndex: "{{ route('notifications.index') }}"
        };
        window.WISP_TEMPLATE_LIST = <?php echo json_encode($templateList ?? []); ?>;
        window.WISP_MESSAGE_HAS_TEMPLATE = <?php echo json_encode(collect($allMessagesForEdit ?? [])->pluck('is_published', 'id')->toArray()); ?>;
        window.WISP_EDIT_MESSAGES = <?php echo json_encode(collect($allMessagesForEdit ?? [])->keyBy('id')->map(function ($m) {
    return ['id' => $m->id, 'message_type' => $m->message_type, 'page_title' => $m->title, 'recipient_full_name' => $m->recipient_name, 'recipient_name' => $m->recipient_special_name, 'greeting' => $m->greeting, 'wish_message' => $m->message ?? $m->wish_message ?? '', 'last_note' => $m->last_note, 'receiving_date' => $m->receiving_date ? (\Carbon\Carbon::parse($m->receiving_date)->format('Y-m-d')) : '', 'sender_name' => $m->sender_name];
})->toArray()); ?>;
        window.WISP_ACTIVE_SECTION = "@yield('user-section', 'dashboard')";
        window.WISP_VIEWS_LAST_7_DAYS = <?php echo json_encode($viewsLast7Days ?? []); ?>;
        window.WISP_DASHBOARD_STATS = <?php echo json_encode($dashboardStats ?? []); ?>;
        window.WISP_FLASH = {
            success: <?php echo json_encode(session('success')); ?>,
            error: <?php echo json_encode(session('error')); ?>,
            errors: <?php echo json_encode(isset($errors) && $errors->any() ? $errors->all() : []); ?>
        };
    </script>
    <script src="{{ asset('js/user-page.js') }}?v={{ filemtime(public_path('js/user-page.js')) }}"></script>
    @stack('scripts')

<script>
(function() {
    var form   = document.getElementById('headerSearchForm');
    var input  = document.getElementById('headerSearchInput');
    var mobBtn = document.getElementById('headerSearchMobBtn');
    
    if (input) {
        input.addEventListener('input', function() {
            var val = input.value;
            if (window.location.pathname !== '/user-page/search') {
                if (val.trim().length > 0) {
                    window.location.href = '/user-page/search?q=' + encodeURIComponent(val);
                }
            } else {
                // Trigger live search if we are already on search page
                if (typeof triggerLiveSearch === 'function') {
                    triggerLiveSearch(val);
                }
            }
        });
        
        // Auto-focus search input on load of the search page and place cursor at end
        if (window.location.pathname === '/user-page/search') {
            input.focus();
            var val = input.value;
            input.value = '';
            input.value = val;
        }
    }
    
    if (mobBtn && form) {
        mobBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            form.classList.toggle('mob-open');
            if (form.classList.contains('mob-open') && input) {
                input.focus();
            }
        });
        document.addEventListener('click', function(e) {
            if (!form.contains(e.target) && e.target !== mobBtn) {
                form.classList.remove('mob-open');
            }
        });
    }
})();
</script>
</body>


@push('scripts')
<script>
// Template Preview Popup Functionality (component-based)
const previewPopup = document.getElementById('preview-popup');
const closePopup = document.getElementById('close-popup');
const carouselInner = document.getElementById('carousel-inner');
const dots = document.querySelectorAll('.dot');
let currentTemplateIndex = 0;
let currentPreviewMessageId = null;

function getTotalTemplates() {
    return (window.WISP_TEMPLATE_LIST && window.WISP_TEMPLATE_LIST.length) ? window.WISP_TEMPLATE_LIST.length : 6;
}

function loadIframes(messageId) {
    var total = getTotalTemplates();
    for (var i = 1; i <= total; i++) {
        var frame = document.getElementById('tpl-frame-' + i);
        if (frame) {
            frame.removeAttribute('data-loaded');
            frame.src = 'about:blank';
        }
    }
    var midEl = document.getElementById('template-message-id');
    if (midEl) midEl.value = messageId;
}

function loadFrameIfNeeded(index) {
    var list = window.WISP_TEMPLATE_LIST;
    if (!list || !list[index]) return;
    var frame = document.getElementById('tpl-frame-' + (index + 1));
    if (!frame || frame.getAttribute('data-loaded')) return;
    var messageId = currentPreviewMessageId;
    if (!messageId) return;
    var baseUrl = (window.WISP_ROUTES && window.WISP_ROUTES.templatesPreview) + '?message_id=' + encodeURIComponent(messageId) + '&template=';
    frame.src = baseUrl + encodeURIComponent(list[index]);
    frame.setAttribute('data-loaded', '1');
}

function updatePreviewCounter() {
    var total = getTotalTemplates();
    var text = 'Template ' + (currentTemplateIndex + 1) + ' of ' + total;
    var el = document.getElementById('preview-template-counter');
    if (el) el.textContent = text;
}

function goToTemplate(index) {
    var total = getTotalTemplates();
    if (total <= 0) return;
    if (index < 0) index = total - 1;
    if (index >= total) index = 0;
    currentTemplateIndex = index;
    if (carouselInner) carouselInner.style.transform = 'translateX(-' + currentTemplateIndex * 100 + '%)';
    dots.forEach(function (dot, i) {
        dot.classList.toggle('active', i === currentTemplateIndex);
        dot.setAttribute('aria-selected', i === currentTemplateIndex ? 'true' : 'false');
    });
    updatePreviewCounter();
    var templateNameEl = document.getElementById('template-name');
    if (templateNameEl && window.WISP_TEMPLATE_LIST && window.WISP_TEMPLATE_LIST[currentTemplateIndex])
        templateNameEl.value = window.WISP_TEMPLATE_LIST[currentTemplateIndex];
    loadFrameIfNeeded(currentTemplateIndex);
    var headerSave = document.getElementById('header-save-template-btn');
    if (headerSave) {
        headerSave.setAttribute('data-template-index', currentTemplateIndex);
    }
}

function updatePreviewSaveButton() {
    var saveBtn = document.getElementById('preview-save-template-btn');
    if (!saveBtn) return;
    var hasTemplate = window.WISP_MESSAGE_HAS_TEMPLATE && currentPreviewMessageId && window.WISP_MESSAGE_HAS_TEMPLATE[currentPreviewMessageId];
    saveBtn.style.display = hasTemplate ? 'none' : '';
}

function goNext() { goToTemplate(currentTemplateIndex + 1); }
function goPrev() { goToTemplate(currentTemplateIndex - 1); }

var nextTplBtn = document.getElementById('next-template');
var prevTplBtn = document.getElementById('prev-template');
if (nextTplBtn) nextTplBtn.addEventListener('click', goNext);
if (prevTplBtn) prevTplBtn.addEventListener('click', goPrev);
dots.forEach(function (dot) { dot.addEventListener('click', function () { goToTemplate(parseInt(dot.getAttribute('data-index'), 10)); }); });

// Remove save button from preview modal since we are taking it out
  var previewSaveBtn = document.getElementById('preview-save-template-btn');
  if (previewSaveBtn) {
      previewSaveBtn.style.display = 'none';
  }
  
  // Header save button logic for selection mode
  var headerSaveBtn = document.getElementById('header-save-template-btn');
  if (headerSaveBtn) {
      headerSaveBtn.addEventListener('click', function () {
          var templateNameEl = document.getElementById('template-name');
          var messageIdEl = document.getElementById('template-message-id');
          var msgSelect = document.getElementById('template-page-message-id');
          
          if (msgSelect && messageIdEl) {
              messageIdEl.value = msgSelect.value;
          }
          
          var tplIndex = parseInt(headerSaveBtn.getAttribute('data-template-index'), 10) || 0;
          if (templateNameEl && window.WISP_TEMPLATE_LIST && window.WISP_TEMPLATE_LIST[tplIndex]) {
              templateNameEl.value = window.WISP_TEMPLATE_LIST[tplIndex];
          }
          
          document.getElementById('template-select-form').submit();
      });
  }

// Touch swipe: left/right slide to next/prev template on phone
var carouselTouch = document.getElementById('preview-carousel-touch');
if (carouselTouch) {
    var touchStartX = 0;
    carouselTouch.addEventListener('touchstart', function (e) {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });
    carouselTouch.addEventListener('touchend', function (e) {
        if (previewSingleMessageMode) return;
        var touchEndX = e.changedTouches[0].clientX;
        var delta = touchStartX - touchEndX;
        if (delta > 50) goNext();
        else if (delta < -50) goPrev();
    }, { passive: true });
}

// Open popup and load component previews
function onPreviewClick(e, btn) {
    e.preventDefault();
    setPreviewSingleMessageMode(false);
    const source = btn.getAttribute('data-source');
    if (source === 'create') {
        const dateEl = document.getElementById('receiving-date');
        const dateVal = dateEl && dateEl.value ? dateEl.value : '';
        const resolverUrl = dateVal ?
            (window.WISP_ROUTES && window.WISP_ROUTES.templatesFindByDate) + '?date=' + encodeURIComponent(dateVal) :
            (window.WISP_ROUTES && window.WISP_ROUTES.templatesLatest);
        fetch(resolverUrl)
            .then(async (res) => {
                if (!res.ok) throw new Error('No message found to preview.');
                return res.json();
            })
            .then((data) => {
                if (!data.found) throw new Error('No message found to preview.');
                currentPreviewMessageId = String(data.message_id);
                loadIframes(data.message_id);
                previewPopup.classList.add('active');
                document.body.style.overflow = 'hidden';
                goToTemplate(0);
                updatePreviewSaveButton();
            })
            .catch((err) => alert(err.message));
    } else {
        const selectEl = document.getElementById('template-page-message-id');
        const messageId = selectEl && selectEl.value ? selectEl.value : '';
        if (!messageId) {
            alert('Please select a message first.');
            return;
        }
        currentPreviewMessageId = String(messageId);
        loadIframes(messageId);
        previewPopup.classList.add('active');
        document.body.style.overflow = 'hidden';
        goToTemplate(0);
        updatePreviewSaveButton();
    }
}

document.querySelectorAll('.preview-trigger').forEach(btn => {
    btn.addEventListener('click', (e) => onPreviewClick(e, btn));
});
const previewTriggerEl = document.getElementById('preview-trigger');
if (previewTriggerEl) {
    previewTriggerEl.addEventListener('click', (e) => onPreviewClick(e, previewTriggerEl));
}

// Close popup
if (closePopup) closePopup.addEventListener('click', function () {
    setPreviewSingleMessageMode(false);
    if (previewPopup) previewPopup.classList.remove('active');
    document.body.style.overflow = '';
});
if (previewPopup) previewPopup.addEventListener('click', function (e) {
    if (e.target === previewPopup) {
        setPreviewSingleMessageMode(false);
        previewPopup.classList.remove('active');
        document.body.style.overflow = '';
    }
});
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && previewPopup && previewPopup.classList.contains('active')) {
        setPreviewSingleMessageMode(false);
        previewPopup.classList.remove('active');
        document.body.style.overflow = '';
    }
});
// My Messages: Preview button opens the preview modal with that message's template only (no arrows)
var previewSingleMessageMode = false;
function setPreviewSingleMessageMode(single) {
    previewSingleMessageMode = !!single;
    var prevBtn = document.getElementById('prev-template');
    var nextBtn = document.getElementById('next-template');
    var counterEl = document.getElementById('preview-template-counter');
    var headerCenter = document.querySelector('.preview-header-center h3');
    if (prevBtn) prevBtn.style.display = single ? 'none' : '';
    if (nextBtn) nextBtn.style.display = single ? 'none' : '';
    if (counterEl) counterEl.style.display = single ? 'none' : '';
    if (headerCenter) headerCenter.textContent = single ? 'Preview' : 'Preview Templates';
    previewPopup.classList.toggle('preview-single-message', single);
}
document.addEventListener('click', function (e) {
    var btn = e.target && e.target.closest && e.target.closest('.open-preview-modal');
    if (!btn) return;
    e.preventDefault();
    var messageId = btn.getAttribute('data-message-id') || (btn.closest('tr') && btn.closest('tr').getAttribute('data-message-id'));
    if (!messageId) return;
    var templateIndex = btn.getAttribute('data-template-index');
    var templateName = btn.getAttribute('data-template-name');
    currentPreviewMessageId = String(messageId);
    loadIframes(messageId);
    previewPopup.classList.add('active');
    document.body.style.overflow = 'hidden';
    var index = templateIndex !== null && templateIndex !== '' ? parseInt(templateIndex, 10) : 0;
    if (isNaN(index)) index = 0;
    goToTemplate(index);
    // From My Messages: show only this message's template, hide arrows and counter
    setPreviewSingleMessageMode(templateIndex !== null && templateIndex !== '' && templateName);
    updatePreviewSaveButton();
    var saveBtn = document.getElementById('preview-save-template-btn');
    if (saveBtn && previewSingleMessageMode) saveBtn.style.display = 'none';
});

</script>
@endpush
</body></html>
