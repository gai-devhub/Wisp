<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --executive-navy: #0a192f;
            --gold-accent: #af944d;
            --slate-gray: #64748b;
            --pure-white: #ffffff;
        }

        body {
            margin: 0;
            background-color: #fcfcfc;
            font-family: 'Inter', sans-serif;
            color: var(--executive-navy);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Subtle grid background reflecting engineering roots */
        .bg-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(var(--slate-gray) 0.5px, transparent 0.5px);
            background-size: 40px 40px;
            opacity: 0.1;
            z-index: -1;
        }

        .document-frame {
            background: var(--pure-white);
            width: 90%;
            max-width: 900px;
            padding: 80px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        /* Top Accent Bar */
        .document-frame::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--gold-accent);
        }

        .meta-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--slate-gray);
            margin-bottom: 60px;
            font-weight: 600;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 50px;
            align-items: center;
        }

        .portrait-container {
            border: 1px solid #f0f0f0;
            padding: 12px;
            background: white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        }

        .portrait-container img {
            width: 100%;
            display: block;
            filter: grayscale(20%);
        }

        h1 {
            font-family: 'Libre Baskerville', serif;
            font-size: 2.8rem;
            margin: 0 0 15px 0;
            font-weight: 700;
            color: var(--executive-navy);
        }

        .honorific {
            font-family: 'Libre Baskerville', serif;
            font-style: italic;
            font-size: 1.1rem;
            color: var(--gold-accent);
            margin-bottom: 30px;
            display: block;
        }

        .inspirational-content {
            font-size: 1.05rem;
            line-height: 1.9;
            color: #334155;
            text-align: justify;
        }

        .executive-signature {
            margin-top: 50px;
            padding-top: 25px;
            border-top: 1px solid #f1f5f9;
        }

        .sig-name {
            font-family: 'Libre Baskerville', serif;
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }

        .sig-title {
            font-size: 0.75rem;
            color: var(--slate-gray);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 5px;
        }

        #audio-control {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--executive-navy);
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 0.65rem;
            letter-spacing: 2px;
            cursor: pointer;
            transition: 0.3s;
        }

        #audio-control:hover {
            background: var(--gold-accent);
        }

        @media (max-width: 800px) {
            .document-frame { padding: 40px; }
            .hero-grid { grid-template-columns: 1fr; }
            h1 { font-size: 2.2rem; }
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
    <div class="bg-grid"></div>

    <div class="document-frame">
        <div class="meta-header">
            <div>{{ $message->title ?? 'GAI Corp' }} // Commemorative Portfolio</div>
            <div>Ref: {{ $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('M Y') : '' }}</div>
        </div>

        <div class="hero-grid">
            <div class="portrait-container">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
                @endif
            </div>

            <div class="text-block">
                <span class="honorific">In Acknowledgement of a 13-Year Journey</span>
                <h1>{{ $message->recipient_special_name ?? $message->recipient_name ?? '' }}💕</h1>
                
                <div class="inspirational-content">
                    <p>{!! $message->message_display !!}</p></br>
                    <p>{!! $message->last_note ?? '' !!}</p>
                </div>

                <div class="executive-signature">
                    @auth
                        <p>{{ auth()->user()->name }}</p>
                        <p>{{ auth()->user()->title }}</p>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="audio-control" data-idle-label="ACTIVATE AUDITORY ATMOSPHERE" aria-label="Toggle music">ACTIVATE AUDITORY ATMOSPHERE</button>
    </x-background-music>
</body>
</html>



