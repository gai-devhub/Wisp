<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Pinyon+Script&family=Playfair+Display:ital,wght@0,400;1,400&family=Inter:wght@300&display=swap" rel="stylesheet">
    <style>
        :root {
            --deep-burgundy: #1a0a0a;
            --soft-rose: #d4a5a5;
            --gold-leaf: #c5a059;
            --off-white: #fdfaf6;
        }

        body {
            margin: 0;
            background-color: var(--deep-burgundy);
            color: var(--off-white);
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden; overflow-y: auto;
        }

        /* Soft glowing ambient light */
        .ambient-light {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80vw;
            height: 80vh;
            background: radial-gradient(circle, rgba(212, 165, 165, 0.05) 0%, transparent 70%);
            z-index: 1;
        }

        .romance-container {
            position: relative;
            z-index: 10;
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            width: 90%;
            max-width: 1000px;
            gap: 50px;
            align-items: center;
        }

        .image-frame {
            position: relative;
            padding: 10px;
            border: 1px solid rgba(212, 165, 165, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .image-frame img {
            width: 100%;
            display: block;
            filter: contrast(1.1) brightness(0.9);
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }

        .image-frame::after {
            content: "";
            position: absolute;
            top: -20px;
            right: -20px;
            width: 100px;
            height: 100px;
            border-top: 2px solid var(--gold-leaf);
            border-right: 2px solid var(--gold-leaf);
        }

        .poetry-section {
            padding-left: 20px;
        }

        .salutation {
            font-family: 'Pinyon Script', cursive;
            font-size: 3rem;
            color: var(--soft-rose);
            margin-bottom: 10px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-style: italic;
            font-weight: 400;
            margin: 0 0 30px 0;
            line-height: 1.1;
        }

        .love-letter {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            line-height: 2;
            color: rgba(253, 250, 246, 0.85);
            font-style: italic;
        }

        .highlight {
            color: var(--gold-leaf);
            font-weight: 600;
        }

        .signature {
            margin-top: 40px;
            font-family: 'Pinyon Script', cursive;
            font-size: 2.5rem;
            color: var(--soft-rose);
        }

        /* Play control under portrait */
        #audio-toggle {
            background: none;
            border: 1px solid var(--soft-rose);
            color: var(--soft-rose);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        #audio-toggle:hover {
            background: var(--soft-rose);
            color: var(--deep-burgundy);
        }

        @media (max-width: 800px) {
            .romance-container { grid-template-columns: 1fr; text-align: center; overflow-y: auto; padding: 40px 0; }
            .poetry-section { padding: 0; }
            .image-frame { max-width: 300px; margin: 0 auto; }
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

    <div class="ambient-light"></div>

        <div class="romance-container">
        <div class="image-frame">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ asset('storage/' . $mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
            @endif
            <x-background-music :mediaFiles="$mediaFiles">
                <button type="button" id="audio-toggle" data-idle-label="♪" aria-label="Toggle music">♪</button>
            </x-background-music>
        </div>

        <div class="poetry-section">
            <div class="salutation">{{ $message->greeting ?? 'Hello' }}</div>
            <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
            
            <div class="love-letter">
            <p>{!! $message->message_display !!}</p>
            <p>{!! $message->last_note ?? '' !!}</p>
            </div>

            <div class="signature">
                @auth
                    <p>{{ auth()->user()->name }}</p>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>