<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to WISP</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #0f172a; background: #f8fafc; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 24px auto; padding: 32px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; }
        .header { text-align: center; margin-bottom: 32px; }
        .header h1 { margin: 0; color: #6366f1; font-size: 28px; font-weight: 700; }
        .body p { line-height: 1.6; margin: 16px 0; color: #334155; }
        .btn-wrap { text-align: center; margin: 32px 0; }
        .btn { background: linear-gradient(135deg, #6366f1, #a855f7); color: #fff; text-decoration: none; padding: 14px 28px; border-radius: 8px; display: inline-block; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(99,102,241,0.2); }
        .features { background: #f1f5f9; padding: 20px; border-radius: 8px; margin: 24px 0; }
        .features ul { padding-left: 20px; margin: 0; color: #334155; }
        .features li { margin-bottom: 10px; line-height: 1.5; }
        .footer { margin-top: 32px; font-size: 0.85rem; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 16px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to WISP!</h1>
        </div>
        <div class="body">
            <p>Hi {{ explode(' ', $user->name ?? $user->username)[0] }},</p>
            <p>We are thrilled to have you onboard. WISP is your central command center for managing, sharing, and organizing your secure messages and digital workspace.</p>
            
            <div class="features">
                <strong>Here is what you can do next:</strong>
                <ul style="margin-top: 12px;">
                    <li>Set up your <strong>Control Center</strong> preferences.</li>
                    <li>Secure your account with a custom <strong>2FA Passcode</strong>.</li>
                    <li>Start drafting and sending encrypted messages.</li>
                </ul>
            </div>

            <div class="btn-wrap">
                <a href="{{ route('user.page') }}" class="btn" target="_blank" rel="noopener">Access Your Dashboard</a>
            </div>

            <p>If you have any questions or need assistance, feel free to reach out to our support team.</p>
            <p>Welcome to the family,<br><strong>The WISP Team</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} WISP. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
