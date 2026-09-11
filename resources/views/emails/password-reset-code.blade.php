<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Password Reset Code — WISP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; -webkit-text-size-adjust: 100%; }
        body { margin: 0; padding: 0; width: 100% !important; background-color: #F8FAFC; font-family: 'Inter', system-ui, -apple-system, sans-serif; color: #334155; }
        table { border-collapse: collapse; }
        .wrapper { width: 100%; background-color: #F8FAFC; padding: 24px 12px; }
        .email-container { max-width: 580px; width: 100%; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04); }
        .content { padding: 32px 28px; }
        .eyebrow { display: inline-block; background: #FFF3EB; color: #E8674A; font-size: 11px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; padding: 5px 12px; border-radius: 50px; margin-bottom: 16px; }
        h1.title { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; font-size: 21px; font-weight: 800; color: #0F172A; margin: 0 0 14px; line-height: 1.3; }
        p { font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 14px; }
        .code-box { background: #F8FAFC; border: 2px dashed #CBD5E1; border-radius: 12px; padding: 18px 12px; text-align: center; font-family: 'Plus Jakarta Sans', monospace; font-size: 30px; font-weight: 800; letter-spacing: 8px; color: #0F172A; margin: 20px 0; }
        .warn-box { background: #FEF2F2; border-left: 4px solid #EF4444; border-radius: 8px; padding: 14px 16px; margin: 18px 0; font-size: 13px; color: #991B1B; line-height: 1.5; }
        .footer { padding: 20px 24px 24px; text-align: center; font-size: 12px; color: #94A3B8; border-top: 1px solid #F1F5F9; background-color: #FAFAFA; }
        .footer a { color: #E8674A; text-decoration: none; font-weight: 600; }
        .footer p { font-size: 12px; color: #94A3B8; margin: 0 0 4px; }
        
        @media only screen and (max-width: 600px) {
            .wrapper { padding: 8px 4px !important; }
            .email-container { width: 100% !important; border-radius: 12px !important; }
            .content { padding: 20px 16px !important; }
            .footer { padding: 16px 16px 20px !important; }
            h1.title { font-size: 19px !important; }
            .code-box { font-size: 24px !important; letter-spacing: 5px !important; padding: 14px 8px !important; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="email-container">
            <div class="content">
                <span class="eyebrow">Account Security</span>
                <h1 class="title">Reset your password</h1>
                <p>Hello,</p>
                <p>You recently requested to reset your password for your WISP account. Use the code below to reset your password:</p>

                <div class="code-box">{{ $code }}</div>

                <p style="text-align: center; font-size: 13px; color: #64748B; margin-top: -6px;">This verification code expires in {{ $expiresInMinutes ?? 15 }} minutes.</p>

                <div class="warn-box">
                    <strong>Keep this code private.</strong> WISP staff will never ask you for your password reset code.
                </div>

                <p>If you did not request a password reset, you can safely ignore this email.</p>
                <p style="margin-top: 20px; font-size: 13px; color: #64748B;">Thanks,<br><strong style="color: #0F172A;">The WISP Team</strong></p>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} WISP. All rights reserved.</p>
                <p>Need help? <a href="mailto:hello@wisp.app">Contact support</a></p>
            </div>
        </div>
    </div>
</body>
</html>