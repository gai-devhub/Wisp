<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#E8674A">
    <title>Page Expired — WISP</title>
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --card-bg: rgba(255, 255, 255, 0.85);
            --text: #2B1F3D;
            --text-muted: #6E6178;
            --accent: #E8674A;
            --accent-hover: #C7502F;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: #FDF6EC;
            background-image: radial-gradient(circle at 10% 15%, rgba(232, 103, 74, 0.12), transparent 28%), radial-gradient(circle at 80% 22%, rgba(43, 31, 61, 0.10), transparent 34%), radial-gradient(circle at 60% 76%, rgba(232, 103, 74, 0.08), transparent 24%), linear-gradient(120deg, #FDF6EC 0%, #F5EAD9 48%, #FDF6EC 100%);
            background-attachment: fixed;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
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
            filter: drop-shadow(0 10px 15px rgba(232, 103, 74, 0.2));
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
            padding: 56px 48px;
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
            font-family: 'Fraunces', Georgia, serif;
        }

        p {
            margin: 0 0 32px;
            font-size: 1.05rem;
            line-height: 1.6;
            color: var(--text-muted);
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 10px 20px -5px rgba(232, 103, 74, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
            text-decoration: none;
            margin-top: 1rem;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -5px rgba(232, 103, 74, 0.5);
            filter: brightness(1.05);
        }

        .btn-home:active {
            transform: translateY(1px);
            box-shadow: 0 4px 10px -3px rgba(232, 103, 74, 0.4);
        }

        @media (max-width: 480px) {
            .container {
                padding: 40px 24px;
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
        <h1>This page has expired</h1>
        <p>The message or link you opened is no longer available. It may have reached its expiry date or time.</p>
        <a href="{{ route('home') }}" class="btn-home"><i class="fas fa-home"></i> Back to home</a>
    </div>
</body>
</html>