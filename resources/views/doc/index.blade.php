<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Help Center — WISP</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-light: rgba(99, 102, 241, 0.08);
            --secondary: #a855f7;
            --accent: #ec4899;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg: #ffffff;
            --bg-subtle: #f8fafc;
            --bg-glass: rgba(255, 255, 255, 0.8);
            --radius: 12px;
            --radius-lg: 20px;
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            --transition: 0.2s ease;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --text: #f8fafc;
                --text-muted: #94a3b8;
                --border: #334155;
                --bg: #0f172a;
                --bg-subtle: #1e293b;
                --bg-glass: rgba(15, 23, 42, 0.8);
                --primary-light: rgba(99, 102, 241, 0.15);
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            background-image:
                radial-gradient(circle at 0% 30%, rgba(99, 102, 241, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 100% 70%, rgba(236, 72, 153, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 20% 90%, rgba(168, 85, 247, 0.04) 0%, transparent 45%);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.6;
            -webkit-tap-highlight-color: transparent;
            display: flex;
            flex-direction: column;
        }

        /* Top Header */
        header.top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background: var(--bg-glass);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .logo img {
            height: 36px;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .back-home:hover {
            color: var(--primary);
        }

        /* Dashboard Layout */
        .dashboard {
            display: flex;
            flex: 1;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            padding: 40px 20px;
            border-right: 1px solid var(--border);
            position: sticky;
            top: 77px;
            height: calc(100vh - 77px);
            overflow-y: auto;
        }

        .nav-group {
            margin-bottom: 30px;
        }

        .nav-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 12px;
            padding-left: 12px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 4px;
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .nav-item:hover {
            background: var(--bg-subtle);
        }

        .nav-item.active {
            background: var(--primary-light);
            color: var(--primary);
        }

        .nav-item.active i {
            color: var(--primary);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 40px 60px;
            max-width: 900px;
        }

        .tab-pane {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-pane.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .content-header h1 {
            font-size: 2.5rem;
            color: var(--text);
            margin-bottom: 8px;
        }

        .content-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn:hover {
            background: var(--primary-hover);
        }

        /* Typography inside content */
        .tab-pane h2 {
            font-size: 1.5rem;
            margin-top: 30px;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .tab-pane p {
            margin-bottom: 16px;
            font-size: 1.05rem;
            color: var(--text);
        }

        .tab-pane ul {
            margin-bottom: 16px;
            padding-left: 20px;
        }

        .tab-pane li {
            margin-bottom: 8px;
        }

        /* FAQ Styling */
        .faq-item {
            background: var(--bg-subtle);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            transition: var(--transition);
        }
        
        .faq-item:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .faq-question {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 10px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .faq-question i {
            color: var(--primary);
            margin-top: 5px;
        }

        .faq-answer {
            color: var(--text-muted);
            padding-left: 28px;
        }

        /* Mobile specific */
        .mobile-nav-toggle {
            display: none;
            background: var(--bg-subtle);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 12px 20px;
            border-radius: 8px;
            width: 100%;
            margin-bottom: 20px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 500;
            text-align: left;
            justify-content: space-between;
            align-items: center;
        }

        @media (max-width: 992px) {
            .dashboard {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                padding: 20px;
                border-right: none;
                border-bottom: 1px solid var(--border);
                display: none; /* hidden by default on mobile, toggled by JS */
            }

            .sidebar.show {
                display: block;
            }

            .mobile-nav-toggle {
                display: flex;
            }

            .main-content {
                padding: 20px;
            }

            .content-header {
                flex-direction: column;
                gap: 20px;
            }
            
            header.top-header {
                padding: 15px 20px;
            }
        }

        @media print {
            .sidebar, .top-header, .mobile-nav-toggle, .btn {
                display: none !important;
            }
            body {
                background: white;
                color: black;
            }
            .main-content {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <header class="top-header">
        <a href="{{ route('home') }}" class="logo">
            <img src="{{ asset('img/logo.png') }}" alt="WISP">
            <span>WISP</span>
        </a>
        <a href="{{ route('home') }}" class="back-home">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </header>

    <div class="dashboard">
        <div style="padding: 20px 20px 0 20px;" class="mobile-only">
            <button class="mobile-nav-toggle" onclick="document.querySelector('.sidebar').classList.toggle('show')">
                <span><i class="fas fa-bars"></i> Menu</span>
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>

        <aside class="sidebar">
            <div class="nav-group">
                <div class="nav-title">Legal</div>
                <a class="nav-item active" data-target="terms" onclick="switchTab('terms')">
                    <i class="fas fa-file-contract"></i> Terms of Service
                </a>
                <a class="nav-item" data-target="privacy" onclick="switchTab('privacy')">
                    <i class="fas fa-shield-alt"></i> Privacy Policy
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-title">Help Center & FAQ</div>
                <a class="nav-item" data-target="account" onclick="switchTab('account')">
                    <i class="fas fa-user-circle"></i> Account & Settings
                </a>
                <a class="nav-item" data-target="messages" onclick="switchTab('messages')">
                    <i class="fas fa-envelope-open-text"></i> Messages & Vault
                </a>
                <a class="nav-item" data-target="media" onclick="switchTab('media')">
                    <i class="fas fa-images"></i> Templates & Media
                </a>
                <a class="nav-item" data-target="sharing" onclick="switchTab('sharing')">
                    <i class="fas fa-share-nodes"></i> Links & Sharing
                </a>
                <a class="nav-item" data-target="security" onclick="switchTab('security')">
                    <i class="fas fa-lock"></i> Security & Deletion
                </a>
            </div>
        </aside>

        <main class="main-content">
            <!-- TERMS OF SERVICE -->
            <div id="terms" class="tab-pane active">
                <div class="content-header">
                    <div>
                        <h1>Terms of Service</h1>
                        <p>Last Updated: {{ date('F j, Y') }}</p>
                    </div>
                    <button onclick="window.print()" class="btn"><i class="fas fa-download"></i> Download PDF</button>
                </div>
                
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing and using WISP ("we", "our", or "us"), you agree to be bound by these Terms of Service. If you do not agree with any part of these terms, you may not use our platform.</p>

                <h2>2. Description of Service</h2>
                <p>WISP provides a platform for generating, storing, and sharing digital greetings, messages, and well-wishes. We reserve the right to modify, suspend, or discontinue any part of the service at any time without prior notice.</p>

                <h2>3. User Accounts</h2>
                <ul>
                    <li>You must be at least 13 years old to create an account.</li>
                    <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
                    <li>You agree to notify us immediately of any unauthorized use of your account.</li>
                    <li>We reserve the right to terminate accounts that violate our terms or engage in malicious activity.</li>
                </ul>

                <h2>4. User Content</h2>
                <p>You retain full ownership of the text, images, and other media you upload or create using WISP. By using our platform, you grant us a non-exclusive, worldwide, royalty-free license to store, process, and display your content solely for the purpose of providing the service.</p>
                <p>You agree NOT to upload content that is:</p>
                <ul>
                    <li>Illegal, abusive, harassing, or defamatory.</li>
                    <li>Infringing on any third-party intellectual property rights.</li>
                    <li>Containing malware, viruses, or harmful code.</li>
                </ul>

                <h2>5. Premium and Billing</h2>
                <p>Certain features of WISP may require a paid subscription. All payments are processed securely through our third-party payment providers (e.g., Paystack). Subscription fees are non-refundable unless legally required. We reserve the right to change our pricing upon prior notice.</p>

                <h2>6. Limitation of Liability</h2>
                <p>WISP is provided on an "AS IS" and "AS AVAILABLE" basis. We do not warrant that the service will be uninterrupted, completely secure, or error-free. In no event shall WISP or its owners be liable for any indirect, incidental, or consequential damages arising from your use of the service.</p>

                <h2>7. Contact Us</h2>
                <p>If you have any questions or concerns about these Terms of Service, please contact us at support@wispmsg.com.</p>
            </div>

            <!-- PRIVACY POLICY -->
            <div id="privacy" class="tab-pane">
                <div class="content-header">
                    <div>
                        <h1>Privacy Policy</h1>
                        <p>Last Updated: {{ date('F j, Y') }}</p>
                    </div>
                    <button onclick="window.print()" class="btn"><i class="fas fa-download"></i> Download PDF</button>
                </div>

                <h2>1. Introduction</h2>
                <p>At WISP, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our application.</p>

                <h2>2. Information We Collect</h2>
                <p>We may collect information about you in a variety of ways, including:</p>
                <ul>
                    <li><strong>Personal Data:</strong> Name, email address, username, and profile pictures when you register or authenticate via third-party providers (like Google or Spotify).</li>
                    <li><strong>Content Data:</strong> The messages, templates, images, and other media you upload or create using our service.</li>
                    <li><strong>Usage Data:</strong> Information about how you navigate and use our application, including IP addresses, browser types, and device information.</li>
                </ul>

                <h2>3. How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Create and manage your account.</li>
                    <li>Deliver targeted services, such as delivering your scheduled wishes to recipients.</li>
                    <li>Process payments and manage subscriptions.</li>
                    <li>Improve our website, application, and overall user experience.</li>
                </ul>

                <h2>4. Disclosure of Your Information</h2>
                <p>We do not sell, trade, or rent your personal identification information to others. We may use third-party service providers (such as email delivery services or payment processors) to help us operate our business, and we may share your information with these third parties solely for those limited purposes.</p>

                <h2>5. Security of Your Information</h2>
                <p>We use administrative, technical, and physical security measures to help protect your personal information (including encryption of sensitive data like passcodes and passwords). While we have taken reasonable steps to secure the personal information you provide to us, please be aware that no security measures are perfect or impenetrable.</p>

                <h2>6. Third-Party Integrations</h2>
                <p>WISP integrates with third-party services like Google and Spotify. When you authenticate using these services, we receive specific data (like your name and email) as permitted by your privacy settings with those providers.</p>
            </div>

            <!-- FAQ: Account & Settings -->
            <div id="account" class="tab-pane">
                <div class="content-header">
                    <div>
                        <h1>Account & Settings</h1>
                        <p>Everything you need to know about setting up and customizing your WISP experience.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> How do I create an account?</div>
                    <div class="faq-answer">Creating an account is simple. Click the "Log in / Sign up" button on the home page, select the "Sign up" tab, and enter your email, username, and a strong password. Alternatively, you can sign up instantly using your Google or Spotify account.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> Where can I change my profile picture or name?</div>
                    <div class="faq-answer">Navigate to your User Dashboard, click on "Settings" in the sidebar, and go to the "General" tab. Here you can upload a new avatar and update your display name or username.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> How do I reset my password?</div>
                    <div class="faq-answer">If you are logged out, click "Forgot Password" on the login screen. We will send a verification code to your email. Enter the code to securely reset your password. If you are logged in, you can change your password via the Security Settings page.</div>
                </div>
            </div>

            <!-- FAQ: Messages & Vault -->
            <div id="messages" class="tab-pane">
                <div class="content-header">
                    <div>
                        <h1>Messages & Vault</h1>
                        <p>Learn how to craft, schedule, and secure your wishes.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> How do I create a new message?</div>
                    <div class="faq-answer">From your dashboard, click "Create Message". You'll be taken to an editor where you can type your wish, attach images, select a beautiful template, and even ask our AI Assistant to generate a personalized message for you!</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> Can I schedule a message for the future?</div>
                    <div class="faq-answer">Yes! When creating or editing a message, simply set the "Schedule Date". WISP will automatically send or reveal the message to your recipient on the specified date.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> What is the WISP Vault?</div>
                    <div class="faq-answer">The Vault is a secure folder inside your account. By moving a message to the Vault, it becomes passcode-protected. You will need to enter your personal passcode to view or edit it.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> How do I set up my Vault Passcode?</div>
                    <div class="faq-answer">Go to Settings > Security and look for the "Passcode" section. You can set a 4-digit or 6-digit pin. Keep this safe, as it protects your most sensitive messages!</div>
                </div>
            </div>

            <!-- FAQ: Templates & Media -->
            <div id="media" class="tab-pane">
                <div class="content-header">
                    <div>
                        <h1>Templates & Media</h1>
                        <p>Make your messages beautiful with themes and music.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> How do I use templates?</div>
                    <div class="faq-answer">When creating a message, click on the "Templates" tab. You can browse through our gallery of professionally designed themes for birthdays, anniversaries, and holidays. Selecting a template instantly applies it to your current message.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> How do I add music to my message?</div>
                    <div class="faq-answer">WISP integrates with Spotify! Connect your Spotify account from the Music tab, search for any track or playlist, and attach it to your message. When your recipient opens the link, the music will play automatically.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> Where do my uploaded photos go?</div>
                    <div class="faq-answer">All photos you upload are securely stored in your Media Library. You can access this library from the dashboard to view, delete, or reuse images across multiple messages.</div>
                </div>
            </div>

            <!-- FAQ: Links & Sharing -->
            <div id="sharing" class="tab-pane">
                <div class="content-header">
                    <div>
                        <h1>Links & Sharing</h1>
                        <p>How to send your magical wishes to others.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> How do I generate a link for my message?</div>
                    <div class="faq-answer">Once your message is saved, click the "Share" or "Generate Link" button. WISP will create a unique, private URL that you can copy and send to anyone via WhatsApp, Messenger, or text.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> Can I send messages directly via email or SMS?</div>
                    <div class="faq-answer">Absolutely. In the Sharing center, you can input a recipient's email address or phone number, and WISP will deliver the link directly to them, either instantly or on your scheduled date.</div>
                </div>
            </div>

            <!-- FAQ: Security & Deletion -->
            <div id="security" class="tab-pane">
                <div class="content-header">
                    <div>
                        <h1>Security & Account Deletion</h1>
                        <p>Managing your data and privacy.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> Is my data secure?</div>
                    <div class="faq-answer">Yes, WISP encrypts sensitive data (like your vault passcode and private messages). Our servers use industry-standard security protocols to ensure your wishes are safe from unauthorized access.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> How do I delete my account?</div>
                    <div class="faq-answer">We're sad to see you go! To permanently delete your account, navigate to Settings > General. Scroll to the very bottom and click the red "Delete Account" button. You will be asked to confirm. <strong>Please note: This action is irreversible and all your messages, media, and data will be permanently erased.</strong></div>
                </div>

                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> Can I export my data before deleting?</div>
                    <div class="faq-answer">Yes, you can request a data export from your Settings page before you delete your account to keep a backup of your precious messages and media.</div>
                </div>
            </div>

        </main>
    </div>

    <script>
        function switchTab(tabId) {
            // Update nav items
            document.querySelectorAll('.nav-item').forEach(el => {
                el.classList.remove('active');
            });
            document.querySelector(`.nav-item[data-target="${tabId}"]`).classList.add('active');

            // Update content panes
            document.querySelectorAll('.tab-pane').forEach(el => {
                el.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');

            // Update URL without reloading
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);

            // On mobile, hide sidebar after selection
            if (window.innerWidth <= 992) {
                document.querySelector('.sidebar').classList.remove('show');
            }
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Initialize based on URL parameter
        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab && document.getElementById(tab)) {
                switchTab(tab);
            }
        });
    </script>
</body>

</html>
