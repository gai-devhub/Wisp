@extends('user.base-user')

@section('content')
<div class="user-page-container billing-premium-container">

    {{-- ═══════════════════════════════════════════════════
         PRO UPGRADE BANNER — shown when user hits message limit
    ══════════════════════════════════════════════════════ --}}
    @if(session('upgrade_prompt'))
    <div class="wisp-upgrade-wall" id="wisp-upgrade-wall">
        {{-- Close button --}}
        <button class="upgrade-wall-close" onclick="document.getElementById('wisp-upgrade-wall').style.display='none'" title="Close">
            <i class="fas fa-times"></i>
        </button>

        {{-- Hero image --}}
        <div class="upgrade-wall-hero">
            <img src="{{ asset('img/pro-image.jpeg') }}" alt="WISP Premium Access" class="upgrade-hero-img">
        </div>

        {{-- Limit message --}}
        <div class="upgrade-wall-body">
            <div class="upgrade-limit-badge">
                <i class="fas fa-lock"></i> Free Limit Reached
            </div>
            <h2 class="upgrade-wall-title">{{ session('upgrade_message', "You've reached your 5-message free limit.") }}</h2>
            <p class="upgrade-wall-sub">Upgrade to <strong>WISP Premium</strong> for unlimited messages, 200+ templates, unlimited links &amp; more.</p>

            {{-- Pricing Cards --}}
            <div class="upgrade-pricing-row">
                {{-- Monthly --}}
                <div class="upgrade-plan-card">
                    <div class="upgrade-plan-label">MONTHLY PLAN</div>
                    <div class="upgrade-plan-price">
                        <span class="upgrade-currency">$</span><span class="upgrade-amount">{{ number_format($price, 2) }}</span>
                        <span class="upgrade-period">/month</span>
                    </div>
                    <p class="upgrade-plan-note">Billed monthly</p>
                    <ul class="upgrade-features">
                        <li><i class="fas fa-check"></i> 200+ premium templates</li>
                        <li><i class="fas fa-check"></i> Unlimited AI writing</li>
                        <li><i class="fas fa-check"></i> Unlimited messages</li>
                        <li><i class="fas fa-check"></i> Unlimited sharing</li>
                        <li><i class="fas fa-check"></i> Unlimited links generation</li>
                        <li><i class="fas fa-check"></i> Track views &amp; insights</li>
                    </ul>
                    <a href="javascript:void(0)" href="{{ route('user.billing.method', ['plan' => 'monthly']) }}" class="upgrade-plan-btn upgrade-btn-monthly">
                        Get Started
                    </a>
                </div>

                {{-- Half Year --}}
                <div class="upgrade-plan-card upgrade-plan-featured">
                    <div class="upgrade-plan-badge-featured">HALF YEAR PLAN</div>
                    <div class="upgrade-plan-price">
                        <span class="upgrade-currency">$</span><span class="upgrade-amount">{{ number_format($price * 6 * 0.85, 2) }}</span>
                        <span class="upgrade-period">/6 months</span>
                    </div>
                    <p class="upgrade-plan-note">Billed every 6 months</p>
                    <ul class="upgrade-features">
                        <li><i class="fas fa-check"></i> 200+ premium templates</li>
                        <li><i class="fas fa-check"></i> Unlimited AI writing</li>
                        <li><i class="fas fa-check"></i> Unlimited messages</li>
                        <li><i class="fas fa-check"></i> Unlimited sharing</li>
                        <li><i class="fas fa-check"></i> Unlimited links generation</li>
                        <li><i class="fas fa-check"></i> Track views &amp; insights</li>
                    </ul>
                    <a href="javascript:void(0)" href="{{ route('user.billing.method', ['plan' => 'halfyear']) }}" class="upgrade-plan-btn upgrade-btn-featured">
                        Choose Half Year
                    </a>
                </div>

                {{-- Yearly --}}
                <div class="upgrade-plan-card">
                    <div class="upgrade-plan-label">YEARLY PLAN</div>
                    <div class="upgrade-plan-price">
                        <span class="upgrade-currency">$</span><span class="upgrade-amount">{{ number_format($price * 12 * 0.70, 2) }}</span>
                        <span class="upgrade-period">/year</span>
                    </div>
                    <p class="upgrade-plan-note">Billed yearly</p>
                    <ul class="upgrade-features">
                        <li><i class="fas fa-check"></i> 200+ premium templates</li>
                        <li><i class="fas fa-check"></i> Unlimited AI writing</li>
                        <li><i class="fas fa-check"></i> Unlimited messages</li>
                        <li><i class="fas fa-check"></i> Unlimited sharing</li>
                        <li><i class="fas fa-check"></i> Unlimited links generation</li>
                        <li><i class="fas fa-check"></i> Track views &amp; insights</li>
                    </ul>
                    <a href="javascript:void(0)" href="{{ route('user.billing.method', ['plan' => 'yearly']) }}" class="upgrade-plan-btn upgrade-btn-yearly">
                        Go Premium
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Upgrade wall CSS --}}
    



    @endif

    {{-- ═══════════════════════════════════════════════════
         STANDARD BILLING PAGE HEADER
    ══════════════════════════════════════════════════════ --}}
    <div class="premium-header-glass">
        <div class="premium-header-content">
            <h1 class="premium-title">Subscription &amp; Billing</h1>
            <p class="premium-subtitle">Manage your premium access and payment history in style.</p>
        </div>
        <div class="premium-header-icon">
            <i class="fas fa-gem"></i>
        </div>
    </div>

    @if(session('success'))
        <div class="premium-alert alert-success mt-4">
            <i class="fas fa-check-circle alert-icon"></i>
            <div class="alert-content">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('warning'))
        <div class="premium-alert alert-warning mt-4">
            <i class="fas fa-exclamation-triangle alert-icon"></i>
            <div class="alert-content">{{ session('warning') }}</div>
        </div>
    @endif

    <div class="billing-grid mt-4">
        <!-- Current Plan Section -->
        <div class="plan-section mb-5">
            <h3 class="card-heading mb-4 billing-plan-title">Current Plan</h3>

            @if($subscription && $subscription->status == 'active' && $subscription->expires_at > now())
                <div class="status-banner banner-active">
                    <div class="banner-icon-wrap"><i class="fas fa-shield-check"></i></div>
                    <div class="banner-text">
                        <strong>Premium Active</strong>
                        @if($subscription->admin_granted)
                            <span>Your account has been granted unlimited premium access by an admin.</span>
                        @else
                            <span>Your subscription is active until {{ $subscription->expires_at->format('M j, Y') }}.</span>
                        @endif
                    </div>
                </div>
            @elseif($subscription && $subscription->status == 'pending')
                <div class="status-banner banner-pending">
                    <div class="banner-icon-wrap"><i class="fas fa-hourglass-half"></i></div>
                    <div class="banner-text">
                        <strong>Payment Pending</strong>
                        <span>Your manual payment is currently being reviewed by an admin. We'll update your status shortly.</span>
                    </div>
                </div>
            @else
                <div class="status-banner banner-locked">
                    <div class="banner-icon-wrap"><i class="fas fa-lock"></i></div>
                    <div class="banner-text">
                        <strong>No Active Subscription</strong>
                        <span>You do not have an active subscription. Premium features are locked.</span>
                    </div>
                </div>

                
                <div class="subscribe-actions-group" style="width: 100%; justify-content: space-between;">
                    <a href="{{ route('user.billing.method', ['plan' => 'monthly']) }}" class="subscribe-card" style="display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                        <div class="badge-best-value" style="background: #f1f5f9; color: #64748b; position: absolute; top: -12px; left: 50%; transform: translateX(-50%); font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: bold; white-space: nowrap;">0% OFF</div>
                        <div>
                            <h4 class="billing-plan-title">Monthly Plan</h4>
                            <p class="text-muted billing-plan-desc">Flexible, pay as you go</p>
                            <div class="price-display billing-price-wrap">
                                <span class="currency billing-price-currency">$</span>
                                <span class="amount billing-price-amount">{{ number_format($price, 2) }}</span>
                                <span class="period billing-price-period">/mo</span>
                            </div>
                            <ul class="upgrade-features mt-4" style="text-align: left; font-size: 13px; color: #64748b; list-style: none; padding: 0;">
                                <li style="margin-bottom: 8px;"><i class="fas fa-check text-indigo-500 mr-2"></i> 50+ premium templates</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check text-indigo-500 mr-2"></i> 50 AI messages/mo</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check text-indigo-500 mr-2"></i> 20 links/mo</li>
                            </ul>
                        </div>
                        <div class="plan-btn monthly-btn mt-4">
                            Select Monthly
                        </div>
                    </a>

                    <a href="{{ route('user.billing.method', ['plan' => 'halfyear']) }}" class="subscribe-card" style="border: 2px solid #6d28d9; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                        <div class="badge-best-value" style="background: #e0e7ff; color: #4338ca; position: absolute; top: -12px; left: 50%; transform: translateX(-50%); font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: bold; white-space: nowrap;">15% OFF</div>
                        <div>
                            <h4 class="billing-plan-title" style="color: #6d28d9;">Half Year Plan</h4>
                            <p class="text-muted billing-plan-desc">Great value for 6 months</p>
                            <div class="price-display billing-price-wrap">
                                <span class="currency billing-price-currency" style="color: #6d28d9;">$</span>
                                <span class="amount billing-price-amount" style="color: #6d28d9;">{{ number_format($price * 6 * 0.85, 2) }}</span>
                                <span class="period billing-price-period">/6mo</span>
                            </div>
                            <ul class="upgrade-features mt-4" style="text-align: left; font-size: 13px; color: #64748b; list-style: none; padding: 0;">
                                <li style="margin-bottom: 8px;"><i class="fas fa-check text-indigo-500 mr-2"></i> 200+ premium templates</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check text-indigo-500 mr-2"></i> Unlimited AI writing</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check text-indigo-500 mr-2"></i> Unlimited messages</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check text-indigo-500 mr-2"></i> Unlimited links & sharing</li>
                            </ul>
                        </div>
                        <div class="plan-btn mt-4" style="background: white; border: 1px solid #6d28d9; color: #6d28d9;">
                            Select Half Year
                        </div>
                    </a>

                    <a href="{{ route('user.billing.method', ['plan' => 'yearly']) }}" class="subscribe-card yearly-card" style="display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                        <div class="badge-best-value" style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); white-space: nowrap;">30% OFF BEST VALUE</div>
                        <div>
                            <h4 class="billing-yearly-title">Yearly Plan</h4>
                            <p class="text-muted billing-plan-desc">Save automatically every year</p>
                            <div class="price-display billing-price-wrap">
                                <span class="currency billing-price-currency-yearly">$</span>
                                <span class="amount billing-price-amount">{{ number_format($price * 12 * 0.70, 2) }}</span>
                                <span class="period billing-price-period">/yr</span>
                            </div>
                            <ul class="upgrade-features mt-4" style="text-align: left; font-size: 13px; color: #64748b; list-style: none; padding: 0;">
                                <li style="margin-bottom: 8px;"><i class="fas fa-check text-indigo-500 mr-2"></i> 200+ premium templates</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check text-indigo-500 mr-2"></i> Unlimited AI writing</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check text-indigo-500 mr-2"></i> Unlimited messages</li>
                                <li style="margin-bottom: 8px;"><i class="fas fa-check text-indigo-500 mr-2"></i> Unlimited links & sharing</li>
                            </ul>
                        </div>
                        <div class="plan-btn btn-premium-subscribe billing-plan-btn-wrap mt-4">
                            Select Yearly <i class="fas fa-arrow-right ml-1"></i>
                        </div>
                    </a>
                
                </div>
                </div>             @endif
        </div>

        <!-- Payment History Card -->
        <div class="premium-card history-card glass-panel mt-5" style="margin-top: 60px !important;">
            <div class="card-inner">
                <div class="history-header">
                    <h3 class="card-heading">Payment History</h3>
                </div>
                <div class="history-table-container">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $hist)
                            <tr>
                                <td class="font-weight-500">{{ $hist->created_at->format('M j, Y') }}</td>
                                <td class="price-cell">${{ number_format($hist->amount, 2) }}</td>
                                <td>
                                    <span class="method-pill">
                                        <i class="fas {{ str_contains($hist->payment_method, 'paystack') ? 'fa-credit-card' : (str_contains($hist->payment_method, 'admin') ? 'fa-crown' : 'fa-money-bill-wave') }}"></i>
                                        {{ ucfirst(str_replace('_', ' ', $hist->payment_method)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-pill status-{{ $hist->status }}">
                                        {{ ucfirst($hist->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <div class="empty-state-content">
                                        <i class="fas fa-receipt"></i>
                                        <p>No past payments found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($paystackPublicKey)



@endif

@endsection
