<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;1,400&family=Montserrat:wght@300;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --aurora-white: #ffffff;
            --aurora-mist: #f4f5fc;
            --accent-blue: #7c7cf8;
            --text-gray: #4a4a4a;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            background: var(--aurora-mist);
            font-family: 'Montserrat', sans-serif;
            color: var(--text-gray);
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Animated Soft Background Glow */
        .mist-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 30% 30%, #fff 0%, transparent 60%),
                        radial-gradient(circle at 70% 70%, #eeeff9 0%, transparent 60%);
            z-index: 1;
            animation: moveMist 10s infinite alternate;
        }

        @keyframes moveMist {
            from { transform: scale(1); }
            to { transform: scale(1.1) rotate(1deg); }
        }

        .simple-card {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            width: 85%;
            max-width: 500px;
            padding: 60px 40px;
            text-align: center;
            border-radius: 2px; /* Sharp, modern look */
            box-shadow: 0 40px 100px rgba(124, 124, 248, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .princess-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 30px;
            overflow: hidden;
            border: 2px solid #fff;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .princess-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            font-weight: 300;
            margin: 0 0 10px 0;
            color: #13133f;
        }

        .tagline {
            font-size: 0.6rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--accent-blue);
            margin-bottom: 30px;
            display: block;
        }

        .message {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem;
            line-height: 1.8;
            font-style: italic;
            color: #555;
            margin-bottom: 40px;
        }

        .signature {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            color: #af944d; /* Gold touch */
        }

        /* Minimal Audio Control */
        #music-trigger {
            position: fixed;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            background: none;
            border: 1px solid var(--accent-blue);
            color: var(--accent-blue);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.6rem;
            letter-spacing: 3px;
            cursor: pointer;
            z-index: 100;
            transition: 0.3s;
        }

        #music-trigger:hover {
            background: var(--accent-blue);
            color: #fff;
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
            .content-section { padding: 15px 10px !important;  overflow-y: auto; max-height: 100vh;  justify-content: flex-start !important; padding-top: 40px !important; }
        }
    </style>
</head>
<body>

    <div class="mist-bg"></div>

    <div class="simple-card">
        <div class="princess-avatar">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
            @endif
        </div>

        <span class="tagline">{{ $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('M Y') : '' }} Years of Grace</span>
        <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
        
        <div class="message">
            <p>{!! $message->message_display !!}</p></br>
            <p>{!! $message->last_note ?? '' !!}</p>
        </div>

        @auth
            <div class="signature">{{ auth()->user()->name }}</div>
            <p style="font-size: 0.5rem; letter-spacing: 2px; color: #ccc; margin-top: 10px;">{{ $message->title ?? 'GAI TECH' }} // {{ auth()->user()->title }}</p>
        @endauth
    </div>

    <button type="button" id="music-trigger" data-idle-label="ENABLE AUDIO" >PLAY SYMPHONY</button>

    <x-background-music :mediaFiles="$mediaFiles">

    </x-background-music>
</body>
</html>

