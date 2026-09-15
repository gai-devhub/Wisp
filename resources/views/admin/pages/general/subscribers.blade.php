@extends('admin.base-admin')
@section('admin-section', 'subscribers')

@section('content')
<div class="content-section active" id="subscribers">

    {{-- Page Header --}}
    <div class="admin-card-header-bar">
        <h2 class="admin-page-title">
            <span class="admin-title-icon-box">
                <i class="fas fa-envelope-open-text"></i>
            </span>
            Subscribers
        </h2>
        <button class="admin-btn admin-btn-primary" onclick="openBroadcastModal()">
            <i class="fas fa-paper-plane"></i> Broadcast Update
        </button>
    </div>

    @if(session('success'))
        <div class="admin-alert admin-alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="admin-alert admin-alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Card --}}
    <div class="db-card">
        
        {{-- Filter bar --}}
        <div class="message-filter-box subscriber-filter-box">
            <div class="header-search-wrap subscriber-search-email">
                <i class="fas fa-search header-search-icon-inner"></i>
                <input type="text" id="subscriberFilterEmail" placeholder="Search email..." class="header-search-input">
            </div>
            <div class="header-search-wrap subscriber-search-date">
                <i class="fas fa-calendar-alt header-search-icon-inner"></i>
                <input type="date" id="subscriberFilterDate" class="header-search-input subscriber-date-input">
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="data-table" id="subscribersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Date Subscribed</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $subscriber)
                        <tr data-email="{{ strtolower(e($subscriber->email)) }}"
                            data-date="{{ $subscriber->created_at->format('Y-m-d') }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-semibold">{{ $subscriber->email }}</td>
                            <td>{{ $subscriber->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                @if($subscriber->status === 'active')
                                    <span class="admin-badge admin-badge-success">Active</span>
                                @else
                                    <span class="admin-badge admin-badge-neutral">Unsubscribed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="admin-table-empty">
                                <i class="fas fa-inbox"></i>
                                <div class="admin-table-empty-title">No subscribers found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($subscribers, 'currentPage') && $subscribers->lastPage() > 0)
            <div class="custom-pagination">
                <div class="pagination-info">
                    Showing page {{ $subscribers->currentPage() }} of {{ $subscribers->lastPage() }}
                </div>
                <div class="pagination-btns">
                    <button type="button" class="pagination-btn" aria-label="Previous" @if($subscribers->onFirstPage()) disabled @endif onclick="@if(!$subscribers->onFirstPage()) location.href='{{ $subscribers->previousPageUrl() }}'; @endif"><i class="fas fa-chevron-left"></i> Previous</button>
                    <button type="button" class="pagination-btn" aria-label="Next" @if(!$subscribers->hasMorePages()) disabled @endif onclick="@if($subscribers->hasMorePages()) location.href='{{ $subscribers->nextPageUrl() }}'; @endif">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        @endif
    </div>

    <!-- Broadcast Modal -->
    <div id="broadcastModal" class="broadcast-modal-backdrop" onclick="if(event.target===this) closeBroadcastModal()">
        <div class="broadcast-modal-card">
            <div class="broadcast-modal-header">
                <h3 class="admin-section-title">
                    <i class="fas fa-paper-plane text-primary"></i> Send Broadcast Email
                </h3>
                <button type="button" onclick="closeBroadcastModal()" class="broadcast-modal-close" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.subscribers.sendUpdate') }}" method="POST" class="admin-form-padding">
                @csrf
                <div class="form-group-mb">
                    <label class="admin-form-label">Subject</label>
                    <input type="text" name="subject" required placeholder="e.g. New WISP Templates Available!" class="admin-form-input">
                </div>
                
                <div class="form-group-mb-lg">
                    <label class="admin-form-label">Message</label>
                    <textarea name="message" required rows="6" placeholder="Type your message here..." class="admin-form-input admin-textarea-resizable"></textarea>
                    <p class="admin-form-help">
                        <i class="fas fa-info-circle text-primary help-icon"></i>
                        <span>This message will be sent to all active subscribers. An unsubscribe link will be attached automatically.</span>
                    </p>
                </div>
                
                <div class="modal-footer-actions">
                    <button type="button" onclick="closeBroadcastModal()" class="admin-btn admin-btn-secondary">Cancel</button>
                    <button type="submit" class="admin-btn admin-btn-primary">
                        <i class="fas fa-paper-plane"></i> Send to All Active
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openBroadcastModal() {
        const modal = document.getElementById('broadcastModal');
        modal.style.display = 'flex';
    }
    
    function closeBroadcastModal() {
        const modal = document.getElementById('broadcastModal');
        modal.style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const emailInput = document.getElementById('subscriberFilterEmail');
        const dateInput = document.getElementById('subscriberFilterDate');
        const rows = document.querySelectorAll('#subscribersTable tbody tr[data-email]');

        function filterTable() {
            const emailTerm = emailInput.value.toLowerCase();
            const dateTerm = dateInput.value;

            rows.forEach(row => {
                const email = row.getAttribute('data-email');
                const date = row.getAttribute('data-date');
                
                const matchEmail = email.includes(emailTerm);
                const matchDate = dateTerm === '' || date === dateTerm;

                if (matchEmail && matchDate) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        emailInput?.addEventListener('input', filterTable);
        dateInput?.addEventListener('change', filterTable);
    });
</script>
@endsection
