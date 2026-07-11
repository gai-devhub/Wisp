@extends('admin.base-admin')

@section('admin-section', 'finances-overview')

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

    <div class="flex justify-end mb-6">
        <button onclick="document.getElementById('addExpenseModal').classList.add('active');" 
                class="px-6 py-3 font-semibold rounded-xl border-0 bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer text-sm shadow-lg shadow-indigo-200 transition-all hover:-translate-y-0.5 flex items-center">
            <i class="fas fa-plus mr-2"></i> Record Expense
        </button>
    </div>

    {{-- Bank Account Header Style --}}
    <div style="background: linear-gradient(135deg, #1e1b4b, #3730a3, #4f46e5); color: white; border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 20px 25px -5px rgba(49, 46, 129, 0.2), 0 10px 10px -5px rgba(49, 46, 129, 0.1); position: relative; overflow: hidden;">
        <!-- Decorative Background Circle -->
        <div style="position: absolute; top: -8rem; right: -8rem; width: 24rem; height: 24rem; background: rgba(255,255,255,0.05); border-radius: 50%; filter: blur(48px);"></div>
        
        <div style="position: relative; z-index: 10;">
            <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #c7d2fe; margin-bottom: 0.25rem; font-weight: 600;">Main Operating Account</div>
            <div style="font-size: 2.25rem; font-weight: 700; display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                ${{ number_format($netTotal, 2) }}
                <span style="font-size: 0.875rem; font-weight: 500; color: #c7d2fe; background: rgba(255,255,255,0.1); padding: 0.2rem 0.6rem; border-radius: 9999px;">Available</span>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <span style="font-size: 0.7rem; color: #a5b4fc; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Account Name</span>
                    <span style="font-size: 0.875rem; font-weight: 500; letter-spacing: 0.025em; color: white;">{{ $paymentMethod ? $paymentMethod->account_name : 'No Account' }}</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <span style="font-size: 0.7rem; color: #a5b4fc; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Account Number</span>
                    <span style="font-size: 0.875rem; font-weight: 500; letter-spacing: 0.025em; color: white;">{{ $paymentMethod ? $paymentMethod->account_number : '**** ****' }}</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <span style="font-size: 0.7rem; color: #a5b4fc; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Status</span>
                    <span style="font-size: 0.875rem; font-weight: 500; color: #6ee7b7; display: flex; align-items: center; gap: 0.375rem;">
                        <i class="fas fa-circle" style="font-size: 8px;"></i> {{ $paymentMethod && $paymentMethod->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Stats Grid --}}
    <div class="flex flex-wrap gap-4 mb-6">
        {{-- Monthly Finance --}}
        <div class="flex-1 min-w-[200px] bg-white border border-slate-100 rounded-xl p-5 shadow-sm flex flex-col items-center">
            <div class="text-[13px] font-bold text-slate-800 self-start w-full mb-4">Monthly Finance</div>
            
            <div class="relative w-[106px] h-[106px] mb-6 flex items-center justify-center flex-shrink-0" style="max-width: 106px; max-height: 106px;">
                <canvas id="monthlyDonut"></canvas>
                <div class="absolute flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-sm font-extrabold text-slate-900 leading-none mb-1">${{ number_format($netMonthly) }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">NET</span>
                </div>
            </div>

            <div class="w-full text-[10px] font-bold text-slate-500 uppercase tracking-wider flex flex-col gap-2.5">
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Deposits</span>
                    <span class="text-slate-800">${{ number_format($incomeMonthly) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Withdrawals</span>
                    <span class="text-slate-800">${{ number_format($expenseMonthly) }}</span>
                </div>
            </div>
        </div>

        {{-- Quarterly Finance --}}
        <div class="flex-1 min-w-[200px] bg-white border border-slate-100 rounded-xl p-5 shadow-sm flex flex-col items-center">
            <div class="text-[13px] font-bold text-slate-800 self-start w-full mb-4">Quarterly Finance</div>
            
            <div class="relative w-[106px] h-[106px] mb-6 flex items-center justify-center flex-shrink-0" style="max-width: 106px; max-height: 106px;">
                <canvas id="quarterlyDonut"></canvas>
                <div class="absolute flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-sm font-extrabold text-slate-900 leading-none mb-1">${{ number_format($netQuarterly) }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">NET</span>
                </div>
            </div>

            <div class="w-full text-[10px] font-bold text-slate-500 uppercase tracking-wider flex flex-col gap-2.5">
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Deposits</span>
                    <span class="text-slate-800">${{ number_format($incomeQuarterly) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Withdrawals</span>
                    <span class="text-slate-800">${{ number_format($expenseQuarterly) }}</span>
                </div>
            </div>
        </div>

        {{-- Annual Finance --}}
        <div class="flex-1 min-w-[200px] bg-white border border-slate-100 rounded-xl p-5 shadow-sm flex flex-col items-center">
            <div class="text-[13px] font-bold text-slate-800 self-start w-full mb-4">Annual Finance</div>
            
            <div class="relative w-[106px] h-[106px] mb-6 flex items-center justify-center flex-shrink-0" style="max-width: 106px; max-height: 106px;">
                <canvas id="annualDonut"></canvas>
                <div class="absolute flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-sm font-extrabold text-slate-900 leading-none mb-1">${{ number_format($netAnnually) }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">NET</span>
                </div>
            </div>

            <div class="w-full text-[10px] font-bold text-slate-500 uppercase tracking-wider flex flex-col gap-2.5">
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Deposits</span>
                    <span class="text-slate-800">${{ number_format($incomeAnnually) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Withdrawals</span>
                    <span class="text-slate-800">${{ number_format($expenseAnnually) }}</span>
                </div>
            </div>
        </div>

        {{-- All-Time Finance --}}
        <div class="flex-1 min-w-[200px] bg-white border border-slate-100 rounded-xl p-5 shadow-sm flex flex-col items-center">
            <div class="text-[13px] font-bold text-slate-800 self-start w-full mb-4">All-Time Finance</div>
            
            <div class="relative w-[106px] h-[106px] mb-6 flex items-center justify-center flex-shrink-0" style="max-width: 106px; max-height: 106px;">
                <canvas id="allTimeDonut"></canvas>
                <div class="absolute flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-sm font-extrabold text-slate-900 leading-none mb-1">${{ number_format($netTotal) }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">NET</span>
                </div>
            </div>

            <div class="w-full text-[10px] font-bold text-slate-500 uppercase tracking-wider flex flex-col gap-2.5">
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Deposits</span>
                    <span class="text-slate-800">${{ number_format($incomeTotal) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Withdrawals</span>
                    <span class="text-slate-800">${{ number_format($expenseTotal) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Mid Charts Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Monthly Cashflow --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm">
            <div class="flex justify-between items-center mb-8">
                <div class="text-lg text-slate-900 font-bold flex items-center gap-2">
                    Monthly Cashflow
                    <i class="far fa-question-circle text-sm text-slate-400" title="Income vs Expenses for the current month"></i>
                </div>
                <div class="border border-slate-200/80 bg-white/60 px-3 py-1.5 rounded-lg text-xs text-slate-600 font-semibold flex items-center gap-2 cursor-pointer hover:bg-white transition-colors">
                    This Month <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex flex-col">
                    <div class="text-xs text-slate-500 mb-1 uppercase font-bold tracking-wider">Net Cashflow</div>
                    <div class="text-3xl text-slate-900 font-extrabold mb-1">${{ number_format($netMonthly, 2) }}</div>
                    <div class="text-sm text-slate-400 font-medium">Total</div>
                </div>
                
                <div class="relative w-[152px] h-[152px] flex items-center justify-center flex-shrink-0" style="max-width: 152px; max-height: 152px;">
                    <canvas id="cashflowChart"></canvas>
                </div>

                {{-- Legend Only --}}
                <div class="flex flex-col gap-4 w-full md:w-auto">
                    <div class="flex items-center justify-between gap-6">
                        <div class="flex items-center gap-2.5 text-sm text-slate-700 font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Total Deposits
                        </div>
                        <div class="text-base text-slate-900 font-bold">${{ number_format($incomeMonthly, 2) }}</div>
                    </div>
                    <div class="flex items-center justify-between gap-6">
                        <div class="flex items-center gap-2.5 text-sm text-slate-700 font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            Total Withdrawals
                        </div>
                        <div class="text-base text-slate-900 font-bold">${{ number_format($expenseMonthly, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profit Margin --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm">
            <div class="flex justify-between items-center mb-8">
                <div class="text-lg text-slate-900 font-bold flex items-center gap-2">
                    All-Time Profit Margin
                    <i class="far fa-question-circle text-sm text-slate-400" title="Total revenue vs total costs"></i>
                </div>
                <div class="border border-slate-200/80 bg-white/60 px-3 py-1.5 rounded-lg text-xs text-slate-600 font-semibold flex items-center gap-2 cursor-pointer hover:bg-white transition-colors">
                    All Time <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex flex-col">
                    <div class="text-xs text-slate-500 mb-1 uppercase font-bold tracking-wider">Net Profit</div>
                    <div class="text-3xl text-slate-900 font-extrabold">${{ number_format($netTotal, 2) }}</div>
                </div>
                
                <div class="relative w-[152px] h-[152px] flex items-center justify-center flex-shrink-0" style="max-width: 152px; max-height: 152px;">
                    <canvas id="profitMarginChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-2xl font-extrabold text-slate-900">
                            @php
                                $margin = $incomeTotal > 0 ? round(($netTotal / $incomeTotal) * 100) : 0;
                            @endphp
                            {{ $margin }}%
                        </div>
                        <div class="text-xs text-slate-500 font-bold uppercase tracking-wider">Margin</div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 w-full md:w-auto">
                    <div class="flex items-center justify-between gap-6">
                        <div class="flex items-center gap-2.5 text-sm text-slate-700 font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                            Gross Revenue
                        </div>
                        <div class="text-base text-slate-900 font-bold">${{ number_format($incomeTotal, 2) }}</div>
                    </div>
                    <div class="flex items-center justify-between gap-6">
                        <div class="flex items-center gap-2.5 text-sm text-slate-700 font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Total Cost
                        </div>
                        <div class="text-base text-slate-900 font-bold">${{ number_format($expenseTotal, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Expense Modal --}}
<div id="addExpenseModal" class="modal" style="position: fixed; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; max-width: 450px; width: 90%; padding: 2.5rem; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); position: relative; margin: auto;">
        <!-- Close Button -->
        <button type="button" onclick="document.getElementById('addExpenseModal').classList.remove('active');" style="position: absolute; top: 1.5rem; right: 1.5rem; background: transparent; border: none; font-size: 1.25rem; color: #94a3b8; cursor: pointer; z-index: 10;">
            <i class="fas fa-times"></i>
        </button>
        
        <h3 style="margin: 0 0 2rem 0; color: #0f172a; font-size: 1.25rem; font-weight: 700; display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            Record New Expense
        </h3>
        
        <form action="{{ route('admin.finances.expenses.store') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem; color: #334155;">Amount ($)</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-weight: 700;">$</span>
                    <input type="number" step="0.01" min="0" name="amount" required style="width: 100%; padding: 0.875rem 1rem 0.875rem 2.25rem; border-radius: 0.75rem; border: 2px solid #e2e8f0; font-size: 1rem; font-weight: 600; outline: none; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem; color: #334155;">Category</label>
                <select name="category" required style="width: 100%; padding: 0.875rem 1rem; border-radius: 0.75rem; border: 2px solid #e2e8f0; font-size: 0.875rem; font-weight: 500; outline: none; background: white; box-sizing: border-box;">
                    <option value="AWS Services">AWS Services</option>
                    <option value="API Costs">API Costs (AI, etc.)</option>
                    <option value="Domain & Hosting">Domain & Hosting</option>
                    <option value="SMS API">SMS API</option>
                    <option value="WhatsApp API">WhatsApp API</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem; color: #334155;">Expense Date</label>
                <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 0.875rem 1rem; border-radius: 0.75rem; border: 2px solid #e2e8f0; font-size: 0.875rem; font-weight: 500; outline: none; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem; color: #334155;">Description / Memo</label>
                <input type="text" name="description" placeholder="e.g., Monthly EC2 bill" style="width: 100%; padding: 0.875rem 1rem; border-radius: 0.75rem; border: 2px solid #e2e8f0; font-size: 0.875rem; outline: none; box-sizing: border-box;">
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('addExpenseModal').classList.remove('active');" style="padding: 0.75rem 1.5rem; border-radius: 0.75rem; border: none; background: #f1f5f9; color: #475569; font-weight: 700; font-size: 0.875rem; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 0.75rem 1.5rem; border-radius: 0.75rem; border: none; background: #4f46e5; color: white; font-weight: 700; font-size: 0.875rem; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2);">Authorize Transfer</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') return;

        function createMiniDonut(id, inc, exp) {
            var el = document.getElementById(id);
            if (!el) return;
            
            if (inc == 0 && exp == 0) {
                new Chart(el, {
                    type: 'doughnut',
                    data: { labels: ['No Data'], datasets: [{ data: [1], backgroundColor: ['#e2e8f0'], borderWidth: 0, hoverOffset: 0 }] },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '80%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
                });
                return;
            }

            new Chart(el, {
                type: 'doughnut',
                data: {
                    labels: ['Deposits', 'Withdrawals'],
                    datasets: [{
                        data: [inc, exp],
                        backgroundColor: ['#10b981', '#f43f5e'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    plugins: { legend: { display: false }, tooltip: { enabled: true } },
                    animation: { animateScale: true }
                }
            });
        }

        createMiniDonut('monthlyDonut', {{ $incomeMonthly > 0 ? $incomeMonthly : 0 }}, {{ $expenseMonthly > 0 ? $expenseMonthly : 0 }});
        createMiniDonut('quarterlyDonut', {{ $incomeQuarterly > 0 ? $incomeQuarterly : 0 }}, {{ $expenseQuarterly > 0 ? $expenseQuarterly : 0 }});
        createMiniDonut('annualDonut', {{ $incomeAnnually > 0 ? $incomeAnnually : 0 }}, {{ $expenseAnnually > 0 ? $expenseAnnually : 0 }});
        createMiniDonut('allTimeDonut', {{ $incomeTotal > 0 ? $incomeTotal : 0 }}, {{ $expenseTotal > 0 ? $expenseTotal : 0 }});

        var pmEl = document.getElementById('profitMarginChart');
        if(pmEl) {
            const rev = {{ $incomeTotal > 0 ? $incomeTotal : 0 }};
            const cost = {{ $expenseTotal > 0 ? $expenseTotal : 0 }};
            
            const emptyColor = 'rgba(226, 232, 240, 1)';
            
            if (rev == 0 && cost == 0) {
                new Chart(pmEl, {
                    type: 'doughnut',
                    data: {
                        labels: ['No Data'],
                        datasets: [{ data: [1], backgroundColor: [emptyColor], borderWidth: 0 }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        cutout: '80%', 
                        plugins: { legend: { display: false }, tooltip: { enabled: false } } 
                    }
                });
            } else {
                new Chart(pmEl, {
                    type: 'doughnut',
                    data: {
                        labels: ['Gross Revenue', 'Total Cost'],
                        datasets: [{
                            data: [
                                {{ $netTotal > 0 ? $netTotal : 0 }},
                                {{ $expenseTotal > 0 ? $expenseTotal : 0 }}
                            ],
                            backgroundColor: ['#4f46e5', '#f59e0b'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '80%',
                        plugins: { legend: { display: false }, tooltip: { enabled: true } },
                        animation: { animateScale: true }
                    }
                });
            }
        }

        var cfEl = document.getElementById('cashflowChart');
        if (cfEl) {
            const cfInc = {{ $incomeMonthly > 0 ? $incomeMonthly : 0 }};
            const cfExp = {{ $expenseMonthly > 0 ? $expenseMonthly : 0 }};
            
            if (cfInc == 0 && cfExp == 0) {
                new Chart(cfEl, {
                    type: 'doughnut',
                    data: {
                        labels: ['No Data'],
                        datasets: [{ data: [1], backgroundColor: ['rgba(226, 232, 240, 1)'], borderWidth: 0 }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        cutout: '80%', 
                        plugins: { legend: { display: false }, tooltip: { enabled: false } } 
                    }
                });
            } else {
                new Chart(cfEl, {
                    type: 'bar',
                    data: {
                        labels: ['This Month'],
                        datasets: [
                            {
                                label: 'Deposits',
                                data: [cfInc],
                                backgroundColor: '#10b981',
                                borderRadius: 4,
                                barPercentage: 0.6
                            },
                            {
                                label: 'Withdrawals',
                                data: [cfExp],
                                backgroundColor: '#f43f5e',
                                borderRadius: 4,
                                barPercentage: 0.6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { display: false, grid: { display: false } },
                            y: { display: false, grid: { display: false }, beginAtZero: true }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': $' + context.raw;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    });
</script>
@endsection
