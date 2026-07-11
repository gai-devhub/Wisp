@extends('user.base-user')

@section('title', 'Select Payment Method')

@section('content')
@php
    $planName = '';
    $duration = '';
    $discount = '';
    $finalPrice = 0;
    
    if ($plan === 'yearly') {
        $planName = 'Yearly Plan';
        $duration = '12 Months';
        $discount = '30% OFF';
        $finalPrice = $price * 12 * 0.70;
    } elseif ($plan === 'halfyear') {
        $planName = 'Half Year Plan';
        $duration = '6 Months';
        $discount = '15% OFF';
        $finalPrice = $price * 6 * 0.85;
    } else {
        $planName = 'Monthly Plan';
        $duration = '1 Month';
        $discount = 'No Discount';
        $finalPrice = $price;
    }
@endphp
<style>
    .checkout-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        margin-top: 40px;
    }
    .checkout-summary {
        flex: 1 1 350px;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        border-radius: 20px;
        padding: 40px;
        color: white;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .checkout-summary::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(109, 40, 217, 0.4);
        filter: blur(50px);
        border-radius: 50%;
    }
    .checkout-form-container {
        flex: 2 1 500px;
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
    }
    .summary-title { font-size: 1.2rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 30px; }
    .summary-plan { font-size: 2.2rem; font-weight: 800; margin-bottom: 5px; }
    .summary-duration { font-size: 1.1rem; color: #cbd5e1; margin-bottom: 30px; }
    
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 1.1rem; color: #cbd5e1; }
    .summary-divider { height: 1px; background: rgba(255,255,255,0.1); margin: 20px 0; }
    .summary-total { font-size: 1.8rem; font-weight: 800; color: white; display: flex; justify-content: space-between; align-items: center; margin-top: 20px;}
    
    .method-card {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        padding: 25px 30px;
        border-radius: 16px;
        margin-bottom: 20px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .method-card:hover {
        border-color: #6d28d9;
        background: #f5f3ff;
        box-shadow: 0 10px 25px rgba(109, 40, 217, 0.15);
        transform: translateY(-3px);
    }
    .method-card span {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        text-decoration: none !important;
    }
    .method-card i.mr-3 {
        font-size: 1.8rem;
        color: #6d28d9;
        width: 40px;
        margin-right: 20px;
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        padding: 10px 20px;
        border-radius: 30px;
        border: none;
        color: #475569;
        cursor: pointer;
        margin-bottom: 35px;
        font-weight: 600;
        text-decoration: none !important;
        font-size: 1.05rem;
        transition: all 0.2s;
    }
    .btn-back:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .btn-back i { margin-right: 8px; }
    a { text-decoration: none !important; }
    .trust-badges { display: flex; gap: 15px; margin-top: 40px; color: #cbd5e1; font-size: 0.9rem; align-items: center; }
</style>
<div class="user-page-container billing-premium-container" style="padding-top: 10px; margin: 0 auto; max-width: 1100px;">
    
    <div class="checkout-layout">
        <!-- Left: Order Summary -->
        <div class="checkout-summary">
            <div class="summary-title">Order Summary</div>
            <div class="summary-plan">{{ $planName }}</div>
            <div class="summary-duration">Billed for {{ $duration }}</div>
            
            <div class="summary-row">
                <span>Base Price</span>
                <span>${{ number_format($price, 2) }}/mo</span>
            </div>
            <div class="summary-row">
                <span>Discount</span>
                <span style="color: #34d399; font-weight: 600;">{{ $discount }}</span>
            </div>
            
            <div class="summary-divider"></div>
            
            <div class="summary-total">
                <span>Total Due</span>
                <span>${{ number_format($finalPrice, 2) }}</span>
            </div>
            
            <div class="trust-badges">
                <i class="fas fa-lock"></i>
                <span>Guaranteed safe & secure checkout powered by Paystack.</span>
            </div>
        </div>
        
        <!-- Right: Method Selection -->
        <div class="checkout-form-container">
            <a href="{{ route('user.billing.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Plans</a>
            
            <h2 class="mb-2" style="font-weight: 800; font-size: 2rem; color: #0f172a;">Payment Method</h2>
            <p class="text-muted mb-5" style="font-size: 1.1rem;">Choose how you'd like to pay for your subscription.</p>
            
            <a href="{{ route('user.billing.checkout', ['plan' => $plan, 'method' => 'card']) }}">
                <div class="method-card">
                    <span><i class="fas fa-credit-card mr-3"></i> Credit or Debit Card</span>
                    <i class="fas fa-chevron-right text-muted"></i>
                </div>
            </a>
            
            <a href="{{ route('user.billing.checkout', ['plan' => $plan, 'method' => 'mobile_money']) }}">
                <div class="method-card">
                    <span><i class="fas fa-mobile-alt mr-3"></i> Mobile Money</span>
                    <i class="fas fa-chevron-right text-muted"></i>
                </div>
            </a>
            
            <div class="text-center mt-4 text-muted" style="font-size: 0.9rem;">
                <i class="fas fa-shield-alt mr-1"></i> Your payment information is encrypted and secure.
            </div>
        </div>
    </div>
    
</div>
@endsection
