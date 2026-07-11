@extends('admin.base-admin')
@section('admin-section', 'users')
@section('content')

<div class="content-section active" id="users">

    {{-- Page Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <h2 class="flex items-center gap-3 text-2xl font-bold text-slate-800 m-0">
            <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-users"></i>
            </span>
            User Management
        </h2>
        <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-indigo-200"
                id="addUserBtn">
            <i class="fas fa-plus"></i> Add New User
        </button>
    </div>

    {{-- Card --}}
    <div class="db-card overflow-hidden">

        {{-- Filter bar --}}
        <div class="flex flex-wrap gap-4 p-5 border-b border-slate-100 bg-slate-50">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
                <select id="userFilterStatus"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 cursor-pointer transition-all">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="blocked">Blocked</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Role</label>
                <select id="userFilterRole"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 cursor-pointer transition-all">
                    <option value="">All roles</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="data-table" id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Last Login</th>
                        <th>Last Logout</th>
                        <th>Time Spent</th>
                        <th>Messages</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users ?? [] as $user)
                        @php
                            /** @var \App\Models\User $user */
                            $lastLogin  = $user->last_login_at;
                            $lastLogout = $user->last_logout_at;
                            $timeSpent  = null;
                            if ($lastLogin && $lastLogout && $lastLogout->gte($lastLogin)) {
                                $mins = $lastLogin->diffInMinutes($lastLogout);
                                if ($mins >= 60) {
                                    $h = (int) floor($mins / 60);
                                    $m = $mins % 60;
                                    $timeSpent = $h . 'h' . ($m ? ' ' . $m . 'm' : '');
                                } else {
                                    $timeSpent = $mins . 'm';
                                }
                            }
                            $userRowNum = ($users->currentPage() - 1) * $users->perPage() + $loop->iteration;
                        @endphp
                        <tr data-username="{{ strtolower(e($user->username)) }}"
                            data-email="{{ strtolower(e($user->email)) }}"
                            data-status="{{ $user->status }}"
                            data-role="{{ $user->role }}"
                            data-user-id="{{ $user->id }}"
                            style="cursor: pointer;"
                            onclick="toggleUserDetails('{{ $user->id }}')">

                            <td>{{ $userRowNum }}</td>
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold
                                    {{ $user->role === 'admin' ? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($user->role ?? 'user') }}
                                </span>
                            </td>
                            <td>
                                @if($lastLogin)
                                    <div style="font-size: 0.8rem; color: #64748b;">
                                        <div style="font-weight: 500; color: #334155;">{{ $lastLogin->format('Y-m-d') }}</div>
                                        <div>{{ $lastLogin->format('H:i:s') }}</div>
                                    </div>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lastLogout)
                                    <div style="font-size: 0.8rem; color: #64748b;">
                                        <div style="font-weight: 500; color: #334155;">{{ $lastLogout->format('Y-m-d') }}</div>
                                        <div>{{ $lastLogout->format('H:i:s') }}</div>
                                    </div>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($timeSpent)
                                    <span style="font-family: monospace; background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem;">{{ $timeSpent }}</span>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #4f46e5;">{{ $user->wish_messages_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="action-btn view"
                                        data-user-id="{{ $user->id }}" data-username="{{ $user->username }}"
                                        data-email="{{ $user->email }}" data-status="{{ $user->status }}"
                                        data-role="{{ $user->role }}"
                                        data-edit-action="{{ route('admin.users.update', $user->id) }}">View</button>

                                    @if($user->role !== 'admin' || $user->id !== auth()->id())
                                        <button type="button" class="action-btn edit"
                                            onclick="openUserModal(this)"
                                            data-action="{{ route('admin.users.update', $user->id) }}"
                                            data-status="{{ $user->status }}"
                                            data-role="{{ $user->role }}">Edit</button>
                                        
                                        @if($user->status === 'blocked')
                                            <form method="POST" action="{{ route('admin.users.unblock', $user->id) }}"
                                                class="inline" style="display: inline-block; margin: 0;" onsubmit="event.preventDefault(); showAdminConfirm('Unblock this user?', () => this.submit());">
                                                @csrf
                                                <button type="submit" class="action-btn edit" style="background-color: var(--warning, #f59e0b); color: white;">Unblock</button>
                                            </form>
                                        @else
                                            <button type="button" class="action-btn edit" style="background-color: var(--warning, #f59e0b); color: white;"
                                                onclick="openBlockModal(this)"
                                                data-action="{{ route('admin.users.block', $user->id) }}">Block</button>
                                        @endif
                                        
                                        <button type="button" class="action-btn delete"
                                            onclick="openDeleteModal(this)"
                                            data-username="{{ e($user->username) }}"
                                            data-action="{{ route('admin.users.destroy', $user->id) }}">Delete</button>
                                    @else
                                        <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- User Details Dropdown Row --}}
                        <tr id="user-details-row-{{ $user->id }}" class="user-detail-dropdown-row" style="display: none;">
                            <td colspan="9" style="padding: 0; border-bottom: 2px solid var(--primary, #4f46e5);">
                                <div style="background: var(--bg-body, #f8fafc); padding: 20px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                                        
                                        {{-- Account Info Card --}}
                                        <div class="db-card" style="padding: 16px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
                                            <h5 style="margin-top: 0; margin-bottom: 12px; color: #1e293b; font-size: 0.95rem;">
                                                <i class="fas fa-user-circle text-primary" style="color: #4f46e5;"></i> Account Info
                                            </h5>
                                            <div style="margin-bottom: 8px; font-size: 0.9rem;">
                                                <strong style="color: #64748b;">Joined Date:</strong> 
                                                <span style="color: #334155; font-weight: 500;">{{ $user->created_at ? $user->created_at->format('M j, Y g:i A') : 'Unknown' }}</span>
                                            </div>
                                            <div style="margin-bottom: 8px; font-size: 0.9rem;">
                                                <strong style="color: #64748b;">Email Status:</strong> 
                                                @if(isset($user->email_verified_at) && $user->email_verified_at)
                                                    <span style="color: #10b981; font-weight: 500;"><i class="fas fa-check"></i> Verified</span>
                                                @else
                                                    <span style="color: #f59e0b; font-weight: 500;"><i class="fas fa-exclamation-triangle"></i> Pending</span>
                                                @endif
                                            </div>
                                            <div style="font-size: 0.9rem;">
                                                <strong style="color: #64748b;">Account Status:</strong> 
                                                <span style="color: #334155; font-weight: 500;">{{ ucfirst($user->status ?? 'active') }}</span>
                                            </div>
                                        </div>
                    
                                        {{-- Security & Features Card --}}
                                        <div class="db-card" style="padding: 16px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
                                            <h5 style="margin-top: 0; margin-bottom: 12px; color: #1e293b; font-size: 0.95rem;">
                                                <i class="fas fa-shield-alt" style="color: #10b981;"></i> Security & Role
                                            </h5>
                                            <div style="margin-bottom: 8px; font-size: 0.9rem; display: flex; align-items: center; justify-content: space-between;">
                                                <strong style="color: #64748b;">2FA Status:</strong> 
                                                @if(!empty($user->settings->login_passcode))
                                                    <span style="color: #10b981; font-weight: 600;"><i class="fas fa-check-circle"></i> Enabled</span>
                                                @else
                                                    <span style="color: #f43f5e; font-weight: 600;"><i class="fas fa-times-circle"></i> Disabled</span>
                                                @endif
                                            </div>
                                            <div style="margin-bottom: 8px; font-size: 0.9rem; display: flex; align-items: center; justify-content: space-between;">
                                                <strong style="color: #64748b;">User Role:</strong> 
                                                <span style="color: #334155; font-weight: 500;">{{ ucfirst($user->role ?? 'user') }}</span>
                                            </div>
                                            <div style="font-size: 0.9rem; display: flex; align-items: center; justify-content: space-between;">
                                                <strong style="color: #64748b;">Premium Account:</strong> 
                                                @if(isset($user->is_premium) && $user->is_premium)
                                                    <span style="color: #f59e0b; font-weight: 600;"><i class="fas fa-star"></i> Yes</span>
                                                @else
                                                    <span style="color: #64748b; font-weight: 500;">No</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Account Usage Card --}}
                                        <div class="db-card" style="padding: 16px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
                                            <h5 style="margin-top: 0; margin-bottom: 12px; color: #1e293b; font-size: 0.95rem;">
                                                <i class="fas fa-chart-pie text-info" style="color: #06b6d4;"></i> Account Usage
                                            </h5>
                                            <div style="margin-bottom: 8px; font-size: 0.9rem;">
                                                <strong style="color: #64748b;">Total Messages:</strong> 
                                                <span style="color: #334155; font-weight: 500;">{{ $user->wishMessages()->count() }}</span>
                                            </div>
                                            <div style="margin-bottom: 8px; font-size: 0.9rem;">
                                                <strong style="color: #64748b;">Media Files Uploaded:</strong> 
                                                <span style="color: #334155; font-weight: 500;">{{ $user->mediaFiles()->count() }}</span>
                                            </div>
                                            <div style="font-size: 0.9rem;">
                                                <strong style="color: #64748b;">Storage Used:</strong> 
                                                <span style="color: #334155; font-weight: 500; font-family: monospace; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ number_format($user->totalStorageUsage() / 1048576, 2) }} MB</span>
                                            </div>
                                        </div>
                    
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="user-row-empty">
                            <td colspan="9" class="px-4 py-12 text-center text-slate-400">
                                <i class="fas fa-users text-4xl mb-3 block opacity-30"></i>
                                No users yet.
                            </td>
                        </tr>
                    @endforelse
                    <tr class="user-row-no-results hidden">
                        <td colspan="9" class="px-4 py-10 text-center text-slate-400">
                            No users match your search or filters.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
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

<script>
    function toggleUserDetails(id) {
        if (event.target.closest('.action-btn') || event.target.closest('form')) {
            return;
        }

        const targetRow = document.getElementById(`user-details-row-${id}`);
        const isCurrentlyVisible = targetRow.style.display !== 'none';

        // Hide all others
        document.querySelectorAll('.user-detail-dropdown-row').forEach(row => {
            row.style.display = 'none';
        });

        // Toggle clicked one
        if (!isCurrentlyVisible) {
            targetRow.style.display = 'table-row';
        }
    }
</script>

@endsection