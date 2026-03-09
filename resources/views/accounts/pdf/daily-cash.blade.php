<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Daily Cash Summary</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1a1a2e; background: #fff; }
        .page-header { background: linear-gradient(135deg, #065f46 0%, #047857 100%); color: #fff; padding: 24px 28px 18px; margin-bottom: 20px; }
        .page-header .hotel-name { font-size: 18px; font-weight: 700; letter-spacing: 1px; margin-bottom: 4px; }
        .page-header .report-title { font-size: 13px; color: #a7f3d0; font-weight: 600; margin-bottom: 2px; }
        .page-header .report-period { font-size: 9px; color: #d1fae5; }
        .content { padding: 0 28px 24px; }
        .totals-row { background: #f0fdf4; border-radius: 8px; padding: 12px 16px; margin-bottom: 18px; display: table; width: 100%; }
        .totals-cell { display: table-cell; padding: 0 12px; text-align: center; }
        .totals-cell .label { font-size: 8px; text-transform: uppercase; color: #888; margin-bottom: 4px; }
        .totals-cell .val { font-size: 14px; font-weight: 700; }
        .val.green { color: #16a34a; }
        .val.red { color: #dc2626; }
        .val.blue { color: #2563eb; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #065f46; color: #fff; }
        thead th { padding: 8px 10px; font-size: 9px; font-weight: 600; text-align: left; }
        thead th.r { text-align: right; }
        tbody tr:nth-child(even) { background: #f0fdf4; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #d1fae5; font-size: 9px; }
        tbody td.r { text-align: right; font-weight: 600; }
        tbody td.pos { color: #16a34a; }
        tbody td.neg { color: #dc2626; }
        tbody td.net-pos { color: #2563eb; }
        tbody td.net-neg { color: #ea580c; }
        tfoot tr { background: #064e3b; color: #fff; }
        tfoot td { padding: 8px 10px; font-weight: 700; font-size: 9px; }
        tfoot td.r { text-align: right; }
        .footer { margin-top: 24px; padding-top: 8px; border-top: 1px solid #e5e7eb; font-size: 8px; color: #aaa; }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="hotel-name">🏨 Hotel Management System</div>
        <div class="report-title">Daily Cash Summary</div>
        <div class="report-period">Period: {{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}</div>
    </div>
    <div class="content">
        @php
            $totalIn  = array_sum(array_column($daily, 'cashIn'));
            $totalOut = array_sum(array_column($daily, 'cashOut'));
            $netTotal = $totalIn - $totalOut;
        @endphp
        <div class="totals-row">
            <div class="totals-cell">
                <div class="label">Total Cash In</div>
                <div class="val green">{{ money($totalIn) }}</div>
            </div>
            <div class="totals-cell" style="border-left:1px solid #d1fae5; border-right:1px solid #d1fae5;">
                <div class="label">Total Cash Out</div>
                <div class="val red">{{ money($totalOut) }}</div>
            </div>
            <div class="totals-cell">
                <div class="label">Net Movement</div>
                <div class="val {{ $netTotal >= 0 ? 'blue' : 'red' }}">{{ money($netTotal) }}</div>
            </div>
        </div>

        @if(!empty($daily))
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="r">Opening</th>
                    <th class="r">Cash In</th>
                    <th class="r">Cash Out</th>
                    <th class="r">Net</th>
                    <th class="r">Closing</th>
                </tr>
            </thead>
            <tbody>
                @foreach($daily as $date => $data)
                <tr>
                    <td><strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong></td>
                    <td class="r">{{ money($data['opening']) }}</td>
                    <td class="r pos">+{{ money($data['cashIn']) }}</td>
                    <td class="r neg">-{{ money($data['cashOut']) }}</td>
                    <td class="r {{ $data['net'] >= 0 ? 'net-pos' : 'net-neg' }}">{{ $data['net'] >= 0 ? '+' : '' }}{{ money($data['net']) }}</td>
                    <td class="r"><strong>{{ money($data['closing']) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>PERIOD TOTALS</td>
                    <td class="r">—</td>
                    <td class="r">+{{ money($totalIn) }}</td>
                    <td class="r">-{{ money($totalOut) }}</td>
                    <td class="r">{{ $netTotal >= 0 ? '+' : '' }}{{ money($netTotal) }}</td>
                    <td class="r">—</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="color:#aaa; padding:16px 0;">No cash movement found for the selected period.</p>
        @endif

        <div class="footer">
            Generated: {{ now()->format('d M Y, h:i A') }} &nbsp;|&nbsp; Hotel Management System — Confidential
        </div>
    </div>
</body>
</html>
