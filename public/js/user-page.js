// ========== User Dashboard ==========

// Mobile sidebar toggle
var menuToggle = document.getElementById('menuToggle');
var sidebar = document.getElementById('sidebar');
var sidebarOverlay = document.getElementById('sidebarOverlay');
if (menuToggle && sidebar && sidebarOverlay) {
    var mobileSidebarMq = window.matchMedia('(max-width: 992px)');

    function syncMobileSidebarScrollLock() {
        var mobile = mobileSidebarMq.matches;
        var open = sidebar.classList.contains('active');
        document.body.style.overflow = mobile && open ? 'hidden' : '';
    }

    menuToggle.addEventListener('click', function () {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        syncMobileSidebarScrollLock();
    });
    sidebarOverlay.addEventListener('click', function () {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        syncMobileSidebarScrollLock();
    });

    if (typeof mobileSidebarMq.addEventListener === 'function') {
        mobileSidebarMq.addEventListener('change', syncMobileSidebarScrollLock);
    } else if (typeof mobileSidebarMq.addListener === 'function') {
        mobileSidebarMq.addListener(syncMobileSidebarScrollLock);
    }
    window.addEventListener('resize', syncMobileSidebarScrollLock);

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (!sidebar.classList.contains('active')) return;
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        syncMobileSidebarScrollLock();
    });
}

// Sidebar Settings dropdown toggle
document.querySelectorAll('.has-dropdown > a.dropdown-toggle').forEach(function (toggle) {
    toggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var parentLi = toggle.closest('.has-dropdown');
        if (!parentLi) return;
        parentLi.classList.toggle('open');
    });
});

// Toast Notifications Logic

function showNotification(title, message, type = 'success', duration = 5000) {
    const container = document.getElementById('notificationContainer');
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    const icon = type === 'success' ? 'fas fa-check' :
        type === 'error' ? 'fas fa-exclamation-circle' :
            type === 'warning' ? 'fas fa-exclamation-triangle' :
                'fas fa-info-circle';

    notification.innerHTML = `
                <div class="notification-icon">
                    <i class="${icon}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${title}</div>
                    <div class="notification-message">${message}</div>
                </div>
                <button class="notification-close">
                    <i class="fas fa-times"></i>
                </button>
            `;

    container.appendChild(notification);

    // Trigger animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // Auto remove after duration
    const autoRemove = setTimeout(() => {
        closeNotification(notification);
    }, duration);

    // Close button event
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        clearTimeout(autoRemove);
        closeNotification(notification);
    });
}

function closeNotification(notification) {
    notification.classList.remove('show');
    notification.classList.add('hide');

    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}
window.showNotification = showNotification;

// (End of Toast Notification logic)

// Flash toasts (2s) – trigger from here so showNotification is guaranteed defined
document.addEventListener('DOMContentLoaded', function () {
    var flash = window.WISP_FLASH || {};
    if (flash.success && typeof showNotification === 'function')
        showNotification('Success', flash.success, 'success', 2000);
    if (flash.error && typeof showNotification === 'function')
        showNotification('Error', flash.error, 'error', 2000);
    if (flash.errors && flash.errors.length && typeof showNotification === 'function')
        showNotification('Please fix the following', flash.errors[0], 'error', 2000);
});


// Copy link / copy text – works app-wide (delegated), works in HTTP and HTTPS
function copyToClipboard(text) {
    if (!text) return false;
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text).then(function () { return true; }).catch(function () { return false; });
    }
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    ta.style.top = '0';
    document.body.appendChild(ta);
    ta.focus();
    ta.setSelectionRange(0, text.length);
    var ok = false;
    try { ok = document.execCommand('copy'); } catch (e) { }
    document.body.removeChild(ta);
    return Promise.resolve(ok);
}
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.copy-link-btn') || e.target.closest('[data-copy]');
    if (!btn) return;
    e.preventDefault();
    var text = btn.getAttribute('data-url') || btn.getAttribute('data-copy') || '';
    if (!text) return;
    copyToClipboard(text).then(function (ok) {
        if (typeof showNotification === 'function')
            showNotification(ok ? 'Copied' : 'Copy failed', ok ? 'Link copied to clipboard.' : 'Could not copy.', ok ? 'success' : 'error', 2000);
        else
            alert(ok ? 'Link copied to clipboard!' : 'Could not copy.');
    });
});

