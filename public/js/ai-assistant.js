(function () {
    'use strict';

    var form = document.getElementById('aiForm');
    var input = document.getElementById('aiInput');
    var messages = document.getElementById('aiMessages');
    var sendBtn = document.getElementById('aiSendBtn');
    var route = window.WISP_AI_ROUTE || '';
    var createRoute = window.WISP_CREATE_ROUTE || '/user-page/create';
    var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    var chatHistory = [];

    function appendMessage(text, role, isFinal, finalData) {
        if (!messages) return;
        var msg = document.createElement('article');
        msg.className = 'ai-msg ' + role;
        msg.textContent = text;

        if (isFinal && finalData) {
            var btnWrap = document.createElement('div');
            btnWrap.style.marginTop = '12px';
            btnWrap.style.display = 'flex';
            btnWrap.style.justifyContent = 'flex-end'; // Far right corner

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.title = 'Insert into Create Message Page';
            btn.style.background = 'none';
            btn.style.border = 'none';
            btn.style.color = 'var(--primary, #6366f1)';
            btn.style.fontSize = '1.25rem';
            btn.style.cursor = 'pointer';
            btn.style.padding = '4px';
            btn.style.transition = 'color 0.2s';
            btn.innerHTML = '<i class="fas fa-share-from-square"></i>';

            btn.addEventListener('click', function () {
                sessionStorage.setItem('ai_generated_title', finalData.title || '');
                sessionStorage.setItem('ai_generated_recipient', finalData.recipient_name || '');
                sessionStorage.setItem('ai_generated_body', finalData.body || '');
                window.location.href = createRoute;
            });

            btnWrap.appendChild(btn);
            msg.appendChild(btnWrap);
        }

        messages.appendChild(msg);
        messages.scrollTop = messages.scrollHeight;
    }

    function setBusy(isBusy) {
        if (sendBtn) sendBtn.disabled = isBusy;
        if (input) input.disabled = isBusy;
    }

    function parseAiResponse(rawText) {
        try {
            var match = rawText.match(/\{[\s\S]*"type"\s*:\s*"final_message"[\s\S]*\}/);
            if (match) {
                return JSON.parse(match[0]);
            }
            return JSON.parse(rawText);
        } catch (e) {
            return null;
        }
    }

    function sendMessage(text) {
        if (!route || !text) return;
        setBusy(true);
        appendMessage(text, 'user');

        if (input) {
            input.value = '';
            input.style.height = 'auto';
        }

        fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                user_message: text,
                history: chatHistory
            })
        }).then(function (res) {
            return res.json();
        }).then(function (data) {
            var reply = (data && data.success && data.message) ? data.message : (data && data.error ? data.error : (data && data.message ? data.message : 'Unable to respond right now.'));

            var finalData = parseAiResponse(reply);

            if (finalData && finalData.type === 'final_message') {
                appendMessage(finalData.body, 'assistant', true, finalData);
                chatHistory.push({ role: 'user', content: text });
                chatHistory.push({ role: 'assistant', content: JSON.stringify(finalData) });
            } else {
                appendMessage(reply, 'assistant');
                chatHistory.push({ role: 'user', content: text });
                chatHistory.push({ role: 'assistant', content: reply });
            }
        }).catch(function () {
            appendMessage('Network error. Please try again.', 'assistant');
        }).finally(function () {
            setBusy(false);
            if (input) input.focus();
        });
    }

    if (form && input) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var text = (input.value || '').trim();
            if (!text) return;
            sendMessage(text);
        });

        input.addEventListener('keydown', function (e) {
            if ((e.key === 'Enter' || e.keyCode === 13) && !e.shiftKey) {
                e.preventDefault();
                var text = (this.value || '').trim();
                if (!text) return;
                sendMessage(text);
            }
        });

        input.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 140) + 'px';
        });
    }
})();
