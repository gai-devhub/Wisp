<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $updateSubject }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f6f7ff; color: #333; line-height: 1.6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .header img { max-width: 80px; }
        h1 { color: #6366f1; font-size: 24px; margin-bottom: 20px; text-align: center; }
        .content { font-size: 16px; margin-bottom: 20px; color: #555; white-space: pre-wrap; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #999; }
        .unsubscribe { color: #999; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>WISP</h2>
        </div>
        <h1>{{ $updateSubject }}</h1>
        <div class="content">{{ $messageContent }}</div>
        
        <div class="footer">
            <p>You received this email because you are subscribed to the WISP newsletter.</p>
            <p><a href="{{ route('unsubscribe', $subscriber->token) }}" class="unsubscribe">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>
