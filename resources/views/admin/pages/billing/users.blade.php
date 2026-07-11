@extends('admin.base-admin')

@section('admin-section', 'billing')

@section('content')
<div class="content-section active" id="billing">
    <!-- Header Section -->
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <h2 class="flex items-center gap-3 text-2xl font-bold text-slate-800 m-0">
            <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-users opacity-90"></i>
            </span>
            Users &amp; Premium Access
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

    <!-- Grant Premium Access Panel -->
    <div class="db-card overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800 m-0 flex items-center gap-2">
                <i class="fas fa-crown text-amber-500"></i> Grant Premium Access
            </h3>
            <span class="text-xs text-slate-500 font-medium">Manually control user premium status</span>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Expires</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users ?? [] as $u)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <img src="{{ $u->profile_picture_url }}" alt="avatar" class="w-7 h-7 rounded-full object-cover">
                                    <span class="font-bold text-slate-800">{{ $u->name }}</span>
                                    <span class="text-slate-400 text-xs">&#64;{{ $u->username }}</span>
                                </div>
                            </td>
                            <td class="text-slate-500 text-sm">{{ $u->email }}</td>
                            <td>
                                @if($u->is_premium && $u->admin_granted)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700">
                                        <i class="fas fa-crown"></i> Admin Granted
                                    </span>
                                @elseif($u->is_premium)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">
                                        <i class="fas fa-gem"></i> Premium
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-500">
                                        <i class="fas fa-lock"></i> Free
                                    </span>
                                @endif
                            </td>
                            <td class="text-slate-500 text-sm">
                                @if($u->premium_expires)
                                    {{ $u->premium_expires->format('M j, Y') }}
                                @elseif($u->is_premium && $u->admin_granted)
                                    <span class="text-amber-600 font-semibold">Unlimited</span>
                                @else
                                    &mdash;
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($u->is_premium && $u->admin_granted)
                                        <form method="POST" action="{{ route('admin.billing.revoke', $u->id) }}" class="m-0 p-0">
                                            @csrf
                                            <button type="button"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border-0 cursor-pointer transition-colors"
                                                onclick="window.showAdminConfirm('Revoke premium access for {{ addslashes($u->name) }}?', () => this.closest('form').submit())">
                                                <i class="fas fa-times"></i> Revoke
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.billing.grant', $u->id) }}" class="m-0 p-0">
                                            @csrf
                                            <button type="button"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold rounded-lg border-0 cursor-pointer transition-colors"
                                                onclick="window.showAdminConfirm('Grant premium access for {{ addslashes($u->name) }}?', () => this.closest('form').submit())">
                                                <i class="fas fa-crown"></i> Grant Premium
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-sm">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($users, 'currentPage') && $users->lastPage() > 0)
            <div class="custom-pagination">
                <div class="pagination-info">
                    Showing page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                </div>
                <div class="pagination-btns">
                    <button type="button" class="pagination-btn" aria-label="Previous" @if($users->onFirstPage()) disabled @endif onclick="@if(!$users->onFirstPage()) location.href='{{ $users->previousPageUrl() }}'; @endif"><i class="fas fa-chevron-left"></i> Previous</button>
                    <button type="button" class="pagination-btn" aria-label="Next" @if(!$users->hasMorePages()) disabled @endif onclick="@if($users->hasMorePages()) location.href='{{ $users->nextPageUrl() }}'; @endif">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
