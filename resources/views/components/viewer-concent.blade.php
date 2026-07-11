<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>{{ isset($pageTitle) ? $pageTitle : 'View message' }}</title>
    <style>
        :root {
            --card-bg: rgba(255, 255, 255, 0.98);
            --text: #1e293b;
            --accent: #6366f1;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: #b8d4e8;
            background-image: url('{{ asset("img/wisp 1.0.png") }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: cover;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: var(--text);
            padding: 20px;
        }

        .container {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 56px 48px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
        }

        h1 {
            margin: 0 0 16px;
            font-size: 1.875rem;
            font-weight: 700;
            color: #0f172a;
        }

        p {
            margin: 0 0 32px;
            font-size: 1.1rem;
            line-height: 1.6;
            color: #475569;
        }

        .agree-form {
            margin: 0;
        }

        .agree-form .btn-agree {
            display: inline-block;
            padding: 14px 36px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            background: var(--accent);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(124, 58, 237, 0.45);
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
        }

        .agree-form .btn-agree:hover {
            background: #6d28d9;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.5);
        }

        .agree-form .btn-agree:active {
            transform: translateY(0);
        }

        .pin-input {
            width: 100%;
            max-width: 280px;
            padding: 14px 20px;
            margin-bottom: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            font-size: 1.05rem;
            text-align: center;
            outline: none;
            transition: all 0.2s ease;
            color: var(--text);
            background: #f8fafc;
        }

        .pin-input::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .pin-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            background: #ffffff;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.95rem;
            margin-bottom: 20px;
            font-weight: 500;
        }

        @media (max-width: 480px) {
            .container {
                padding: 40px 24px;
            }
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ isset($pageTitle) ? $pageTitle : 'View message' }}</h1>
        <p>{{ $creatorName ?? 'Someone' }} wrote a {{ $messageTypeDisplay ?? 'special' }} message for you this day. Click Agree to view message.</p>

        @if(isset($type) && isset($slug))
            <form method="POST" action="{{ route('message.consent.accept', ['type' => $type, 'slug' => $slug]) }}" class="agree-form">
                @csrf
                
                @if($isVaulted ?? false)
                    <div>
                        <input type="password" name="vault_pin" class="pin-input" placeholder="Enter Vault PIN" required>
                    </div>
                @endif

                @if(session('error'))
                    <div class="error-message">{{ session('error') }}</div>
                @endif

                <button type="submit" class="btn-agree">Agree</button>
            </form>
        @else
            <p class="text-muted small">Unable to load this message.</p>
        @endif
    </div>
</body>
</html>
