<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Expense Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111; background: #fff; }

        .page-header {
            border-bottom: 3px solid #111;
            padding: 20px 32px 16px;
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }
        .header-left { display: table-cell; vertical-align: middle; width: 70%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 30%; }

        .hotel-name    { font-size: 22px; font-weight: 700; letter-spacing: 1.5px; color: #000; text-transform: uppercase; }
        .hotel-tagline { font-size: 9px; color: #555; letter-spacing: 0.5px; margin-top: 2px; }
        .watermark     { font-size: 8px; color: #bbb; text-transform: uppercase; letter-spacing: 2px; margin-top: 4px; }

        .report-badge  {
            background: #111; color: #fff;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            padding: 6px 14px; display: inline-block; border-radius: 2px;
        }
        .report-period { font-size: 9px; color: #555; margin-top: 5px; }

        .content { padding: 0 32px 24px; }

        .total-card {
            background: #f5f5f5; border: 1px solid #ddd; border-top: 3px solid #111;
            border-radius: 4px; padding: 14px 20px; margin-bottom: 22px; display: inline-block;
        }
        .total-card .label  { font-size: 8.5px; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 4px; }
        .total-card .amount { font-size: 22px; font-weight: 700; color: #111; }
        .total-card .sub    { font-size: 8px; color: #999; margin-top: 3px; }

        .section-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: #111; margin: 0 0 12px;
            padding-bottom: 5px; border-bottom: 2px solid #111;
        }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #111; color: #fff; }
        thead th { padding: 9px 14px; font-size: 9.5px; font-weight: 700; text-align: left; }
        thead th.r { text-align: right; }
        tbody tr:nth-child(even) { background: #f9f9f9; }
        tbody td { padding: 8px 14px; border-bottom: 1px solid #e0e0e0; font-size: 10px; color: #222; }
        tbody td.r { text-align: right; font-weight: 600; }
        tbody td.seq { color: #999; }
        tfoot tr { background: #333; color: #fff; }
        tfoot td { padding: 9px 14px; font-weight: 700; font-size: 10px; }
        tfoot td.r { text-align: right; }

        .doc-footer {
            margin-top: 28px; padding-top: 8px;
            border-top: 1px solid #ccc;
            font-size: 8.5px; color: #888;
            display: table; width: 100%;
        }
        .doc-footer .fl { display: table-cell; }
        .doc-footer .fr { display: table-cell; text-align: right; }
    </style>
</head>
<body>

@php
    function pdfMoney($amount) {
        return 'Tk. ' . number_format((float)$amount, 2);
    }
@endphp

    <div class="page-header">
        <div class="header-left">
            <div class="hotel-name">{{ config('app.name', 'Hotel Management') }}</div>
            <div class="hotel-tagline">Hospitality &bull; Excellence &bull; Comfort</div>
            <div class="watermark">Official Financial Document</div>
        </div>
        <div class="header-right">
            <div class="report-badge">Expense Report</div>
            <div class="report-period">
                Period: <strong>{{ $from->format('d M Y') }}</strong> &mdash; <strong>{{ $to->format('d M Y') }}</strong>
            </div>
            <div class="report-period" style="margin-top:3px;">
                Generated: {{ now()->format('d M Y, h:i A') }}
            </div>
        </div>
    </div>

    <div class="content">

        <div class="total-card">
            <div class="label">Total Expenses for Period</div>
            <div class="amount">{{ pdfMoney($total) }}</div>
            <div class="sub">Sum of all expense accounts</div>
        </div>

        <div class="section-title">Expense Breakdown by Account</div>

        @if(!empty($breakdown))
        <table>
            <thead>
                <tr><th>#</th><th>Account</th><th class="r">Amount (Tk.)</th></tr>
            </thead>
            <tbody>
                @foreach($breakdown as $i => $row)
                <tr>
                    <td class="seq">{{ $i + 1 }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td class="r">{{ pdfMoney($row['amount']) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="2">Total Expense</td><td class="r">{{ pdfMoney($total) }}</td></tr>
            </tfoot>
        </table>
        @else
        <p style="color:#aaa; padding:16px 0;">No expenses recorded in this period.</p>
        @endif

        <div class="doc-footer">
            <div class="fl">{{ config('app.name', 'Hotel Management') }} &mdash; Confidential Financial Report</div>
            <div class="fr">Page 1 of 1</div>
        </div>
    </div>
</body>
</html>
