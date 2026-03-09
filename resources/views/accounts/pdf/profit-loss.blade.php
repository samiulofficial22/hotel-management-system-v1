<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }

        .page-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); color: #fff; padding: 28px 32px 22px; margin-bottom: 24px; }
        .page-header .hotel-name { font-size: 20px; font-weight: 700; letter-spacing: 1px; margin-bottom: 4px; }
        .page-header .report-title { font-size: 14px; color: #a0c4ff; font-weight: 600; margin-bottom: 2px; }
        .page-header .report-period { font-size: 10px; color: #cddeff; }

        .content { padding: 0 32px 24px; }

        .summary-grid { display: table; width: 100%; margin-bottom: 22px; border-collapse: separate; border-spacing: 8px; }
        .summary-card { display: table-cell; width: 33%; background: #f8f9fc; border-radius: 8px; padding: 14px 16px; border-left: 4px solid #ccc; vertical-align: top; }
        .summary-card.revenue { border-left-color: #22c55e; }
        .summary-card.expense { border-left-color: #ef4444; }
        .summary-card.profit-pos { border-left-color: #3b82f6; }
        .summary-card.profit-neg { border-left-color: #f97316; }
        .summary-card .label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #888; margin-bottom: 6px; }
        .summary-card .amount { font-size: 18px; font-weight: 700; }
        .summary-card.revenue .amount { color: #16a34a; }
        .summary-card.expense .amount { color: #dc2626; }
        .summary-card.profit-pos .amount { color: #2563eb; }
        .summary-card.profit-neg .amount { color: #ea580c; }

        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #555; margin: 18px 0 8px; padding-bottom: 5px; border-bottom: 2px solid #e5e7eb; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        thead tr { background: #1e293b; color: #fff; }
        thead th { padding: 8px 12px; font-size: 10px; font-weight: 600; text-align: left; }
        thead th.r { text-align: right; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:hover { background: #f1f5f9; }
        tbody td { padding: 7px 12px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        tbody td.r { text-align: right; font-weight: 600; }
        tfoot tr { background: #1e293b; color: #fff; }
        tfoot td { padding: 8px 12px; font-weight: 700; font-size: 10px; }
        tfoot td.r { text-align: right; }

        .footer { margin-top: 28px; padding-top: 10px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #aaa; display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="hotel-name">🏨 Hotel Management System</div>
        <div class="report-title">Profit & Loss Report</div>
        <div class="report-period">Period: {{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}</div>
    </div>

    <div class="content">
        <table class="summary-grid" style="border-spacing:8px;">
            <tr>
                <td class="summary-card revenue">
                    <div class="label">Total Revenue</div>
                    <div class="amount" style="color:#16a34a;">{{ money($revenue) }}</div>
                </td>
                <td class="summary-card expense">
                    <div class="label">Total Expense</div>
                    <div class="amount" style="color:#dc2626;">{{ money($expense) }}</div>
                </td>
                <td class="{{ $profit >= 0 ? 'summary-card profit-pos' : 'summary-card profit-neg' }}">
                    <div class="label">Net {{ $profit >= 0 ? 'Profit' : 'Loss' }}</div>
                    <div class="amount" style="color:{{ $profit >= 0 ? '#2563eb' : '#ea580c' }};">{{ money(abs($profit)) }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">📈 Revenue Breakdown</div>
        @if(!empty($revenueBreakdown))
        <table>
            <thead><tr><th>Account</th><th class="r">Amount</th></tr></thead>
            <tbody>
                @foreach($revenueBreakdown as $rb)
                <tr><td>{{ $rb['name'] }}</td><td class="r" style="color:#16a34a;">{{ money($rb['amount']) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot><tr><td>Total Revenue</td><td class="r">{{ money($revenue) }}</td></tr></tfoot>
        </table>
        @else
        <p style="color:#aaa; font-size:10px; padding:8px 0;">No revenue recorded in this period.</p>
        @endif

        <div class="section-title">📉 Expense Breakdown</div>
        @if(!empty($expenseBreakdown))
        <table>
            <thead><tr><th>Account</th><th class="r">Amount</th></tr></thead>
            <tbody>
                @foreach($expenseBreakdown as $eb)
                <tr><td>{{ $eb['name'] }}</td><td class="r" style="color:#dc2626;">{{ money($eb['amount']) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot><tr><td>Total Expense</td><td class="r">{{ money($expense) }}</td></tr></tfoot>
        </table>
        @else
        <p style="color:#aaa; font-size:10px; padding:8px 0;">No expenses recorded in this period.</p>
        @endif
    </div>

    <div class="content">
        <div class="footer">
            <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
            <span>Hotel Management System — Confidential</span>
        </div>
    </div>
</body>
</html>
