<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@300;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark: #121212;
            --accent: #d4af37;
            --gray: #f4f4f4;
        }

        body {
            margin: 0;
            background: var(--dark);
            color: white;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* Architectural Side Border */
        body::before {
            content: "";
            position: fixed;
            left: 0;
            top: 0;
            width: 80px;
            height: 100%;
            background: var(--accent);
            opacity: 0.8;
        }

        .main-frame {
            width: 85%;
            max-width: 1200px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 100px;
            align-items: center;
            padding-left: 100px;
        }

        .visual-anchor {
            position: relative;
        }

        .visual-anchor img {
            width: 100%;
            border-radius: 4px;
            filter: grayscale(100%) contrast(1.2);
            transition: 0.8s filter ease;
        }

        .visual-anchor:hover img {
            filter: grayscale(0%);
        }

        .visual-anchor::after {
            content: "EST. 2013";
            position: absolute;
            bottom: -30px;
            left: 0;
            font-size: 0.7rem;
            letter-spacing: 10px;
            color: var(--accent);
        }

        .content-block {
            border-left: 1px solid rgba(255,255,255,0.1);
            padding-left: 60px;
        }

        .id-header {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-bottom: 20px;
            color: var(--accent);
        }

        h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 5rem;
            margin: 0 0 30px 0;
            line-height: 1;
        }

        .manifesto-body {
            font-size: 1.1rem;
            line-height: 2;
            font-weight: 300;
            color: #ccc;
            margin-bottom: 40px;
        }

        .footer-details {
            display: flex;
            gap: 40px;
            margin-top: 60px;
        }

        .official-sign {
            border-top: 1px solid var(--accent);
            padding-top: 15px;
        }

        .name {
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .title {
            font-size: 0.7rem;
            color: #777;
            text-transform: uppercase;
            margin-top: 5px;
        }

        #audio-btn {
            position: fixed;
            top: 40px;
            right: 40px;
            background: none;
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 0.6rem;
            letter-spacing: 3px;
        }

        #audio-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        @media (max-width: 1000px) {
            .main-frame { grid-template-columns: 1fr; padding: 60px 20px 60px 100px; }
            h1 { font-size: 3.5rem; }
            body::before { width: 10px; }
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

    <div class="main-frame">
        <div class="visual-anchor">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ asset('storage/' . $mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
            @endif
        </div>

        <div class="content-block">
            <div class="id-header">Commemorative Status: Active</div>
            <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
            
            <div class="manifesto-body">
            <p>{!! $message->message_display !!}</p></br>
            <p>{!! $message->last_note ?? '' !!}</p>
            </div>

            <div class="footer-details">
                @auth
                    <div class="official-sign">
                        <div class="name">{{ auth()->user()->name }}</div>
                        <div class="title">{{ auth()->user()->title }}</div>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="audio-btn" data-idle-label="AUDIO" aria-label="Toggle music">AUDIO</button>
    </x-background-music>
</body>
</html>