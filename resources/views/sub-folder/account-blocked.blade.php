<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#6366f1">
    <title>Account blocked — WISP</title>
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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 20px 50px -12px rgba(99, 102, 241, 0.2);
            border: 1px solid rgba(99, 102, 241, 0.1);
        }
        .logo { width: 56px; height: 56px; margin-bottom: 1.25rem; }
        .icon-circle {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.25rem;
            background: rgba(239, 68, 68, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc2626;
            font-size: 2rem;
        }
        h1 { font-size: 1.5rem; font-weight: 600; color: #0f172a; margin-bottom: 0.5rem; }
        p { font-size: 1rem; color: #475569; line-height: 1.5; margin-bottom: 1rem; }
        .contact-box {
            text-align: left;
            background: rgba(99, 102, 241, 0.06);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
        }
        .contact-box h2 { font-size: 0.9rem; font-weight: 600; color: #0f172a; margin-bottom: 0.75rem; }
        .contact-links { display: flex; flex-direction: column; gap: 0.5rem; }
        .contact-links a, .contact-links span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            color: #6366f1;
            text-decoration: none;
        }
        .contact-links a:hover { text-decoration: underline; }
        .contact-links span { color: #64748b; }
        .contact-links .fa { width: 18px; text-align: center; }
        a.btn-home {
            display: inline-block;
            margin-top: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: #6366f1;
            color: #fff;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            transition: background 0.2s, transform 0.15s;
        }
        a.btn-home:hover { background: #4f46e5; transform: translateY(-1px); }
        .logout-form { margin-top: 1rem; }
        .logout-form button {
            background: transparent;
            color: #64748b;
            border: none;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: underline;
            font-family: inherit;
        }
        .logout-form button:hover { color: #0f172a; }
    </style>
</head>
<body>
    <div class="card">
        <img src="{{ asset('img/logo.png') }}" alt="WISP" class="logo">
        <div class="icon-circle"><i class="fas fa-user-lock"></i></div>
        <h1>Your account has been blocked</h1>
        <p>Please contact the administrator to resolve this. You can reach them using the options below.</p>

        @if(!empty($contact_email) || !empty($contact_phone) || !empty($contact_whatsapp))
        <div class="contact-box">
            <h2>Contact administrator</h2>
            <div class="contact-links">
                @if(!empty($contact_email))
                    <a href="mailto:{{ $contact_email }}"><i class="fas fa-envelope"></i> {{ $contact_email }}</a>
                @endif
                @if(!empty($contact_whatsapp))
                    @php $wa = preg_replace('/\D/', '', $contact_whatsapp); @endphp
                    @if(strlen($wa) > 0)
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> WhatsApp ({{ $contact_whatsapp }})</a>
                    @endif
                @endif
                @if(!empty($contact_phone))
                    <a href="tel:{{ $contact_phone }}"><i class="fas fa-phone"></i> Call {{ $contact_phone }}</a>
                @endif
            </div>
        </div>
        @else
        <p class="contact-box">Contact your administrator for assistance.</p>
        @endif

        <a href="{{ route('home') }}" class="btn-home"><i class="fas fa-home"></i> Back to home</a>
        <form method="POST" action="{{ route('auth.logout') }}" class="logout-form">
            @csrf
            <button type="submit">Log out</button>
        </form>
    </div>
</body>
</html>
