@extends('admin.base-admin')

@section('admin-section', 'billing')

@section('content')
<div class="content-section active" id="billing">
    <!-- Header Section -->
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <h2 class="flex items-center gap-3 text-2xl font-bold text-slate-800 m-0">
            <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-file-invoice-dollar opacity-90"></i>
            </span>
            Billing &amp; Subscriptions
        </h2>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.billing.settings') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-indigo-200 no-underline">
                <i class="fas fa-cog"></i> Settings
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-semibold text-sm flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Recent Subscriptions -->
    <div class="db-card overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800 m-0">Recent Subscriptions</h3>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subscriptions ?? [] as $sub)
                        <tr>
                            <td>
                                <div class="font-bold text-slate-800">{{ $sub->user->username ?? 'Unknown' }}</div>
                            </td>
                            <td class="font-semibold text-slate-700">
                                ${{ number_format($sub->amount, 2) }}
                            </td>
                            <td class="text-slate-600 capitalize">
                                {{ $sub->payment_method }}
                            </td>
                            <td class="text-slate-500 font-mono">
                                {{ $sub->reference_code }}
                            </td>
                            <td>
                                @if($sub->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">Active</span>
                                @elseif($sub->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700">Pending</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-700 capitalize">{{ $sub->status }}</span>
                                @endif
                            </td>
                            <td class="text-slate-500 whitespace-nowrap">
                                {{ $sub->created_at->format('M j, Y H:i') }}
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    @if($sub->status === 'pending')
                                        <form method="POST" action="{{ route('admin.billing.approve', $sub->id) }}" class="m-0 p-0">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition-colors border-0 cursor-pointer" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.billing.reject', $sub->id) }}" class="m-0 p-0">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors border-0 cursor-pointer" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 font-bold">&mdash;</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <h4 class="text-base font-bold text-slate-700 m-0 mb-1">No Subscriptions Found</h4>
                                <p class="text-sm font-medium text-slate-500 m-0">No active or pending subscriptions right now.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($subscriptions, 'currentPage') && $subscriptions->lastPage() > 0)
            <div class="custom-pagination">
                <div class="pagination-info">
                    Showing page {{ $subscriptions->currentPage() }} of {{ $subscriptions->lastPage() }}
                </div>
                <div class="pagination-btns">
                    <button type="button" class="pagination-btn" aria-label="Previous" @if($subscriptions->onFirstPage()) disabled @endif onclick="@if(!$subscriptions->onFirstPage()) location.href='{{ $subscriptions->previousPageUrl() }}'; @endif"><i class="fas fa-chevron-left"></i> Previous</button>
                    <button type="button" class="pagination-btn" aria-label="Next" @if(!$subscriptions->hasMorePages()) disabled @endif onclick="@if($subscriptions->hasMorePages()) location.href='{{ $subscriptions->nextPageUrl() }}'; @endif">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
