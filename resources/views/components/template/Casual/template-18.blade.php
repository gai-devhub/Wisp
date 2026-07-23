<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@1,400;1,700&family=Outfit:wght@100;300;600&family=Playfair+Display:ital,wght@1,900&family=Pinyon+Script&display=swap" rel="stylesheet">
    <style>
        :root {
            --aurora-lavender: #eeeff9;
            --aurora-blue: #7c7cf8;
            --glass: rgba(255, 255, 255, 0.4);
            --gold: #c5a059;
            --text-navy: #13133f;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            background: #ffffff;
            color: var(--text-navy);
            font-family: 'Outfit', sans-serif;
            overflow: hidden;
        }

        /* Ambient Aurora Background */
        .ambient-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, #ffffff 0%, var(--aurora-lavender) 100%);
            z-index: 1;
        }

        /* Main Interface Layout */
        .viewport {
            position: relative;
            z-index: 10;
            height: 100vh;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            align-items: stretch;
        }

        /* Visual Side with Arch Effect */
        .visual-anchor {
            position: relative;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-portal {
            width: 100%;
            height: 100%;
            border-radius: 300px 300px 0 0; /* Elegant High-End Arch */
            overflow: hidden;
            border: 1px solid rgba(124, 124, 248, 0.2);
            box-shadow: 0 40px 100px rgba(0,0,0,0.05);
        }

        .image-portal img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 1.5s cubic-bezier(0.19, 1, 0.22, 1);
        }

        .visual-anchor:hover img {
            transform: scale(1.05);
        }

        /* Information Side */
        .content-hub {
            background: var(--glass);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            padding: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-left: 1px solid rgba(255, 255, 255, 0.2);
         overflow-y: auto !important; max-height: 100vh !important; justify-content: flex-start !important; padding-top: 40px !important; }

        .meta-tag {
            font-size: 0.65rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--aurora-blue);
            margin-bottom: 25px;
            font-weight: 600;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 4.5rem;
            line-height: 0.9;
            margin: 0 0 30px 0;
            font-style: italic;
        }

        .message-stream {
            font-family: 'Bodoni Moda', serif;
            font-size: 1.3rem;
            line-height: 1.9;
            color: #444;
            font-style: italic;
            margin-bottom: 50px;
        }

        .soul-signature {
            font-family: 'Pinyon Script', cursive;
            font-size: 3rem;
            color: var(--gold);
            margin-top: 10px;
        }

        /* THE FLOATING AUDIO CONTROL */
        #audio-toggle {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: var(--aurora-blue);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            box-shadow: 0 10px 30px rgba(124, 124, 248, 0.3);
            z-index: 1000;
            transition: 0.3s;
        }

        #audio-toggle:hover {
            transform: scale(1.1);
            background: var(--text-navy);
        }

        /* Navigation Button */
        .next-btn {
            align-self: flex-start;
            background: none;
            border: 1px solid var(--aurora-blue);
            color: var(--aurora-blue);
            padding: 12px 30px;
            font-family: 'Outfit';
            letter-spacing: 3px;
            text-transform: uppercase;
            font-size: 0.7rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .next-btn:hover {
            background: var(--aurora-blue);
            color: white;
        }

        @media (max-width: 1000px) {
            .viewport { grid-template-columns: 1fr; overflow-y: auto; }
            .visual-anchor { height: 50vh; }
            h1 { font-size: 3rem; }
            .content-hub { padding: 40px; }
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
                        .visual-anchor img, .hero-img-wrapper img, .left-panel img, .image-portal img, .image-section img {
                height: 100% !important;
                flex-grow: 1;
                object-fit: cover !important;
                min-height: 40vh !important;
            }
            .viewport, .container, .wrapper, .main-container {
                display: flex !important;
                flex-direction: column !important;
            }
                        .visual-anchor img, .hero-img-wrapper img, .left-panel img, .image-portal img, .image-section img {
                height: 100% !important;
                flex-grow: 1;
                object-fit: cover !important;
                min-height: 40vh !important;
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

    <div class="ambient-bg"></div>

    <div class="viewport">
        <div class="visual-anchor">
            <div class="image-portal">
                @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                    <img src="{{ s3_url($mediaFiles->recipient_image) }}" alt="{{ $message->recipient_name ?? '' }}">
                @endif
            </div>
        </div>

        <div class="content-hub">
            <div class="meta-tag">{{ $message->receiving_date ? \Carbon\Carbon::parse($message->receiving_date)->format('M Y') : '' }} // The Eternal Gem</div>
            <h1>{{ $message->title }}</h1>
            
            <div class="message-stream">
                <p>{!! $message->message_display !!}</p></br>
                <p>{!! $message->last_note ?? '' !!}</p>
            </div>


            <div style="margin-top: 40px;">
                @auth
                    <p>{{ auth()->user()->name }}</p>
                @endauth
            </div>
        </div>
    </div>

    <x-background-music :mediaFiles="$mediaFiles">
        <button type="button" id="audio-toggle" data-idle-label="♪" aria-label="Toggle music">♪</button>
    </x-background-music>
</body>
</html>




