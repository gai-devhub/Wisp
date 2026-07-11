<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Models\User;

class AdminBillingController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['user', 'manualPaymentMethod'])->latest()->paginate(10);
        return view('admin.pages.billing.index', compact('subscriptions'));
    }

    public function users()
    {
        // All non-admin users with their current premium status
        $users = tap(User::where('role', '!=', 'admin')
            ->orderBy('name')
            ->paginate(10), function ($paginated) {
                $paginated->getCollection()->transform(function ($user) {
                    $activeSub = Subscription::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->where(function ($q) {
                            $q->where('expires_at', '>', now())
                              ->orWhere('admin_granted', true);
                        })
                        ->latest()
                        ->first();
    
                    $user->is_premium      = (bool) $activeSub;
                    $user->admin_granted   = $activeSub && $activeSub->admin_granted;
                    $user->premium_expires = $activeSub && !$activeSub->admin_granted ? $activeSub->expires_at : null;
                    return $user;
                });
            });

        return view('admin.pages.billing.users', compact('users'));
    }

    public function approve($id)
    {
        $subscription = Subscription::findOrFail($id);
        $basePrice = DB::table('system_settings')->where('key', 'subscription_price_monthly')->value('value') ?? '0.50';
        $isYearly = $subscription->amount >= ((float)$basePrice * 8.0);
        $isHalfYearly = !$isYearly && $subscription->amount >= ((float)$basePrice * 4.5);
        
        $subscription->status = 'active';
        $subscription->starts_at = now();
        if ($isYearly) {
            $subscription->expires_at = now()->addDays(365);
        } elseif ($isHalfYearly) {
            $subscription->expires_at = now()->addDays(183);
        } else {
            $subscription->expires_at = now()->addDays(30);
        }
        $subscription->save();

        if ($isYearly) {
            $message = 'Subscription approved. User now has 1 year of access.';
        } elseif ($isHalfYearly) {
            $message = 'Subscription approved. User now has 6 months of access.';
        } else {
            $message = 'Subscription approved. User now has 30 days of access.';
        }
        return redirect()->back()->with('success', $message);
    }

    public function reject($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->status = 'rejected';
        $subscription->save();

        return redirect()->back()->with('success', 'Subscription rejected.');
    }

    /**
     * Admin grants unlimited premium access to a user (no expiry).
     */
    public function grantAccess($userId)
    {
        $user = User::findOrFail($userId);

        // Revoke any existing admin-granted subscriptions first
        Subscription::where('user_id', $userId)
            ->where('admin_granted', true)
            ->update(['status' => 'revoked']);

        Subscription::create([
            'user_id'          => $userId,
            'amount'           => 0,
            'payment_method'   => 'admin_grant',
            'status'           => 'active',
            'reference_code'   => 'ADMIN-GRANT-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'starts_at'        => now(),
            'expires_at'       => now()->addYears(10), // effectively unlimited (under 2038 limit)
            'admin_granted'    => true,
            'admin_granted_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', "Premium access granted to {$user->name}.");
    }

    /**
     * Admin revokes premium access granted manually.
     */
    public function revokeAccess($userId)
    {
        $user = User::findOrFail($userId);

        Subscription::where('user_id', $userId)
            ->where('admin_granted', true)
            ->where('status', 'active')
            ->update(['status' => 'revoked']);

        return redirect()->back()->with('success', "Premium access revoked for {$user->name}.");
    }

    public function settings()
    {
        $settings = DB::table('system_settings')->get()->pluck('value', 'key')->toArray();
        return view('admin.pages.billing.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'payment_system_enabled'   => 'nullable|boolean',
            'subscription_price_monthly' => 'required|numeric|min:0',
            'paystack_public_key'      => 'nullable|string',
            'paystack_secret_key'      => 'nullable|string',
        ]);

        $data['payment_system_enabled'] = $request->has('payment_system_enabled') ? '1' : '0';

        foreach ($data as $key => $value) {
            DB::table('system_settings')->updateOrInsert(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('success', 'Billing settings updated.');
    }

    public function methods()
    {
        $methods = PaymentMethod::paginate(10);
        return view('admin.pages.billing.methods', compact('methods'));
    }

    public function storeMethod(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:momo,bank',
            'account_name'   => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'instructions'   => 'nullable|string',
        ]);

        $data['is_active'] = $request->has('is_active');
        PaymentMethod::create($data);

        return redirect()->back()->with('success', 'Payment method added.');
    }

    public function destroyMethod($id)
    {
        PaymentMethod::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Payment method deleted.');
    }
}
