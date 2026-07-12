<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@1,400;1,700&family=Montserrat:wght@300;600&family=Pinyon+Script&display=swap" rel="stylesheet">
    <style>
        :root {
            --deep-red: #5a0001;
            --velvet: #8b0000;
            --gold: #d4af37;
            --off-white: #fffafa;
        }

        body {
            margin: 0;
            background: linear-gradient(135deg, var(--deep-red) 0%, #1a0000 100%);
            font-family: 'Montserrat', sans-serif;
            color: var(--off-white);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden; overflow-y: auto;
        }

        /* Subtle floating heart particles */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .main-card {
            position: relative;
            z-index: 10;
            width: 90%;
            max-width: 1000px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            box-shadow: 0 50px 100px rgba(0,0,0,0.5);
            overflow: hidden;
        }

        .image-side {
            position: relative;
            overflow: hidden;
        }

        .image-side img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: contrast(1.1) brightness(0.8);
            transition: 0.5s;
        }

        .main-card:hover .image-side img {
            filter: contrast(1.1) brightness(1);
            transform: scale(1.05);
        }

        .content-side {
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(to right, rgba(0,0,0,0.3), transparent);
        }

        .salutation {
            font-family: 'Pinyon Script', cursive;
            font-size: 3rem;
            color: var(--gold);
            margin-bottom: 0;
        }

        h1 {
            font-family: 'Bodoni Moda', serif;
            font-size: 3.5rem;
            font-style: italic;
            margin: 0 0 30px 0;
            line-height: 1;
            color: white;
        }

        .message-body {
            font-family: 'Bodoni Moda', serif;
            font-size: 1.2rem;
            line-height: 1.8;
            color: rgba(255, 250, 250, 0.9);
            font-style: italic;
        }

        .highlight {
            color: var(--gold);
            font-weight: 700;
        }

        .executive-signature {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(212, 175, 55, 0.3);
        }

        .sig-name {
            font-family: 'Pinyon Script', cursive;
            font-size: 2.5rem;
            color: var(--gold);
            margin: 0;
        }

        .sig-title {
            font-size: 0.7rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
        }

        #music-btn {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: var(--gold);
            color: var(--deep-red);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 100;
            font-size: 1.2rem;
        }

        @media (max-width: 850px) {
            .main-card { grid-template-columns: 1fr; height: 90vh; overflow-y: auto; }
            .image-side { height: 40vh; }
            .content-side { padding: 30px; }
            h1 { font-size: 2.5rem; }
        }
    
        /* Universal Mobile Responsiveness */
        @media (max-width: 768px) {
            h1 { font-size: clamp(2rem, 8vw, 3rem) !important; margin: 10px 0 !important; }
            h2, .greeting-text { font-size: clamp(1.5rem, 6vw, 2.2rem) !important; }
            p, .message-body, .description { font-size: 1rem !important; line-height: 1.5 !important; }
            body { padding: 10px !important; min-height: 100vh !important; overflow-y: auto !important; }
            .container { flex-direction: column !important; padding: 0 !important; }
            .content-section, .visual-section { padding: 20px !important; width: 100% !important; border: none !important; }
            .card { width: 100% !important; padding: 15px !important; max-height: none !important; margin-top: 10px !important; }
            .hero-img-wrapper, .logo { width: min(150px, 40vw) !important; height: min(150px, 40vw) !important; }
            #audioPlayer { bottom: 15px !important; right: 15px !important; padding: 8px !important; }
        }

        @media (max-width: 480px) {
            h1 { font-size: clamp(1.6rem, 6vw, 2rem) !important; }
            .card { padding: 10px !important; border-radius: 16px !important; }
            .content-section { padding: 15px 10px !important; }
        }
    </style>
</head>
<body>

    <div id="particles-js"></div>

    <div class="main-card">
        <div class="image-side">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
            @endif
        </div>
        
        <div class="content-side">
            <div class="salutation">{{ $message->greeting ?? 'Hello' }}</div>
            <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
            
            <div class="message-body">
                <p>{!! $message->message_display !!}</p></br>
                <p>{!! $message->last_note ?? '' !!}</p>
            </div>

            <div class="executive-signature">
                @auth
                    <p>{{ auth()->user()->name }}</p>
                    <p>{{ auth()->user()->title }}</p>
                @endauth
            </div>
        </div>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="music-btn" data-idle-label="❤" aria-label="Toggle music">❤</button>
    </x-background-music>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 30 },
                color: { value: "#d4af37" },
                shape: { type: "circle" },
                opacity: { value: 0.2 },
                size: { value: 3, random: true },
                move: { enable: true, speed: 1, direction: "bottom" }
            }
        });
        }
    </script>
</body>
</html>