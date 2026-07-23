<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #ff4d8d;
            --secondary: #4169e1;
            --accent: #ffd700;
            --dark: #1a1a2e;
            --light: #f8f8ff;
            --card-bg: rgba(255, 255, 255, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark) 0%, #16213e 100%);
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

        .stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .star {
            position: absolute;
            background-color: white;
            border-radius: 50%;
            animation: twinkle 5s infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.2;
            }

            50% {
                opacity: 1;
            }
        }

        .gem-container {
            position: absolute;
            top: 50px;
            right: 50px;
            animation: float 6s ease-in-out infinite;
        }

        .gem {
            font-size: 3rem;
            filter: drop-shadow(0 0 10px var(--accent));
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }

        .title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.5s ease;
        }

        .title:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        .datetime {
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: var(--accent);
        }

        .countdown {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            position: relative;
            color: #333;
            margin-bottom: 30px;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
            border: 3px solid var(--primary);
            box-shadow: 0 0 15px rgba(255, 77, 141, 0.5);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--dark);
        }

        .message {
            line-height: 1.8;
            font-size: 1.1rem;
        }

        .message strong {
            color: var(--primary);
            font-weight: 600;
        }

        .message i {
            color: var(--secondary);
            font-style: italic;
        }

        .message b {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 700;
        }

        .hearts {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .heart {
            position: absolute;
            font-size: 1.5rem;
            opacity: 0;
            animation: fall 10s linear infinite;
        }

        @keyframes fall {
            0% {
                transform: translateY(-10vh) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(110vh) rotate(360deg);
                opacity: 0;
            }
        }

        .audio-player {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--card-bg);
            border-radius: 50px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            z-index: 100;
        }

        .play-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }

        @media (max-width: 768px) {
            .title {
                font-size: 2.5rem;
            }

            .card {
                padding: 20px;
            }

            .avatar {
                width: 60px;
                height: 60px;
            }

            .card-title {
                font-size: 1.5rem;
            }

            .gem-container {
                top: 20px;
                right: 20px;
            }
        }

        @media (max-width: 480px) {
            .title {
                font-size: 2rem;
            }

            .audio-player {
                bottom: 10px;
                right: 10px;
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
            /* Global Mobile Scroll Fix */
        @media (max-width: 768px) {
            body, html {
                overflow-y: auto !important;
                height: auto !important;
            }
            .viewport, .container, .wrapper, .main-container {
                height: auto !important;
                min-height: 100vh;
                overflow: visible !important;
            }
            .content-hub, .message-panel, .message-box, .letter-box, .content-area, .text-panel, .message-wrapper, .message-container, .content-section, .text-section, .message-content, .main-content, .card {
                max-height: none !important;
                overflow-y: visible !important;
                height: auto !important;
            }
        }
    </style>
</head>

<body>
    <div class="stars" id="stars"></div>
    <div class="hearts" id="hearts"></div>

    <div class="gem-container">
        <div class="gem">💎</div>
    </div>

    <div class="overlay" id="overlay">
        <div>Screenshots are not allowed</div>
    </div>

    <div class="header">
        <h1 class="title">{{ $message->title }} </h1>
        <div class="datetime" id="currentTime"></div>
        <div class="countdown" id="countdownMessage"></div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="avatar">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name }}">
                @endif
            </div>
            <h2 class="card-title">{{ $message->recipient_special_name ?? $message->recipient_name }}💕</h2>
        </div>
        <div class="message">
            <p><strong>{{ $message->recipient_special_name ?? $message->recipient_name }},</strong> if I stand to be
                corrected.</p>
            <br>
            <p><strong>{{ $message->recipient_name }}</strong></p>
            <br>
            <p>{!! $message->message_display !!}</p>
            <p><strong>{{ $message->last_note }}</strong></p>
        </div>
    </div>


        <x-background-music :mediaFiles="$mediaFiles">
        <div class="audio-player" id="audioPlayer">
            <button type="button" class="play-btn" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
</div>
    </x-background-music>

    <script>
        // Create stars background
        const starsContainer = document.getElementById('stars');
        for (let i = 0; i < 100; i++) {
            const star = document.createElement('div');
            star.classList.add('star');
            star.style.left = `${Math.random() * 100}%`;
            star.style.top = `${Math.random() * 100}%`;
            star.style.width = `${Math.random() * 3 + 1}px`;
            star.style.height = star.style.width;
            star.style.animationDelay = `${Math.random() * 5}s`;
            starsContainer.appendChild(star);
        }

        // Create falling hearts
        const heartsContainer = document.getElementById('hearts');
        function createHeart() {
            const heart = document.createElement('div');
            heart.classList.add('heart');
            heart.innerHTML = '❤️';
            heart.style.left = `${Math.random() * 100}%`;
            heart.style.fontSize = `${Math.random() * 1.5 + 1}rem`;
            heart.style.animationDuration = `${Math.random() * 10 + 5}s`;
            heartsContainer.appendChild(heart);

            setTimeout(() => {
                heart.remove();
            }, 15000);
        }

        setInterval(createHeart, 300);


        // Display current date and time
        function updateTime() {
            const now = new Date();
            document.getElementById("currentTime").innerText = now.toLocaleString();
        }


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

    </script>
</body>

</html>
