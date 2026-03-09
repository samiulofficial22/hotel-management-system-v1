<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payroll Cost Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }
        .page-header { background: linear-gradient(135deg, #1e3a5f 0%, #0f2942 100%); color: #fff; padding: 28px 32px 22px; margin-bottom: 24px; }
        .page-header .hotel-name { font-size: 20px; font-weight: 700; letter-spacing: 1px; margin-bottom: 4px; }
        .page-header .report-title { font-size: 14px; color: #93c5fd; font-weight: 600; margin-bottom: 2px; }
        .page-header .report-period { font-size: 10px; color: #bfdbfe; }
        .content { padding: 0 32px 24px; }
        .total-card { background: #eff6ff; border-left: 5px solid #2563eb; border-radius: 8px; padding: 14px 20px; margin-bottom: 22px; display: inline-block; }
        .total-card .label { font-size: 9px; text-transform: uppercase; color: #2563eb; font-weight: 700; margin-bottom: 4px; }
        .total-card .amount { font-size: 22px; font-weight: 700; color: #1e40af; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #1e3a5f; color: #fff; }
        thead th { padding: 9px 14px; font-size: 10px; font-weight: 600; text-align: left; }
        thead th.r { text-align: right; }
        tbody tr:nth-child(even) { background: #eff6ff; }
        tbody td { padding: 8px 14px; border-bottom: 1px solid #dbeafe; font-size: 10px; }
        tbody td.r { text-align: right; font-weight: 600; }
        tfoot tr { background: #1e3a5f; color: #fff; }
        tfoot td { padding: 9px 14px; font-weight: 700; font-size: 10px; }
        tfoot td.r { text-align: right; }
        .footer { margin-top: 28px; padding-top: 10px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #aaa; }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="hotel-name">🏨 Hotel Management System</div>
        <div class="report-title">Payroll Cost Report</div>
        <div class="report-period">Period: {{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}</div>
    </div>
    <div class="content">
        <div class="total-card">
            <div class="label">Total Payroll Cost</div>
            <div class="amount">{{ money($totalCost) }}</div>
        </div>

        @if($runs->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Payroll Title</th>
                    <th>Period</th>
                    <th>Paid At</th>
                    <th class="r">Total Net Salary</th>
                </tr>
            </thead>
            <tbody>
                @foreach($runs as $i => $run)
                <tr>
                    <td style="color:#999;">{{ $i + 1 }}</td>
                    <td><strong>{{ $run->title }}</strong></td>
                    <td>{{ $run->period_start->format('d M Y') }} – {{ $run->period_end->format('d M Y') }}</td>
                    <td>{{ $run->paid_at?->format('d M Y') ?? '—' }}</td>
                    <td class="r">{{ money($run->items->sum('net_salary')) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">Total Payroll Cost</td>
                    <td class="r">{{ money($totalCost) }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="color:#aaa; padding:16px 0;">No payroll runs paid in this period.</p>
        @endif

        <div class="footer">
            Generated: {{ now()->format('d M Y, h:i A') }} &nbsp;|&nbsp; Hotel Management System — Confidential
        </div>
    </div>
</body>
</html>
