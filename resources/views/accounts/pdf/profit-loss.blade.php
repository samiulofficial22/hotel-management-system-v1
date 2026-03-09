<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Profit &amp; Loss Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111; background: #fff; }

        /* ── HEADER ── */
        .page-header {
            border-bottom: 3px solid #111;
            padding: 20px 32px 16px;
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }
        .header-left { display: table-cell; vertical-align: middle; width: 70%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 30%; }

        .hotel-name   { font-size: 22px; font-weight: 700; letter-spacing: 1.5px; color: #000; text-transform: uppercase; }
        .hotel-tagline{ font-size: 9px; color: #555; letter-spacing: 0.5px; margin-top: 2px; }
        .watermark    { font-size: 8px; color: #bbb; text-transform: uppercase; letter-spacing: 2px; margin-top: 4px; }

        .report-badge {
            background: #111; color: #fff;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            padding: 6px 14px; display: inline-block; border-radius: 2px;
        }
        .report-period { font-size: 9px; color: #555; margin-top: 5px; }

        /* ── SUMMARY CARDS ── */
        .summary-grid { display: table; width: 100%; margin-bottom: 22px; border-collapse: separate; border-spacing: 8px; }
        .summary-card { display: table-cell; width: 33%; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; padding: 12px 16px; border-top: 3px solid #111; vertical-align: top; }
        .summary-card .label  { font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #777; margin-bottom: 6px; }
        .summary-card .amount { font-size: 18px; font-weight: 700; color: #111; }
        .summary-card .sub    { font-size: 8px; color: #999; margin-top: 3px; }

        /* ── SECTION TITLE ── */
        .section-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: #111; margin: 18px 0 8px;
            padding-bottom: 5px; border-bottom: 2px solid #111;
        }

        /* ── TABLE ── */
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        thead tr { background: #111; color: #fff; }
        thead th { padding: 8px 12px; font-size: 9.5px; font-weight: 700; text-align: left; }
        thead th.r { text-align: right; }
        tbody tr:nth-child(even) { background: #f9f9f9; }
        tbody td { padding: 7px 12px; border-bottom: 1px solid #e0e0e0; font-size: 10px; color: #222; }
        tbody td.r { text-align: right; font-weight: 600; }
        tfoot tr { background: #333; color: #fff; }
        tfoot td { padding: 8px 12px; font-weight: 700; font-size: 10px; }
        tfoot td.r { text-align: right; }

        /* ── FOOTER ── */
        .doc-footer {
            margin-top: 28px; padding-top: 8px;
            border-top: 1px solid #ccc;
            font-size: 8.5px; color: #888;
            display: table; width: 100%;
        }
        .doc-footer .fl { display: table-cell; }
        .doc-footer .fr { display: table-cell; text-align: right; }

        .content { padding: 0 32px 24px; }
    </style>
</head>
<body>

@php
    function pdfMoney($amount) {
        return 'Tk. ' . number_format((float)$amount, 2);
    }
@endphp

    {{-- ── PROFESSIONAL HEADER ── --}}
    <div class="page-header">
        <div class="header-left">
            <div class="hotel-name">{{ config('app.name', 'Hotel Management') }}</div>
            <div class="hotel-tagline">Hospitality &bull; Excellence &bull; Comfort</div>
            <div class="watermark">Official Financial Document</div>
        </div>
        <div class="header-right">
            <div class="report-badge">Profit &amp; Loss Report</div>
            <div class="report-period">
                Period: <strong>{{ $from->format('d M Y') }}</strong> &mdash; <strong>{{ $to->format('d M Y') }}</strong>
            </div>
            <div class="report-period" style="margin-top:3px;">
                Generated: {{ now()->format('d M Y, h:i A') }}
            </div>
        </div>
    </div>

    <div class="content">

        {{-- SUMMARY CARDS --}}
        <table class="summary-grid" style="border-spacing:8px;">
            <tr>
                <td class="summary-card">
                    <div class="label">Total Revenue</div>
                    <div class="amount">{{ pdfMoney($revenue) }}</div>
                    <div class="sub">Gross income for the period</div>
                </td>
                <td class="summary-card">
                    <div class="label">Total Expense</div>
                    <div class="amount">{{ pdfMoney($expense) }}</div>
                    <div class="sub">Total costs incurred</div>
                </td>
                <td class="summary-card">
                    <div class="label">Net {{ $profit >= 0 ? 'Profit' : 'Loss' }}</div>
                    <div class="amount">{{ pdfMoney(abs($profit)) }}</div>
                    <div class="sub">{{ $profit >= 0 ? 'Revenue exceeds expenses' : 'Expenses exceed revenue' }}</div>
                </td>
            </tr>
        </table>

        {{-- REVENUE --}}
        <div class="section-title">Revenue Breakdown</div>
        @if(!empty($revenueBreakdown))
        <table>
            <thead><tr><th>#</th><th>Account</th><th class="r">Amount (Tk.)</th></tr></thead>
            <tbody>
                @foreach($revenueBreakdown as $i => $rb)
                <tr>
                    <td style="color:#999;">{{ $i + 1 }}</td>
                    <td>{{ $rb['name'] }}</td>
                    <td class="r">{{ pdfMoney($rb['amount']) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot><tr><td colspan="2">Total Revenue</td><td class="r">{{ pdfMoney($revenue) }}</td></tr></tfoot>
        </table>
        @else
        <p style="color:#aaa; font-size:10px; padding:8px 0;">No revenue recorded in this period.</p>
        @endif

        {{-- EXPENSE --}}
        <div class="section-title">Expense Breakdown</div>
        @if(!empty($expenseBreakdown))
        <table>
            <thead><tr><th>#</th><th>Account</th><th class="r">Amount (Tk.)</th></tr></thead>
            <tbody>
                @foreach($expenseBreakdown as $i => $eb)
                <tr>
                    <td style="color:#999;">{{ $i + 1 }}</td>
                    <td>{{ $eb['name'] }}</td>
                    <td class="r">{{ pdfMoney($eb['amount']) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot><tr><td colspan="2">Total Expense</td><td class="r">{{ pdfMoney($expense) }}</td></tr></tfoot>
        </table>
        @else
        <p style="color:#aaa; font-size:10px; padding:8px 0;">No expenses recorded in this period.</p>
        @endif

        {{-- NET SUMMARY ROW --}}
        <table style="margin-top:14px;">
            <tfoot>
                <tr>
                    <td style="font-weight:700; font-size:11px;">Net {{ $profit >= 0 ? 'Profit' : 'Loss' }}</td>
                    <td class="r" style="font-size:13px;">{{ pdfMoney(abs($profit)) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- FOOTER --}}
        <div class="doc-footer">
            <div class="fl">{{ config('app.name', 'Hotel Management') }} &mdash; Confidential Financial Report</div>
            <div class="fr">Page 1 of 1</div>
        </div>

    </div>
</body>
</html>
