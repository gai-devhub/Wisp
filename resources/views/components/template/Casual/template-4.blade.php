<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@1,600&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold-leaf: #d4af37;
            --off-white: #fafafa;
            --text-dark: #1a1a1a;
        }

        body {
            background-color: var(--off-white);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Decorative background elements */
        .bg-accent {
            position: fixed;
            top: 0;
            right: 0;
            width: 30vw;
            height: 100vh;
            background: #f0f0f0;
            z-index: -1;
        }

        .main-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 80px 20px;
            display: flex;
            flex-direction: column;
            gap: 60px;
        }

        /* Top Section: Overlapping Image & Title */
        .hero-section {
            position: relative;
            display: flex;
            align-items: flex-end;
        }

        .image-container {
            width: 60%;
            position: relative;
            z-index: 2;
        }

        .image-container img {
            width: 100%;
            display: block;
            border: 15px solid white;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .main-title {
            position: absolute;
            right: 0;
            bottom: -30px;
            z-index: 3;
            text-align: right;
        }

        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 5rem;
            margin: 0;
            line-height: 0.8;
            color: var(--gold-leaf);
            font-style: italic;
        }

        .birthday-text {
            font-size: 1.2rem;
            letter-spacing: 8px;
            text-transform: uppercase;
            margin-top: 10px;
            display: block;
            color: #888;
        }

        /* Bottom Section: The Letter */
        .letter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }

        .letter-content {
            padding-top: 40px;
            border-top: 1px solid var(--gold-leaf);
        }

        .letter-content p {
            line-height: 2;
            font-size: 1.05rem;
            margin-bottom: 25px;
            color: #333;
        }

        .highlight {
            color: var(--gold-leaf);
            font-weight: 600;
        }

        .signature {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-style: italic;
            margin-top: 40px;
        }

        /* Floating Controls */
        #music-btn {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: white;
            border: 1px solid #ddd;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 100;
            transition: 0.3s;
        }

        #music-btn:hover {
            border-color: var(--gold-leaf);
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            h1 { font-size: 3rem; }
            .hero-section { flex-direction: column; align-items: center; }
            .image-container { width: 90%; }
            .main-title { position: static; text-align: center; margin-top: 20px; }
            .letter-grid { grid-template-columns: 1fr; }
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
    <div class="bg-accent"></div>
    
    <div class="main-wrapper">
        <section class="hero-section">
            <div class="image-container">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ asset('storage/' . $mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
                @endif
            </div>
            <div class="main-title">
                <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
                <span class="birthday-text">{{ $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('M Y') : '' }}</span>
            </div>
        </section>

        <section class="letter-grid">
            <div class="empty-space">
            </div>
            <div class="letter-content">
                <p>{!! $message->message_display ?? nl2br(e($message->message ?? '')) !!}</p>
                <div class="signature">
                    {!! nl2br(e($message->last_note ?? '')) !!}
                </div>
            </div>
        </section>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="music-btn" data-idle-label="♪" aria-label="Toggle music">♪</button>
    </x-background-music>
</body>
</html>
