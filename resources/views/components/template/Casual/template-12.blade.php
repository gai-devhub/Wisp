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

        /* Decorative Border */
        .page-border {
            position: fixed;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 1px solid rgba(175, 148, 77, 0.3);
            pointer-events: none;
            z-index: 100;
        }

        .wedding-canvas {
            background: var(--ivory);
            width: 90%;
            max-width: 950px;
            padding: 100px 60px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.05);
            text-align: center;
            position: relative;
        }

        .monogram {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            letter-spacing: 10px;
            color: var(--gold-leaf);
            margin-bottom: 50px;
            text-transform: uppercase;
        }

        .hero-image {
            width: 300px;
            height: 400px;
            margin: 0 auto 60px auto;
            border: 1px solid #eee;
            padding: 10px;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        }

        .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: sepia(10%);
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            font-weight: 300;
            font-style: italic;
            margin: 0 0 20px 0;
            color: var(--charcoal);
        }

        .vow-text {
            max-width: 650px;
            margin: 0 auto;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.3rem;
            line-height: 2;
            color: #4a4a4a;
        }

        .date-location {
            margin-top: 50px;
            font-size: 0.7rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--gold-leaf);
        }

        .formal-signature {
            margin-top: 60px;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
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
            font-size: 0.6rem;
            letter-spacing: 3px;
            color: var(--gold-leaf);
            z-index: 200;
        }

        @media (max-width: 600px) {
            .wedding-canvas { padding: 60px 20px; }
            h1 { font-size: 2.2rem; }
            .hero-image { width: 220px; height: 300px; }
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
            <div class="monogram">M & K</div>

        <div class="hero-image">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                <img src="{{ asset('storage/' . $mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
            @endif
        </div>

        <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
        
        <div class="vow-text">
            <p>{!! $message->message_display !!}</p></br>
            <p>{!! $message->last_note ?? '' !!}</p>
        </div>

        <div class="date-location">
            {{ $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('M Y') : '' }} — Ghana
        </div>

        <div class="formal-signature">
            @auth
                <p>{{ auth()->user()->name }}</p>
            @endauth
        </div>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="music-toggle" data-idle-label="BEGIN SYMPHONY">BEGIN SYMPHONY</button>
    </x-background-music>
</body>
</html>