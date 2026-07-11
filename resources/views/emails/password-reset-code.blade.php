<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Code</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #6366f1;">WISP Password Reset</h2>
        <p>Hello,</p>
        <p>You recently requested to reset your password for your WISP account. Here is your verification code:</p>
        
        <div style="background-color: #f8fafc; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 4px; border-radius: 6px; margin: 20px 0;">
            {{ $code }}
        </div>
        
        <p>If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
        <p>Thanks,<br>The WISP Team</p>
    </div>
</body>
</html>
