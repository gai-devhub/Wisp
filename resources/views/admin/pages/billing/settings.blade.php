@extends('admin.base-admin')

@section('admin-section', 'billing')

@section('content')
<div class="content-section active" id="billing">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.billing.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-colors no-underline">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800 m-0">Billing Settings</h2>
    </div>

    <form method="POST" action="{{ route('admin.billing.settings.update') }}">
        @csrf
        <div style="display: flex; gap: 1.5rem; align-items: stretch; flex-wrap: wrap;">
            
            {{-- Left Card: General Configuration --}}
            <div class="db-card overflow-hidden flex flex-col" style="flex: 1; min-width: 300px;">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800 m-0">Configuration</h3>
                </div>
                <div class="p-6 space-y-6 flex-1">
                    {{-- Toggle Payment System --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <label class="flex items-start gap-3 cursor-pointer select-none m-0">
                            <input type="checkbox" name="payment_system_enabled" value="1" {{ ($settings['payment_system_enabled'] ?? '0') === '1' ? 'checked' : '' }} 
                                class="mt-1 w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                            <div>
                                <span class="block text-base font-bold text-slate-800 leading-tight">Enable Payment System</span>
                                <span class="block text-sm font-medium text-slate-500 mt-1">If disabled, the application is completely free for all users.</span>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Monthly Subscription Price (USD)</label>
                        <div class="relative flex items-center">
                            <span class="absolute text-slate-400 font-bold pointer-events-none" style="left: 1rem;">$</span>
                            <input type="number" step="0.01" name="subscription_price_monthly" value="{{ $settings['subscription_price_monthly'] ?? '0.50' }}" required
                                class="w-full pr-3.5 py-3 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all font-mono"
                                style="padding-left: 2.5rem;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Card: Paystack API Keys --}}
            <div class="db-card overflow-hidden flex flex-col" style="flex: 1; min-width: 300px;">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-key text-xs"></i>
                    </span>
                    <h4 class="text-lg font-bold text-slate-800 m-0">Paystack API Keys</h4>
                </div>
                
                <div class="p-6 space-y-5 flex-1">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Public Key</label>
                        <input type="text" name="paystack_public_key" value="{{ $settings['paystack_public_key'] ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Secret Key</label>
                        <input type="password" name="paystack_secret_key" value="{{ $settings['paystack_secret_key'] ?? '' }}"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all font-mono">
                    </div>
                </div>
            </div>

        </div>

        {{-- Submit Button (Bottom) --}}
        <div class="mt-8" style="display: flex; justify-content: flex-end;">
            <button type="submit" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl cursor-pointer border-0 transition-all shadow-md shadow-indigo-200 hover:shadow-lg hover:shadow-indigo-300" style="padding: 0.75rem 2rem; gap: 0.5rem;">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
