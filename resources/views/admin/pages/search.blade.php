@extends('admin.base-admin')
@section('admin-section', 'search')
@section('content')

<div class="content-section active" id="search">
    {{-- Page Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <h2 class="flex items-center gap-3 text-2xl font-bold text-slate-800 m-0">
            <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-search"></i>
            </span>
            Search Results
        </h2>
    </div>

    <div class="mb-6">
        <p class="text-slate-600 font-medium">Showing results for: <span class="font-bold text-indigo-600">"{{ $query }}"</span></p>
    </div>

    {{-- Users Results --}}
    <div class="db-card overflow-hidden mb-6">
        <div class="flex flex-wrap gap-4 p-5 border-b border-slate-100 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800 m-0"><i class="fas fa-users mr-2 text-indigo-500"></i> Users ({{ count($users) }})</h3>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; flex-shrink: 0;">
                                        {{ strtoupper(substr($user->username, 0, 1)) }}
                                    </div>
                                    <span style="font-weight: 600;">{{ $user->username }}</span>
                                </div>
                            </td>
                            <td class="blur-sensitive">{{ $user->email }}</td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold {{ $user->role === 'admin' ? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($user->role ?? 'user') }}
                                </span>
                            </td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700"><i class="fas fa-check-circle mr-1.5"></i>Active</span>
                                @elseif($user->status === 'blocked')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-700"><i class="fas fa-ban mr-1.5"></i>Blocked</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700"><i class="fas fa-clock mr-1.5"></i>{{ ucfirst($user->status ?? 'Pending') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.users.page') }}?search={{ urlencode($user->email) }}" class="action-btn view inline-block text-center" style="text-decoration: none;">View in Users</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400">
                                <i class="fas fa-users text-4xl mb-3 block opacity-30"></i>
                                No users found for this query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Messages Results --}}
    <div class="db-card overflow-hidden">
        <div class="flex flex-wrap gap-4 p-5 border-b border-slate-100 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800 m-0"><i class="fas fa-envelope mr-2 text-indigo-500"></i> Messages ({{ count($messages) }})</h3>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Recipient</th>
                        <th>Creator</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($messages as $msg)
                        <tr>
                            <td>{{ $msg->title ?? '-' }}</td>
                            <td class="blur-sensitive">{{ $msg->recipient_name ?? $msg->recipient_special_name ?? '-' }}</td>
                            <td>{{ $msg->user ? $msg->user->username : '-' }}</td>
                            <td>{{ $msg->is_published ? 'Published' : ($msg->expires_at && $msg->expires_at->isPast() ? 'Expired' : 'Draft') }}</td>
                            <td>
                                <a href="{{ route('admin.messages.page') }}" class="action-btn view inline-block text-center" style="text-decoration: none;">View in Messages</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400">
                                <i class="fas fa-envelope text-4xl mb-3 block opacity-30"></i>
                                No messages found for this query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
