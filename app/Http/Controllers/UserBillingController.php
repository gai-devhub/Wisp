<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserBillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();

        $history = Subscription::where('user_id', $user->id)->latest()->get();
        
        $price = DB::table('system_settings')->where('key', 'subscription_price_monthly')->value('value') ?? '0.50';
        $paystackPublicKey = DB::table('system_settings')->where('key', 'paystack_public_key')->value('value');

        return view('user.pages.billing.index', compact('user', 'subscription', 'history', 'price', 'paystackPublicKey'));
    }

    public function methodSelection(Request $request)
    {
        $plan = $request->query('plan', 'monthly');
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();
            
        $price = DB::table('system_settings')->where('key', 'subscription_price_monthly')->value('value') ?? '0.50';
            
        return view('user.pages.billing.method', compact('user', 'subscription', 'plan', 'price'));
    }

    public function checkout(Request $request)
    {
        $plan = $request->query('plan', 'monthly');
        $method = $request->query('method', 'card');
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest()
            ->first();
            
        $price = DB::table('system_settings')->where('key', 'subscription_price_monthly')->value('value') ?? '0.50';
            
        return view('user.pages.billing.checkout', compact('user', 'subscription', 'plan', 'method', 'price'));
    }

    public function verifyPaystack(Request $request)
    {
        $reference = $request->reference;
        $secretKey = DB::table('system_settings')->where('key', 'paystack_secret_key')->value('value');

        if (!$secretKey) {
            return redirect()->route('user.billing.index')->with('error', 'Payment gateway not configured.');
        }

        $response = Http::withToken($secretKey)->get("https://api.paystack.co/transaction/verify/{$reference}");
        
        if ($response->successful()) {
            $data = $response->json('data');
            
            if ($data['status'] === 'success') {
                $plan = $request->query('plan', 'monthly');
                $basePrice = DB::table('system_settings')->where('key', 'subscription_price_monthly')->value('value') ?? '0.50';
                $price = clonePriceForPlan($plan, (float)$basePrice);
                $expiresAt = $plan === 'yearly' ? now()->addDays(365) : ($plan === 'halfyear' ? now()->addDays(183) : now()->addDays(30));
                
                Subscription::create([
                    'user_id' => Auth::id(),
                    'amount' => $price,
                    'payment_method' => 'paystack',
                    'status' => 'active',
                    'reference_code' => $reference,
                    'starts_at' => now(),
                    'expires_at' => $expiresAt,
                ]);

                return redirect()->route('user.billing.index')->with('success', 'Subscription activated successfully!');
            }
        }

        return redirect()->route('user.billing.index')->with('error', 'Payment verification failed.');
    }

    public function charge(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|in:monthly,halfyear,yearly',
            'payment_type' => 'required|string|in:card,mobile_money',
        ]);

        $user = Auth::user();
        
        $basePrice = DB::table('system_settings')->where('key', 'subscription_price_monthly')->value('value') ?? 0.50;
        $basePrice = (float)$basePrice;
        
        $amountPaid = clonePriceForPlan($request->plan, $basePrice);

        $secretKey = DB::table('system_settings')->where('key', 'paystack_secret_key')->value('value');
        if (!$secretKey) return response()->json(['success' => false, 'message' => 'Payment gateway not configured.'], 400);
        
        $payload = [
            'email' => $user->email,
            'amount' => round($amountPaid * 100),
        ];

        if ($request->payment_type === 'card') {
            $payload['card'] = [
                'number' => preg_replace('/\s+/', '', $request->card_number),
                'cvv' => $request->cvv,
                'expiry_month' => $request->expiry_month,
                'expiry_year' => $request->expiry_year,
            ];
        } else {
            $payload['mobile_money'] = [
                'phone' => $request->phone,
                'provider' => $request->provider,
            ];
        }

        try {
            $response = Http::withToken($secretKey)->post('https://api.paystack.co/charge', $payload);
            $data = $response->json();
            
            if (!$response->successful() && !isset($data['data']['status'])) {
                return response()->json(['success' => false, 'message' => $data['message'] ?? 'Payment failed.'], 400);
            }

            $status = $data['data']['status'] ?? null;
            $reference = $data['data']['reference'] ?? null;

            if ($status === 'success') {
                return $this->handleSuccessfulCharge($user, $request->plan, $amountPaid, $reference);
            } elseif (in_array($status, ['send_otp', 'send_pin', 'send_phone', 'open_url'])) {
                return response()->json([
                    'success' => true, 
                    'status' => $status, 
                    'reference' => $reference,
                    'message' => $data['data']['display_text'] ?? 'Verification required.',
                    'url' => $data['data']['url'] ?? null
                ]);
            } else {
                return response()->json(['success' => false, 'message' => $data['data']['message'] ?? 'Payment failed.'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Paystack charge error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error while charging.'], 500);
        }
    }

    public function submitOtp(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'otp' => 'required|string',
            'plan' => 'required|string',
        ]);

        $user = Auth::user();
        $secretKey = DB::table('system_settings')->where('key', 'paystack_secret_key')->value('value');
        
        try {
            $response = Http::withToken($secretKey)->post('https://api.paystack.co/charge/submit_otp', [
                'reference' => $request->reference,
                'otp' => $request->otp
            ]);
            $data = $response->json();

            if ($response->successful() && isset($data['data']['status'])) {
                $status = $data['data']['status'];
                if ($status === 'success') {
                    $basePrice = DB::table('system_settings')->where('key', 'subscription_price_monthly')->value('value') ?? 0.50;
                    $amountPaid = clonePriceForPlan($request->plan, (float)$basePrice);
                    return $this->handleSuccessfulCharge($user, $request->plan, $amountPaid, $request->reference);
                }
            }
            return response()->json(['success' => false, 'message' => $data['data']['message'] ?? 'OTP verification failed.'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error verifying OTP.'], 500);
        }
    }

    private function handleSuccessfulCharge($user, $plan, $amountPaid, $reference)
    {
        $expiresAt = $plan === 'yearly' ? now()->addDays(365) : ($plan === 'halfyear' ? now()->addDays(183) : now()->addDays(30));

        $exists = Subscription::where('reference_code', $reference)->first();
        if ($exists) {
            return response()->json(['success' => true, 'message' => 'Already processed']);
        }

        Subscription::create([
            'user_id' => $user->id,
            'amount' => $amountPaid,
            'payment_method' => 'paystack_direct',
            'status' => 'active',
            'reference_code' => $reference,
            'starts_at' => now(),
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment successful',
            'status' => 'success'
        ]);
    }
}

function clonePriceForPlan($plan, $basePrice) {
    if ($plan === 'yearly') return $basePrice * 12 * 0.70; // 30% off
    if ($plan === 'halfyear') return $basePrice * 6 * 0.85; // 15% off
    return $basePrice;
}
