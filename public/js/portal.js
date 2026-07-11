document.addEventListener('DOMContentLoaded', function () {
    var menuToggle = document.querySelector('[data-menu-toggle]');
    var sidebar = document.querySelector('[data-portal-sidebar]');
    var overlay = document.querySelector('[data-mobile-overlay]');
    var previewModal = document.getElementById('portalPreviewModal');
    var previewFrame = document.getElementById('portalPreviewFrame');
    var previewTitle = document.getElementById('portalPreviewTitle');

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('is-open');
        if (overlay) overlay.classList.remove('is-open');
    }

    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (modal === previewModal && previewFrame) previewFrame.src = 'about:blank';
        document.body.style.overflow = '';
    }

    if (menuToggle && sidebar && overlay) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('is-open');
            overlay.classList.toggle('is-open');
        });

        overlay.addEventListener('click', closeSidebar);

        document.querySelectorAll('[data-nav-link]').forEach(function (link) {
            link.addEventListener('click', closeSidebar);
        });
    }

    document.querySelectorAll('[data-close-modal]').forEach(function (button) {
        button.addEventListener('click', function () {
            closeModal(button.closest('.portal-modal'));
        });
    });

    document.querySelectorAll('.portal-modal').forEach(function (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) closeModal(modal);
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.portal-modal.is-open').forEach(closeModal);
        }
    });

    document.querySelectorAll('[data-preview-url]').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            if (!previewFrame) return;
            previewFrame.src = button.getAttribute('data-preview-url');
            if (previewTitle) previewTitle.textContent = button.getAttribute('data-preview-title') || 'Preview';
            openModal(previewModal);
        });
    });

    var viewUserModal = document.getElementById('adminViewUserModal');
    document.querySelectorAll('.btn-view-user').forEach(function (button) {
        button.addEventListener('click', function () {
            var setText = function (id, value) {
                var el = document.getElementById(id);
                if (el) el.textContent = value || 'N/A';
            };
            setText('viewUserName', button.dataset.username);
            setText('viewUserEmail', button.dataset.email);
            setText('viewUserStatus', button.dataset.status);
            setText('viewUserRole', button.dataset.role);
            openModal(viewUserModal);
        });
    });

    var editUserModal = document.getElementById('adminEditUserModal');
    var editUserForm = document.getElementById('adminEditUserForm');
    document.querySelectorAll('.btn-edit-user').forEach(function (button) {
        button.addEventListener('click', function () {
            if (editUserForm) editUserForm.action = button.dataset.action || '';
            var nameEl = document.getElementById('adminEditUserName');
            var emailEl = document.getElementById('adminEditUserEmail');
            var statusEl = document.getElementById('adminEditUserStatus');
            if (nameEl) nameEl.value = button.dataset.username || '';
            if (emailEl) emailEl.value = button.dataset.email || '';
            if (statusEl) statusEl.value = button.dataset.status || 'active';
            openModal(editUserModal);
        });
    });

    var actionModal = document.getElementById('adminActionModal');
    var actionForm = document.getElementById('adminActionForm');
    var actionMethod = document.getElementById('adminActionMethod');
    var actionTitle = document.getElementById('adminActionTitle');
    var actionMessage = document.getElementById('adminActionMessage');
    var actionSubmit = document.getElementById('adminActionSubmit');

    function bindActionButtons(selector, config) {
        document.querySelectorAll(selector).forEach(function (button) {
            button.addEventListener('click', function () {
                if (actionForm) actionForm.action = button.dataset.action || '';
                if (actionMethod) actionMethod.value = config.method || 'POST';
                if (actionTitle) actionTitle.textContent = config.title;
                if (actionMessage) actionMessage.textContent = (config.messagePrefix || '') + (button.dataset.username || button.dataset.title || 'this item') + (config.messageSuffix || '');
                if (actionSubmit) actionSubmit.textContent = config.submit || 'Continue';
                openModal(actionModal);
            });
        });
    }

    bindActionButtons('.btn-block-user', {
        title: 'Block User',
        submit: 'Block User',
        method: 'POST',
        messagePrefix: 'Are you sure you want to block ',
        messageSuffix: '?'
    });
    bindActionButtons('.btn-delete-user', {
        title: 'Delete User',
        submit: 'Delete User',
        method: 'DELETE',
        messagePrefix: 'Are you sure you want to delete ',
        messageSuffix: '? This cannot be undone.'
    });
    bindActionButtons('.btn-delete-message', {
        title: 'Delete Message',
        submit: 'Delete Message',
        method: 'DELETE',
        messagePrefix: 'Are you sure you want to delete "',
        messageSuffix: '"?'
    });

    var editMessageModal = document.getElementById('adminEditMessageModal');
    var editMessageForm = document.getElementById('adminEditMessageForm');
    document.querySelectorAll('.btn-edit-message').forEach(function (button) {
        button.addEventListener('click', function () {
            if (editMessageForm) editMessageForm.action = button.dataset.action || '';
            var titleEl = document.getElementById('adminEditMessageTitle');
            var contentEl = document.getElementById('adminEditMessageContent');
            var statusEl = document.getElementById('adminEditMessageStatus');
            if (titleEl) titleEl.value = button.dataset.title || '';
            if (contentEl) contentEl.value = button.dataset.content || '';
            if (statusEl) statusEl.value = button.dataset.status || '0';
            openModal(editMessageModal);
        });
    });
});
