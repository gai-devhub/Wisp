@extends('user.base-user')

@section('title', 'Checkout')

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
    
    .form-input {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 25px;
        font-size: 1.1rem;
        color: #1e293b;
        transition: all 0.2s;
        background: #f8fafc;
    }
    .form-input:focus {
        border-color: #6d28d9;
        outline: none;
        background: white;
        box-shadow: 0 0 0 4px rgba(109, 40, 217, 0.1);
    }
    .form-label {
        font-weight: 700;
        color: #475569;
        margin-bottom: 10px;
        display: block;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
    
    .plan-btn {
        background: linear-gradient(135deg, #6d28d9, #4f46e5);
        color: white;
        padding: 18px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 1.2rem;
        border: none;
        transition: all 0.3s;
        width: 100%;
        margin-top: 10px;
        cursor: pointer;
        box-shadow: 0 10px 20px rgba(109, 40, 217, 0.2);
    }
    .plan-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(109, 40, 217, 0.3);
    }
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
        
        <!-- Right: Checkout Form -->
        <div class="checkout-form-container">
            <a href="{{ route('user.billing.method', ['plan' => $plan]) }}" class="btn-back"><i class="fas fa-arrow-left"></i> Change Method</a>
            
            @if($method === 'card')
                <h2 class="mb-5" style="font-weight: 800; font-size: 2rem; color: #0f172a;">Pay with Card</h2>
                <div class="form-group">
                    <label class="form-label">Card Holder Name</label>
                    <input type="text" id="card_name" class="form-input" placeholder="e.g. John Doe" autocomplete="cc-name">
                </div>
                <div class="form-group">
                    <label class="form-label">Card Number</label>
                    <input type="text" id="card_number" class="form-input" placeholder="0000 0000 0000 0000" autocomplete="cc-number">
                </div>
                <div style="display:flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Expiry Date</label>
                        <input type="text" id="expiry_date" class="form-input" placeholder="MM/YY" autocomplete="cc-exp">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">CVV</label>
                        <input type="text" id="cvv" class="form-input" placeholder="123" autocomplete="cc-csc">
                    </div>
                </div>
                <button id="btn-pay-card" class="plan-btn" onclick="processPayment('card')">Pay ${{ number_format($finalPrice, 2) }}</button>
            @else
                <h2 class="mb-5" style="font-weight: 800; font-size: 2rem; color: #0f172a;">Pay with Mobile Money</h2>
                <div class="form-group">
                    <label class="form-label">Provider</label>
                    <select id="momo_provider" class="form-input">
                        <option value="mtn">MTN</option>
                        <option value="vod">Vodafone / Telecel</option>
                        <option value="tgo">AirtelTigo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Mobile Money Number</label>
                    <input type="text" id="momo_phone" class="form-input" placeholder="e.g. 055 123 4567">
                </div>
                <button id="btn-pay-mobile_money" class="plan-btn" onclick="processPayment('mobile_money')">Pay ${{ number_format($finalPrice, 2) }}</button>
            @endif
            
            <div class="text-center mt-4 text-muted" style="font-size: 0.9rem;">
                <i class="fas fa-shield-alt mr-1"></i> Your payment information is encrypted and secure.
            </div>
        </div>
    </div>
</div>

<script>
    const selectedPlan = "{{ $plan }}";

    async function processPayment(type) {
        const payload = {
            plan: selectedPlan,
            payment_type: type,
            _token: "{{ csrf_token() }}"
        };

        if (type === 'card') {
            payload.card_name = document.getElementById('card_name').value;
            payload.card_number = document.getElementById('card_number').value;
            payload.expiry_month = document.getElementById('expiry_date').value.split('/')[0];
            payload.expiry_year = document.getElementById('expiry_date').value.split('/')[1];
            payload.cvv = document.getElementById('cvv').value;
        } else {
            payload.provider = document.getElementById('momo_provider').value;
            payload.phone = document.getElementById('momo_phone').value;
        }

        try {
            document.getElementById('btn-pay-'+type).innerText = 'Processing securely...';
            document.getElementById('btn-pay-'+type).disabled = true;

            const res = await fetch("{{ route('user.billing.charge') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.success) {
                if (data.status === 'success') {
                    alert('Payment successful!');
                    window.location.href = "{{ route('user.billing.index') }}";
                } else if (data.status === 'send_otp' || data.status === 'send_pin') {
                    const otp = prompt(data.message + "\nEnter OTP/PIN:");
                    if (otp) {
                        submitOtp(data.reference, otp);
                    } else {
                        alert('Payment cancelled');
                        window.location.reload();
                    }
                } else if (data.status === 'open_url') {
                    window.location.href = data.url;
                } else if (data.status === 'send_phone') {
                    alert(data.message || 'Please follow the prompt on your phone');
                    window.location.href = "{{ route('user.billing.index') }}";
                }
            } else {
                alert(data.message || 'Payment failed');
                document.getElementById('btn-pay-'+type).innerText = 'Pay ${{ number_format($finalPrice, 2) }}';
                document.getElementById('btn-pay-'+type).disabled = false;
            }
        } catch (e) {
            alert('Server error');
            document.getElementById('btn-pay-'+type).innerText = 'Pay ${{ number_format($finalPrice, 2) }}';
            document.getElementById('btn-pay-'+type).disabled = false;
        }
    }

    async function submitOtp(reference, otp) {
        try {
            const res = await fetch("{{ route('user.billing.otp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reference: reference,
                    otp: otp,
                    plan: selectedPlan,
                    _token: "{{ csrf_token() }}"
                })
            });
            const data = await res.json();
            if (data.success) {
                alert('Payment successful!');
                window.location.href = "{{ route('user.billing.index') }}";
            } else {
                alert(data.message || 'Verification failed');
                window.location.reload();
            }
        } catch (e) {
            alert('Server error');
            window.location.reload();
        }
    }

    // Input Formatting
    document.addEventListener('DOMContentLoaded', function() {
        const cardNumberInput = document.getElementById('card_number');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
                let formattedValue = '';
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) {
                        formattedValue += ' ';
                    }
                    formattedValue += value[i];
                }
                e.target.value = formattedValue.substring(0, 19); // Max 19 chars (16 digits + 3 spaces)
            });
        }

        const expiryDateInput = document.getElementById('expiry_date');
        if (expiryDateInput) {
            expiryDateInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value.substring(0, 5); // Max 5 chars (MM/YY)
            });
        }

        const cvvInput = document.getElementById('cvv');
        if (cvvInput) {
            cvvInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4); // Max 4 digits
            });
        }
    });
</script>
@endsection
