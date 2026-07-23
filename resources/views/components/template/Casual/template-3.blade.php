<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400;700&family=Playfair+Display:ital@1&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gold: #d4af37;
            --soft-cream: #fcfcfc;
            --deep-navy: #1a1a2e;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--soft-cream);
        }

        /* Full Screen Background */
        .bg-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            display: flex;
        }

        .bg-left {
            flex: 1;
            background: linear-gradient(rgba(255,255,255,0.7), rgba(255,255,255,0.7)), url('{{ isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image ? s3_url($mediaFiles->recipient_image) : asset("img/logo.png") }}');
            background-size: cover;
            background-position: center;
            filter: grayscale(40%) blur(2px);
        }

        .bg-right {
            flex: 1;
            background: var(--soft-cream);
        }

        /* Interactive Content Layer */
        .main-container {
            position: relative;
            z-index: 10;
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        .image-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .floating-portrait {
            width: 80%;
            max-width: 400px;
            border-radius: 200px 200px 0 0; /* Arch shape */
            border: 8px solid white;
            box-shadow: 0 30px 60px rgba(0,0,0,0.15);
            transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .floating-portrait:hover {
            transform: translateY(-20px) scale(1.02);
        }

        .text-section {
            flex: 1;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(15px);
            border-left: 1px solid rgba(0,0,0,0.05);
         overflow-y: auto; max-height: 100vh; }

        .title-reveal {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            color: var(--deep-navy);
            margin: 0;
            line-height: 1;
            font-style: italic;
        }

        .subtitle {
            text-transform: uppercase;
            letter-spacing: 5px;
            font-size: 0.8rem;
            color: var(--primary-gold);
            margin-bottom: 20px;
            font-weight: 700;
        }

        .letter-body {
            max-width: 500px;
            font-size: 1.05rem;
            line-height: 1.8;
            color: #444;
            margin-top: 30px;
        }

        .sig-container {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        /* Floating Audio Icon */
        #music-toggle {
            position: fixed;
            top: 40px;
            right: 40px;
            z-index: 100;
            cursor: pointer;
            background: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        #music-toggle:hover {
            background: var(--primary-gold);
            color: white;
        }

        @media (max-width: 900px) {
            .main-container { flex-direction: column; overflow-y: auto; }
            .bg-wrapper { display: none; }
            .image-section { min-height: 50vh; }
            .text-section { padding: 30px; background: white; }
            .title-reveal { font-size: 2.5rem; }
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
            .content-section { padding: 15px 10px !important;  overflow-y: auto; max-height: 100vh; }
        }
    </style>
</head>
<body>

    <div class="bg-wrapper">
        <div class="bg-left"></div>
        <div class="bg-right"></div>
    </div>

    <div class="main-container">
        <section class="image-section">
            @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
            <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name }}" class="floating-portrait">
            @endif
        </section>

        <section class="text-section">
            <div class="subtitle">A Celebration of You</div>
            <h1 class="title-reveal">{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
            
            <div class="letter-body">
            <p>{!! $message->message_display !!}</p>
                <div class="sig-container">
                    {!! nl2br(e($message->last_note ?? '')) !!}
                </div>
            </div>
        </section>
    </div>
    <x-background-music :mediaFiles="$mediaFiles">
        <div id="music-toggle" role="button" tabindex="0" aria-label="Toggle music">
            <span id="icon-state">♫</span>
        </div>
    </x-background-music>
</body>
</html>
