<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\PayrollRun;
use App\Models\PayrollItem;
use App\Services\PayrollRunService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class PayrollController extends Controller
{
    public function __construct(protected PayrollRunService $service) {}

    public function index(): View
    {
        $runs = $this->service->paginate(10);
        return view('hr.payroll.index', compact('runs'));
    }

    public function create(): View
    {
        return view('hr.payroll.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'title' => 'nullable|string|max:100',
        ]);
        $run = $this->service->create(
            Carbon::parse($request->period_start),
            Carbon::parse($request->period_end),
            $request->title
        );
        return redirect()->route('hr.payroll.show', $run)->with('success', __('Payroll run created.'));
    }

    public function show(PayrollRun $payroll_run): View
    {
        $payroll_run->load('items.employee');
        $paymentAccounts = ChartOfAccount::whereIn('code', ['CASH', 'BANK'])->where('is_active', true)->orderBy('code')->get();
        return view('hr.payroll.show', ['run' => $payroll_run, 'paymentAccounts' => $paymentAccounts]);
    }

    public function process(PayrollRun $payroll_run): RedirectResponse
    {
        $this->service->process($payroll_run, (int) auth()->id());
        return redirect()->route('hr.payroll.show', $payroll_run)->with('success', __('Payroll approved.'));
    }

    public function pay(Request $request, PayrollRun $payroll_run): RedirectResponse
    {
        $request->validate(['payment_account_id' => 'required|exists:chart_of_accounts,id']);
        try {
            $this->service->markAsPaid($payroll_run, (int) auth()->id(), (int) $request->payment_account_id);
            return redirect()->route('hr.payroll.show', $payroll_run)->with('success', __('Payroll marked as paid. Ledger entry created.'));
        } catch (ValidationException $e) {
            return redirect()->route('hr.payroll.show', $payroll_run)->withErrors($e->errors());
        }
    }

    public function salarySlipPdf(PayrollItem $payroll_item): Response
    {
        $payroll_item->load(['payrollRun', 'employee']);
        $pdf = Pdf::loadView('hr.payroll.salary-slip', ['item' => $payroll_item]);
        $filename = 'salary-slip-' . ($payroll_item->employee->employee_code ?? $payroll_item->employee_id) . '-' . $payroll_item->payrollRun->period_start->format('Y-m') . '.pdf';
        return $pdf->stream($filename);
    }

    public function updateItem(Request $request, PayrollItem $payroll_item): RedirectResponse
    {
        $run = $payroll_item->payrollRun;
        if ($run->isPaid()) {
            return redirect()->route('hr.payroll.show', $run)->withErrors(['payroll' => __('Paid payroll cannot be edited.')]);
        }
        $request->validate(['allowances' => 'nullable|numeric|min:0', 'deductions' => 'nullable|numeric|min:0']);
        $allowances = (float) ($request->allowances ?? 0);
        $deductions = (float) ($request->deductions ?? 0);
        $net = (float) $payroll_item->base_salary + (float) ($payroll_item->overtime_amount ?? 0) + $allowances - $deductions;
        $payroll_item->update(['allowances' => $allowances, 'deductions' => $deductions, 'net_salary' => $net]);
        return redirect()->route('hr.payroll.show', $run)->with('success', __('Updated.'));
    }
}
