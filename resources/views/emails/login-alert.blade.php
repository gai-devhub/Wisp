<!DOCTYPE html>
<html>
<head>
    <title>New Login Alert</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; color: #333; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="color: #6366f1;">New Login to WISP</h2>
        <p>Hello {{ $user->name ?? 'User' }},</p>
        <p>We noticed a new login to your WISP dashboard.</p>
        <p><strong>Time:</strong> {{ now()->format('F j, Y g:i A') }}</p>
        <p>If this was you, you can safely ignore this email.</p>
        <p>Best,<br>The WISP Team</p>
    </div>
</body>
</html>
