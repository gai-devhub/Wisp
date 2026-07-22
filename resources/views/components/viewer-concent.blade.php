<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>{{ isset($pageTitle) ? $pageTitle : 'View message' }}</title>
    <style>
        :root {
            --card-bg: rgba(255, 255, 255, 0.85);
            --text: #0f172a;
            --text-muted: #475569;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
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
            background-color: #f6f7ff;
            background-image: radial-gradient(circle at 10% 15%, rgba(166, 223, 255, 0.9), transparent 28%), radial-gradient(circle at 80% 22%, rgba(255, 181, 213, 0.78), transparent 34%), radial-gradient(circle at 60% 76%, rgba(255, 202, 230, 0.62), transparent 24%), linear-gradient(120deg, #edf4ff 0%, #f9f4ff 48%, #ffe8f2 100%);
            background-attachment: fixed;
            font-family: 'Outfit', system-ui, -apple-system, sans-serif;
            color: var(--text);
            padding: 20px;
            overflow: hidden; /* Prevent scroll from bouncing elements */
        }

        /* Bouncing background logos */
        .logo-container {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .bouncing-logo {
            position: absolute;
            width: 80px;
            height: 80px;
            opacity: 0;
            animation: floatAround infinite ease-in-out alternate;
            filter: drop-shadow(0 10px 15px rgba(99, 102, 241, 0.2));
        }

        .bouncing-logo:nth-child(1) { top: 10%; left: 15%; animation-duration: 12s; animation-delay: 0s; }
        .bouncing-logo:nth-child(2) { top: 75%; left: 10%; animation-duration: 15s; animation-delay: -3s; }
        .bouncing-logo:nth-child(3) { top: 35%; left: 80%; animation-duration: 14s; animation-delay: -5s; width: 60px; height: 60px; }
        .bouncing-logo:nth-child(4) { top: 80%; left: 70%; animation-duration: 18s; animation-delay: -2s; width: 100px; height: 100px; }
        .bouncing-logo:nth-child(5) { top: 20%; left: 50%; animation-duration: 16s; animation-delay: -7s; width: 50px; height: 50px; }
        .bouncing-logo:nth-child(6) { top: 60%; left: 40%; animation-duration: 13s; animation-delay: -1s; width: 70px; height: 70px; }

        @keyframes floatAround {
            0% { transform: translate(0, 0) rotate(0deg) scale(0.8); opacity: 0; }
            20% { opacity: 0.4; }
            50% { transform: translate(15vw, 15vh) rotate(180deg) scale(1.1); opacity: 0.6; }
            80% { opacity: 0.4; }
            100% { transform: translate(-10vw, 25vh) rotate(360deg) scale(0.9); opacity: 0; }
        }

        .container {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 56px 48px 40px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5) inset;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        h1 {
            margin: 0 0 12px;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        p {
            margin: 0 0 32px;
            font-size: 1.05rem;
            line-height: 1.6;
            color: var(--text-muted);
        }

        .agree-form {
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .pin-wrapper {
            position: relative;
            width: 100%;
            max-width: 300px;
            margin-bottom: 24px;
        }

        .pin-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid transparent;
            border-radius: 16px;
            font-size: 1.1rem;
            text-align: center;
            outline: none;
            transition: all 0.3s ease;
            color: var(--text);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03), 0 0 0 1px rgba(0,0,0,0.05);
            font-family: inherit;
            letter-spacing: 0.1em;
            font-weight: 600;
        }

        .pin-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
            letter-spacing: normal;
        }

        .pin-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15), 0 0 0 1px rgba(99, 102, 241, 0.2);
            background: #ffffff;
            transform: translateY(-1px);
        }

        .btn-agree {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, var(--accent), #7c3aed);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
        }

        .btn-agree:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -5px rgba(99, 102, 241, 0.5);
            filter: brightness(1.05);
        }

        .btn-agree:active {
            transform: translateY(1px);
            box-shadow: 0 4px 10px -3px rgba(99, 102, 241, 0.4);
        }

        .error-message {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 10px 16px;
            border-radius: 12px;
            font-size: 0.95rem;
            margin-bottom: 24px;
            font-weight: 500;
            width: 100%;
            max-width: 300px;
        }

        @media (max-width: 480px) {
            .container {
                padding: 40px 24px 32px;
                border-radius: 20px;
            }
            h1 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="{{ asset('img/logo-circle.png') }}" class="bouncing-logo" alt="">
        <img src="{{ asset('img/logo-circle.png') }}" class="bouncing-logo" alt="">
        <img src="{{ asset('img/logo-circle.png') }}" class="bouncing-logo" alt="">
        <img src="{{ asset('img/logo-circle.png') }}" class="bouncing-logo" alt="">
        <img src="{{ asset('img/logo-circle.png') }}" class="bouncing-logo" alt="">
        <img src="{{ asset('img/logo-circle.png') }}" class="bouncing-logo" alt="">
    </div>

    <div class="container">
        <h1>{{ isset($pageTitle) ? $pageTitle : 'View message' }}</h1>
        <p>{{ $creatorName ?? 'Someone' }} wrote a {{ $messageTypeDisplay ?? 'special' }} message for you this day. Click Agree to view message.</p>

        @if(isset($type) && isset($slug))
            <form method="POST" action="{{ route('message.consent.accept', ['type' => $type, 'slug' => $slug]) }}" class="agree-form">
                @csrf
                
                @if($isVaulted ?? false)
                    <div class="pin-wrapper">
                        <input type="password" name="vault_pin" class="pin-input" placeholder="Enter Vault PIN" required>
                    </div>
                @endif

                @if(session('error'))
                    <div class="error-message">{{ session('error') }}</div>
                @endif

                <button type="submit" class="btn-agree">Agree</button>
                @if(!empty($expiresAt))
                    <p style="font-size: 13px; color: #666; margin-top: 15px; margin-bottom: 0; text-align: center;" id="expiry-text-container">
                        This message will expire after <span id="expiry-countdown" style="color: #ff4444; font-weight: 600;">{{ $expiryHours }} {{ $expiryHours == 1 ? 'hour' : 'hours' }}</span>
                    </p>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const expiresAt = new Date("{{ $expiresAt }}").getTime();
                            const countdownElement = document.getElementById('expiry-countdown');
                            const container = document.getElementById('expiry-text-container');

                            const updateCountdown = () => {
                                const now = new Date().getTime();
                                const distance = expiresAt - now;

                                if (distance <= 0) {
                                    container.innerHTML = "<span style='color: #ff4444; font-weight: 600;'>This message has expired.</span>";
                                    const btn = document.querySelector('.btn-agree');
                                    if(btn) {
                                        btn.disabled = true;
                                        btn.style.opacity = '0.5';
                                        btn.style.cursor = 'not-allowed';
                                    }
                                    return;
                                }

                                const hours = Math.ceil(distance / (1000 * 60 * 60));
                                countdownElement.innerText = hours + (hours === 1 ? ' hour' : ' hours');
                            };

                            updateCountdown();
                            setInterval(updateCountdown, 60000); // Check every minute
                        });
                    </script>
                @elseif(!empty($expiryHours))
                    <p style="font-size: 13px; color: #666; margin-top: 15px; margin-bottom: 0; text-align: center;">
                        This message will expire after <span style="color: #ff4444; font-weight: 600;">{{ $expiryHours }} {{ $expiryHours == 1 ? 'hour' : 'hours' }}</span>
                    </p>
                @endif
            </form>
        @else
            <p class="text-muted small">Unable to load this message.</p>
        @endif
    </div>
</body>
</html>
