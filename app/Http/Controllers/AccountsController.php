<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\LedgerEntry;
use App\Models\PayrollRun;
use App\Services\ChartOfAccountService;
use App\Services\LedgerEntryService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AccountsController extends Controller
{
    public function __construct(
        protected ChartOfAccountService $accountService,
        protected LedgerEntryService $ledgerService
    ) {}

    public function index(): View
    {
        $accounts = $this->accountService->all(false);
        return view('accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        $accounts = $this->accountService->all(false);
        return view('accounts.create', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->accountService->rules());
        try {
            $this->accountService->create($validated);
            return redirect()->route('accounts.index')->with('success', __('Account created.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(ChartOfAccount $account): View
    {
        $accounts = $this->accountService->all(false);
        return view('accounts.edit', compact('account', 'accounts'));
    }

    public function update(Request $request, ChartOfAccount $account): RedirectResponse
    {
        $validated = $request->validate($this->accountService->rules($account->id));
        try {
            $this->accountService->update($account, $validated);
            return redirect()->route('accounts.index')->with('success', __('Account updated.'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function ledger(Request $request): View
    {
        $accountId = $request->input('account_id') ? (int) $request->input('account_id') : null;
        $accounts = $this->accountService->all(false);
        $entries = $this->ledgerService->paginate(25, $accountId);
        return view('accounts.ledger', compact('entries', 'accounts', 'accountId'));
    }

    public function storeEntry(Request $request): RedirectResponse
    {
        $request->validate([
            'entry_date' => 'required|date',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'debit' => 'required_without:credit|nullable|numeric|min:0',
            'credit' => 'required_without:debit|nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        $debit = (float) ($request->debit ?? 0);
        $credit = (float) ($request->credit ?? 0);
        if ($debit > 0) {
            $this->ledgerService->create(['entry_date' => $request->entry_date, 'account_id' => $request->account_id, 'debit' => $debit, 'credit' => 0, 'description' => $request->description], auth()->id());
        }
        if ($credit > 0) {
            $this->ledgerService->create(['entry_date' => $request->entry_date, 'account_id' => $request->account_id, 'debit' => 0, 'credit' => $credit, 'description' => $request->description], auth()->id());
        }
        return redirect()->route('accounts.ledger', ['account_id' => $request->account_id])->with('success', 'Entry added.');
    }

    public function reportsIndex(): View
    {
        return view('accounts.reports-index');
    }

    public function reportProfitLoss(Request $request): View
    {
        $from = $request->filled('from') ? Carbon::parse($request->from) : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to) : now()->endOfMonth();
        $revenue = $this->ledgerService->totalRevenue($from, $to);
        $expense = $this->ledgerService->totalExpense($from, $to);
        $profit = $revenue - $expense;
        return view('accounts.report-profit-loss', compact('from', 'to', 'revenue', 'expense', 'profit'));
    }

    public function reportExpense(Request $request): View
    {
        $from = $request->filled('from') ? Carbon::parse($request->from) : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to) : now()->endOfMonth();
        $breakdown = $this->ledgerService->expenseByAccount($from, $to);
        $total = array_sum(array_column($breakdown, 'amount'));
        return view('accounts.report-expense', compact('from', 'to', 'breakdown', 'total'));
    }

    public function reportDailyCash(Request $request): View
    {
        $from = $request->filled('from') ? Carbon::parse($request->from) : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to) : now()->endOfMonth();
        $daily = $this->ledgerService->dailyCashSummary($from, $to);
        return view('accounts.report-daily-cash', compact('from', 'to', 'daily'));
    }

    public function reportPayrollCost(Request $request): View
    {
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->copy()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now()->copy()->endOfMonth();
        $runs = PayrollRun::with('items.employee')->where('status', PayrollRun::STATUS_PAID)
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from, $to])
            ->orderByDesc('paid_at')
            ->get();
        $totalCost = $runs->sum(fn ($r) => $r->items->sum('net_salary'));
        return view('accounts.report-payroll-cost', compact('from', 'to', 'runs', 'totalCost'));
    }
}
