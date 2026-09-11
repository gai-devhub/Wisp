@extends('user.base-user')

@section('user-section', 'help-support')

@section('content')
<div class="content-section active" id="help-support">
    <style>
        .hs-dashboard {
            max-width: 840px;
            margin: 0 auto;
            padding: 10px 0 40px;
        }

        .hs-header-card {
            background: linear-gradient(135deg, #ffffff 0%, #fffdfa 50%, #faf5ee 100%) !important;
            border: 1px solid #f1e5d5 !important;
            border-radius: 20px !important;
            padding: 36px 32px !important;
            color: #1e293b !important;
            margin-bottom: 28px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
            text-align: center;
        }

        .hs-header-card h1 {
            font-size: 1.75rem !important;
            font-weight: 800 !important;
            margin-bottom: 8px !important;
            color: #1e293b !important;
            letter-spacing: -0.01em;
        }

        .hs-header-card h1 span {
            color: #E8674A;
        }

        .hs-header-card p {
            font-size: 0.96rem !important;
            color: #64748b !important;
            max-width: 560px;
            margin: 0 auto;
            line-height: 1.5;
        }

        .hs-options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        @media (max-width: 600px) {
            .hs-options-grid {
                grid-template-columns: 1fr;
            }
        }

        .hs-option-card {
            background: #ffffff !important;
            border: none !important;
            border-radius: 20px !important;
            padding: 32px 24px !important;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
        }

        .hs-option-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 14px 35px rgba(232, 103, 74, 0.12) !important;
        }

        .hs-option-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: #f8fafc;
            color: #E8674A;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin: 0 auto 18px auto;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .hs-option-card:hover .hs-option-icon {
            background: #E8674A;
            color: #fff;
        }

        .hs-option-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1e293b;
        }

        .hs-option-desc {
            font-size: 0.88rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .hs-option-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #E8674A;
        }
    </style>

    <div class="hs-dashboard">
        <div class="hs-header-card">
            <h1>Help &amp; Support <span>Center</span></h1>
            <p>How can we assist you today? Select one of the options below to get started.</p>
        </div>

        <div class="hs-options-grid">
            <!-- Option 1: Help & Assistance -->
            <a href="{{ route('doc.assistance') }}" class="hs-option-card">
                <div>
                    <div class="hs-option-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="hs-option-title">Help &amp; Assistance</div>
                    <div class="hs-option-desc">Send us your concerns, look up routes to privacy policies, and read our terms.</div>
                </div>
                <div class="hs-option-action">
                    Get Assistance <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <!-- Option 2: Support & Wisp Growth -->
            <a href="{{ route('doc.growth') }}" class="hs-option-card">
                <div>
                    <div class="hs-option-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="hs-option-title">Support &amp; Wisp Growth</div>
                    <div class="hs-option-desc">Submit your detailed ideas, suggestions, and feedback directly using Google Forms to support us.</div>
                </div>
                <div class="hs-option-action">
                    Open Feedback <i class="fas fa-arrow-right"></i>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
