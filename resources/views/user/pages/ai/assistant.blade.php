@extends('user.base-user')
@section('user-section', 'ai-assistant')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ai-assistant.css') }}?v={{ filemtime(public_path('css/ai-assistant.css')) }}">
    <style>
        /* Override ai-page resetting the background since the theme provides one */
        .ai-page {
            min-height: auto;
            height: calc(100vh - var(--header-offset) - var(--bottom-nav-height) - 40px);
            padding: 0;
            display: flex;
            align-items: stretch;
            justify-content: stretch;
            background: transparent !important;
        }
        
        .ai-shell {
            width: 100%;
            height: 100%;
            margin: 0;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            background: var(--bg);
        }

        /* Responsive adjustments for mobile */
        @media (max-width: 768px) {
            .ai-page {
                height: calc(100vh - var(--header-offset) - var(--bottom-nav-height) - 20px);
            }
            .ai-shell {
                border-radius: var(--radius-sm);
                grid-template-rows: 1fr auto; /* Without header, only 2 rows */
            }
            .ai-header {
                display: none; /* Hide header on mobile if redundant with the top nav */
            }
        }
    </style>
@endpush

@section('content')
    <main class="ai-page">
        <section class="ai-shell">
            <header class="ai-header">
                <div class="ai-brand">
                    <img src="{{ asset('img/logo.png') }}" alt="WISP logo">
                    <div>
                        <h1 style="color: var(--text)">WISP AI</h1>
                        <p style="color: var(--text-muted)">Write faster with clean suggestions</p>
                    </div>
                </div>
                <a href="{{ $backUrl }}" class="ai-back-link" style="color: var(--primary)">
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
@endsection

@push('scripts')
    <script>
    window.WISP_AI_ROUTE = "{{ route('ai.generate') }}";
    window.WISP_CREATE_ROUTE = "{{ route('user.create.page') }}";
    </script>
    <script src="{{ asset('js/ai-assistant.js') }}?v={{ filemtime(public_path('js/ai-assistant.js')) }}"></script>
@endpush
