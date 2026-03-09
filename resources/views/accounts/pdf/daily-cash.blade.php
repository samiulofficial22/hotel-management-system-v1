<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Daily Cash Summary</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #111; background: #fff; }

        .page-header {
            border-bottom: 3px solid #111;
            padding: 18px 28px 14px;
            margin-bottom: 18px;
            display: table;
            width: 100%;
        }
        .header-left { display: table-cell; vertical-align: middle; width: 70%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 30%; }

        .hotel-name    { font-size: 20px; font-weight: 700; letter-spacing: 1.5px; color: #000; text-transform: uppercase; }
        .hotel-tagline { font-size: 8.5px; color: #555; letter-spacing: 0.5px; margin-top: 2px; }
        .watermark     { font-size: 7.5px; color: #bbb; text-transform: uppercase; letter-spacing: 2px; margin-top: 4px; }

        .report-badge  {
            background: #111; color: #fff;
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            padding: 5px 12px; display: inline-block; border-radius: 2px;
        }
        .report-period { font-size: 8.5px; color: #555; margin-top: 5px; }

        .content { padding: 0 28px 24px; }

        .totals-row { background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; padding: 10px 14px; margin-bottom: 16px; display: table; width: 100%; }
        .totals-cell { display: table-cell; padding: 0 12px; text-align: center; }
        .totals-cell:first-child { padding-left: 0; }
        .totals-cell .label { font-size: 7.5px; text-transform: uppercase; color: #888; margin-bottom: 4px; letter-spacing: 0.5px; }
        .totals-cell .val   { font-size: 13px; font-weight: 700; color: #111; }
        .totals-sep { display: table-cell; border-left: 1px solid #ddd; }

        .section-title {
            font-size: 9.5px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: #111; margin: 0 0 10px;
            padding-bottom: 4px; border-bottom: 2px solid #111;
        }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #111; color: #fff; }
        thead th { padding: 8px 10px; font-size: 9px; font-weight: 700; text-align: left; }
        thead th.r { text-align: right; }
        tbody tr:nth-child(even) { background: #f9f9f9; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #e0e0e0; font-size: 9px; color: #222; }
        tbody td.r { text-align: right; font-weight: 600; }
        tfoot tr { background: #333; color: #fff; }
        tfoot td { padding: 8px 10px; font-weight: 700; font-size: 9px; }
        tfoot td.r { text-align: right; }

        .doc-footer {
            margin-top: 22px; padding-top: 7px;
            border-top: 1px solid #ccc;
            font-size: 8px; color: #888;
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
    $totalIn  = array_sum(array_column($daily, 'cashIn'));
    $totalOut = array_sum(array_column($daily, 'cashOut'));
    $netTotal = $totalIn - $totalOut;
@endphp

    <div class="page-header">
        <div class="header-left">
            <div class="hotel-name">{{ config('app.name', 'Hotel Management') }}</div>
            <div class="hotel-tagline">Hospitality &bull; Excellence &bull; Comfort</div>
            <div class="watermark">Official Financial Document</div>
        </div>
        <div class="header-right">
            <div class="report-badge">Daily Cash Summary</div>
            <div class="report-period">
                Period: <strong>{{ $from->format('d M Y') }}</strong> &mdash; <strong>{{ $to->format('d M Y') }}</strong>
            </div>
            <div class="report-period" style="margin-top:3px;">
                Generated: {{ now()->format('d M Y, h:i A') }}
            </div>
        </div>
    </div>

    <div class="content">

        <div class="totals-row">
            <div class="totals-cell">
                <div class="label">Total Cash In</div>
                <div class="val">{{ pdfMoney($totalIn) }}</div>
            </div>
            <div class="totals-sep"></div>
            <div class="totals-cell">
                <div class="label">Total Cash Out</div>
                <div class="val">{{ pdfMoney($totalOut) }}</div>
            </div>
            <div class="totals-sep"></div>
            <div class="totals-cell">
                <div class="label">Net Movement</div>
                <div class="val">{{ ($netTotal >= 0 ? '+' : '') . pdfMoney($netTotal) }}</div>
            </div>
        </div>

        <div class="section-title">Daily Breakdown</div>

        @if(!empty($daily))
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="r">Opening (Tk.)</th>
                    <th class="r">Cash In (+)</th>
                    <th class="r">Cash Out (-)</th>
                    <th class="r">Net</th>
                    <th class="r">Closing (Tk.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($daily as $date => $data)
                <tr>
                    <td><strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong></td>
                    <td class="r">{{ pdfMoney($data['opening']) }}</td>
                    <td class="r">+{{ pdfMoney($data['cashIn']) }}</td>
                    <td class="r">-{{ pdfMoney($data['cashOut']) }}</td>
                    <td class="r"><strong>{{ ($data['net'] >= 0 ? '+' : '') . pdfMoney($data['net']) }}</strong></td>
                    <td class="r"><strong>{{ pdfMoney($data['closing']) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Period Totals</td>
                    <td class="r">&mdash;</td>
                    <td class="r">+{{ pdfMoney($totalIn) }}</td>
                    <td class="r">-{{ pdfMoney($totalOut) }}</td>
                    <td class="r">{{ ($netTotal >= 0 ? '+' : '') . pdfMoney($netTotal) }}</td>
                    <td class="r">&mdash;</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="color:#aaa; padding:16px 0;">No cash movement found for the selected period.</p>
        @endif

        <div class="doc-footer">
            <div class="fl">{{ config('app.name', 'Hotel Management') }} &mdash; Confidential Financial Report</div>
            <div class="fr">Page 1 of 1</div>
        </div>
    </div>
</body>
</html>
