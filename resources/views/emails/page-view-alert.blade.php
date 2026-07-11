<!DOCTYPE html>
<html>
<head>
    <title>Page View Alert</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; color: #333; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="color: #6366f1;">New Page View</h2>
        <p>Hello {{ $user->name ?? 'User' }},</p>
        <p>Someone just viewed your message: <strong>{{ $messageTitle }}</strong>.</p>
        <p>This is an automated alert based on your notification preferences in WISP.</p>
        <p>Best,<br>The WISP Team</p>
    </div>
</body>
</html>
