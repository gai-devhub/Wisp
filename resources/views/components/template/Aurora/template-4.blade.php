<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('img/logo-circle.png') }}" type="image/png">
    <title>{{ $message->title }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #a78bfa;
            --secondary: #c4b5fd;
            --bg: #2d1b69;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --new: #7c3aed;
            --text-dark: #1e1b4b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            position: relative;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden; overflow-y: auto;
            font-family: 'Montserrat', sans-serif;
            background: var(--bg);
            color: #fff;
            display: flex;
        }

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
            opacity: 0.6;
        }

        /* Cinematic Split Structure */
        .container {
            display: flex;
            width: 100%;
            height: 100%;
            z-index: 10;
        }

        /* Left Side: Visual Hero Section */
        .visual-hero {
            flex: 1.2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            background: linear-gradient(135deg, rgba(45, 27, 105, 0.9), rgba(124, 58, 237, 0.3));
            padding: 40px;
        }

        .profile-container {
            position: relative;
            width: 300px;
            height: 300px;
            margin-bottom: 0;
        }

        /* Offset Square Image Style */
        .hero-photo-wrapper {
            position: absolute;
            width: 100%;
            height: 100%;
            background: var(--primary);
            box-shadow: 20px 20px 0 rgba(196, 181, 253, 0.3);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: rotate(-3deg);
        }

        .profile-container:hover .hero-photo-wrapper {
            transform: rotate(0deg) scale(1.05);
            box-shadow: 0 0 40px rgba(167, 139, 250, 0.5);
        }

        .hero-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* dedicated circular music button positioning */
        .music-action-btn {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid var(--primary);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2rem;
            color: var(--primary);
            cursor: pointer;
            box-shadow: 0 0 20px rgba(167, 139, 250, 0.2);
            transition: all 0.3s ease;
            margin-top: 30px;
        }

        .music-action-btn:hover {
            transform: scale(1.1);
            background: var(--primary);
            color: #fff;
            box-shadow: 0 0 30px rgba(167, 139, 250, 0.5);
        }

        /* Right Side: Narrative Message Section */
        .message-panel {
            flex: 1;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: var(--text-dark);
            padding: 80px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
            position: relative;
        }

        h1#mainTitle {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            line-height: 1;
            margin: 0 0 10px 0;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 700;
        }

        .recipient-subtitle {
            font-size: 1.5rem;
            color: #666;
            margin-bottom: 50px;
            font-weight: 300;
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        .description {
            font-size: 1.25rem;
            line-height: 1.9;
            color: #333;
            max-width: 600px;
        }

        .description strong {
            display: block;
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            color: var(--new);
            margin-bottom: 20px;
        }

        .last-note {
            display: block;
            font-weight: 600;
            color: var(--new);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            margin-top: 40px;
        }

        @media (max-width: 992px) {
            body {
                flex-direction: column;
                overflow-y: auto;
                height: auto;
            }

            .container {
                flex-direction: column;
                height: auto;
            }

            .visual-hero {
                height: auto;
                padding: 60px 20px;
                flex: none;
            }

            .message-panel {
                padding: 40px 30px;
                flex: none;
            }

            h1#mainTitle {
                font-size: 2.5rem;
            }

            .profile-container {
                width: 220px;
                height: 220px;
            }
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
    <div id="particles-js"></div>
    <div class="overlay" id="overlay"></div>
    <div class="container">
        <div class="visual-hero">
            <div class="profile-container">
                <div class="hero-photo-wrapper">
                    @if(isset($mediaFiles) && $mediaFiles && $mediaFiles->recipient_image)
                        <img src="{{ s3_url($mediaFiles->recipient_image) }}" class="hero-photo" alt="Profile">
                    @else
                        <div style="width: 100%; height: 100%; background: var(--secondary); opacity: 0.5;"></div>
                    @endif
                </div>
            </div>

            <x-background-music :mediaFiles="$mediaFiles">
                <div id="toggleAudio" class="music-action-btn" title="Toggle Music" role="button" tabindex="0" aria-label="Toggle music">&#9835;</div>
            </x-background-music>
        </div>

        <div class="message-panel">
            <h1 id="mainTitle">{{ $message->title }}</h1>
            <div class="recipient-subtitle">{{ $message->recipient_special_name ?? $message->recipient_name }}</div>

            <div class="description">
                <strong>{{ $message->greeting }}</strong>
                <div style="margin-top: 10px;">{!! $message->message_display !!}</div>
                <span class="last-note">{{ $message->last_note }}</span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 60, density: { enable: true, value_area: 800 } },
                color: { value: "#c4b5fd" },
                shape: { type: "circle" },
                opacity: { value: 0.3, random: true },
                size: { value: 3, random: true },
                line_linked: { enable: false },
                move: { enable: true, speed: 1, direction: "top", out_mode: "out" }
            }
        });

        // Security logic maintained
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (e.key === 'PrintScreen') {
                document.getElementById('overlay').style.display = 'block';
                alert('Screenshots disabled.');
                e.preventDefault();
            }
        });
    </script>
</body>

</html>



