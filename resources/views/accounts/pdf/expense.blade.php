<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Expense Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }
        .page-header { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); color: #fff; padding: 28px 32px 22px; margin-bottom: 24px; }
        .page-header .hotel-name { font-size: 20px; font-weight: 700; letter-spacing: 1px; margin-bottom: 4px; }
        .page-header .report-title { font-size: 14px; color: #ddd6fe; font-weight: 600; margin-bottom: 2px; }
        .page-header .report-period { font-size: 10px; color: #ede9fe; }
        .content { padding: 0 32px 24px; }
        .total-card { background: #fdf2f8; border-left: 5px solid #7c3aed; border-radius: 8px; padding: 14px 20px; margin-bottom: 22px; display: inline-block; }
        .total-card .label { font-size: 9px; text-transform: uppercase; color: #7c3aed; font-weight: 700; margin-bottom: 4px; }
        .total-card .amount { font-size: 22px; font-weight: 700; color: #4c1d95; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #4c1d95; color: #fff; }
        thead th { padding: 9px 14px; font-size: 10px; font-weight: 600; text-align: left; }
        thead th.r { text-align: right; }
        tbody tr:nth-child(even) { background: #faf5ff; }
        tbody td { padding: 8px 14px; border-bottom: 1px solid #ede9fe; font-size: 10px; }
        tbody td.r { text-align: right; font-weight: 600; color: #7c3aed; }
        tfoot tr { background: #4c1d95; color: #fff; }
        tfoot td { padding: 9px 14px; font-weight: 700; font-size: 10px; }
        tfoot td.r { text-align: right; }
        .footer { margin-top: 28px; padding-top: 10px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #aaa; }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="hotel-name">🏨 Hotel Management System</div>
        <div class="report-title">Expense Report</div>
        <div class="report-period">Period: {{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}</div>
    </div>
    <div class="content">
        <div class="total-card">
            <div class="label">Total Expenses</div>
            <div class="amount">{{ money($total) }}</div>
        </div>

        @if(!empty($breakdown))
        <table>
            <thead>
                <tr><th>#</th><th>Account</th><th class="r">Amount</th></tr>
            </thead>
            <tbody>
                @foreach($breakdown as $i => $row)
                <tr>
                    <td style="color:#999;">{{ $i + 1 }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td class="r">{{ money($row['amount']) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="2">Total Expense</td><td class="r">{{ money($total) }}</td></tr>
            </tfoot>
        </table>
        @else
        <p style="color:#aaa; padding:16px 0;">No expenses recorded in this period.</p>
        @endif

        <div class="footer">
            <span>Generated: {{ now()->format('d M Y, h:i A') }}</span> &nbsp;|&nbsp; Hotel Management System — Confidential
        </div>
    </div>
</body>
</html>
