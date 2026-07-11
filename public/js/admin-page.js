/**
 * =============================================================================
 * WISP Admin Panel — admin-page.js
 * =============================================================================
 * Structure:
 *   1.  Toast Notification System
 *   2.  Theme Management
 *   3.  Sidebar & Mobile Navigation
 *   4.  Modal Management
 *   5.  Action Button Handlers (Users & Messages)
 *   6.  Global Search & Date Filter (dispatches to table filters)
 *   7.  User Table Filtering
 *   8.  Message Table Filtering
 *   9.  Log Table Filtering
 *   10. Gmail-style Notification Panel
 *   11. Analytics Charts & KPIs
 *   12. Activity Table Pagination
 *   13. Bulk Operations
 *   14. Export Link Helpers
 *   15. Flash Message Handler
 *   16. Link Copy Buttons
 *   17. Initialization (DOMContentLoaded)
 * =============================================================================
 */

(function () {
    'use strict';

    /* =========================================================================
       1. TOAST NOTIFICATION SYSTEM
    ========================================================================= */

    const ICONS = {
        success: 'fas fa-check-circle',
        error:   'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info:    'fas fa-info-circle',
    };

    /**
     * Shows a toast notification.
     * @param {'success'|'error'|'warning'|'info'} type
     * @param {string} title
     * @param {string} message
     * @param {number} [duration=5000] - ms before auto-close. 0 = permanent.
     * @returns {string} notificationId
     */
    function showNotification(type, title, message, duration) {
        if (duration === undefined) duration = 5000;
        const container = document.getElementById('notificationContainer');
        if (!container) return '';

        const id   = 'ntf-' + Date.now();
        const icon = ICONS[type] || ICONS.info;

        const el = document.createElement('div');
        el.id        = id;
        el.className = 'notification-toast ' + (type || 'info');
        el.innerHTML =
            '<div class="notification-toast-icon"><i class="' + icon + '"></i></div>' +
            '<div class="notification-toast-content"><h4>' + _esc(title) + '</h4><p>' + _esc(message) + '</p></div>' +
            '<button class="notification-toast-close" onclick="window.closeNotification(\'' + id + '\')" aria-label="Close">' +
            '<i class="fas fa-times"></i></button>';

        container.appendChild(el);

        // Animate in
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { el.classList.add('show'); });
        });

        if (duration > 0) {
            setTimeout(function () { closeNotification(id); }, duration);
        }

        return id;
    }

    /** Removes a toast by id. */
    function closeNotification(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('show');
        setTimeout(function () { el && el.parentNode && el.parentNode.removeChild(el); }, 350);
    }

    /** Simple HTML escape. */
    function _esc(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // Expose globally for inline onclick handlers
    window.closeNotification = closeNotification;
    window.showNotification  = showNotification;


    /* =========================================================================
       2. THEME MANAGEMENT
    ========================================================================= */

    function initTheme() {
        const html  = document.documentElement;
        const btn   = document.getElementById('themeToggleBtn');
        const icon  = btn ? btn.querySelector('i') : null;
        const saved = localStorage.getItem('wisp-admin-theme');

        const applyTheme = function (theme) {
            html.setAttribute('data-theme', theme);
            if (icon) {
                icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        };

        // Restore saved or system preference
        if (saved) {
            applyTheme(saved);
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            applyTheme('dark');
        }

        if (btn) {
            btn.addEventListener('click', function () {
                const current  = html.getAttribute('data-theme') || 'light';
                const newTheme = current === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
                localStorage.setItem('wisp-admin-theme', newTheme);
                showNotification('info', 'Theme', 'Switched to ' + newTheme + ' mode.', 1800);
            });
        }
    }


    /* =========================================================================
       3. SIDEBAR & MOBILE NAVIGATION
    ========================================================================= */

    function initSidebar() {
        const toggle  = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (!toggle || !sidebar) return;

        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
        });

        if (overlay) {
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }

        // Close sidebar on mobile when a real nav link is clicked
        document.querySelectorAll('.nav-link:not(.dropdown-toggle)').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 992) {
                    sidebar.classList.remove('active');
                    if (overlay) overlay.classList.remove('active');
                }
            });
        });
    }


    /* =========================================================================
       4. MODAL MANAGEMENT
    ========================================================================= */

    /** Open a modal by ID or element. */
    function openModal(modalOrId) {
        var el = typeof modalOrId === 'string' ? document.getElementById(modalOrId) : modalOrId;
        if (el) el.classList.add('active');
    }

    /** Close a modal by ID or element. */
    function closeModal(modalOrId) {
        var el = typeof modalOrId === 'string' ? document.getElementById(modalOrId) : modalOrId;
        if (el) el.classList.remove('active');
    }

    function initModals() {
        // Close modal when clicking the backdrop
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('modal') && e.target.classList.contains('active')) {
                closeModal(e.target);
            }
        });

        // Close with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal.active').forEach(closeModal);
                // Also close preview popup
                var pp = document.getElementById('preview-popup');
                if (pp && pp.classList.contains('active')) {
                    pp.classList.remove('active');
                    document.body.style.overflow = '';
                    var iframe = document.getElementById('admin-message-preview-iframe');
                    if (iframe) iframe.src = 'about:blank';
                }
            }
        });

        // Wire up all .modal-close buttons
        document.querySelectorAll('.modal-close').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var modal = btn.closest('.modal');
                if (modal) closeModal(modal);
            });
        });

        // Specific cancel / close buttons
        _bindClose('closeViewUserModal',    'viewUserModal');
        _bindClose('closeViewModal',        'viewUserModal');
        _bindClose('cancelEditUser',        'editUserModal');
        _bindClose('closeEditUserModal',    'editUserModal');
        _bindClose('cancelBlockUser',       'blockUserModal');
        _bindClose('closeBlockUserModal',   'blockUserModal');
        _bindClose('cancelDeleteUser',      'deleteUserModal');
        _bindClose('closeDeleteUserModal',  'deleteUserModal');
        _bindClose('cancelEditMessage',     'editMessageModal');
        _bindClose('closeEditMessageModal', 'editMessageModal');
        _bindClose('cancelDeleteMessage',   'deleteMessageModal');
        _bindClose('closeDeleteMessageModal', 'deleteMessageModal');

        // Add user modal
        _bindOpen('addUserBtn', 'addUserModal');

        // Send notification modal
        _bindOpen('sendNotificationBtn', 'sendNotificationModal');

        // Preview popup close
        var closePopupBtn = document.getElementById('close-popup');
        var previewPopup  = document.getElementById('preview-popup');
        var previewIframe = document.getElementById('admin-message-preview-iframe');

        function closePreview() {
            if (previewPopup) { previewPopup.classList.remove('active'); document.body.style.overflow = ''; }
            if (previewIframe) previewIframe.src = 'about:blank';
        }

        if (closePopupBtn) closePopupBtn.addEventListener('click', closePreview);
        if (previewPopup) {
            previewPopup.addEventListener('click', function (e) {
                if (e.target === previewPopup) closePreview();
            });
        }
    }

    function _bindOpen(btnId, modalId) {
        var btn = document.getElementById(btnId);
        if (btn) btn.addEventListener('click', function () { openModal(modalId); });
    }

    function _bindClose(btnId, modalId) {
        var btn = document.getElementById(btnId);
        if (btn) btn.addEventListener('click', function () { closeModal(modalId); });
    }

    /* =========================================================================
       4.5 GLOBALLY EXPOSED MODAL HANDLERS
    ========================================================================= */
    window.openUserModal = function(btn) {
        var tr = btn.closest('tr');
        var editUserForm = document.getElementById('editUserForm');
        if (editUserForm) editUserForm.action = btn.getAttribute('data-action') || '';
        
        var un = document.getElementById('editUserName');
        var ue = document.getElementById('editUserEmail');
        var us = document.getElementById('editUserStatus');
        
        if (un && tr) un.value = tr.getAttribute('data-username') || '';
        if (ue && tr) ue.value = tr.getAttribute('data-email') || '';
        if (us) us.value = btn.getAttribute('data-status') || (tr ? tr.getAttribute('data-status') : '') || 'active';
        
        openModal('editUserModal');
    };

    window.openBlockModal = function(btn) {
        var tr = btn.closest('tr');
        var blockUserForm = document.getElementById('blockUserForm');
        if (blockUserForm) blockUserForm.action = btn.getAttribute('data-action') || '';
        
        var bn = document.getElementById('blockUserName');
        if (bn && tr) bn.textContent = tr.getAttribute('data-username') || 'User';
        
        openModal('blockUserModal');
    };

    window.openDeleteModal = function(btn) {
        var tr = btn.closest('tr');
        var deleteUserForm = document.getElementById('deleteUserForm');
        if (deleteUserForm) deleteUserForm.action = btn.getAttribute('data-action') || '';
        
        var dn = document.getElementById('deleteUserName');
        if (dn) dn.textContent = btn.getAttribute('data-username') || (tr ? tr.getAttribute('data-username') : '') || 'User';
        
        openModal('deleteUserModal');
    };


    /* =========================================================================
       5. ACTION BUTTON HANDLERS (Users & Messages)
    ========================================================================= */

    // Stores user data while View modal is open (for "Edit from View")
    var _currentViewUser = null;

    function initActionButtons() {
        var editUserForm   = document.getElementById('editUserForm');
        var blockUserForm  = document.getElementById('blockUserForm');
        var deleteUserForm = document.getElementById('deleteUserForm');
        var previewPopup   = document.getElementById('preview-popup');
        var previewIframe  = document.getElementById('admin-message-preview-iframe');

        // VIEW buttons
        document.querySelectorAll('.action-btn.view').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (btn.hasAttribute('data-user-id')) {
                    // User view
                    _currentViewUser = {
                        action:   btn.getAttribute('data-edit-action') || '',
                        username: btn.getAttribute('data-username') || '',
                        email:    btn.getAttribute('data-email') || '',
                        status:   btn.getAttribute('data-status') || 'active',
                    };
                    var vn = document.getElementById('viewUserName');
                    var vd = document.getElementById('viewUserDetails');
                    if (vn) vn.textContent = _currentViewUser.username || 'User';
                    if (vd) vd.textContent =
                        (_currentViewUser.email || '') +
                        ' • Status: ' + (_currentViewUser.status || '') +
                        ' • Role: ' + (btn.getAttribute('data-role') || '');
                    openModal('viewUserModal');
                } else if (btn.hasAttribute('data-message-id')) {
                    // Message preview
                    var url = btn.getAttribute('data-preview-url');
                    if (previewIframe && url) {
                        previewIframe.src = url;
                        previewIframe.onload = function () {
                            try {
                                var iDoc = previewIframe.contentDocument || previewIframe.contentWindow.document;
                                if (iDoc) {
                                    var style = iDoc.createElement('style');
                                    style.textContent = [
                                        '::-webkit-scrollbar { width: 4px; height: 4px; }',
                                        '::-webkit-scrollbar-track { background: transparent; }',
                                        '::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.18); border-radius: 4px; }',
                                        '::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.32); }',
                                        '* { scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.18) transparent; }'
                                    ].join('\n');
                                    (iDoc.head || iDoc.documentElement).appendChild(style);
                                }
                            } catch(err) { /* cross-origin — ignore */ }
                        };
                    }
                    if (previewPopup) { previewPopup.classList.add('active'); document.body.style.overflow = 'hidden'; }
                }
            });
        });

        // "Edit from View" button inside the View modal
        var editFromView = document.getElementById('editUserFromView');
        if (editFromView) {
            editFromView.addEventListener('click', function () {
                closeModal('viewUserModal');
                if (_currentViewUser && editUserForm) {
                    editUserForm.action = _currentViewUser.action || '';
                    var un = document.getElementById('editUserName');
                    var ue = document.getElementById('editUserEmail');
                    var us = document.getElementById('editUserStatus');
                    if (un) un.value = _currentViewUser.username || '';
                    if (ue) ue.value = _currentViewUser.email || '';
                    if (us) us.value = _currentViewUser.status || 'active';
                }
                openModal('editUserModal');
            });
        }

        // EDIT buttons
        document.querySelectorAll('.action-btn.edit').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (btn.hasAttribute('data-user-id')) {
                    if (editUserForm) editUserForm.action = btn.getAttribute('data-action') || '';
                    var un = document.getElementById('editUserName');
                    var ue = document.getElementById('editUserEmail');
                    var us = document.getElementById('editUserStatus');
                    if (un) un.value = btn.getAttribute('data-username') || '';
                    if (ue) ue.value = btn.getAttribute('data-email') || '';
                    if (us) us.value = btn.getAttribute('data-status') || 'active';
                    openModal('editUserModal');
                } else if (btn.hasAttribute('data-message-id')) {
                    var form = document.getElementById('editMessageForm');
                    if (form) form.action = btn.getAttribute('data-edit-action') || '';
                    var et = document.getElementById('editMessageTitle');
                    var ec = document.getElementById('editMessageContent');
                    var es = document.getElementById('editMessageStatus');
                    if (et) et.value = btn.getAttribute('data-title') || '';
                    if (ec) ec.value = btn.getAttribute('data-content') || '';
                    if (es) es.value = btn.getAttribute('data-status') || '0';
                    openModal('editMessageModal');
                }
            });
        });

        // BLOCK buttons
        document.querySelectorAll('.action-btn.block').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (blockUserForm) blockUserForm.action = btn.getAttribute('data-action') || '';
                var bn = document.getElementById('blockUserName');
                if (bn) bn.textContent = btn.getAttribute('data-username') || 'User';
                openModal('blockUserModal');
            });
        });

        // DELETE buttons
        document.querySelectorAll('.action-btn.delete').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (btn.hasAttribute('data-user-id')) {
                    if (deleteUserForm) deleteUserForm.action = btn.getAttribute('data-action') || '';
                    var dn = document.getElementById('deleteUserName');
                    if (dn) dn.textContent = btn.getAttribute('data-username') || 'User';
                    openModal('deleteUserModal');
                } else if (btn.hasAttribute('data-message-id')) {
                    var form = document.getElementById('deleteMessageForm');
                    if (form) form.action = btn.getAttribute('data-action') || '';
                    var dt = document.getElementById('deleteMessageTitle');
                    if (dt) dt.textContent = btn.getAttribute('data-title') || 'Message';
                    openModal('deleteMessageModal');
                }
            });
        });

        // Other page-specific controls
        _initSystemControls();
    }

    function _initSystemControls() {
        var checkBtn   = document.getElementById('checkUpdatesBtn');
        var installBtn = document.getElementById('installUpdatesBtn');

        if (checkBtn && installBtn) {
            checkBtn.addEventListener('click', function () {
                showNotification('info', 'Checking for Updates', 'Searching for available system updates…', 4000);
                setTimeout(function () {
                    showNotification('success', 'Update Available', 'A new system update is ready to install.', 4000);
                    installBtn.disabled = false;
                }, 2000);
            });
        }

        if (installBtn) {
            installBtn.addEventListener('click', function () {
                showNotification('info', 'Installing Update', 'Please wait, installing system updates…', 4000);
                setTimeout(function () {
                    showNotification('success', 'Update Installed', 'System updated successfully. Please restart.', 4000);
                    installBtn.disabled = true;
                }, 3000);
            });
        }

        var refreshMetrics = document.getElementById('refreshMetrics');
        if (refreshMetrics) {
            refreshMetrics.addEventListener('click', function () {
                showNotification('info', 'Refreshing', 'Updating performance metrics…', 2500);
                setTimeout(function () {
                    var cpu = document.getElementById('cpuUsage');
                    var mem = document.getElementById('memoryUsage');
                    if (cpu) cpu.textContent = Math.floor(Math.random() * 30 + 40) + '%';
                    if (mem) mem.textContent = Math.floor(Math.random() * 20 + 50) + '%';
                }, 900);
            });
        }

        var profilePicInput = document.getElementById('adminProfilePicture');
        var profilePicPrev  = document.getElementById('adminAvatarPreview');
        if (profilePicInput && profilePicPrev) {
            profilePicInput.addEventListener('change', function () {
                var file = this.files && this.files[0];
                if (file && file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function (e) { profilePicPrev.src = e.target.result; };
                    reader.readAsDataURL(file);
                }
            });
        }
    }


    /* =========================================================================
       6. GLOBAL SEARCH & DATE FILTER
       Dispatches changes to all active table filters on the page.
    ========================================================================= */

    function initGlobalSearch() {
        var searchInput = document.getElementById('globalSearchInput');
        var dateInput   = document.getElementById('globalDateFilter');

        function dispatchAll() {
            filterUserTable();
            filterMessageTable();
            filterLogsTable();
            filterBulkUsersTable();
            filterBulkMessagesTable();
            applyGmailFilters();
        }

        if (searchInput) searchInput.addEventListener('input', dispatchAll);
        if (dateInput)   dateInput.addEventListener('change', dispatchAll);
    }


    /* =========================================================================
       7. USER TABLE FILTERING
    ========================================================================= */

    function filterUserTable() {
        var tbody = document.querySelector('#usersTable tbody');
        if (!tbody) return;

        var search       = _searchVal();
        var statusFilter = _val('userFilterStatus');
        var roleFilter   = _val('userFilterRole');
        var dataRows     = tbody.querySelectorAll('tr[data-user-id]');
        var emptyRow     = tbody.querySelector('.user-row-empty');
        var noResultsRow = tbody.querySelector('.user-row-no-results');
        var visible      = 0;

        dataRows.forEach(function (tr) {
            var show =
                _matchSearch(search, tr, ['data-username', 'data-email', 'data-user-id']) &&
                _matchAttr(statusFilter, tr, 'data-status') &&
                _matchAttr(roleFilter, tr, 'data-role');
            tr.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        _toggleEmpty(emptyRow, noResultsRow, dataRows.length, visible);
    }

    // Wire up user-specific filters (called during init)
    function _initUserFilters() {
        _onChange('userFilterStatus', filterUserTable);
        _onChange('userFilterRole',   filterUserTable);
    }


    /* =========================================================================
       8. MESSAGE TABLE FILTERING
    ========================================================================= */

    function filterMessageTable() {
        var tbody = document.querySelector('#messagesTable tbody');
        if (!tbody) return;

        var search       = _searchVal();
        var statusFilter = _val('messageFilterStatus');
        var dataRows     = tbody.querySelectorAll('tr.message-data-row');
        var emptyRow     = tbody.querySelector('.message-row-empty');
        var noResultsRow = tbody.querySelector('.message-row-no-results');
        var visible      = 0;

        dataRows.forEach(function (tr) {
            var show =
                _matchSearch(search, tr, ['data-title', 'data-recipient', 'data-creator', 'data-msg-id']) &&
                _matchAttr(statusFilter, tr, 'data-status');
            tr.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        _toggleEmpty(emptyRow, noResultsRow, dataRows.length, visible);
    }

    function _initMessageFilters() {
        _onChange('messageFilterStatus', filterMessageTable);
    }


    /* =========================================================================
       9. LOG TABLE FILTERING
    ========================================================================= */

    function filterLogsTable() {
        var tbody = document.querySelector('#logsTable tbody');
        if (!tbody) return;

        var search      = _searchVal();
        var typeFilter  = _val('logFilterType');
        var dateFilter  = _val('globalDateFilter');
        var dataRows    = tbody.querySelectorAll('tr.log-data-row');
        var emptyRow    = tbody.querySelector('.log-row-empty');
        var noResults   = tbody.querySelector('.log-row-no-results');
        var visible     = 0;

        dataRows.forEach(function (tr) {
            var matchSearch = !search ||
                (tr.getAttribute('data-activity') || '').toLowerCase().includes(search) ||
                (tr.getAttribute('data-user')     || '').toLowerCase().includes(search) ||
                (tr.getAttribute('data-details')  || '').toLowerCase().includes(search);
            var matchType = !typeFilter ||
                (tr.getAttribute('data-activity') || '').toLowerCase() === typeFilter;
            var matchDate = !dateFilter ||
                (tr.getAttribute('data-date') || '') === dateFilter;

            var show = matchSearch && matchType && matchDate;
            tr.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        _toggleEmpty(emptyRow, noResults, dataRows.length, visible);
    }

    function _initLogFilters() {
        _onChange('logFilterType', filterLogsTable);
    }


    /* =========================================================================
       10. GMAIL-STYLE NOTIFICATION PANEL
    ========================================================================= */

    function initGmailPanel() {
        var rows        = Array.from(document.querySelectorAll('.gmail-row'));
        var details     = Array.from(document.querySelectorAll('.gmail-detail'));
        var navItems    = Array.from(document.querySelectorAll('.gmail-nav-item'));
        var selectAll   = document.getElementById('gmailSelectAll');
        var listPanel   = document.getElementById('gmailListPanel');
        var detailPanel = document.getElementById('gmailDetailPanel');
        var activeFilter = 'all';

        if (!rows.length) return;

        function setActive(id) {
            rows.forEach(function (r) {
                r.classList.toggle('is-active', r.getAttribute('data-notification-id') === String(id));
            });
            details.forEach(function (d) {
                d.classList.toggle('is-active', d.getAttribute('data-notification-detail') === String(id));
            });
            // Mobile: hide list, show detail
            if (listPanel && detailPanel && window.innerWidth <= 768) {
                listPanel.style.display = 'none';
                detailPanel.style.display = 'flex';
            }
        }

        function applyGmailFilters() {
            var query = _searchVal();
            var firstVisibleId = null;

            rows.forEach(function (row) {
                var haystack = (row.getAttribute('data-search') || '').toLowerCase();
                var rowType  = (row.getAttribute('data-type')   || 'info').toLowerCase();
                var show     = (!query || haystack.includes(query)) &&
                               (activeFilter === 'all' || rowType === activeFilter);
                row.hidden = !show;
                if (show && !firstVisibleId) {
                    firstVisibleId = row.getAttribute('data-notification-id');
                }
            });

            // If current active row is now hidden, activate first visible
            var stillActive = rows.find(function (r) { return !r.hidden && r.classList.contains('is-active'); });
            if (!stillActive && firstVisibleId) setActive(firstVisibleId);
            if (!firstVisibleId) details.forEach(function (d) { d.classList.remove('is-active'); });
        }

        // Row click → show detail
        rows.forEach(function (row) {
            row.addEventListener('click', function () {
                setActive(row.getAttribute('data-notification-id'));
            });
        });

        // Nav filter buttons
        navItems.forEach(function (item) {
            item.addEventListener('click', function () {
                navItems.forEach(function (n) { n.classList.remove('active'); });
                item.classList.add('active');
                activeFilter = item.getAttribute('data-filter') || 'all';
                applyGmailFilters();
            });
        });

        // Select-all checkbox
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                var checked = this.checked;
                rows.forEach(function (row) {
                    if (!row.hidden) {
                        var cb = row.querySelector('.gmail-row-check');
                        if (cb) cb.checked = checked;
                    }
                });
            });
        }

        // Mobile back button
        document.querySelectorAll('.gmail-back-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (listPanel)   listPanel.style.display = '';
                if (detailPanel) detailPanel.style.display = '';
            });
        });

        // Star toggle
        document.querySelectorAll('.gmail-star').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                btn.classList.toggle('is-starred');
            });
        });

        // Activate first row on load if none already active
        applyGmailFilters();

        // Export filter hook (global search re-runs on input)
        window._applyGmailFilters = applyGmailFilters;
    }

    function applyGmailFilters() {
        if (window._applyGmailFilters) window._applyGmailFilters();
    }


    /* =========================================================================
       11. ANALYTICS CHARTS & KPIs
    ========================================================================= */

    var BAR_H = 180; // bar chart height in px

    function renderBarChart(id, labels, values, maxVal) {
        var el = document.getElementById(id);
        if (!el) return;
        var max = maxVal || Math.max.apply(null, values.concat([1]));
        el.innerHTML = labels.map(function (label, i) {
            var val  = values[i] || 0;
            var pct  = max > 0 ? val / max : 0;
            var h    = Math.max(pct * BAR_H, val > 0 ? 6 : 3);
            return '<div class="bar-wrap"><div class="bar-fill" style="height:' + h +
                   'px" title="' + val + '"></div><span class="bar-label">' + label + '</span></div>';
        }).join('');
    }

    function renderLineChart(id, labels, values) {
        var wrap = document.getElementById(id);
        if (!wrap || !values.length) return;
        var w   = Math.max(wrap.clientWidth || 600, 400);
        var h   = 160;
        var max = Math.max.apply(null, values.concat([1]));
        var pad = { t: 6, r: 6, b: 6, l: 6 };
        var x0  = pad.l, x1 = w - pad.r, y0 = pad.t, y1 = h - pad.b;
        var n   = values.length;
        var fx  = function (i) { return x0 + (i / Math.max(n - 1, 1)) * (x1 - x0); };
        var fy  = function (v) { return y1 - (v / max) * (y1 - y0); };

        var path = 'M ' + fx(0) + ' ' + fy(values[0] || 0);
        for (var i = 1; i < n; i++) path += ' L ' + fx(i) + ' ' + fy(values[i] || 0);
        var area = path + ' L ' + fx(n - 1) + ' ' + y1 + ' L ' + x0 + ' ' + y1 + ' Z';
        var uid  = 'lgr' + id.replace(/\W/g, '') + Date.now();

        wrap.innerHTML =
            '<svg viewBox="0 0 ' + w + ' ' + h + '" preserveAspectRatio="xMidYMid meet">' +
            '<defs><linearGradient id="' + uid + '" x1="0" y1="0" x2="0" y2="1">' +
            '<stop offset="0%" stop-color="var(--primary)" stop-opacity="0.3"/>' +
            '<stop offset="100%" stop-color="var(--primary)" stop-opacity="0"/>' +
            '</linearGradient></defs>' +
            '<path class="line-area" d="' + area + '" fill="url(#' + uid + ')"/>' +
            '<path class="line-path" d="' + path + '" vector-effect="non-scaling-stroke"/>' +
            '</svg>';
    }

    function _kpi(id, text) {
        var el = document.getElementById(id);
        if (el) el.textContent = text;
    }

    function loadAnalytics() {
        var data  = window.WISP_ANALYTICS || {};
        var days  = parseInt((_val('analyticsRange') || '30'), 10);
        var kpis  = data.kpis || {};

        var labels   = Array.isArray(data.labels)      ? data.labels      : [];
        var vLabels  = Array.isArray(data.view_labels)  ? data.view_labels : [];
        var usage    = Array.isArray(data.usage)        ? data.usage       : [];
        var ugrowth  = Array.isArray(data.user_growth)  ? data.user_growth  : [];
        var messages = Array.isArray(data.messages)     ? data.messages    : [];
        var views    = Array.isArray(data.views)        ? data.views       : [];
        var peaks    = Array.isArray(data.peak_hours)   ? data.peak_hours  : Array(24).fill(0);

        var take  = Math.min(days, labels.length);
        var slice = function (arr) { return arr.length >= take ? arr.slice(-take) : arr; };
        var sl = slice(labels), su = slice(usage), sd = slice(ugrowth), sm = slice(messages);

        // KPIs
        _kpi('kpiPageViews',        (kpis.page_views       ?? (su.reduce(function (a, b) { return a + b; }, 0))).toLocaleString());
        _kpi('kpiUniqueVisitors',   String(kpis.unique_visitors  ?? '—'));
        _kpi('kpiAvgSession',       String(kpis.avg_session      ?? '—'));
        _kpi('kpiBounceRate',       String(kpis.bounce_rate      ?? '—'));
        _kpi('kpiNewUsers',         String(kpis.new_users        ?? '—'));
        _kpi('kpiTotalUsers',       (kpis.total_users ?? 0).toLocaleString());
        _kpi('kpiActiveUsers',      String(kpis.active_users_7d  ?? '—'));
        _kpi('kpiMessagesCreated',  String(kpis.messages_created ?? '—'));
        _kpi('kpiMessagesPublished',String(kpis.messages_published ?? '—'));
        _kpi('kpiMessagesExpired',  String(kpis.messages_expired ?? '—'));
        _kpi('kpiTotalViews',       (kpis.total_views ?? 0).toLocaleString());
        _kpi('kpiViewsToday',       String(kpis.views_today ?? '—'));
        _kpi('kpiTopMessage',       String(kpis.top_message ?? '—'));

        // Charts
        renderBarChart('chartUsage',     sl, su,       Math.max.apply(null, su.concat([1])));
        renderLineChart('chartUsageLine',sl, su);
        renderBarChart('chartUserGrowth',sl, sd,       Math.max.apply(null, sd.concat([1])));
        renderBarChart('chartMessages',  sl, sm,       Math.max.apply(null, sm.concat([1])));
        renderBarChart('chartViews',     vLabels, views, Math.max.apply(null, views.concat([1])));

        var hours = Array.from({ length: 24 }, function (_, i) { return i + 'h'; });
        renderBarChart('chartPeakHours', hours, peaks, Math.max.apply(null, peaks.concat([1])));
    }

    function initAnalytics() {
        var refreshBtn  = document.getElementById('analyticsRefreshBtn');
        var rangeSelect = document.getElementById('analyticsRange');
        var apiUrl      = (window.WISP_ROUTES && window.WISP_ROUTES.adminAnalytics) || '';

        function fetchAndRender() {
            var days = parseInt(_val('analyticsRange') || '30', 10);
            if (!apiUrl) { loadAnalytics(); return; }
            if (refreshBtn) refreshBtn.disabled = true;

            fetch(apiUrl + '?days=' + days, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) {
                    if (data && typeof data === 'object') window.WISP_ANALYTICS = data;
                    loadAnalytics();
                })
                .catch(loadAnalytics)
                .finally(function () { if (refreshBtn) refreshBtn.disabled = false; });
        }

        // Render immediately from server-injected window.WISP_ANALYTICS
        loadAnalytics();

        if (refreshBtn)  refreshBtn.addEventListener('click', fetchAndRender);
        if (rangeSelect) rangeSelect.addEventListener('change', fetchAndRender);

        // Fetch fresh data in background
        if (apiUrl) fetchAndRender();
    }


    /* =========================================================================
       12. ACTIVITY TABLE PAGINATION
    ========================================================================= */

    function initActivityPagination() {
        var pagination = document.querySelector('.activity-pagination[data-activity-url]');
        var tbody      = document.getElementById('activityTableBody');
        var prevBtn    = document.getElementById('activityPrevBtn');
        var nextBtn    = document.getElementById('activityNextBtn');

        if (!pagination || !tbody) return;

        var apiUrl  = pagination.dataset.activityUrl;
        var curPage = parseInt(pagination.dataset.activityPage || '1', 10);

        function updateUI(data) {
            if (!data) return;
            curPage = data.page;

            if (data.items && data.items.length) {
                tbody.innerHTML = data.items.map(function (a) {
                    return '<tr><td>' + (a.time || '—') + '</td><td>' + (a.user || '—') +
                           '</td><td>' + (a.activity || '—') + '</td><td>' + (a.details || '') + '</td></tr>';
                }).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted" style="padding:24px;">No activity yet.</td></tr>';
            }

            if (prevBtn) prevBtn.disabled = !data.has_prev;
            if (nextBtn) nextBtn.disabled = !data.has_next;
        }

        function fetchPage(page) {
            var sep = apiUrl.includes('?') ? '&' : '?';
            fetch(apiUrl + sep + 'page=' + page, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (r) { return r.json(); })
                .then(updateUI)
                .catch(function () { showNotification('error', 'Error', 'Could not load activity.', 4000); });
        }

        if (prevBtn) prevBtn.addEventListener('click', function () {
            if (!this.disabled && curPage > 1) fetchPage(curPage - 1);
        });
        if (nextBtn) nextBtn.addEventListener('click', function () {
            if (!this.disabled) fetchPage(curPage + 1);
        });
    }


    /* =========================================================================
       13. BULK OPERATIONS
    ========================================================================= */

    function initBulkOperations() {
        // Tab switching
        document.querySelectorAll('[data-bulk-tab]').forEach(function (tab) {
            tab.addEventListener('click', function () {
                document.querySelectorAll('[data-bulk-tab]').forEach(function (t) { t.classList.remove('active'); });
                document.querySelectorAll('.bulk-tab').forEach(function (c) { c.classList.remove('active'); });
                tab.classList.add('active');
                var target = document.getElementById(tab.getAttribute('data-bulk-tab'));
                if (target) target.classList.add('active');
            });
        });

        // Bulk Users
        _initBulkTable({
            tableSelector:   '#bulkUsersTable tbody',
            rowSelector:     'tr.bulk-user-row',
            cbSelector:      '.bulk-user-checkbox',
            selectAllId:     'bulkUserSelectAll',
            countId:         'bulkUserSelectedCount',
            applyBtnId:      'bulkUserApplyBtn',
            actionSelectId:  'bulkUserAction',
            actionHiddenId:  'bulkUserActionHidden',
            formId:          'bulkUserForm',
            statusFilterId:  'bulkUserStatusFilter',
            searchAttrs:     ['data-username', 'data-email'],
            noun:            'user',
        });

        // Bulk Messages
        _initBulkTable({
            tableSelector:   '#bulkMessagesTable tbody',
            rowSelector:     'tr.bulk-message-row',
            cbSelector:      '.bulk-message-checkbox',
            selectAllId:     'bulkMessageSelectAll',
            countId:         'bulkMessageSelectedCount',
            applyBtnId:      'bulkMessageApplyBtn',
            actionSelectId:  'bulkMessageAction',
            actionHiddenId:  'bulkMessageActionHidden',
            formId:          'bulkMessageForm',
            statusFilterId:  'bulkMessageStatusFilter',
            searchAttrs:     ['data-title'],
            noun:            'message',
        });
    }

    function _initBulkTable(cfg) {
        var tbody      = document.querySelector(cfg.tableSelector);
        var selectAll  = document.getElementById(cfg.selectAllId);
        var countEl    = document.getElementById(cfg.countId);
        var applyBtn   = document.getElementById(cfg.applyBtnId);
        var statusFilt = document.getElementById(cfg.statusFilterId);

        function filterRows() {
            if (!tbody) return;
            var search  = _searchVal();
            var status  = statusFilt ? statusFilt.value.toLowerCase() : '';
            tbody.querySelectorAll(cfg.rowSelector).forEach(function (tr) {
                var matchSearch = !search || cfg.searchAttrs.some(function (a) {
                    return (tr.getAttribute(a) || '').toLowerCase().includes(search);
                });
                var matchStatus = !status || (tr.getAttribute('data-status') || '').toLowerCase() === status;
                tr.style.display = matchSearch && matchStatus ? '' : 'none';
            });
        }

        function updateCount() {
            var n = document.querySelectorAll(cfg.cbSelector + ':checked').length;
            if (countEl) countEl.textContent = n + ' selected';
            if (applyBtn) applyBtn.disabled = n === 0;
        }

        document.querySelectorAll(cfg.cbSelector).forEach(function (cb) {
            cb.addEventListener('change', updateCount);
        });

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                var checked = this.checked;
                document.querySelectorAll(cfg.cbSelector).forEach(function (cb) {
                    var row = cb.closest('tr');
                    if (row && row.style.display !== 'none') cb.checked = checked;
                });
                updateCount();
            });
        }

        if (applyBtn) {
            applyBtn.addEventListener('click', function () {
                var actionEl = document.getElementById(cfg.actionSelectId);
                var action   = actionEl ? actionEl.value : '';
                if (!action) { showNotification('warning', 'No Action', 'Please select an action first.', 3000); return; }
                var n = document.querySelectorAll(cfg.cbSelector + ':checked').length;
                if (n === 0) { showNotification('warning', 'No Selection', 'Please select at least one ' + cfg.noun + '.', 3000); return; }
                var msg = 'Apply "' + action + '" to ' + n + ' ' + cfg.noun + '(s)?';
                var proceed = function() {
                    var hidden = document.getElementById(cfg.actionHiddenId);
                    var form   = document.getElementById(cfg.formId);
                    if (hidden) hidden.value = action;
                    if (form) form.submit();
                };
                
                if (window.showAdminConfirm) window.showAdminConfirm(msg, proceed);
                else { if (confirm(msg)) proceed(); }
            });
        }

        if (statusFilt) statusFilt.addEventListener('change', filterRows);

        // Register with global search
        cfg._filter = filterRows;
    }

    function filterBulkUsersTable() {
        var tbody = document.querySelector('#bulkUsersTable tbody');
        if (!tbody) return;
        var search = _searchVal();
        var status = _val('bulkUserStatusFilter');
        tbody.querySelectorAll('tr.bulk-user-row').forEach(function (tr) {
            var ok = _matchSearch(search, tr, ['data-username', 'data-email']) && _matchAttr(status, tr, 'data-status');
            tr.style.display = ok ? '' : 'none';
        });
    }

    function filterBulkMessagesTable() {
        var tbody = document.querySelector('#bulkMessagesTable tbody');
        if (!tbody) return;
        var search = _searchVal();
        var status = _val('bulkMessageStatusFilter');
        tbody.querySelectorAll('tr.bulk-message-row').forEach(function (tr) {
            var ok = _matchSearch(search, tr, ['data-title']) && _matchAttr(status, tr, 'data-status');
            tr.style.display = ok ? '' : 'none';
        });
    }


    /* =========================================================================
       14. EXPORT LINK HELPERS
    ========================================================================= */

    function initExportHelpers() {
        _bindExportFilter('exportUserStatus',    'exportUsersBtn');
        _bindExportFilter('exportMessageStatus', 'exportMessagesBtn');
    }

    function _bindExportFilter(selectId, btnId) {
        var sel = document.getElementById(selectId);
        var btn = document.getElementById(btnId);
        if (!sel || !btn) return;
        sel.addEventListener('change', function () {
            var base = btn.href.split('?')[0];
            btn.href = base + '?status=' + encodeURIComponent(sel.value);
        });
    }


    /* =========================================================================
       15. FLASH MESSAGE HANDLER
    ========================================================================= */

    function handleFlashMessages() {
        var flash = document.getElementById('adminFlash');
        if (!flash || !flash.dataset.message) return;
        var type  = (flash.dataset.type || 'success').toLowerCase();
        var title = type === 'error' ? 'Error' : 'Success';
        showNotification(type, title, flash.dataset.message, 4000);
        flash.remove();
    }


    /* =========================================================================
       16. LINK COPY BUTTONS
    ========================================================================= */

    function initCopyButtons() {
        document.querySelectorAll('.btn-copy-link').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var url = btn.getAttribute('data-copy') || '';
                if (!url) return;

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function () {
                        btn.classList.add('copied');
                        btn.innerHTML = '<i class="fas fa-check"></i> Copied';
                        setTimeout(function () {
                            btn.classList.remove('copied');
                            btn.innerHTML = '<i class="fas fa-copy"></i> Copy';
                        }, 2000);
                    }).catch(function () {
                        showNotification('error', 'Copy Failed', 'Could not copy to clipboard.', 3000);
                    });
                } else {
                    showNotification('info', 'Copy', 'Copy the link from the address bar.', 3000);
                }
            });
        });
    }


    /* =========================================================================
       UTILITY HELPERS (private)
    ========================================================================= */

    /** Get trimmed lowercase value of globalSearchInput. */
    function _searchVal() {
        var el = document.getElementById('globalSearchInput');
        return el ? el.value.trim().toLowerCase() : '';
    }

    /** Get lowercase value of a select/input by id. */
    function _val(id) {
        var el = document.getElementById(id);
        return el ? el.value.toLowerCase() : '';
    }

    /** True if row's data-attribute values contain the search term. */
    function _matchSearch(search, tr, attrs) {
        if (!search) return true;
        return attrs.some(function (a) {
            return (tr.getAttribute(a) || '').toLowerCase().includes(search);
        });
    }

    /** True if tr's attribute matches filter (empty filter = show all). */
    function _matchAttr(filter, tr, attr) {
        return !filter || (tr.getAttribute(attr) || '').toLowerCase() === filter;
    }

    /** Show/hide empty-state and no-results rows. */
    function _toggleEmpty(emptyRow, noResultsRow, total, visible) {
        if (emptyRow)     emptyRow.style.display     = total === 0            ? '' : 'none';
        if (noResultsRow) noResultsRow.style.display = (total > 0 && visible === 0) ? '' : 'none';
    }

    /** Bind change event listener by element id. */
    function _onChange(id, fn) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', fn);
    }


    /* =========================================================================
       17. INITIALIZATION — DOMContentLoaded
    ========================================================================= */

    document.addEventListener('DOMContentLoaded', function () {

        // Core UI systems
        initTheme();
        initSidebar();
        initModals();

        // Data / interactions
        initActionButtons();
        initGlobalSearch();
        initGmailPanel();
        initAnalytics();
        initActivityPagination();
        initBulkOperations();
        initExportHelpers();
        initCopyButtons();

        // Per-page filter wiring
        _initUserFilters();
        _initMessageFilters();
        _initLogFilters();

        // Flash messages
        handleFlashMessages();

    });

})();
