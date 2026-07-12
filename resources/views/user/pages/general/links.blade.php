@extends('user.base-user')

@section('user-section', 'links')

@section('content')
<div class="content-section active" id="links">
    @if(session('generated_link'))
        <div class="alert alert-success links-alert">
            <strong>Link generated:</strong>
            <div class="generated-link links-alert-link">
                <input type="text" class="form-control" id="newly-generated-link" value="{{ session('generated_link') }}" readonly>
                <button type="button" class="btn btn-primary copy-link-btn" data-url="{{ session('generated_link') }}">Copy link</button>
            </div>
        </div>
    @endif
    <!-- Header Section -->
    <div class="messages-header-toolbar">
        <h2 class="messages-header-title">
            <i class="fas fa-link text-primary messages-header-icon"></i> 
            Generate Links
        </h2>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h3>Generated Links</h3>
            </div>
            <div class="card-body">
                @forelse(($generatedLinks ?? []) as $link)
                    @php $msg = $link->wishMessage; @endphp
                    <div class="generated-link-item links-history-item">
                        <strong>{{ $msg ? $msg->title : 'Message' }}</strong> - {{ $msg ? $msg->recipient_name : '' }}
                        <div class="generated-link links-history-input-wrap">
                            <input type="text" class="form-control form-control-sm links-history-input" value="{{ $link->generated_url }}" readonly>
                            <button type="button" class="btn btn-primary copy-link-btn" data-url="{{ $link->generated_url }}">Copy</button>
                        </div>
                    </div>
                @empty
                    <div class="empty-table-cell" style="padding: 60px 20px;">
                        <div class="empty-table-icon-wrap">
                            <i class="fas fa-link"></i>
                        </div>
                        <h4 class="empty-table-title" style="font-size: 0.95rem;">No Generated Links</h4>
                        <p class="empty-table-desc" style="font-size: 0.85rem;">Select a saved message above and click Generate Link.</p>
                    </div>
                @endforelse
            </div>
            @if(isset($generatedLinks) && $generatedLinks->hasPages())
                <div class="custom-pagination">
                    <div class="pagination-btns">
                        <button type="button" class="pagination-btn" aria-label="Previous" @if($generatedLinks->onFirstPage()) disabled @endif onclick="@if(!$generatedLinks->onFirstPage()) location.href='{{ route('user.links.page', ['links_page' => $generatedLinks->currentPage() - 1]) }}'; @endif"><i class="fas fa-chevron-left"></i> Previous</button>
                        <button type="button" class="pagination-btn" aria-label="Next" @if(!$generatedLinks->hasMorePages()) disabled @endif onclick="@if($generatedLinks->hasMorePages()) location.href='{{ route('user.links.page', ['links_page' => $generatedLinks->currentPage() + 1]) }}'; @endif">Next <i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
