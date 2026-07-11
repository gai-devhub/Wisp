<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Message</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #0f172a; background: #f8fafc; margin: 0; padding: 0; }
        .container { max-width: 650px; margin: 16px auto; padding: 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { margin: 0; color: #1e3a8a; font-size: 24px; }
        .body p { line-height: 1.6; margin: 12px 0; }
        .btn-wrap { text-align: center; margin: 24px 0; }
        .btn { background: #6366f1; color: #fff; text-decoration: none; padding: 12px 20px; border-radius: 8px; display: inline-block; font-weight: 600; }
        .footer { margin-top: 24px; font-size: 0.85rem; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>You've received a message</h1>
        </div>
        <div class="body">
            <p>Hi {{ $recipientName ?? 'there' }},</p>
            <p>{{ $senderName ?? 'A friend' }} has created a {{ $messageTypeDisplay ?? 'special' }} message for you and shared it with you by email.</p>
            <p><strong>Message title:</strong> {{ $messageTitle }}</p>
            <p><strong>Content:</strong></p>
            <p style="white-space: pre-wrap;">{{ Str::limit($messageBody, 120, '...') }}</p>

            <div class="btn-wrap">
                <a href="{{ $link }}" class="btn" target="_blank" rel="noopener">View your full message</a>
            </div>

            @if(!empty($vaultPin))
            <div style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 16px; border-radius: 8px; text-align: center; margin-bottom: 24px;">
                <p style="margin: 0; font-size: 0.95rem; color: #64748b; margin-bottom: 8px;">To unlock this message, use the secure PIN:</p>
                <div style="font-size: 1.5rem; font-weight: 700; color: #1e293b; letter-spacing: 2px;">{{ $vaultPin }}</div>
            </div>
            @endif

            <p>If the button above does not work, copy and paste this URL into your browser:</p>
            <p><a href="{{ $link }}">{{ $link }}</a></p>

            <p>Regards,<br>WISP Team</p>
        </div>
        <div class="footer">
            <p>This message was sent by a WISP user. If you did not expect this email, please ignore it.</p>
        </div>
    </div>
</body>
</html>