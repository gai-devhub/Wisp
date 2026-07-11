<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Expense;
use Carbon\Carbon;

class AdminFinanceController extends Controller
{
    private function getFinanceData()
    {
        $now = Carbon::now();
        
        // Income queries (Active subscriptions)
        $incomeMonthly = Subscription::where('status', 'active')
            ->whereMonth('starts_at', $now->month)
            ->whereYear('starts_at', $now->year)
            ->sum('amount');
            
        $incomeQuarterly = Subscription::where('status', 'active')
            ->whereBetween('starts_at', [$now->copy()->firstOfQuarter(), $now->copy()->lastOfQuarter()])
            ->sum('amount');
            
        $incomeAnnually = Subscription::where('status', 'active')
            ->whereYear('starts_at', $now->year)
            ->sum('amount');
            
        $incomeTotal = Subscription::where('status', 'active')->sum('amount');

        // Expense queries
        $expenseMonthly = Expense::whereMonth('expense_date', $now->month)
            ->whereYear('expense_date', $now->year)
            ->sum('amount');
            
        $expenseQuarterly = Expense::whereBetween('expense_date', [$now->copy()->firstOfQuarter(), $now->copy()->lastOfQuarter()])
            ->sum('amount');
            
        $expenseAnnually = Expense::whereYear('expense_date', $now->year)
            ->sum('amount');

        $expenseTotal = Expense::sum('amount');

        // Net Profits
        $netMonthly = $incomeMonthly - $expenseMonthly;
        $netQuarterly = $incomeQuarterly - $expenseQuarterly;
        $netAnnually = $incomeAnnually - $expenseAnnually;
        $netTotal = $incomeTotal - $expenseTotal;

        // Transactions for the table
        $subscriptions = Subscription::with('user')->where('status', 'active')->orderBy('starts_at', 'desc')->get()->map(function($sub) {
            $methodName = $sub->payment_method;
            if ($sub->paymentMethod) {
                $methodName = $sub->paymentMethod->name;
            } else if ($methodName === 'paystack') {
                $methodName = 'Paystack';
            }
            return (object)[
                'type' => 'income',
                'amount' => $sub->amount,
                'date' => $sub->starts_at,
                'category' => 'WISP Subscription',
                'description' => "Subscription via {$methodName}",
                'user_name' => $sub->user ? $sub->user->username : 'Unknown User',
                'full_name' => $sub->user ? $sub->user->name : 'N/A'
            ];
        });

        $expenses = Expense::orderBy('expense_date', 'desc')->get()->map(function($exp) {
            return (object)[
                'type' => 'expense',
                'amount' => $exp->amount,
                'date' => Carbon::parse($exp->expense_date),
                'category' => $exp->category,
                'description' => $exp->description,
                'user_name' => null,
                'full_name' => null
            ];
        });

        $transactions = $subscriptions->concat($expenses)->sortByDesc('date')->values();

        $paymentMethod = \App\Models\PaymentMethod::where('is_active', true)->first();

        return compact(
            'incomeMonthly', 'incomeQuarterly', 'incomeAnnually', 'incomeTotal',
            'expenseMonthly', 'expenseQuarterly', 'expenseAnnually', 'expenseTotal',
            'netMonthly', 'netQuarterly', 'netAnnually', 'netTotal',
            'transactions', 'paymentMethod'
        );
    }

    public function transactions(Request $request)
    {
        $data = $this->getFinanceData();
        return view('admin.pages.finance.transactions', $data);
    }



    public function overview(Request $request)
    {
        $data = $this->getFinanceData();
        return view('admin.pages.finance.overview', $data);
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'expense_date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        Expense::create([
            'amount' => $request->amount,
            'category' => $request->category,
            'expense_date' => $request->expense_date,
            'description' => $request->description
        ]);

        return redirect()->back()->with('success', 'Expense recorded successfully!');
    }
}
