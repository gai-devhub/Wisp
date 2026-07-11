<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,600;1,400&family=Montserrat:wght@200;400&display=swap" rel="stylesheet">
    <style>
        :root {
            --ivory: #fdfdfb;
            --gold-leaf: #af944d;
            --charcoal: #2c2c2c;
            --soft-sepia: #f4f1ea;
        }

        body {
            margin: 0;
            background-color: var(--soft-sepia);
            font-family: 'Montserrat', sans-serif;
            color: var(--charcoal);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Decorative Frame */
        .page-border {
            position: fixed;
            top: 25px;
            left: 25px;
            right: 25px;
            bottom: 25px;
            border: 1px solid rgba(175, 148, 77, 0.4);
            pointer-events: none;
            z-index: 100;
        }

        .wedding-canvas {
            background: var(--ivory);
            width: 85%;
            max-width: 1000px;
            padding: 80px 40px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.05);
            text-align: center;
            position: relative;
            animation: fadeIn 2s ease-in-out;
        }

        .monogram {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem;
            letter-spacing: 12px;
            color: var(--gold-leaf);
            margin-bottom: 40px;
            text-transform: uppercase;
        }

        .hero-layout {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
        }

        .portrait-arch {
            width: 320px;
            height: 450px;
            border-radius: 160px 160px 0 0; /* Elegant Arch */
            overflow: hidden;
            border: 2px solid var(--gold-leaf);
            padding: 10px;
            background: white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .portrait-arch img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 150px 150px 0 0;
            filter: sepia(15%);
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4rem;
            font-weight: 300;
            font-style: italic;
            margin: 0;
            color: var(--charcoal);
        }

        .vow-section {
            max-width: 700px;
            margin: 40px auto;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem;
            line-height: 1.9;
            color: #3a3a3a;
            font-style: italic;
        }

        .meta-data {
            margin-top: 40px;
            font-size: 0.75rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--gold-leaf);
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .formal-signature {
            margin-top: 30px;
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-style: italic;
        }

        #music-toggle {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.65rem;
            letter-spacing: 4px;
            color: var(--gold-leaf);
            z-index: 200;
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .wedding-canvas { padding: 50px 20px; width: 95%; }
            h1 { font-size: 2.5rem; }
            .portrait-arch { width: 240px; height: 340px; }
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

    <div class="page-border"></div>

    <div class="wedding-canvas">
        <div class="monogram">{{ $message->title }}</div>

        <div class="hero-layout">
            <div class="portrait-arch">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ asset('storage/' . $mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
                @endif
            </div>

            <div class="text-content">
                <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
                
                <div class="vow-section">
                    <p>{!! $message->message_display !!}</p></br>
                    <p>{!! $message->last_note ?? '' !!}</p>
                </div>

                <div class="meta-data">
                    {{ $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('M Y') : '' }} — Ghana
                </div>

                <div class="formal-signature">
                    @auth
                        <p>{{ auth()->user()->name }}</p>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="music-toggle" data-idle-label="PLAY SYMPHONY">PLAY SYMPHONY</button>
    </x-background-music>
</body>
</html>