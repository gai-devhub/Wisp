<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@1,400;1,700&family=Montserrat:wght@300;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --crimson: #7b0000;
            --obsidian: #0a0a0a;
            --gold: #d4af37;
            --white-silk: #fcfcfc;
        }

        body {
            margin: 0;
            background: var(--obsidian);
            font-family: 'Montserrat', sans-serif;
            color: var(--white-silk);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Subtle glowing red background light */
        .ambient-red {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, #3a0000 0%, transparent 80%);
            z-index: 1;
        }

        .folder-container {
            position: relative;
            z-index: 10;
            width: 90%;
            max-width: 800px;
            background: var(--white-silk);
            padding: 80px 60px;
            border-left: 15px solid var(--crimson);
            box-shadow: 25px 25px 50px rgba(0,0,0,0.5);
            color: #1a1a1a;
            animation: folderOpen 1.5s ease-out;
        }

        @keyframes folderOpen {
            from { transform: perspective(1000px) rotateY(-30deg); opacity: 0; }
            to { transform: perspective(1000px) rotateY(0deg); opacity: 1; }
        }

        .official-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 50px;
        }

        .ref-no {
            font-size: 0.65rem;
            letter-spacing: 3px;
            color: var(--crimson);
            font-weight: 700;
        }

        .content-wrap {
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .portrait-inset {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 4px solid var(--crimson);
            margin: 0 auto;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(123, 0, 0, 0.2);
        }

        .portrait-inset img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .text-section {
            text-align: center;
         overflow-y: auto; max-height: 100vh;  justify-content: flex-start !important; padding-top: 40px !important; }

        .title-label {
            font-family: 'Cinzel', serif;
            font-size: 0.8rem;
            letter-spacing: 8px;
            color: var(--gold);
            margin-bottom: 15px;
            display: block;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-style: italic;
            margin: 0 0 30px 0;
            color: var(--crimson);
        }

        .manifesto-body {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            line-height: 2;
            color: #333;
            font-style: italic;
            max-width: 600px;
            margin: 0 auto;
        }

        .highlight {
            color: var(--crimson);
            font-weight: 700;
            border-bottom: 1px solid var(--gold);
        }

        .footer-sig {
            margin-top: 60px;
            display: flex;
            justify-content: center;
            gap: 50px;
            border-top: 1px solid #eee;
            padding-top: 30px;
        }

        .sig-item {
            text-align: center;
        }

        .sig-name {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            font-size: 1rem;
            margin: 0;
        }

        .sig-title {
            font-size: 0.65rem;
            letter-spacing: 2px;
            color: #777;
            text-transform: uppercase;
            margin-top: 5px;
        }

        #music-btn {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: var(--crimson);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-family: 'Cinzel', serif;
            font-size: 0.7rem;
            letter-spacing: 2px;
            cursor: pointer;
            z-index: 100;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        @media (max-width: 600px) {
            .folder-container { padding: 40px 20px; }
            h1 { font-size: 2.2rem; }
            .footer-sig { flex-direction: column; gap: 20px; }
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
            /* Global Mobile Scroll Fix */
        @media (max-width: 1024px) {
            body, html {
                overflow-y: auto !important;
                height: auto !important;
            }
                        .visual-anchor, .hero-img-wrapper, .left-panel, .image-portal, .image-section {
                height: auto !important;
                min-height: 40vh;
                position: relative !important;
            }
            .viewport, .container, .wrapper, .main-container {
                display: flex !important;
                flex-direction: column !important;
            }
            .viewport, .container, .wrapper, .main-container {
                height: auto !important;
                min-height: 100vh;
                overflow: visible !important;
            }
            .content-hub, .message-panel, .message-box, .letter-box, .content-area, .text-panel, .message-wrapper, .message-container, .content-section, .text-section, .message-content, .main-content, .card {
                max-height: none !important;
                overflow-y: visible !important;
                height: auto !important;
            }
        }
    </style>
</head>
<body>

    <div class="ambient-red"></div>

    <div class="folder-container">
        <div class="official-header">
            <div class="ref-no">VAL-REF: {{ $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('M Y') : '' }}</div>
            <div class="ref-no">{{ $message->title ?? 'GAI CORP' }} // PERSONAL DEDICATION</div>
        </div>

        <div class="content-wrap">
            <div class="portrait-inset">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
                @endif
            </div>

            <div class="text-section">
                <span class="title-label">{{ $message->title ?? 'Executive Commemoration' }}</span>
                <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
                
                <div class="manifesto-body">
                    <p>{!! $message->message_display !!}</p></br>
                    <p>{!! $message->last_note ?? '' !!}</p>
                </div>
            </div>
        </div>

        <div class="footer-sig">
            @auth
                <div class="sig-item">
                    <p>{{ auth()->user()->name }}</p>
                    <p>{{ auth()->user()->title }}</p>
                </div>
            @endauth
        </div>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="music-btn" data-idle-label="AUDIO" aria-label="Toggle music">AUDIO</button>
    </x-background-music>
</body>
</html>



