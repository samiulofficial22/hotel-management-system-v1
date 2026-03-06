<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Salary Slip - {{ $item->employee->full_name ?? 'Employee' }}</title>
    <style>
        @page { 
            margin: 0.5in; 
            size: A4;
        }
        * {
            box-sizing: border-box;
        }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 11.5px; 
            color: #333; 
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .container {
            width: 100%;
            border: 1px solid #2c3e50;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
        }
        .company-address {
            font-size: 11px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        .doc-type {
            background-color: #2c3e50;
            color: white;
            padding: 4px 15px;
            display: inline-block;
            margin-top: 10px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        .details-table {
            width: 100%;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        .details-table td {
            padding: 3px 0;
            vertical-align: top;
            word-wrap: break-word;
        }
        .label {
            font-weight: bold;
            width: 120px;
            color: #2c3e50;
        }
        .colon {
            width: 15px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        .main-table th {
            background-color: #f2f4f6;
            color: #2c3e50;
            border: 1px solid #bdc3c7;
            padding: 8px;
            text-align: left;
            text-transform: uppercase;
            font-size: 11px;
        }
        .main-table td {
            border: 1px solid #bdc3c7;
            padding: 8px;
        }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        
        .summary-row {
            background-color: #f9f9f9;
        }
        .net-salary-row {
            background-color: #ecf0f1;
            font-size: 14px;
        }
        .net-salary-label {
            font-weight: bold;
            color: #2c3e50;
            text-align: right;
        }
        .net-salary-value {
            font-weight: bold;
            color: #c0392b;
            text-align: right;
        }
        
        .words-section {
            margin-top: 15px;
            font-style: italic;
            color: #2c3e50;
            font-size: 11px;
        }
        
        .footer {
            margin-top: 40px;
        }
        .signature-table {
            width: 100%;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
        }
        .sig-line {
            width: 160px;
            border-top: 1px solid #2c3e50;
            margin: 0 auto;
            padding-top: 5px;
            font-size: 11px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 70px;
            color: rgba(0, 0, 0, 0.03);
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="company-name">{{ config('app.name', 'Hotel Management System') }}</h1>
            <div class="company-address">Official Salary Certificate & Pay Slip</div>
            <div class="doc-type">Salary Slip</div>
        </div>

        <table class="details-table">
            <tr>
                <td class="label">Employee Name</td>
                <td class="colon">:</td>
                <td class="text-bold">{{ $item->employee->full_name ?? '-' }}</td>
                <td class="label">Employee Code</td>
                <td class="colon">:</td>
                <td>{{ $item->employee->employee_code ?? $item->employee->employee_number ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Designation</td>
                <td class="colon">:</td>
                <td>{{ $item->employee->designation ?? '-' }}</td>
                <td class="label">Department</td>
                <td class="colon">:</td>
                <td>{{ $item->employee->departmentRelation->name ?? $item->employee->department ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Month/Year</td>
                <td class="colon">:</td>
                <td>{{ $item->payrollRun->period_start->format('F Y') }}</td>
                <td class="label">Working Days</td>
                <td class="colon">:</td>
                <td>{{ $item->working_days ?? '-' }}</td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th colspan="2">Earnings</th>
                    <th colspan="2">Deductions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td class="text-right">{{ money($item->base_salary) }}</td>
                    <td>Professional Tax</td>
                    <td class="text-right">0.00</td>
                </tr>
                <tr>
                    <td>Overtime</td>
                    <td class="text-right">{{ money($item->overtime_amount ?? 0) }}</td>
                    <td>PF / Pension Plan</td>
                    <td class="text-right">0.00</td>
                </tr>
                <tr>
                    <td>Allowances</td>
                    <td class="text-right">{{ money($item->allowances ?? 0) }}</td>
                    <td>Other Deductions</td>
                    <td class="text-right">{{ money($item->deductions ?? 0) }}</td>
                </tr>
                <tr class="summary-row">
                    <td class="text-bold">Total Addition</td>
                    <td class="text-right text-bold">{{ money(($item->base_salary ?? 0) + ($item->overtime_amount ?? 0) + ($item->allowances ?? 0)) }}</td>
                    <td class="text-bold">Total Deduction</td>
                    <td class="text-right text-bold">{{ money($item->deductions ?? 0) }}</td>
                </tr>
                <tr class="net-salary-row">
                    <td colspan="3" class="net-salary-label">NET SALARY</td>
                    <td class="net-salary-value">{{ money($item->net_salary) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="words-section">
            <strong>Remarks:</strong> This is a confidential document.
        </div>

        <div class="footer">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="sig-line">Employee Signature</div>
                    </td>
                    <td></td>
                    <td>
                        <div class="sig-line">Authorized Signatory</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="watermark">CONFIDENTIAL</div>
    </div>
    
    <div style="margin-top: 15px; font-size: 9px; color: #95a5a6; text-align: center;">
        This is a computer-generated document and does not require a physical signature for internal validation.
        <br>Generated on {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>
