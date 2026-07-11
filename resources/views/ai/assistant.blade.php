<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WISP AI Assistant</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/ai-assistant.css') }}?v={{ filemtime(public_path('css/ai-assistant.css')) }}">
</head>
<body>
    <main class="ai-page">
        <section class="ai-shell">
            <header class="ai-header">
                <div class="ai-brand">
                    <img src="{{ asset('img/logo.png') }}" alt="WISP logo">
                    <div>
                        <h1>WISP AI</h1>
                        <p>Write faster with clean suggestions</p>
                    </div>
                </div>
                <a href="{{ $backUrl }}" class="ai-back-link">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </header>

            <section class="ai-messages" id="aiMessages">
                <article class="ai-msg assistant">
                    Hi! I'm here to help you write a message. What kind of message are you looking to create today?
                </article>
            </section>

            <form class="ai-input-wrap" id="aiForm">
                <textarea id="aiInput" class="ai-input" rows="1" placeholder="Type your request..."></textarea>
                <button type="submit" id="aiSendBtn" class="ai-send-btn" title="Send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </section>
    </main>

    <script>
    window.WISP_AI_ROUTE = "{{ route('ai.generate') }}";
    window.WISP_CREATE_ROUTE = "{{ route('user.create.page') }}";
    </script>
    <script src="{{ asset('js/ai-assistant.js') }}?v={{ filemtime(public_path('js/ai-assistant.js')) }}"></script>
</body>
</html>
