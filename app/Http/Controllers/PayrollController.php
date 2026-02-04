<?php

namespace App\Http\Controllers;

use App\Models\PayrollRun;
use App\Models\PayrollItem;
use App\Services\PayrollRunService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        return view('hr.payroll.show', ['run' => $payroll_run]);
    }

    public function process(PayrollRun $payroll_run): RedirectResponse
    {
        $this->service->process($payroll_run, (int) auth()->id());
        return redirect()->route('hr.payroll.show', $payroll_run)->with('success', __('Payroll processed.'));
    }

    public function updateItem(Request $request, PayrollItem $payroll_item): RedirectResponse
    {
        $request->validate(['allowances' => 'nullable|numeric|min:0', 'deductions' => 'nullable|numeric|min:0']);
        $allowances = (float) ($request->allowances ?? 0);
        $deductions = (float) ($request->deductions ?? 0);
        $net = $payroll_item->base_salary + $allowances - $deductions;
        $payroll_item->update(['allowances' => $allowances, 'deductions' => $deductions, 'net_salary' => $net]);
        return redirect()->route('hr.payroll.show', $payroll_item->payrollRun)->with('success', 'Updated.');
    }
}
