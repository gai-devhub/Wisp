@extends('admin.base-admin')

@section('admin-section', 'finances-transactions')

@section('content')
<div class="min-h-screen pb-10 font-sans bg-transparent" id="finances">
    @if(session('success'))
        <div class="bg-green-100/90 text-green-800 px-5 py-4 rounded-xl mb-6 border border-green-200 font-medium flex items-center shadow-sm backdrop-blur-sm">
            <i class="fas fa-check-circle mr-2 text-lg"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100/90 text-red-800 px-5 py-4 rounded-xl mb-6 border border-red-200 font-medium shadow-sm backdrop-blur-sm">
            <ul class="list-disc pl-5 m-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Statement Header --}}
    <div style="background: linear-gradient(135deg, #1e1b4b, #3730a3, #4f46e5); color: white; border-radius: 24px; padding: 2.5rem; margin-bottom: 2rem; box-shadow: 0 20px 25px -5px rgba(49, 46, 129, 0.2), 0 10px 10px -5px rgba(49, 46, 129, 0.1); display: flex; flex-direction: row; justify-content: space-between; align-items: center; position: relative; overflow: hidden; flex-wrap: wrap; gap: 1.5rem;">
        <!-- Decorative Background Circle -->
        <div style="position: absolute; top: -8rem; right: -8rem; width: 24rem; height: 24rem; background: rgba(255,255,255,0.05); border-radius: 50%; filter: blur(48px);"></div>
        
        <div style="position: relative; z-index: 10;">
            <div style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #c7d2fe; margin-bottom: 0.5rem; font-weight: 600;">Operating Account Balance</div>
            <div style="font-size: 3rem; font-weight: 700; display: flex; align-items: center; gap: 0.75rem; margin: 0;">
                ${{ number_format($netTotal, 2) }}
            </div>
        </div>
        <div style="position: relative; z-index: 10; text-align: right;">
            <div style="font-size: 0.75rem; color: #a5b4fc; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.25rem;">Statement Period</div>
            <div style="font-size: 1.125rem; font-weight: 500; letter-spacing: 0.025em; color: white;">All Time ({{ \Carbon\Carbon::now()->format('M Y') }})</div>
        </div>
    </div>

    {{-- Transaction History Card --}}
    <div class="db-card overflow-hidden">
        {{-- Filter bar --}}
        <div class="flex flex-wrap gap-4 p-5 border-b border-slate-100 bg-slate-50">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5" style="display: block; margin-bottom: 0.375rem; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Type</label>
                <select class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 cursor-pointer transition-all" style="width: 100%; border-radius: 0.75rem; border: 1px solid #e2e8f0; padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                    <option value="">All Transactions</option>
                    <option value="income">Deposits (Income)</option>
                    <option value="expense">Withdrawals (Expenses)</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5" style="display: block; margin-bottom: 0.375rem; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Search</label>
                <div class="relative" style="position: relative;">
                    <i class="fas fa-search absolute text-slate-400" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="text" placeholder="Search by description or category..." class="w-full rounded-xl text-sm outline-none transition-colors focus:border-indigo-500 bg-white" style="width: 100%; border-radius: 0.75rem; border: 1px solid #e2e8f0; padding: 0.5rem 1rem 0.5rem 2.5rem; font-size: 0.875rem; outline: none; background: white;">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table" id="transactionsTable">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Category / Tag</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td>
                                <div class="font-bold text-slate-900 text-sm">{{ $tx->date->format('M d, Y') }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $tx->date->format('g:i A') }}</div>
                            </td>
                            <td>
                                @if($tx->user_name)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; flex-shrink: 0;">
                                            {{ strtoupper(substr($tx->user_name, 0, 1)) }}
                                        </div>
                                        <span style="font-weight: 600; font-size: 0.875rem; color: #334155;">{{ $tx->user_name }}</span>
                                    </div>
                                @else
                                    <span style="font-size: 0.875rem; color: #94a3b8; font-style: italic;">System / Admin</span>
                                @endif
                            </td>
                            <td>
                                @if($tx->type === 'income')
                                    <span class="inline-flex items-center px-2 py-1 text-[10px] font-bold rounded border tracking-wider" style="background-color: #dcfce7; color: #15803d; border-color: #bbf7d0;"><i class="fas fa-arrow-down mr-1"></i> DEPOSIT</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 text-[10px] font-bold rounded border tracking-wider" style="background-color: #fee2e2; color: #b91c1c; border-color: #fecaca;"><i class="fas fa-arrow-up mr-1"></i> WITHDRAWAL</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="{{ $tx->type === 'income' ? 'background-color: #dcfce7; color: #16a34a;' : 'background-color: #fee2e2; color: #dc2626;' }}">
                                        <i class="fas {{ $tx->type === 'income' ? 'fa-wallet' : 'fa-receipt' }}"></i>
                                    </div>
                                    <span class="text-slate-900 font-bold text-sm">{{ $tx->category }}</span>
                                </div>
                            </td>
                            <td class="text-slate-600 text-sm">
                                {{ $tx->description ?? 'No description provided' }}
                            </td>
                            <td class="font-extrabold text-base" style="{{ $tx->type === 'income' ? 'color: #16a34a;' : 'color: #0f172a;' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="action-btn view" onclick="viewReceipt({
                                        date: '{{ $tx->date->format('M d, Y g:i A') }}',
                                        type: '{{ $tx->type }}',
                                        category: '{{ addslashes($tx->category) }}',
                                        description: '{{ addslashes($tx->description ?? 'No description provided') }}',
                                        amount: '{{ number_format($tx->amount, 2) }}',
                                        user_name: '{{ addslashes($tx->user_name ?? '') }}',
                                        full_name: '{{ addslashes($tx->full_name ?? '') }}',
                                        raw_date: '{{ $tx->date->format("Y-m-d") }}'
                                    })">View</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="custom-pagination">
            <div class="pagination-info">
                Showing page 1 of 1
            </div>
            <div class="pagination-btns">
                <button type="button" class="pagination-btn" aria-label="Previous" disabled><i class="fas fa-chevron-left"></i> Previous</button>
                <button type="button" class="pagination-btn" aria-label="Next" disabled>Next <i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>

{{-- Receipt Modal --}}
<div id="receiptModal" class="modal" style="position: fixed; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; max-width: 400px; width: 90%; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); position: relative; overflow: hidden; margin: auto;">
        <!-- Close Button -->
        <button type="button" onclick="document.getElementById('receiptModal').classList.remove('active');" style="position: absolute; top: 1.25rem; right: 1.25rem; background: transparent; border: none; font-size: 1.25rem; color: #94a3b8; cursor: pointer; z-index: 10;">
            <i class="fas fa-times"></i>
        </button>
        
        <div style="background: #f8fafc; padding: 2rem; text-align: center; border-bottom: 1px solid #f1f5f9;">
            <div id="receiptIcon" style="width: 4rem; height: 4rem; border-radius: 50%; margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                <i class="fas fa-receipt"></i>
            </div>
            <div id="receiptAmount" style="font-size: 1.875rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">$0.00</div>
            <div id="receiptType" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Transaction Type</div>
        </div>
        
        <div style="padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                <span style="font-size: 0.875rem; font-weight: 600; color: #64748b;">Date</span>
                <span id="receiptDate" style="font-size: 0.875rem; font-weight: 700; color: #0f172a;">N/A</span>
            </div>
            <div id="receiptUserRow" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                <span style="font-size: 0.875rem; font-weight: 600; color: #64748b;">Customer</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: #0f172a;"><span id="receiptFullName">N/A</span> (<span id="receiptUsername">N/A</span>)</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                <span style="font-size: 0.875rem; font-weight: 600; color: #64748b;">Category</span>
                <span id="receiptCategory" style="font-size: 0.875rem; font-weight: 700; color: #0f172a;">N/A</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem; padding: 1rem 0;">
                <span style="font-size: 0.875rem; font-weight: 600; color: #64748b;">Description / Memo</span>
                <span id="receiptDesc" style="font-size: 0.875rem; font-weight: 500; color: #334155; background: #f8fafc; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #f1f5f9;">N/A</span>
            </div>
            
            <button type="button" id="receiptPrintBtn" onclick="window.print()" style="width: 100%; margin-top: 1rem; padding: 0.75rem; border-radius: 0.75rem; border: 2px solid #e2e8f0; background: white; color: #475569; font-weight: 700; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-sizing: border-box;">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <div id="receiptPrintUrl" style="display: none; text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: #64748b; font-family: monospace;">
                <!-- URL injected by JS -->
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: 80mm 180mm;
        margin: 0;
    }
    body * {
        visibility: hidden;
    }
    #receiptModal, #receiptModal * {
        visibility: visible;
    }
    #receiptModal {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }
    #receiptModal > div {
        box-shadow: none !important;
        max-width: 100% !important;
        width: 100% !important;
        border-radius: 0 !important;
    }
    #receiptPrintBtn, #receiptModal .fa-times {
        display: none !important;
    }
    #receiptPrintUrl {
        display: block !important;
    }
}
</style>

<script>
    function viewReceipt(tx) {
        document.getElementById('receiptDate').innerText = tx.date;
        document.getElementById('receiptCategory').innerText = tx.category;
        document.getElementById('receiptDesc').innerText = tx.description;
        
        let userRow = document.getElementById('receiptUserRow');
        if (tx.user_name) {
            userRow.style.display = 'flex';
            document.getElementById('receiptUsername').innerText = tx.user_name;
            document.getElementById('receiptFullName').innerText = tx.full_name || 'N/A';
        } else {
            userRow.style.display = 'none';
        }
        
        let amtEl = document.getElementById('receiptAmount');
        let iconEl = document.getElementById('receiptIcon');
        let typeEl = document.getElementById('receiptType');
        let printUrlEl = document.getElementById('receiptPrintUrl');
        
        // Build the URL text for printing
        let usernameStr = tx.user_name ? encodeURIComponent(tx.user_name) : 'expense';
        let dateStr = tx.raw_date;
        printUrlEl.innerText = window.location.host + '/payment/' + dateStr + '/' + usernameStr;
        
        if (tx.type === 'income') {
            amtEl.innerText = '+$' + tx.amount;
            amtEl.style.color = '#16a34a'; // green-600
            typeEl.innerText = 'Payment Received';
            iconEl.innerHTML = '<i class="fas fa-arrow-down"></i>';
            iconEl.style.backgroundColor = '#dcfce7';
            iconEl.style.color = '#16a34a';
        } else {
            amtEl.innerText = '-$' + tx.amount;
            amtEl.style.color = '#dc2626'; // red-600
            typeEl.innerText = 'Expense Paid';
            iconEl.innerHTML = '<i class="fas fa-arrow-up"></i>';
            iconEl.style.backgroundColor = '#fee2e2';
            iconEl.style.color = '#dc2626';
        }
        
        document.getElementById('receiptModal').classList.add('active');
    }
</script>
@endsection
