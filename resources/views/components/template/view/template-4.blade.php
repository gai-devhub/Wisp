<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B8B;
            --secondary: #6C63FF;
            --accent: #FFD166;
            --dark: #1A1A2E;
            --light: #FFFFFF;
            --gray: #F5F5F7;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 16px 20px 80px;
            position: relative;
            overflow-x: hidden;
        }

        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.5;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            opacity: 0.1;
            animation: float 15s infinite ease-in-out;
        }

        .circle:nth-child(1) {
            width: 250px;
            height: 250px;
            top: -50px;
            left: -50px;
        }

        .circle:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -30px;
            right: -30px;
            animation-delay: -5s;
        }

        .circle:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 40%;
            right: 20%;
            animation-delay: -10s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(20px, 40px) scale(1.05);
            }
        }

        .container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 16px;
        }

        .title {
            font-size: 3.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 15px;
            letter-spacing: -1px;
        }

        .subtitle {
            font-size: 1.2rem;
            color: #666;
            font-weight: 400;
            margin-bottom: 10px;
        }

        .datetime {
            font-size: 1rem;
            color: var(--secondary);
            font-weight: 500;
            margin-bottom: 5px;
        }

        .countdown {
            font-size: 0.9rem;
            color: #888;
            background: rgba(255, 255, 255, 0.7);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
        }

        .card {
            background: var(--light);
            border-radius: 20px;
            padding: 35px;
            max-width: 460px;
            width: 95%;
            max-height: 85vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 40px;
            position: relative;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 20px;
            border: 3px solid var(--light);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
        }

        .message {
            line-height: 1.7;
            font-size: 1.1rem;
            color: #444;
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
        }

        .message b {
            color: var(--dark);
            font-weight: 700;
            background: linear-gradient(120deg, rgba(255, 107, 139, 0.1) 0%, rgba(108, 99, 255, 0.1) 100%);
            padding: 2px 6px;
            border-radius: 4px;
        }

        .audio-player {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: var(--light);
            border-radius: 50px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            z-index: 100;
            transition: var(--transition);
        }

        .audio-player:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .play-btn:hover {
            transform: scale(1.1);
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 500;
            text-align: center;
            padding: 20px;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--light);
            color: #333;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateX(150%);
            transition: transform 0.3s ease;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification-icon {
            color: var(--primary);
            font-size: 1.2rem;
        }

        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            width: 0%;
            z-index: 1000;
            transition: width 0.3s ease;
        }

        @media (max-width: 768px) {
            .title {
                font-size: 2.8rem;
            }

            .card {
                padding: 25px;
            }

            .avatar {
                width: 60px;
                height: 60px;
            }

            .card-title {
                font-size: 1.5rem;
            }

            .audio-player {
                bottom: 20px;
                right: 20px;
            }
        }

        @media (max-width: 480px) {
            .title {
                font-size: 2.3rem;
            }

            .audio-player {
                bottom: 15px;
                right: 15px;
                padding: 10px 15px;
            }

            .message {
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
    </style>
</head>

<body>
    <div class="progress-bar" id="progressBar"></div>
    <div class="background">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="overlay" id="overlay">
        <div>Screenshots are not allowed on this page</div>
    </div>

    <div class="notification" id="notification">
        <span class="notification-icon">⚠️</span>
        <span>Screenshot detected!</span>
    </div>

    <div class="container">
        <div class="header">
            <h1 class="title">{{ $message->title }}</h1>
            <div class="subtitle">A Special Message For You</div>
            <div class="datetime" id="currentTime"></div>
            <div class="countdown" id="countdownMessage"></div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="avatar">
                    @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                        <img src="{{ s3_url($mediaFiles->recipient_image) }}"
                            alt="{{ $message->recipient_name }}">
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
    </div>

        <x-background-music :mediaFiles="$mediaFiles">
        <div class="audio-player" id="audioPlayer">
            <button type="button" class="play-btn" id="toggleAudio" aria-label="Toggle music">&#9835;</button>
</div>
    </x-background-music>

    <script>
        // Progress bar
        const progressBar = document.getElementById('progressBar');
        window.addEventListener('scroll', () => {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollPercent = (scrollTop / (documentHeight - windowHeight)) * 100;
            progressBar.style.width = scrollPercent + '%';
        });

        // // Security features
        document.addEventListener('keydown', function (e) {
            if (e.key === 'PrintScreen') {
                document.getElementById('overlay').style.display = 'flex';
                const notification = document.getElementById('notification');
                notification.classList.add('show');

                setTimeout(() => {
                    document.getElementById('overlay').style.display = 'none';
                    notification.classList.remove('show');
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