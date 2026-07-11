@extends('user.base-user')
@section('user-section', 'share-messages')
@section('content')
<div class="content-section active" id="share-messages">
    <div class="messages-header-toolbar">
        <h2 class="messages-header-title">
            <i class="fas fa-share-alt text-primary messages-header-icon"></i> 
            Share Message
        </h2>
    </div>
    
    <div class="card messages-form-card" style="max-width: 750px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: 2rem;">
        <div class="card-body" style="padding: 30px;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h4 style="font-weight: 700; color: #1e293b; font-size: 1.5rem; margin-bottom: 4px;">Share Message</h4>
                        <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 20px;">{{ $message->title }} — {{ $message->recipient_name }}</p>
                    </div>
                </div>
            </div>
            
            <hr style="margin-top: 0; margin-bottom: 24px; border-top: 1px solid #e2e8f0;">

            <!-- Generated Link -->
            <div class="form-group mb-4">
                <label style="font-size: 0.8rem; font-weight: 700; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px;">Generated Link</label>
                <div class="generated-link" style="margin-top: 0; margin-bottom: 0;">
                    <input type="text" class="form-control" id="share-page-link" value="{{ $message->generated_link }}" readonly>
                    <button type="button" class="btn btn-primary" id="share-page-copy-btn" style="background: #6366f1; border-color: #6366f1; font-weight: 600;">Copy</button>
                </div>
            </div>

            <!-- Send to Recipient Buttons -->
            <style>
                @media (max-width: 480px) {
                    .share-page-method-btn {
                        padding: 8px 4px !important;
                        font-size: 0.75rem !important;
                        gap: 4px !important;
                    }
                }
            </style>
            <div class="form-group mb-4">
                <label style="font-size: 0.8rem; font-weight: 700; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 12px;">Send to Recipient</label>
                <div style="display: flex; gap: 6px; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 4px; overflow-y: hidden;">
                    <button type="button" class="share-page-method-btn" data-method="whatsapp" style="flex: 1; min-width: 0; white-space: nowrap; border: 1px solid #cbd5e1; background: transparent; color: #64748b; border-radius: 50px; padding: 8px 12px; font-weight: 600; font-size: 0.85rem; display: flex; justify-content: center; align-items: center; gap: 6px;"><i class="fab fa-whatsapp"></i> WhatsApp</button>
                    <button type="button" class="share-page-method-btn active" data-method="email" style="flex: 1; min-width: 0; white-space: nowrap; border: 1px solid #cbd5e1; background: transparent; color: #64748b; border-radius: 50px; padding: 8px 12px; font-weight: 600; font-size: 0.85rem; display: flex; justify-content: center; align-items: center; gap: 6px;"><i class="far fa-envelope"></i> Email</button>
                    <button type="button" class="share-page-method-btn" data-method="sms" style="flex: 1; min-width: 0; white-space: nowrap; border: 1px solid #cbd5e1; background: transparent; color: #64748b; border-radius: 50px; padding: 8px 12px; font-weight: 600; font-size: 0.85rem; display: flex; justify-content: center; align-items: center; gap: 6px;"><i class="fas fa-phone-alt"></i> SMS</button>
                    <button type="button" class="share-page-method-btn" data-method="all" style="flex: 1; min-width: 0; white-space: nowrap; border: 1px solid #cbd5e1; background: transparent; color: #64748b; border-radius: 50px; padding: 8px 12px; font-weight: 600; font-size: 0.85rem; display: flex; justify-content: center; align-items: center; gap: 6px;"><i class="fas fa-share-nodes"></i> All</button>
                </div>
            </div>

            <!-- Message Preview -->
            <div class="form-group mb-4" id="share-page-preview-wrap">
                <label style="font-size: 0.8rem; font-weight: 700; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px;">Message Preview</label>
                <textarea id="share-page-preview" class="form-control" rows="3" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.95rem; color: #334155; padding: 16px; resize: none; box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);">Hey {{ $message->recipient_name }}! I've created a special message for you. Check it out here: {{ $message->generated_link }}</textarea>
            </div>

            <!-- Recipient Input -->
            <div class="form-group mb-4" id="share-page-recipient-wrap">
                <label id="share-page-recipient-label" style="font-size: 0.8rem; font-weight: 700; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px;">Recipient Phone</label>
                <input type="text" id="share-page-recipient-input" value="{{ $message->recipient_phone }}" class="form-control" placeholder="+1234567890" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 1rem; padding: 14px 16px; color: #334155; box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);">
            </div>

            <!-- Recipient Email (For 'All' Option) -->
            <div class="form-group mb-4" id="share-page-recipient-email-wrap" style="display: none;">
                <label style="font-size: 0.8rem; font-weight: 700; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px;">Recipient Email</label>
                <input type="email" id="share-page-recipient-email-input" class="form-control" placeholder="email@example.com" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 1rem; padding: 14px 16px; color: #334155; box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);">
            </div>

            <!-- Schedule Accordion -->
            <div class="mb-4">
                <button type="button" id="share-page-schedule-toggle" style="width: 100%; border: 1px solid #bfdbfe; background: transparent; color: #334155; border-radius: 12px; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; font-size: 0.95rem; font-weight: 500; outline: none; transition: all 0.2s;">
                    <span><i class="far fa-calendar-alt" style="margin-right: 8px; color: #64748b;"></i> <span id="share-page-schedule-text">Schedule for later</span></span>
                    <i class="fas fa-chevron-right" id="share-page-schedule-icon" style="color: #94a3b8;"></i>
                </button>
                
                <div id="share-page-schedule-content" style="display: none; background: #eff6ff; border: 1px solid #bfdbfe; border-top: none; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 20px; margin-top: -8px; padding-top: 24px;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #94a3b8; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 12px; display: block;">Schedule Date & Time</label>
                    <div style="display: flex; gap: 16px;">
                        <div style="flex: 1;">
                            <label style="font-size: 0.8rem; color: #64748b; margin-bottom: 6px;">Date</label>
                            <input type="date" id="share-page-schedule-date" class="form-control" style="background: #e2e8f0; border: 1px solid transparent; border-radius: 10px; font-size: 0.95rem; padding: 12px 14px; color: #334155;">
                        </div>
                        <div style="flex: 1;">
                            <label style="font-size: 0.8rem; color: #64748b; margin-bottom: 6px;">Time</label>
                            <input type="time" id="share-page-schedule-time" class="form-control" style="background: #e2e8f0; border: 1px solid transparent; border-radius: 10px; font-size: 0.95rem; padding: 12px 14px; color: #334155;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Action Button -->
            <button type="button" id="share-page-submit-btn" class="btn btn-primary" style="margin-top: 24px; width: 100%; border-radius: 12px; padding: 16px; font-weight: 600; font-size: 1.05rem; display: flex; justify-content: center; align-items: center; gap: 8px; background: #6366f1; border: none; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);">
                <i class="fas fa-paper-plane" id="share-page-submit-icon"></i> <span id="share-page-submit-text">Send via WhatsApp</span>
            </button>
        </div>
    </div>
</div>
@push('scripts')
<script>
// Share Messages: Send method dropdown (WhatsApp / SMS / Email) – show phone or email field
var sendMethodSelect = document.getElementById('send-method-select');
var sendViaPhoneWrap = document.getElementById('send-via-phone-wrap');
var sendViaEmailWrap = document.getElementById('send-via-email-wrap');
var sendViaScheduleWrap = document.getElementById('send-via-schedule-wrap');
var openShareMessagesBtn = document.getElementById('open-share-messages-btn');
var sendSmsForm = document.getElementById('send-sms-form');
var sendRecipientPhone = document.getElementById('send-recipient-phone');
var smsRecipientPhone = document.getElementById('sms-recipient-phone');
var scheduleRecipientPhoneWrap = document.getElementById('schedule-recipient-phone-wrap');
var scheduleRecipientPhone = document.getElementById('schedule-recipient-phone');
var scheduleRecipientEmail = document.getElementById('schedule-recipient-email');
var scheduleChannelHidden = document.getElementById('schedule-channel-hidden');
var shareMessagesMessageTextEl = document.getElementById('share-messages-message-text');
var shareCustomMessageWrap = document.getElementById('share-custom-message-wrap');
if (sendMethodSelect) {
    function updateSendMethodUI() {
        var method = sendMethodSelect.value;
        if (sendViaPhoneWrap) sendViaPhoneWrap.style.display = (method === 'share-messages' || method === 'sms') ? 'block' : 'none';
        if (sendViaEmailWrap) sendViaEmailWrap.style.display = method === 'email' ? 'block' : 'none';
        if (sendViaScheduleWrap) sendViaScheduleWrap.style.display = method === 'schedule' ? 'block' : 'none';
        if (openShareMessagesBtn) openShareMessagesBtn.style.display = method === 'share-messages' ? '' : 'none';
        if (sendSmsForm) sendSmsForm.style.display = method === 'sms' ? 'inline-block' : 'none';
        if (shareCustomMessageWrap) shareCustomMessageWrap.style.display = (method === 'share-messages' || method === 'sms') ? 'block' : 'none';

        var scheduleInlineChannel = document.getElementById('schedule-inline-channel');
        if (scheduleInlineChannel) {
            scheduleInlineChannel.style.display = method === 'schedule' ? 'inline-block' : 'none';
        }

        if (method === 'schedule') {
            if (scheduleChannelHidden) {
                scheduleChannelHidden.value = (scheduleInlineChannel ? scheduleInlineChannel.value : 'email');
            }
            if (scheduleRecipientEmail) scheduleRecipientEmail.required = true;
            if (scheduleRecipientPhoneWrap) scheduleRecipientPhoneWrap.style.display = 'none';
            if (scheduleRecipientPhone) scheduleRecipientPhone.required = false;
        } else {
            if (scheduleChannelHidden) scheduleChannelHidden.value = 'email';
            if (scheduleRecipientEmail) scheduleRecipientEmail.required = false;
            if (scheduleRecipientPhone) scheduleRecipientPhone.required = false;
            if (scheduleRecipientPhoneWrap) scheduleRecipientPhoneWrap.style.display = 'none';
        }
    }
    // Choose schedule inner channel dropdown behavior
    var scheduleChannelSelect = document.getElementById('schedule-inline-channel') || document.getElementById('schedule-channel');
    if (scheduleChannelSelect) {
        scheduleChannelSelect.addEventListener('change', function () {
            var value = this.value;
            if (scheduleChannelHidden) scheduleChannelHidden.value = value;
            if (scheduleRecipientEmail) {
                scheduleRecipientEmail.required = (value === 'email');
                scheduleRecipientEmail.closest('.form-group').style.display = (value === 'email') ? 'block' : 'none';
            }
            if (scheduleRecipientPhone) {
                scheduleRecipientPhone.required = (value === 'sms');
                scheduleRecipientPhone.closest('.form-group').style.display = (value === 'sms') ? 'block' : 'none';
            }
        });
    }
    sendMethodSelect.addEventListener('change', updateSendMethodUI);
    updateSendMethodUI();
}
if (openShareMessagesBtn && sendRecipientPhone && shareMessagesMessageTextEl) {
    openShareMessagesBtn.addEventListener('click', function (e) {
        e.preventDefault();
        var phone = (sendRecipientPhone.value || '').replace(/\D/g, '');
        if (!phone) {
            if (typeof showNotification === 'function') showNotification('Enter phone', 'Enter recipient phone number.', 'warning', 2000);
            else alert('Enter recipient phone number.');
            return;
        }
        if (phone.charAt(0) !== '+') phone = '+' + phone;
        var text = (shareMessagesMessageTextEl.value || '').trim();
        var url = 'https://wa.me/' + phone + (text ? '?text=' + encodeURIComponent(text) : '');
        window.open(url, '_blank', 'noopener');
    });
}
if (sendSmsForm && sendRecipientPhone && smsRecipientPhone) {
    sendSmsForm.addEventListener('submit', function (e) {
        smsRecipientPhone.value = sendRecipientPhone.value || '';
    });
}

// Share Messages: send via AJAX and update "Current share status" list (last 2)
var shareSendsList = document.getElementById('shareSendsList');
var sendEmailFormShare = document.querySelector('.send-email-form-inline');
var sendSmsFormShare = document.getElementById('send-sms-form');
function prependShareSendAndTrim(shareSend) {
    if (!shareSendsList) return;
    var empty = shareSendsList.querySelector('.share-send-empty');
    if (empty) empty.remove();
    var li = document.createElement('li');
    li.className = 'share-send-item';
    li.setAttribute('data-id', shareSend.id || '');
    var statusText = 'Unknown';
    if (shareSend.status === 'sending') statusText = 'Sending…';
    else if (shareSend.status === 'sent') statusText = 'Sent';
    else if (shareSend.status === 'scheduled') statusText = 'Scheduled';
    else if (shareSend.status === 'failed') statusText = 'Failed';

    li.innerHTML = '<span class="share-send-channel">' + (shareSend.channel === 'email' ? 'Email' : 'SMS') + '</span> ' +
        '<span class="share-send-to">to ' + (shareSend.recipient_masked || '***') + '</span> ' +
        '<span class="share-send-message">"' + (shareSend.message_title || 'Message') + '"</span> ' +
        '<span class="share-send-status status-' + shareSend.status + '">' + statusText + '</span>';
    shareSendsList.insertBefore(li, shareSendsList.firstChild);
    while (shareSendsList.querySelectorAll('.share-send-item').length > 2) {
        shareSendsList.removeChild(shareSendsList.lastChild);
    }
}
function handleShareSendSubmit(e, form, url) {
    e.preventDefault();
    if (!shareSendsList || !url) return;
    var tempLi = document.createElement('li');
    tempLi.className = 'share-send-item share-send-temp';
    tempLi.innerHTML = '<span class="share-send-channel">—</span> <span class="share-send-to">—</span> <span class="share-send-message">—</span> <span class="share-send-status status-sending">Sending…</span>';
    shareSendsList.insertBefore(tempLi, shareSendsList.firstChild);
    var empty = shareSendsList.querySelector('.share-send-empty');
    if (empty) empty.remove();
    var formData = new FormData(form);
    var opts = { method: 'POST', body: formData, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } };
    fetch(url, opts)
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, json: j }; }); })
        .then(function (result) {
            if (tempLi.parentNode) tempLi.remove();
            var s = result.json && result.json.share_send;
            if (s) prependShareSendAndTrim(s);
            if (typeof showNotification === 'function') {
                if (result.ok) showNotification('Sent', result.json.success ? 'Message sent successfully.' : (result.json.error || 'Sent'), 'success', 2000);
                else showNotification('Error', result.json.error || 'Send failed.', 'error', 2000);
            }
        })
        .catch(function () {
            if (tempLi.parentNode) tempLi.remove();
            if (typeof showNotification === 'function') showNotification('Error', 'Request failed. Try again.', 'error', 2000);
        });
}
if (sendEmailFormShare && window.WISP_ROUTES && window.WISP_ROUTES.shareMessagesSendEmail) {
    sendEmailFormShare.addEventListener('submit', function (e) {
        handleShareSendSubmit(e, sendEmailFormShare, window.WISP_ROUTES.shareMessagesSendEmail);
    });
}
if (sendSmsFormShare && window.WISP_ROUTES && window.WISP_ROUTES.shareMessagesSendSms) {
    sendSmsFormShare.addEventListener('submit', function (e) {
        if (smsRecipientPhone) smsRecipientPhone.value = sendRecipientPhone ? sendRecipientPhone.value : '';
        handleShareSendSubmit(e, sendSmsFormShare, window.WISP_ROUTES.shareMessagesSendSms);
    });
}
</script>
@endpush
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentMessageId = {{ $message->id }};
    let selectedMethod = 'whatsapp';
    let isScheduling = false;

    // Elements
    const linkInput = document.getElementById('share-page-link');
    const previewArea = document.getElementById('share-page-preview');
    const recipientLabel = document.getElementById('share-page-recipient-label');
    const recipientInput = document.getElementById('share-page-recipient-input');
    const recipientEmailWrap = document.getElementById('share-page-recipient-email-wrap');
    const submitBtn = document.getElementById('share-page-submit-btn');
    const submitText = document.getElementById('share-page-submit-text');
    const submitIcon = document.getElementById('share-page-submit-icon');
    const methodBtns = document.querySelectorAll('.share-page-method-btn');
    
    const scheduleToggle = document.getElementById('share-page-schedule-toggle');
    const scheduleContent = document.getElementById('share-page-schedule-content');
    const scheduleText = document.getElementById('share-page-schedule-text');
    const scheduleIcon = document.getElementById('share-page-schedule-icon');

    // Copy link
    document.getElementById('share-page-copy-btn').addEventListener('click', function() {
        linkInput.select();
        document.execCommand('copy');
        const oldText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-check"></i> Copied';
        setTimeout(() => this.innerHTML = oldText, 2000);
    });

    // Method selection
    methodBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            setMethod(this.getAttribute('data-method'));
        });
    });

    function setMethod(method) {
        selectedMethod = method;
        methodBtns.forEach(b => {
            if (b.getAttribute('data-method') === method) {
                b.classList.add('active');
                b.style.borderColor = '#3b82f6';
                b.style.background = '#eff6ff';
                b.style.color = '#3b82f6';
            } else {
                b.classList.remove('active');
                b.style.borderColor = '#cbd5e1';
                b.style.background = 'transparent';
                b.style.color = '#64748b';
            }
        });

        const previewWrap = document.getElementById('share-page-preview-wrap');

        if (method === 'email') {
            recipientLabel.textContent = 'Recipient Email';
            recipientInput.placeholder = 'email@example.com';
            recipientInput.type = 'email';
            recipientEmailWrap.style.display = 'none';
            if (previewWrap) previewWrap.style.display = 'none';
        } else if (method === 'all') {
            recipientLabel.textContent = 'Recipient Phone';
            recipientInput.placeholder = '+1234567890';
            recipientInput.type = 'tel';
            recipientEmailWrap.style.display = 'block';
            if (previewWrap) previewWrap.style.display = 'block';
        } else {
            recipientLabel.textContent = 'Recipient Phone';
            recipientInput.placeholder = '+1234567890';
            recipientInput.type = 'tel';
            recipientEmailWrap.style.display = 'none';
            if (previewWrap) previewWrap.style.display = 'block';
        }

        updateSubmitBtn();
    }

    // Schedule Toggle
    scheduleToggle.addEventListener('click', function() {
        isScheduling = !isScheduling;
        updateScheduleUI();
        updateSubmitBtn();
    });

    function updateScheduleUI() {
        if (isScheduling) {
            scheduleContent.style.display = 'block';
            scheduleText.textContent = 'Scheduling enabled — tap to cancel';
            scheduleIcon.className = 'fas fa-times-circle';
            scheduleToggle.style.background = '#eff6ff';
            scheduleToggle.style.borderBottomLeftRadius = '0';
            scheduleToggle.style.borderBottomRightRadius = '0';
            scheduleToggle.style.borderBottom = 'none';
        } else {
            scheduleContent.style.display = 'none';
            scheduleText.textContent = 'Schedule for later';
            scheduleIcon.className = 'fas fa-chevron-right';
            scheduleToggle.style.background = 'transparent';
            scheduleToggle.style.borderBottomLeftRadius = '12px';
            scheduleToggle.style.borderBottomRightRadius = '12px';
            scheduleToggle.style.borderBottom = '1px solid #bfdbfe';
        }
    }

    function updateSubmitBtn() {
        if (isScheduling) {
            submitText.textContent = 'Schedule Message';
            submitIcon.className = 'fas fa-clock';
        } else {
            if (selectedMethod === 'whatsapp') {
                submitText.textContent = 'Send via WhatsApp';
                submitIcon.className = 'fab fa-whatsapp';
            } else if (selectedMethod === 'email') {
                submitText.textContent = 'Send via Email';
                submitIcon.className = 'far fa-envelope';
            } else if (selectedMethod === 'sms') {
                submitText.textContent = 'Send via SMS';
                submitIcon.className = 'fas fa-phone-alt';
            } else if (selectedMethod === 'all') {
                submitText.textContent = 'Send via All Channels';
                submitIcon.className = 'fas fa-share-nodes';
            }
        }
    }

    // Submit Action
    submitBtn.addEventListener('click', function() {
        const text = previewArea.value;
        const recipient = recipientInput.value.trim();
        
        if (isScheduling) {
            const date = document.getElementById('share-page-schedule-date').value;
            const time = document.getElementById('share-page-schedule-time').value;
            if (!date || !time) {
                alert('Please provide both date and time for scheduling.');
                return;
            }
            if (!recipient) {
                alert('Please provide a recipient phone or email.');
                return;
            }
            
            const scheduleChannel = selectedMethod === 'all' ? 'email' : (selectedMethod === 'whatsapp' ? 'sms' : selectedMethod);

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('wish_message_id', currentMessageId);
            formData.append('channel', scheduleChannel);
            if (scheduleChannel === 'email') {
                formData.append('recipient_email', recipient);
            } else {
                formData.append('recipient_phone', recipient);
            }
            formData.append('custom_message', text);
            formData.append('schedule_date', date);
            formData.append('schedule_time', time);

            fetch('{{ route("share-messages.schedule") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Message scheduled successfully!');
                    window.location.href = '{{ route("user.my-messages.page") }}';
                } else {
                    alert('Error scheduling: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(e => {
                console.error(e);
                alert('Error scheduling message.');
            });

            return;
        }

        // Immediate Sending
        if (selectedMethod === 'whatsapp' || selectedMethod === 'all') {
            const encodedText = encodeURIComponent(text);
            const waUrl = recipient ? `https://wa.me/${recipient.replace(/[^0-9]/g, '')}?text=${encodedText}` : `https://wa.me/?text=${encodedText}`;
            window.open(waUrl, '_blank');
        }

        if (selectedMethod === 'sms' || selectedMethod === 'all') {
            if (!recipient && selectedMethod === 'sms') {
                alert('Please provide a recipient phone number for SMS.');
                return;
            }
            if (recipient && !recipient.includes('@')) {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('wish_message_id', currentMessageId);
                formData.append('recipient_phone', recipient);
                formData.append('custom_message', text);
                
                fetch('{{ route("share-messages.sendSms") }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData
                }).then(r => r.json()).then(data => {
                    if (selectedMethod === 'sms') alert(data.success ? 'SMS sent successfully!' : 'Error sending SMS.');
                });
            }
        }

        if (selectedMethod === 'email' || selectedMethod === 'all') {
            if (!recipient && selectedMethod === 'email') {
                alert('Please provide a recipient email.');
                return;
            }
            if (recipient && recipient.includes('@')) {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('wish_message_id', currentMessageId);
                formData.append('recipient_email', recipient);
                formData.append('custom_message', text);

                fetch('{{ route("share-messages.sendEmail") }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData
                }).then(r => r.json()).then(data => {
                    if (selectedMethod === 'email') alert(data.success ? 'Email sent successfully!' : 'Error sending Email.');
                });
            }
        }
        
        if (selectedMethod === 'all') {
            alert('Messages triggered for all selected channels!');
        }
    });
});
</script>
@endpush
