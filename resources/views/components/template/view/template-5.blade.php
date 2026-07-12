<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #D4AF37;
            --secondary: #8A5D34;
            --accent: #C19A6B;
            --dark: #2C1810;
            --light: #F8F5F0;
            --card-bg: rgba(255, 255, 245, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #3A2C1F 0%, #2C1810 100%);
            font-family: 'Montserrat', sans-serif;
            color: var(--light);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        .elegant-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            background:
                radial-gradient(circle at 20% 30%, rgba(212, 175, 55, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 80% 70%, rgba(193, 154, 107, 0.1) 0%, transparent 30%),
                linear-gradient(135deg, rgba(58, 44, 31, 0.9) 0%, rgba(44, 24, 16, 0.9) 100%);
        }

        .ornamental-border {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .border-top {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .border-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .border-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(transparent, var(--primary), transparent);
        }

        .border-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(transparent, var(--primary), transparent);
        }

        .gold-flake {
            position: absolute;
            width: 8px;
            height: 8px;
            background: var(--primary);
            opacity: 0.6;
            border-radius: 50%;
            animation: floatFlake 15s infinite linear;
        }

        @keyframes floatFlake {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.6;
            }

            100% {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            width: 100%;
            max-width: 700px;
            padding: 20px;
        }

        .title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
            letter-spacing: 2px;
            text-shadow: 0 2px 10px rgba(212, 175, 55, 0.3);
        }

        .title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 2px;
            background: var(--primary);
            border-radius: 1px;
        }

        .datetime {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--accent);
            font-weight: 400;
            letter-spacing: 1px;
        }

        .countdown {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            background: rgba(0, 0, 0, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
        }

        .card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 40px;
            max-width: 400px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            position: relative;
            color: var(--dark);
            margin-bottom: 40px;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 20px;
            border: 2px solid var(--primary);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
            transition: transform 0.3s ease;
        }

        .avatar:hover {
            transform: scale(1.05);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            color: var(--dark);
            font-weight: 600;
        }

        .message {
            line-height: 1.8;
            font-size: 1.1rem;
        }

        .message p {
            margin-bottom: 20px;
        }

        .message strong {
            color: var(--secondary);
            font-weight: 600;
        }

        .message i {
            color: var(--accent);
            font-style: italic;
            font-weight: 500;
        }

        .message b {
            color: var(--primary);
            font-weight: 700;
        }

        .audio-player {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: var(--card-bg);
            border-radius: 50px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            z-index: 100;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .play-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--primary);
            border: none;
            color: var(--dark);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .play-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.5);
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 500;
            text-align: center;
            padding: 20px;
        }

        .elegant-gem {
            position: absolute;
            font-size: 2rem;
            color: var(--primary);
            opacity: 0.7;
            z-index: -1;
            animation: sparkle 4s infinite;
        }

        .gem-1 {
            top: 15%;
            left: 10%;
            animation-delay: 0s;
        }

        .gem-2 {
            top: 25%;
            right: 15%;
            animation-delay: 1s;
        }

        .gem-3 {
            bottom: 20%;
            right: 10%;
            animation-delay: 2s;
        }

        .gem-4 {
            bottom: 30%;
            left: 15%;
            animation-delay: 3s;
        }

        @keyframes sparkle {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.2);
                opacity: 1;
                filter: drop-shadow(0 0 8px var(--primary));
            }
        }

        @media (max-width: 768px) {
            .title {
                font-size: 3rem;
            }

            .card {
                padding: 30px 25px;
            }

            .avatar {
                width: 70px;
                height: 70px;
            }

            .card-title {
                font-size: 2rem;
            }

            .audio-player {
                bottom: 20px;
                right: 20px;
            }
        }

        @media (max-width: 480px) {
            .title {
                font-size: 2.5rem;
            }

            .audio-player {
                bottom: 15px;
                right: 15px;
                border-radius: 25px;
            }

            .datetime {
                font-size: 1.1rem;
            }

            .countdown {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 1080px) {
            .card {
                max-height: 80vh;
            }
        }

        @media (max-width: 600px) {
            .card {
                max-height: 75vh;
            }
        }

        @media (max-width: 400px) {
            .card {
                max-height: 70vh;
            }
        }
    </style>
</head>

<body>
    <div class="elegant-background"></div>
    <div class="ornamental-border">
        <div class="border-top"></div>
        <div class="border-bottom"></div>
        <div class="border-left"></div>
        <div class="border-right"></div>
    </div>

    <div class="elegant-gem gem-1">💎</div>
    <div class="elegant-gem gem-2">💎</div>
    <div class="elegant-gem gem-3">💎</div>
    <div class="elegant-gem gem-4">💎</div>

    <div class="overlay" id="overlay">
        <div>Screenshots are not allowed on this page</div>
    </div>

    <div class="header">
        <h1 class="title">{{ $message->title }}</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="avatar">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name }}">
                @endif
            </div>
            <h2 class="card-title">{{ $message->recipient_special_name ?? $message->recipient_name }} 💕</h2>
        </div>
        <div class="message">
            <p><strong>{{ $message->greeting }}</strong></p>
            <p>{!! $message->message_display !!}</p>
            <p><b>{{ $message->last_note }}</b></p>
        </div>
    </div>

        <x-background-music :mediaFiles="$mediaFiles">
        <div class="audio-player" id="audioPlayer">
            <button type="button" class="play-btn" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
</div>
    </x-background-music>

    <script>
        // Create gold flakes
        function createGoldFlakes() {
            const body = document.querySelector('body');
            const flakesCount = 20;

            for (let i = 0; i < flakesCount; i++) {
                const flake = document.createElement('div');
                flake.classList.add('gold-flake');
                flake.style.left = `${Math.random() * 100}%`;
                flake.style.animationDelay = `${Math.random() * 15}s`;
                flake.style.animationDuration = `${15 + Math.random() * 10}s`;
                body.appendChild(flake);
            }
        }

        createGoldFlakes();

       // Security features
        document.addEventListener('keydown', function (e) {
            if (e.key === 'PrintScreen') {
                document.getElementById('overlay').style.display = 'flex';
                setTimeout(() => {
                    document.getElementById('overlay').style.display = 'none';
                }, 2000);
                e.preventDefault();
            }
        });

        // document.addEventListener('contextmenu', function(e) {
        //     e.preventDefault();
        // });
    </script>
</body>

</html>