@extends('user.base-user')

@section('user-section', 'search')

@section('content')
<div class="content-section active" id="search-section">
    <!-- Header Section -->
    <div class="messages-header-toolbar">
        <h2 class="messages-header-title">
            <i class="fas fa-search text-primary messages-header-icon"></i>
            WISP Search Center
        </h2>
    </div>

    <div class="card search-main-card" style="border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 28px 24px; background: #fff;">
        <div id="search-results-container">
            @include('user.pages.general.search-results')
        </div>
    </div>
</div>

<style>
.search-result-item {
    transition: all 0.2s ease-in-out;
}
.search-result-item:hover {
    border-color: var(--primary, #F28C76) !important;
    background: #fcfdfe !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(242, 140, 118,0.06);
}
</style>

<script>
let searchDebounceTimeout = null;

// Live search trigger function
function triggerLiveSearch(query) {
    const container = document.getElementById('search-results-container');
    if (!container) return;
    
    // Clear any pending timeout to debounce typing
    clearTimeout(searchDebounceTimeout);
    
    searchDebounceTimeout = setTimeout(() => {
        // Set loading opacity
        container.style.opacity = '0.55';
        
        fetch('/user-page/search?q=' + encodeURIComponent(query), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            container.innerHTML = data.html;
            container.style.opacity = '1';
        })
        .catch(err => {
            console.error('Error fetching search results:', err);
            container.style.opacity = '1';
        });
    }, 200); // 200ms debounce
}
</script>
@endsection