<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribed | WISP</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f6f7ff;
            background-image:
                radial-gradient(circle at 10% 15%, rgba(166, 223, 255, 0.4), transparent 28%),
                radial-gradient(circle at 80% 22%, rgba(255, 181, 213, 0.3), transparent 34%),
                radial-gradient(circle at 60% 76%, rgba(255, 202, 230, 0.2), transparent 24%),
                linear-gradient(120deg, #edf4ff 0%, #f9f4ff 48%, #ffe8f2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            color: var(--text-main);
        }
        .container {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .icon {
            font-size: 48px;
            color: var(--primary);
            margin-bottom: 20px;
        }
        h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        p {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.2s;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Unsubscribed Successfully</h1>
        <p>You have been successfully removed from our mailing list. You will no longer receive updates and newsletters from us.</p>
        <a href="{{ route('home') }}" class="btn">Return to Home</a>
    </div>
</body>
</html>
