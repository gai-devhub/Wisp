<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to WISP!</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f6f7ff; color: #333; line-height: 1.6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .header img { max-width: 80px; }
        h1 { color: #6366f1; font-size: 24px; margin-bottom: 20px; text-align: center; }
        p { font-size: 16px; margin-bottom: 20px; color: #555; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #999; }
        .unsubscribe { color: #999; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>WISP</h2>
        </div>
        <h1>Welcome to the magic! ✨</h1>
        <p>Hi there,</p>
        <p>Thank you for subscribing to the WISP newsletter! We are thrilled to have you on board.</p>
        <p>You'll be the first to know about new stunning templates, exciting AI writer updates, and special features to make your wishes hit different.</p>
        <p>Stay tuned for some magic coming your way soon!</p>
        <p>Cheers,<br>The WISP Team</p>
        
        <div class="footer">
            <p>You received this email because you subscribed on our website.</p>
            <p><a href="{{ route('unsubscribe', $subscriber->token) }}" class="unsubscribe">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>
