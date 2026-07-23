<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #e64398;
            --secondary: #5e45ad;
            --accent: #ffd166;
            --dark: #1a1a2e;
            --light: #f8f5f9;
            --card-bg: rgba(255, 255, 255, 0.92);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #2d1b69 0%, #1e1e5e 100%);
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

        .background-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 15% 50%, rgba(94, 69, 173, 0.2) 0%, transparent 20%),
                radial-gradient(circle at 85% 30%, rgba(230, 67, 152, 0.2) 0%, transparent 20%),
                radial-gradient(circle at 50% 80%, rgba(255, 209, 102, 0.2) 0%, transparent 20%);
            z-index: -2;
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 15s infinite linear;
        }

        .shape:nth-child(1) {
            width: 150px;
            height: 150px;
            background: var(--primary);
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 100px;
            height: 100px;
            background: var(--secondary);
            top: 20%;
            right: 10%;
            animation-delay: -5s;
        }

        .shape:nth-child(3) {
            width: 200px;
            height: 200px;
            background: var(--accent);
            bottom: 15%;
            left: 15%;
            animation-delay: -10s;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(20px, 40px) rotate(90deg);
            }

            50% {
                transform: translate(0, 80px) rotate(180deg);
            }

            75% {
                transform: translate(-20px, 40px) rotate(270deg);
            }

            100% {
                transform: translate(0, 0) rotate(360deg);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            width: 100%;
            max-width: 600px;
        }

        .title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
            letter-spacing: 2px;
        }

        .title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            border-radius: 2px;
        }

        .datetime {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--accent);
            font-weight: 300;
            letter-spacing: 1px;
        }

        .countdown {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            background: rgba(0, 0, 0, 0.2);
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            -webkit-backdrop-filter: blur(5px);
            backdrop-filter: blur(5px);
        }

        .card {
            background: var(--card-bg);
            border-radius: 25px;
            padding: 40px;
            max-width: 400px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            position: relative;
            color: #333;
            margin-bottom: 40px;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
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
            border: 3px solid var(--primary);
            box-shadow: 0 0 20px rgba(230, 67, 152, 0.4);
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
            font-weight: 700;
        }

        .message {
            line-height: 1.8;
            font-size: 1.15rem;
        }

        .message p {
            margin-bottom: 20px;
        }

        .message strong {
            color: var(--primary);
            font-weight: 600;
        }

        .message i {
            color: var(--secondary);
            font-style: italic;
            font-weight: 500;
        }

        .message b {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 700;
            padding: 2px 0;
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
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            z-index: 100;
        }

        .play-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .play-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(230, 67, 152, 0.5);
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
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            padding: 20px;
        }

        .gem-decoration {
            position: absolute;
            font-size: 2rem;
            opacity: 0.7;
            z-index: -1;
        }

        .gem-1 {
            top: 10%;
            left: 10%;
            animation: sparkle 4s infinite;
        }

        .gem-2 {
            top: 20%;
            right: 15%;
            animation: sparkle 4s infinite 1s;
        }

        .gem-3 {
            bottom: 15%;
            right: 10%;
            animation: sparkle 4s infinite 2s;
        }

        .gem-4 {
            bottom: 25%;
            left: 15%;
            animation: sparkle 4s infinite 3s;
        }

        @keyframes sparkle {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.3);
                opacity: 1;
                filter: drop-shadow(0 0 8px var(--accent));
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
                bottom: 15px;
                right: 15px;
                padding: 10px 15px;
            }
        }

        @media (max-width: 480px) {
            .title {
                font-size: 2.5rem;
            }

            .audio-player {
                bottom: 10px;
                right: 10px;
                border-radius: 25px;
            }

            .datetime {
                font-size: 1.1rem;
            }

            .countdown {
                font-size: 1rem;
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
    <div class="background-pattern"></div>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="gem-decoration gem-1">💎</div>
    <div class="gem-decoration gem-2">💎</div>
    <div class="gem-decoration gem-3">💎</div>
    <div class="gem-decoration gem-4">💎</div>

    <div class="overlay" id="overlay">
        <div>Screenshots are not allowed on this page</div>
    </div>

    <div class="header">
        <h1 class="title">{{ $message->title }}</h1>
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
        // // Check if the page has been accessed before
        // const pageKey = 'pageAccessed';
        // const accessFlag = localStorage.getItem(pageKey);

        // if (accessFlag) {
        //     document.body.innerHTML = '<h1 style="text-align: center; margin-top: 50vh; transform: translateY(-50%); font-family: Montserrat, sans-serif; color: white;">Page Not Found</h1>';
        // } else {
        //     localStorage.setItem(pageKey, 'true');

        //     // Display current date and time
        //     function updateTime() {
        //         const now = new Date();
        //         document.getElementById("currentTime").innerText = now.toLocaleString();
        //     }

        //     // Countdown function
        //     let countdown = 300;
        //     const countdownElement = document.getElementById("countdownMessage");

        //     const countdownInterval = setInterval(() => {
        //         if (countdown > 0) {
        //             countdownElement.innerText = `This page will terminate in ${countdown} seconds Love.`;
        //             countdown--;
        //         } else {
        //             clearInterval(countdownInterval);
        //             document.body.innerHTML = '<h1 style="text-align: center; margin-top: 50vh; transform: translateY(-50%); font-family: Montserrat, sans-serif; color: white;">The page has terminated.</h1>';
        //         }
        //     }, 1000);

        //     setInterval(updateTime, 1000);
        // }

        // // Security features
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


