@extends('admin.base-admin')
@section('admin-section', 'subscribers')

@section('content')
<style>
    [data-theme="dark"] .btn-cancel {
        background-color: #334155 !important;
        color: #f1f5f9 !important;
        border-color: #475569 !important;
    }
    [data-theme="dark"] .btn-cancel:hover {
        background-color: #475569 !important;
    }
    [data-theme="dark"] .modal-close-btn:hover {
        color: #ffffff !important;
    }
</style>
<div class="content-section active" id="subscribers">

    {{-- Page Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <h2 class="flex items-center gap-3 text-2xl font-bold text-slate-800 m-0">
            <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-envelope-open-text"></i>
            </span>
            Subscribers
        </h2>
        <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-indigo-200"
                onclick="openBroadcastModal()">
            <i class="fas fa-paper-plane"></i> Broadcast Update
        </button>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 p-3 mb-6 bg-emerald-50 text-emerald-600 rounded-xl font-medium">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="flex items-center gap-2 p-3 mb-6 bg-red-50 text-red-600 rounded-xl font-medium">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Card --}}
    <div class="db-card overflow-hidden">
        
        {{-- Filter bar --}}
        <div class="flex flex-wrap gap-4 p-5 border-b border-slate-100 bg-slate-50" style="border-bottom-color: rgba(255,255,255,0.05);">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Search</label>
                <div class="flex items-center gap-2 px-3 rounded-xl border border-slate-200 bg-white focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100 transition-all" style="height: 38px;">
                    <i class="fas fa-search text-slate-400 text-sm"></i>
                    <input type="text" id="subscriberFilterEmail" placeholder="Search by email..."
                        class="border-0 bg-transparent outline-none text-sm text-slate-700 w-full placeholder-slate-400"
                        style="box-shadow: none;">
                </div>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Filter by Date</label>
                <input type="date" id="subscriberFilterDate"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 cursor-pointer transition-all">
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
                                    <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold tracking-wide" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">Active</span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold tracking-wide" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Unsubscribed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block opacity-50"></i>
                                No subscribers found.
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
    <div id="broadcastModal" class="modal fixed inset-0 z-[1000] hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="db-card rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden" style="animation:modalIn .2s ease; padding: 0 !important;">
            <div class="flex items-center justify-between p-5 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800 m-0">Send Broadcast Email</h3>
                <button type="button" onclick="closeBroadcastModal()" class="border-0 bg-transparent text-2xl cursor-pointer text-slate-400 hover:text-slate-600 modal-close-btn leading-none">&times;</button>
            </div>
            
            <form action="{{ route('admin.subscribers.sendUpdate') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Subject</label>
                    <input type="text" name="subject" required placeholder="e.g. New WISP Templates Available!"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-800 bg-white outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder-slate-400">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Message</label>
                    <textarea name="message" required rows="8" placeholder="Type your message here..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-800 bg-white outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all resize-y placeholder-slate-400"></textarea>
                    <p class="text-xs text-slate-500 mt-2"><i class="fas fa-info-circle mr-1"></i>This message will be sent to all active subscribers. An unsubscribe link will be attached automatically.</p>
                </div>
                
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeBroadcastModal()" class="btn-cancel px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl cursor-pointer border border-slate-200 transition-colors">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl cursor-pointer border-0 transition-colors flex items-center gap-2">
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
        modal.classList.remove('hidden');
    }
    
    function closeBroadcastModal() {
        const modal = document.getElementById('broadcastModal');
        modal.style.display = 'none';
        modal.classList.add('hidden');
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

        emailInput.addEventListener('input', filterTable);
        dateInput.addEventListener('change', filterTable);
    });
</script>
@endsection
