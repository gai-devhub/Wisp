<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@1,400;1,700&family=Montserrat:wght@300;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --white: #ffffff;
            --soft-gold: #d4af37;
            --deep-red: #8b0000;
            --text-dark: #1a1a1a;
        }

        body {
            margin: 0;
            background-color: var(--white);
            font-family: 'Montserrat', sans-serif;
            color: var(--text-dark);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden; overflow-y: auto;
        }

        /* Ambient Valentine Glow */
        .ambient-glow {
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: radial-gradient(circle at center, rgba(212, 175, 55, 0.05) 0%, transparent 70%);
            z-index: -1;
        }

        .val-frame {
            width: 90%;
            max-width: 900px;
            padding: 60px;
            border: 1px solid #f0f0f0;
            background: var(--white);
            box-shadow: 0 40px 100px rgba(0,0,0,0.03);
            text-align: center;
            position: relative;
        }

        /* The Gold Ribbon Header */
        .val-frame::before {
            content: 'SPECIAL VALENTINE DEDICATION // 2026';
            position: absolute;
            top: 20px;
            left: 0;
            width: 100%;
            font-size: 0.6rem;
            letter-spacing: 5px;
            color: var(--soft-gold);
        }

        .portrait-stack {
            position: relative;
            width: 280px;
            height: 380px;
            margin: 40px auto;
        }

        .portrait-stack img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 10px solid white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transition: transform 0.5s ease;
        }

        .portrait-stack:hover img {
            transform: scale(1.02) rotate(-1deg);
        }

        h1 {
            font-family: 'Bodoni Moda', serif;
            font-size: 3.5rem;
            font-style: italic;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(to right, var(--text-dark), var(--soft-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .val-note {
            max-width: 600px;
            margin: 30px auto;
            font-family: 'Bodoni Moda', serif;
            font-size: 1.25rem;
            line-height: 2;
            color: #444;
            font-style: italic;
        }

        .official-sign-off {
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 30px;
        }

        .name-label {
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 1px;
        }

        .title-label {
            font-size: 0.7rem;
            color: var(--soft-gold);
            text-transform: uppercase;
            margin-top: 5px;
            letter-spacing: 2px;
        }

        #music-btn {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: var(--text-dark);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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
    <div class="ambient-glow"></div>

        <div class="val-frame">
        <div class="portrait-stack">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
            @endif
        </div>

        <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
        
        <div class="val-note">
            <p>{!! $message->message_display !!}</p></br>
            <p>{!! $message->last_note ?? '' !!}</p>
        </div>

        <div class="official-sign-off">
            @auth
                <p>{{ auth()->user()->name }}</p>
            @endauth
        </div>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="music-btn" data-idle-label="❤" aria-label="Toggle music">❤</button>
    </x-background-music>
</body>
</html>