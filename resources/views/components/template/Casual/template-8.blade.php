<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@300;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0a192f;
            --gold-matte: #af944d;
            --slate: #64748b;
            --white: #ffffff;
        }

        body {
            margin: 0;
            background-color: #f8f9fa;
            font-family: 'Montserrat', sans-serif;
            color: var(--navy);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Subtle professional background pattern */
        .bg-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(var(--slate) 0.5px, transparent 0.5px);
            background-size: 30px 30px;
            opacity: 0.1;
            z-index: -1;
        }

        .official-container {
            background: var(--white);
            width: 90%;
            max-width: 850px;
            padding: 80px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        /* Gold accent bar at top */
        .official-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gold-matte);
        }

        .header-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 60px;
        }

        .org-detail {
            font-size: 0.65rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 600;
            color: var(--slate);
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .portrait-frame {
            border: 1px solid #eee;
            padding: 15px;
            background: #fff;
        }

        .portrait-frame img {
            width: 100%;
            filter: contrast(1.1);
            display: block;
        }

        h1 {
            font-family: 'Libre Baskerville', serif;
            font-size: 2.8rem;
            margin: 0 0 20px 0;
            line-height: 1.2;
            font-weight: 700;
        }

        .designation {
            font-family: 'Libre Baskerville', serif;
            font-style: italic;
            font-size: 1.1rem;
            color: var(--gold-matte);
            margin-bottom: 30px;
            display: block;
        }

        .inspirational-body {
            font-size: 1rem;
            line-height: 1.9;
            color: #4a5568;
            text-align: justify;
        }

        .formal-signature {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #f0f0f0;
        }

        .sig-name {
            font-family: 'Libre Baskerville', serif;
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }

        .sig-title {
            font-size: 0.75rem;
            color: var(--slate);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Floating Audio Control */
        #audio-trigger {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--navy);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 0.7rem;
            letter-spacing: 2px;
            cursor: pointer;
            border-radius: 2px;
            transition: 0.3s;
        }

        #audio-trigger:hover {
            background: var(--gold-matte);
        }

        @media (max-width: 768px) {
            .official-container { padding: 40px; }
            .hero-content { grid-template-columns: 1fr; }
            h1 { font-size: 2rem; }
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
        @media (max-width: 768px) {
            body, html {
                overflow-y: auto !important;
                height: auto !important;
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
    <div class="bg-pattern"></div>

    <div class="official-container">
        <div class="header-meta">
            <div class="org-detail">{{ $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('M Y') : '' }}</div>
            <div class="org-detail">{{ $message->title ?? 'Special Commemoration' }}</div>
        </div>

        <div class="hero-content">
            <div class="text-block">
                <span class="designation">In Recognition of Excellence & Friendship</span>
                <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
                
                <div class="inspirational-body">
                    <p>{!! $message->message_display !!}</p></br>
                    <p>{!! $message->last_note ?? '' !!}</p>
                </div>

                <div class="formal-signature">
                    @auth
                        <p class="sig-name">{{ auth()->user()->username }}</p>
                        <p class="sig-title">{{ auth()->user()->title }}</p>
                    @endauth
                </div>
            </div>

            <div class="portrait-frame">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
                @endif
            </div>
        </div>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="audio-trigger" data-idle-label="INITIATE AUDIO">INITIATE AUDIO</button>
    </x-background-music>
</body>
</html>


