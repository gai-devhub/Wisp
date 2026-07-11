<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#6366f1">
    <title>Wrong Link — WISP</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8ecff 50%, #fdf2f8 100%);
            color: #0f172a;
            padding: 1.5rem;
        }
        .card {
            max-width: 420px;
            width: 100%;
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 20px 50px -12px rgba(99, 102, 241, 0.2);
            border: 1px solid rgba(99, 102, 241, 0.1);
        }
        .logo { width: 56px; height: 56px; margin-bottom: 1.25rem; }
        .recipient-circle {
            width: 160px;
            height: 160px;
            margin: 0 auto 1.25rem;
            border-radius: 50%;
            overflow: hidden;
            background: rgba(239, 68, 68, 0.15); /* Reddish tint for wrong link */
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ef4444;
            font-size: 2.5rem;
        }
        .recipient-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }
        p {
            font-size: 1rem;
            color: #475569;
            line-height: 1.5;
        }
        a {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background: #6366f1;
            color: #fff;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            transition: background 0.2s, transform 0.15s;
        }
        a:hover { background: #4f46e5; transform: translateY(-1px); }
    </style>
</head>
<body>
    <div class="card">
        <img src="{{ asset('img/logo.png') }}" alt="WISP" class="logo">
        <div class="recipient-circle">
            <i class="fas fa-link-slash"></i>
        </div>
        <h1>Wrong or Disabled Link</h1>
        <p>The link you opened is incorrect, disabled, or no longer exists. Please ask the sender for the correct link.</p>
        <a href="{{ route('home') }}"><i class="fas fa-home"></i> Back to home</a>
    </div>
</body>
</html>
