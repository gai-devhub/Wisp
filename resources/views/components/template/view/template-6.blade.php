<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --royal-purple: #4A2C82;
            --gold: #D4AF37;
            --light-gold: #F1E5AC;
            --dark-purple: #2D1B4E;
            --light-purple: #7B5AB5;
            --white: #FFFFFF;
            --card-bg: rgba(255, 255, 255, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark-purple) 0%, var(--royal-purple) 100%);
            font-family: 'Montserrat', sans-serif;
            color: var(--white);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        .royal-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 30%, rgba(212, 175, 55, 0.15) 0%, transparent 25%),
                radial-gradient(circle at 80% 70%, rgba(123, 90, 181, 0.15) 0%, transparent 25%),
                url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23d4af37' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            z-index: -2;
        }

        .crown {
            position: absolute;
            font-size: 2rem;
            color: var(--gold);
            opacity: 0.7;
            z-index: -1;
            animation: floatCrown 8s infinite ease-in-out;
        }

        .crown-1 {
            top: 15%;
            left: 10%;
            animation-delay: 0s;
        }

        .crown-2 {
            top: 25%;
            right: 15%;
            animation-delay: 2s;
        }

        .crown-3 {
            bottom: 20%;
            right: 10%;
            animation-delay: 4s;
        }

        .crown-4 {
            bottom: 30%;
            left: 15%;
            animation-delay: 6s;
        }

        @keyframes floatCrown {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
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
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            font-weight: 700;
            color: var(--gold);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
            letter-spacing: 2px;
            text-shadow: 0 2px 10px rgba(212, 175, 55, 0.3);
        }

        .title::before,
        .title::after {
            content: '✨';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
        }

        .title::before {
            left: -40px;
        }

        .title::after {
            right: -40px;
        }

        .datetime {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--light-gold);
            font-weight: 400;
            letter-spacing: 1px;
        }

        .countdown {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            background: rgba(0, 0, 0, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            -webkit-backdrop-filter: blur(5px);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(212, 175, 55, 0.3);
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
            color: var(--dark-purple);
            margin-bottom: 40px;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--gold), var(--light-purple), var(--gold));
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
            border: 2px solid var(--gold);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
            transition: transform 0.3s ease;
            position: relative;
        }

        .avatar::after {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border-radius: 50%;
            border: 2px solid var(--light-purple);
            opacity: 0;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }

            100% {
                transform: scale(1.2);
                opacity: 0;
            }
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
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--dark-purple);
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
            color: var(--royal-purple);
            font-weight: 600;
        }

        .message i {
            color: var(--light-purple);
            font-style: italic;
            font-weight: 500;
        }

        .message b {
            color: var(--gold);
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
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .play-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--gold);
            border: none;
            color: var(--dark-purple);
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

        .royal-border {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .r-border-top {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .r-border-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        .r-border-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(transparent, var(--gold), transparent);
        }

        .r-border-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(transparent, var(--gold), transparent);
        }

        @media (max-width: 768px) {
            .title {
                font-size: 3rem;
            }

            .title::before,
            .title::after {
                display: none;
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
                right: 10px;
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
            /* Global Mobile Scroll Fix */
        @media (max-width: 1024px) {
            body, html {
                overflow-y: auto !important;
                height: auto !important;
            }
                        .visual-anchor, .hero-img-wrapper, .left-panel, .image-portal, .image-section {
                height: auto !important;
                min-height: 40vh;
                position: relative !important;
            }
                        .visual-anchor img, .hero-img-wrapper img, .left-panel img, .image-portal img, .image-section img {
                height: 100% !important;
                flex-grow: 1;
                object-fit: cover !important;
                min-height: 40vh !important;
            }
            .viewport, .container, .wrapper, .main-container {
                display: flex !important;
                flex-direction: column !important;
            }
                        .visual-anchor img, .hero-img-wrapper img, .left-panel img, .image-portal img, .image-section img {
                height: 100% !important;
                flex-grow: 1;
                object-fit: cover !important;
                min-height: 40vh !important;
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
    <div class="royal-pattern"></div>
    <div class="royal-border">
        <div class="r-border-top"></div>
        <div class="r-border-bottom"></div>
        <div class="r-border-left"></div>
        <div class="r-border-right"></div>
    </div>

    <div class="crown crown-1">👑</div>
    <div class="crown crown-2">👑</div>
    <div class="crown crown-3">👑</div>
    <div class="crown crown-4">👑</div>

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


