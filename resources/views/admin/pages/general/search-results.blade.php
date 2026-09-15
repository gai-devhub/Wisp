@if(empty($query))
    <div class="search-hero-box">
        <div class="search-hero-icon">
            <i class="fas fa-search"></i>
        </div>
        <h4 class="search-hero-title">WISP Navigation &amp; Search</h4>
        <p class="search-hero-subtitle">Type to filter pages, search messages, and retrieve snippets.</p>
    </div>
@endif

@if(!empty($query) && empty($matchedSystemPages) && (!isset($users) || count($users) === 0) && (!isset($messages) || count($messages) === 0))
    <div class="search-empty-box">
        <div class="search-empty-icon">
            <i class="fas fa-search-minus"></i>
        </div>
        <h4 class="search-empty-title">No results found for "{{ $query }}"</h4>
        <p class="search-empty-desc">Try adjusting your keywords or try searching for things like "users", "messages" or "system".</p>
    </div>
@else
    <div class="search-results-grid">

        {{-- Matched System Pages / Quick Navigation --}}
        @if(!empty($matchedSystemPages))
            <div class="search-result-group">
                <h6 class="search-section-header">
                    <i class="fas fa-directions search-section-icon"></i> Quick Navigation
                </h6>
                <div class="quick-nav-grid">
                    @foreach($matchedSystemPages as $page)
                        <a href="{{ $page['url'] }}" class="quick-nav-link">
                            <div class="quick-nav-card search-result-item">
                                <div class="quick-nav-icon-badge">
                                    <i class="{{ $page['icon'] }}"></i>
                                </div>
                                <div class="quick-nav-info">
                                    <strong class="quick-nav-title">{{ $page['title'] }}</strong>
                                    <span class="quick-nav-desc">{{ $page['description'] }}</span>
                                </div>
                                <i class="fas fa-chevron-right quick-nav-arrow"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Matched Users --}}
        @if(!empty($users) && count($users) > 0)
            <div class="search-result-group">
                <h6 class="search-section-header">
                    <i class="fas fa-users search-section-icon-blue"></i> Matching Users ({{ count($users) }})
                </h6>
                <div class="quick-nav-grid">
                    @foreach($users as $user)
                        <a href="{{ route('admin.users.page') }}?search={{ urlencode($user->email) }}" class="quick-nav-link">
                            <div class="quick-nav-card search-result-item">
                                <div class="quick-nav-icon-badge search-badge-user">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                                <div class="quick-nav-info">
                                    <div class="search-user-header-row">
                                        <strong class="quick-nav-title">{{ $user->username }}</strong>
                                        <span class="admin-badge {{ $user->role === 'admin' ? 'badge-active' : 'admin-badge-neutral' }} search-role-badge">
                                             {{ ucfirst($user->role ?? 'user') }}
                                        </span>
                                    </div>
                                    <span class="quick-nav-desc blur-sensitive">{{ $user->email }}</span>
                                </div>
                                <i class="fas fa-chevron-right quick-nav-arrow"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Matched Messages --}}
        @if(!empty($messages) && count($messages) > 0)
            <div class="search-result-group">
                <h6 class="search-section-header">
                    <i class="fas fa-envelope search-section-icon-green"></i> Matching Messages ({{ count($messages) }})
                </h6>
                <div class="quick-nav-grid">
                    @foreach($messages as $msg)
                        <a href="{{ route('admin.messages.page') }}" class="quick-nav-link">
                            <div class="quick-nav-card search-result-item">
                                <div class="quick-nav-icon-badge search-badge-green">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="quick-nav-info">
                                    <div class="search-user-header-row">
                                        <strong class="quick-nav-title">{{ $msg->title ?? 'Untitled Message' }}</strong>
                                        <span class="admin-badge {{ $msg->is_published ? 'badge-active' : 'admin-badge-neutral' }} search-role-badge">
                                            {{ $msg->is_published ? 'Published' : ($msg->expires_at && $msg->expires_at->isPast() ? 'Expired' : 'Draft') }}
                                        </span>
                                    </div>
                                    <span class="quick-nav-desc">
                                        To: <span class="blur-sensitive">{{ $msg->recipient_name ?? $msg->recipient_special_name ?? 'Anonymous' }}</span>
                                        @if($msg->user) &bull; By: {{ $msg->user->username }} @endif
                                    </span>
                                </div>
                                <i class="fas fa-chevron-right quick-nav-arrow"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endif
