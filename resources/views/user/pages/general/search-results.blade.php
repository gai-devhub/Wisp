@if(empty($searchQuery))
    <div style="text-align: center; padding: 30px 20px 20px 20px;">
        <div style="font-size: 2.2rem; color: #cbd5e1; margin-bottom: 12px;">
            <i class="fas fa-search"></i>
        </div>
        <h4 style="font-size: 1.1rem; font-weight: 600; color: #0f172a; margin: 0 0 6px 0;">WISP Navigation &amp; Search</h4>
        <p style="color: #94a3b8; font-size: 0.85rem; margin: 0; max-width: 320px; margin: 0 auto; line-height: 1.4;">Type to filter pages, search messages, and retrieve snippets.</p>
    </div>
@endif

@if(!empty($searchQuery) && empty($matchedSystemPages) && (!isset($searchResults['messages']) || count($searchResults['messages']) === 0) && (!isset($searchResults['snippets']) || count($searchResults['snippets']) === 0))
    <div style="text-align: center; padding: 60px 20px;">
        <div style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px;">
            <i class="fas fa-search-minus"></i>
        </div>
        <h4 style="font-size: 1.15rem; font-weight: 600; color: #0f172a; margin: 0 0 6px 0;">No results found for "{{ $searchQuery }}"</h4>
        <p style="color: #94a3b8; font-size: 0.88rem; margin: 0;">Try adjusting your keywords or try searching for things like "account" or "theme".</p>
    </div>
@else
    <div class="search-results-grid" style="display: flex; flex-direction: column; gap: 28px;">
        
        {{-- Matched System Pages --}}
        @if(!empty($matchedSystemPages))
            <div>
                <h6 style="font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-directions" style="color: var(--primary, #6366f1);"></i> Quick Navigation
                </h6>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
                    @foreach($matchedSystemPages as $page)
                        <a href="{{ $page['url'] }}" style="text-decoration: none;">
                            <div class="search-result-item" style="display: flex; align-items: center; gap: 14px; padding: 14px; background: #f8fafc; border-radius: 12px; border: 1.5px solid #e2e8f0; transition: all 0.2s; cursor: pointer;">
                                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(99,102,241,0.08); color: var(--primary, #6366f1); display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0;">
                                    <i class="{{ $page['icon'] }}"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <strong style="display: block; font-size: 0.92rem; color: #1e293b; font-weight: 600; margin-bottom: 2px;">{{ $page['title'] }}</strong>
                                    <span style="display: block; font-size: 0.8rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $page['description'] }}</span>
                                </div>
                                <i class="fas fa-chevron-right" style="color: #cbd5e1; font-size: 0.75rem;"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Found Messages --}}
        @if(!empty($searchResults['messages']) && count($searchResults['messages']) > 0)
            <div>
                <h6 style="font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-envelope" style="color: #22c55e;"></i> Matching Messages ({{ count($searchResults['messages']) }})
                </h6>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
                    @foreach($searchResults['messages'] as $msg)
                        <a href="{{ route('user.edit-messages.page', $msg->id) }}" style="text-decoration: none;">
                            <div class="search-result-item" style="display: flex; align-items: center; gap: 14px; padding: 14px; background: #f8fafc; border-radius: 12px; border: 1.5px solid #e2e8f0; transition: all 0.2s;">
                                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(34,197,94,0.08); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0;">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <strong style="display: block; font-size: 0.92rem; color: #1e293b; font-weight: 600; margin-bottom: 2px;">{{ $msg->title }}</strong>
                                    <span style="display: block; font-size: 0.8rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">To: {{ $msg->recipient_name }}</span>
                                </div>
                                <i class="fas fa-chevron-right" style="color: #cbd5e1; font-size: 0.75rem;"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Found Snippets --}}
        @if(!empty($searchResults['snippets']) && count($searchResults['snippets']) > 0)
            <div>
                <h6 style="font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-bookmark" style="color: #f59e0b;"></i> Matching Snippets ({{ count($searchResults['snippets']) }})
                </h6>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($searchResults['snippets'] as $snippet)
                        <div style="display: flex; align-items: flex-start; gap: 14px; padding: 14px; background: #f8fafc; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(245,158,11,0.08); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0; margin-top: 2px;">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <strong style="display: block; font-size: 0.92rem; color: #1e293b; margin-bottom: 4px;">{{ $snippet->message_title }}</strong>
                                <span style="font-size: 0.85rem; color: #64748b; font-style: italic;">"{{ $snippet->snippet_text }}"</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endif
