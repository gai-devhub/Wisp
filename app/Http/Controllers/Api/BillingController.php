<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use App\Models\PaymentHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    /**
     * Get billing info for the mobile app (prices, active plan, public key)
     */
    public function info(Request $request)
    {
        $user = $request->user();
        
        $price = DB::table('system_settings')->where('key', 'subscription_price_monthly')->value('value') ?? 0.50;
        $price = (float)$price;

        $paystackPublicKey = DB::table('system_settings')->where('key', 'paystack_public_key')->value('value');

        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'current_plan' => $activeSubscription ? [
                    'status' => 'active',
                    'expires_at' => $activeSubscription->expires_at,
                    'admin_granted' => $activeSubscription->admin_granted,
                ] : null,
                'plans' => [
                    'monthly' => [
                        'amount' => $price,
                        'name' => 'Monthly Plan'
                    ],
                    'halfyear' => [
                        'amount' => round($price * 6 * 0.85, 2),
                        'name' => 'Half Year Plan'
                    ],
                    'yearly' => [
                        'amount' => round($price * 12 * 0.70, 2),
                        'name' => 'Yearly Plan'
                    ]
                ],
                'paystack_public_key' => $paystackPublicKey
            ]
        ]);
    }

    /**
     * Verify Paystack payment from the mobile app
     */
    public function verify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'plan' => 'required|string|in:monthly,halfyear,yearly'
        ]);

        $reference = $request->reference;
        $plan = $request->plan;
        $user = $request->user();

        $secretKey = DB::table('system_settings')->where('key', 'paystack_secret_key')->value('value');
        if (!$secretKey) {
            return response()->json(['success' => false, 'message' => 'Payment gateway not configured.'], 400);
        }

        try {
            $response = Http::withToken($secretKey)
                ->get("https://api.paystack.co/transaction/verify/{$reference}");

            if ($response->successful() && $response->json('data.status') === 'success') {
                
                $basePrice = DB::table('system_settings')->where('key', 'subscription_price_monthly')->value('value') ?? 0.50;
                $basePrice = (float)$basePrice;

                $months = 1;
                $amountPaid = $basePrice;

                if ($plan == 'halfyear') {
                    $months = 6;
                    $amountPaid = round($basePrice * 6 * 0.85, 2);
                } elseif ($plan == 'yearly') {
                    $months = 12;
                    $amountPaid = round($basePrice * 12 * 0.70, 2);
                }

                // Check if already used
                $exists = PaymentHistory::where('transaction_reference', $reference)->first();
                if ($exists) {
                    return response()->json(['success' => false, 'message' => 'Transaction already processed.'], 400);
                }

                PaymentHistory::create([
                    'user_id' => $user->id,
                    'amount' => $amountPaid,
                    'payment_method' => 'paystack',
                    'transaction_reference' => $reference,
                    'status' => 'completed',
                ]);

                $subscription = Subscription::firstOrNew(['user_id' => $user->id]);
                
                $newExpires = Carbon::now()->addMonths($months);
                if ($subscription->exists && $subscription->status == 'active' && $subscription->expires_at && $subscription->expires_at->isFuture()) {
                    $newExpires = $subscription->expires_at->addMonths($months);
                }

                $subscription->status = 'active';
                $subscription->expires_at = $newExpires;
                $subscription->admin_granted = false;
                $subscription->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified and subscription updated successfully.',
                    'expires_at' => $newExpires
                ]);

            } else {
                return response()->json(['success' => false, 'message' => 'Payment verification failed.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Paystack verification error (API): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error verifying payment. Try again later.'], 500);
        }
    }

    public function charge(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|in:monthly,halfyear,yearly',
            'payment_type' => 'required|string|in:card,mobile_money',
        ]);

        $user = $request->user();
        
        $basePrice = DB::table('system_settings')->where('key', 'subscription_price_monthly')->value('value') ?? 0.50;
        $basePrice = (float)$basePrice;
        
        $amountPaid = $basePrice;
        if ($request->plan == 'halfyear') $amountPaid = round($basePrice * 6 * 0.85, 2);
        elseif ($request->plan == 'yearly') $amountPaid = round($basePrice * 12 * 0.70, 2);

        $secretKey = DB::table('system_settings')->where('key', 'paystack_secret_key')->value('value');
        if (!$secretKey) return response()->json(['success' => false, 'message' => 'Payment gateway not configured.'], 400);
        
        $payload = [
            'email' => $user->email,
            'amount' => $amountPaid * 100, // in kobo/cents
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
            Log::error('Paystack charge error: ' . $e->getMessage());
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

        $user = $request->user();
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
                    $basePrice = (float)$basePrice;
                    $amountPaid = $basePrice;
                    if ($request->plan == 'halfyear') $amountPaid = round($basePrice * 6 * 0.85, 2);
                    elseif ($request->plan == 'yearly') $amountPaid = round($basePrice * 12 * 0.70, 2);

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
        $months = 1;
        if ($plan == 'halfyear') $months = 6;
        elseif ($plan == 'yearly') $months = 12;

        $exists = PaymentHistory::where('transaction_reference', $reference)->first();
        if ($exists) {
            return response()->json(['success' => true, 'message' => 'Already processed']);
        }

        PaymentHistory::create([
            'user_id' => $user->id,
            'amount' => $amountPaid,
            'payment_method' => 'paystack_direct',
            'transaction_reference' => $reference,
            'status' => 'completed',
        ]);

        $subscription = Subscription::firstOrNew(['user_id' => $user->id]);
        $newExpires = Carbon::now()->addMonths($months);
        if ($subscription->exists && $subscription->status == 'active' && $subscription->expires_at && $subscription->expires_at->isFuture()) {
            $newExpires = $subscription->expires_at->addMonths($months);
        }

        $subscription->status = 'active';
        $subscription->expires_at = $newExpires;
        $subscription->admin_granted = false;
        $subscription->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment successful',
            'expires_at' => $newExpires,
            'status' => 'success'
        ]);
    }
}
