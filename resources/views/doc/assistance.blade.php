@extends('user.base-user')

@section('user-section', 'help-support')

@section('content')
<div class="content-section active" id="help-support">
    <style>
        .hs-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px 0 40px;
        }

        .hs-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 20px;
            transition: color 0.2s ease;
        }

        .hs-back-btn:hover {
            color: var(--primary);
        }

        .feedback-wrapper {
            max-width: 100%;
        }

        .feedback-header-card {
            background: linear-gradient(135deg, #ffffff 0%, #fffdfa 50%, #faf5ee 100%) !important;
            border: 1px solid #f1e5d5 !important;
            border-radius: 20px !important;
            padding: 32px !important;
            color: #1e293b !important;
            margin-bottom: 28px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .feedback-header-icon {
            font-size: 2rem !important;
            color: #E8674A !important;
            width: 58px !important;
            height: 58px !important;
            border-radius: 16px !important;
            background: #f8fafc !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        .feedback-header-text h1 {
            font-size: 1.6rem !important;
            font-weight: 800 !important;
            margin: 0 0 6px 0 !important;
            color: #1e293b !important;
        }

        .feedback-header-text p {
            font-size: 0.95rem !important;
            color: #64748b !important;
            margin: 0 !important;
            line-height: 1.5 !important;
        }

        .feedback-form-card {
            background: #ffffff !important;
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04) !important;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .feedback-form-card .card-body {
            padding: 32px;
        }

        .feedback-section-title {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted, #9ca3af);
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border, #f0f0f0);
        }

        .feedback-field {
            margin-bottom: 22px;
        }

        .feedback-field label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 7px;
        }

        .feedback-field label .required-star {
            color: #ef4444;
            margin-left: 3px;
        }

        .feedback-field input[type="text"],
        .feedback-field input[type="email"],
        .feedback-field textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            color: var(--text);
            font-size: 0.92rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            box-sizing: border-box;
        }

        .feedback-field input[type="text"]:focus,
        .feedback-field input[type="email"]:focus,
        .feedback-field textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .feedback-field textarea {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .feedback-submit-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 50%;
            margin: 0 auto;
            padding: 14px 28px !important;
            background: linear-gradient(135deg, #E8674A, #F28C76) !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 12px !important;
            font-size: 0.98rem !important;
            font-weight: 700 !important;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(232, 103, 74, 0.35) !important;
            transition: all 0.2s ease !important;
        }

        .feedback-submit-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(232, 103, 74, 0.45) !important;
        }
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
        }

        .feedback-submit-btn:active {
            transform: translateY(0);
        }

        .quick-links-card {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--border, #e2e8f0);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.05);
        }

        .quick-links-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quick-links-title i {
            color: var(--primary);
        }

        .quick-links-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .quick-link-item {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity 0.2s ease;
        }

        .quick-link-item:hover {
            text-decoration: underline;
            opacity: 0.85;
        }

        @media (max-width: 600px) {
            .feedback-header-card {
                flex-direction: column;
                gap: 12px;
                padding: 24px 20px;
            }

            .feedback-form-card .card-body {
                padding: 20px;
            }
        }
    </style>

    <div class="hs-container">
        <a href="{{ route('user.help-support.page') }}" class="hs-back-btn">
            <i class="fas fa-arrow-left"></i> Back to Help Center
        </a>

        <div class="feedback-wrapper">
            <div class="feedback-header-card">
                <div class="feedback-header-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="feedback-header-text">
                    <h1>Help &amp; Assistance</h1>
                    <p>Submit your questions, issues, or direct concerns to Wisp support team. We usually respond within 24 hours.</p>
                </div>
            </div>

            <div class="feedback-form-card">
                <div class="card-body">
                    @if(session('success'))
                        <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 14px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 500;">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div style="background: #fdf2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 14px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 500;">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('notifications.compose') }}" method="POST">
                        @csrf
                        <p class="feedback-section-title">Your Information</p>

                        <div class="feedback-field">
                            <label for="fb_username">Username</label>
                            <input type="text" id="fb_username" value="{{ auth()->user()->username ?? auth()->user()->name ?? '' }}" readonly style="background: var(--bg-subtle); color: var(--text-muted); cursor: not-allowed; border-color: var(--border);">
                        </div>

                        <div class="feedback-field">
                            <label for="fb_email">Email Address</label>
                            <input type="email" id="fb_email" value="{{ auth()->user()->email ?? '' }}" readonly style="background: var(--bg-subtle); color: var(--text-muted); cursor: not-allowed; border-color: var(--border);">
                        </div>

                        <hr class="feedback-divider">
                        <p class="feedback-section-title">Your Concern</p>

                        <div class="feedback-field">
                            <label for="concern_message">How can we help you? <span class="required-star">*</span></label>
                            <textarea id="concern_message" name="message" placeholder="Describe your concern or issue in detail..." required></textarea>
                        </div>

                        <button type="submit" class="feedback-submit-btn">
                            <i class="fas fa-paper-plane"></i>
                            Send Message to Support
                        </button>
                    </form>
                </div>
            </div>

            {{-- Links to Terms and Privacy --}}
            <div class="quick-links-card">
                <div class="quick-links-title">
                    <i class="fas fa-link"></i> Quick Legal Resources
                </div>
                <div class="quick-links-row">
                    <a href="{{ route('help.terms') }}" target="_blank" class="quick-link-item">
                        <i class="fas fa-file-contract"></i> Terms of Service <i class="fas fa-external-link-alt" style="font-size: 0.75em;"></i>
                    </a>
                    <a href="{{ route('help.policy') }}" target="_blank" class="quick-link-item">
                        <i class="fas fa-shield-alt"></i> Privacy Policy <i class="fas fa-external-link-alt" style="font-size: 0.75em;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
