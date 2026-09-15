@extends('admin.base-admin')
@section('admin-section', 'users')
@section('content')

<div class="content-section active" id="users">

    {{-- Page Header --}}
    <div class="admin-card-header-bar">
        <h2 class="admin-page-title">
            <span class="admin-title-icon-box">
                <i class="fas fa-users"></i>
            </span>
            User Management
        </h2>
        <button class="admin-btn admin-btn-primary" id="addUserBtn">
            <i class="fas fa-plus"></i> Add New User
        </button>
    </div>

    {{-- Card --}}
    <div class="db-card">

        {{-- Filter bar --}}
        <div class="message-filter-box">
            <div class="w-140">
                <select id="userFilterStatus" class="message-filter-select">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="blocked">Blocked</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div class="w-140">
                <select id="userFilterRole" class="message-filter-select">
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
                            class="cursor-pointer"
                            onclick="toggleUserDetails(event, '{{ $user->id }}')">

                            <td>{{ $userRowNum }}</td>
                            <td>
                                <div class="admin-card-header-left">
                                    <div class="user-avatar-circle">
                                        {{ strtoupper(substr($user->username, 0, 1)) }}
                                    </div>
                                    <span class="font-600">{{ $user->username }}</span>
                                </div>
                            </td>
                            <td class="blur-sensitive">{{ $user->email }}</td>
                            <td>
                                <span class="admin-badge {{ $user->role === 'admin' ? 'admin-badge-primary' : 'admin-badge-neutral' }}">
                                    {{ ucfirst($user->role ?? 'user') }}
                                </span>
                            </td>
                            <td>
                                @if($lastLogin)
                                    <div class="text-xs text-muted">
                                        <div class="font-500 text-dark">{{ $lastLogin->format('Y-m-d') }}</div>
                                        <div>{{ $lastLogin->format('H:i:s') }}</div>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lastLogout)
                                    <div class="text-xs text-muted">
                                        <div class="font-500 text-dark">{{ $lastLogout->format('Y-m-d') }}</div>
                                        <div>{{ $lastLogout->format('H:i:s') }}</div>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($timeSpent)
                                    <span class="storage-code-badge">{{ $timeSpent }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-600 text-primary">{{ $user->wish_messages_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="admin-action-dropdown">
                                    <button type="button" class="admin-action-trigger" onclick="toggleActionDropdown(event, {{ $user->id }})">
                                        Action <i class="fas fa-chevron-down dropdown-arrow"></i>
                                    </button>
                                    <div class="admin-action-menu" id="action-menu-{{ $user->id }}">
                                        <button type="button" class="admin-action-item"
                                            data-user-id="{{ $user->id }}" data-username="{{ $user->username }}"
                                            data-email="{{ $user->email }}" data-status="{{ $user->status }}"
                                            data-role="{{ $user->role }}"
                                            data-edit-action="{{ route('admin.users.update', $user->id) }}">
                                            <div class="icon-wrap"><i class="fas fa-eye"></i></div> View
                                            <i class="fas fa-chevron-right ml-auto text-xs opacity-50"></i>
                                        </button>

                                        @if($user->role !== 'admin' || $user->id !== auth()->id())
                                            <button type="button" class="admin-action-item"
                                                onclick="openUserModal(this)"
                                                data-action="{{ route('admin.users.update', $user->id) }}"
                                                data-status="{{ $user->status }}"
                                                data-role="{{ $user->role }}">
                                                <div class="icon-wrap"><i class="fas fa-edit"></i></div> Edit
                                                <i class="fas fa-chevron-right ml-auto text-xs opacity-50"></i>
                                            </button>
                                            
                                            @if($user->status === 'blocked')
                                                <form method="POST" action="{{ route('admin.users.unblock', $user->id) }}" class="m-0 w-full" onsubmit="event.preventDefault(); showAdminConfirm('Unblock this user?', () => this.submit());">
                                                    @csrf
                                                    <button type="submit" class="admin-action-item">
                                                        <div class="icon-wrap"><i class="fas fa-unlock"></i></div> Unblock
                                                        <i class="fas fa-chevron-right ml-auto text-xs opacity-50"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="admin-action-item"
                                                    onclick="openBlockModal(this)"
                                                    data-action="{{ route('admin.users.block', $user->id) }}">
                                                    <div class="icon-wrap"><i class="fas fa-ban"></i></div> Block
                                                    <i class="fas fa-chevron-right ml-auto text-xs opacity-50"></i>
                                                </button>
                                            @endif
                                            
                                            <div class="admin-action-divider"></div>
                                            
                                            <button type="button" class="admin-action-item delete"
                                                onclick="openDeleteModal(this)"
                                                data-username="{{ e($user->username) }}"
                                                data-action="{{ route('admin.users.destroy', $user->id) }}">
                                                <div class="icon-wrap"><i class="fas fa-trash"></i></div> Delete
                                                <i class="fas fa-chevron-right ml-auto text-xs opacity-50"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>

                        {{-- User Details Dropdown Row --}}
                        <tr id="user-details-row-{{ $user->id }}" class="user-detail-dropdown-row">
                            <td colspan="9" class="message-detail-dropdown-cell">
                                <div class="message-detail-dropdown-wrapper">
                                    <div class="message-detail-grid">
                                        
                                        {{-- Account Info Card --}}
                                        <div class="db-card user-detail-card">
                                            <h5>
                                                <i class="fas fa-user-circle text-primary"></i> Account Info
                                            </h5>
                                            <div class="user-detail-item">
                                                <strong>Joined Date:</strong> 
                                                <span>{{ $user->created_at ? $user->created_at->format('M j, Y g:i A') : 'Unknown' }}</span>
                                            </div>
                                            <div class="user-detail-item">
                                                <strong>Email Status:</strong> 
                                                @if(isset($user->email_verified_at) && $user->email_verified_at)
                                                    <span class="text-primary font-500"><i class="fas fa-check"></i> Verified</span>
                                                @else
                                                    <span class="text-muted font-500"><i class="fas fa-exclamation-triangle"></i> Pending</span>
                                                @endif
                                            </div>
                                            <div class="user-detail-item">
                                                <strong>Account Status:</strong> 
                                                <span>{{ ucfirst($user->status ?? 'active') }}</span>
                                            </div>
                                        </div>
                    
                                        {{-- Security & Features Card --}}
                                        <div class="db-card user-detail-card">
                                            <h5>
                                                <i class="fas fa-shield-alt text-primary"></i> Security & Role
                                            </h5>
                                            <div class="user-detail-item flex-between">
                                                <strong>2FA Status:</strong> 
                                                @if(!empty($user->settings->login_passcode))
                                                    <span class="text-primary font-600"><i class="fas fa-check-circle"></i> Enabled</span>
                                                @else
                                                    <span class="text-muted font-600"><i class="fas fa-times-circle"></i> Disabled</span>
                                                @endif
                                            </div>
                                            <div class="user-detail-item flex-between">
                                                <strong>User Role:</strong> 
                                                <span>{{ ucfirst($user->role ?? 'user') }}</span>
                                            </div>
                                            <div class="user-detail-item flex-between">
                                                <strong>Premium Account:</strong> 
                                                @if(isset($user->is_premium) && $user->is_premium)
                                                    <span class="text-primary font-600"><i class="fas fa-star"></i> Yes</span>
                                                @else
                                                    <span class="text-muted font-500">No</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Account Usage Card --}}
                                        <div class="db-card user-detail-card">
                                            <h5>
                                                <i class="fas fa-chart-pie text-primary"></i> Account Usage
                                            </h5>
                                            <div class="user-detail-item">
                                                <strong>Total Messages:</strong> 
                                                <span>{{ $user->wishMessages()->count() }}</span>
                                            </div>
                                            <div class="user-detail-item">
                                                <strong>Media Files Uploaded:</strong> 
                                                <span>{{ $user->mediaFiles()->count() }}</span>
                                            </div>
                                            <div class="user-detail-item">
                                                <strong>Storage Used:</strong> 
                                                <span class="storage-code-badge">{{ number_format($user->totalStorageUsage() / 1048576, 2) }} MB</span>
                                            </div>
                                        </div>
                    
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="user-row-empty">
                            <td colspan="9" class="admin-table-empty">
                                <i class="fas fa-users text-4xl mb-3 block opacity-30"></i>
                                No users yet.
                            </td>
                        </tr>
                    @endforelse
                    <tr class="user-row-no-results hidden">
                        <td colspan="9" class="admin-table-empty">
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
    function toggleUserDetails(event, id) {
        if (event && (event.target.closest('.admin-action-dropdown') || event.target.closest('.admin-action-trigger') || event.target.closest('form') || event.target.closest('button') || event.target.closest('a'))) {
            return;
        }

        const targetRow = document.getElementById(`user-details-row-${id}`);
        if (!targetRow) return;

        const isCurrentlyVisible = targetRow.classList.contains('show') || targetRow.style.display === 'table-row';

        // Hide all others
        document.querySelectorAll('.user-detail-dropdown-row').forEach(row => {
            row.classList.remove('show');
            row.style.display = 'none';
        });

        // Toggle clicked one
        if (!isCurrentlyVisible) {
            targetRow.classList.add('show');
            targetRow.style.display = 'table-row';
        }
    }
</script>

<script>
    function toggleActionDropdown(e, id) {
        e.preventDefault();
        e.stopPropagation();
        
        const menu = document.getElementById(`action-menu-${id}`);
        const trigger = e.currentTarget;
        const isCurrentlyShown = menu.classList.contains('show');

        // Close all other dropdowns first
        closeAllActionMenus();

        if (isCurrentlyShown) {
            return;
        }

        const rect = trigger.getBoundingClientRect();
        const menuWidth = 200;
        
        // Use fixed positioning to escape all table & card overflow clipping
        menu.style.position = 'fixed';
        menu.style.zIndex = '999999';
        menu.style.left = `${Math.max(10, rect.right - menuWidth)}px`;

        const spaceBelow = window.innerHeight - rect.bottom;
        const menuHeight = 220;

        if (spaceBelow < menuHeight) {
            // Open upwards above the action button
            menu.style.top = 'auto';
            menu.style.bottom = `${window.innerHeight - rect.top + 6}px`;
        } else {
            // Open downwards below the action button
            menu.style.top = `${rect.bottom + 6}px`;
            menu.style.bottom = 'auto';
        }

        menu.classList.add('show');
        trigger.classList.add('active');
    }

    function closeAllActionMenus() {
        document.querySelectorAll('.admin-action-menu').forEach(menu => {
            menu.classList.remove('show');
            if (menu.previousElementSibling) {
                menu.previousElementSibling.classList.remove('active');
            }
        });
    }

    // Close dropdowns when scrolling, resizing, or clicking outside
    document.addEventListener('scroll', closeAllActionMenus, true);
    window.addEventListener('resize', closeAllActionMenus);
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.admin-action-dropdown')) {
            closeAllActionMenus();
        }
    });
</script>

@endsection