@extends('admin.base-admin')

@section('admin-section', 'search')

@section('content')
<div class="content-section active" id="search-section">
    <!-- Header Section -->
    <div class="admin-page-header">
        <h2 class="admin-page-title">
            <span class="admin-title-icon-box">
                <i class="fas fa-search"></i>
            </span>
            WISP Search Center
        </h2>
    </div>

    <div class="card search-main-card">
        <div id="search-results-container">
            @include('admin.pages.general.search-results')
        </div>
    </div>
</div>

<script>
let searchDebounceTimeout = null;

function triggerLiveSearch(query) {
    const container = document.getElementById('search-results-container');
    if (!container) return;
    
    clearTimeout(searchDebounceTimeout);
    
    searchDebounceTimeout = setTimeout(() => {
        container.style.opacity = '0.55';
        
        fetch('{{ route("admin.search.page") }}?q=' + encodeURIComponent(query), {
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
            console.error('Error fetching admin search results:', err);
            container.style.opacity = '1';
        });
    }, 200);
}

document.addEventListener('DOMContentLoaded', function() {
    const globalSearchInput = document.getElementById('globalSearchInput');
    if (globalSearchInput) {
        globalSearchInput.addEventListener('input', function() {
            triggerLiveSearch(this.value.trim());
        });
    }
});
</script>
@endsection
