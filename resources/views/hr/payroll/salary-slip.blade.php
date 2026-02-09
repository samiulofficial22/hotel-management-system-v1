<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Salary Slip - {{ $item->employee->full_name ?? 'Employee' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Salary Slip</h1>
        <p>{{ $item->payrollRun->title ?? 'Payroll' }} — {{ $item->payrollRun->period_start->format('F Y') }}</p>
    </div>
    <p><strong>Employee:</strong> {{ $item->employee->full_name ?? '-' }}</p>
    <p><strong>Code:</strong> {{ $item->employee->employee_code ?? $item->employee->employee_number ?? '-' }}</p>
    <p><strong>Department:</strong> {{ $item->employee->departmentRelation->name ?? $item->employee->department ?? '-' }}</p>
    <p><strong>Designation:</strong> {{ $item->employee->designation ?? '-' }}</p>
    <table>
        <thead>
            <tr><th>Earnings</th><th class="text-right">Amount</th></tr>
        </thead>
        <tbody>
            <tr><td>Basic Salary</td><td class="text-right">{{ money($item->base_salary) }}</td></tr>
            @if((float)($item->overtime_amount ?? 0) > 0)
            <tr><td>Overtime</td><td class="text-right">{{ money($item->overtime_amount) }}</td></tr>
            @endif
            @if((float)($item->allowances ?? 0) > 0)
            <tr><td>Allowances</td><td class="text-right">{{ money($item->allowances) }}</td></tr>
            @endif
        </tbody>
    </table>
    <table style="margin-top: 10px;">
        <thead>
            <tr><th>Deductions</th><th class="text-right">Amount</th></tr>
        </thead>
        <tbody>
            @if((float)($item->deductions ?? 0) > 0)
            <tr><td>Deductions</td><td class="text-right">({{ money($item->deductions) }})</td></tr>
            @endif
        </tbody>
    </table>
    <p class="total" style="margin-top: 15px;">Net Salary: {{ money($item->net_salary) }}</p>
    <p style="margin-top: 25px; font-size: 10px; color: #666;">Generated on {{ now()->format('Y-m-d H:i') }}</p>
</body>
</html>
