<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { margin-bottom: 24px; border-bottom: 1px solid #ddd; padding-bottom: 12px; }
        .header h1 { margin: 0; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .totals { margin-top: 16px; max-width: 280px; margin-left: auto; }
        .totals tr td:first-child { text-align: right; padding-right: 12px; }
        .footer { margin-top: 32px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice {{ $invoice->invoice_number }}</h1>
        <p>{{ $invoice->invoice_date?->format('Y-m-d') }} &nbsp;|&nbsp; Due: {{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</p>
    </div>
    <p><strong>Guest:</strong> {{ $invoice->guest->full_name ?? '-' }}</p>
    @if($invoice->booking)
    <p><strong>Booking:</strong> {{ $invoice->booking->booking_number ?? '-' }} &nbsp; <strong>Room:</strong> {{ $invoice->booking->room->number ?? '-' }}</p>
    @endif
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table class="totals">
        <tr><td>Subtotal</td><td>{{ number_format($invoice->subtotal, 2) }}</td></tr>
        @if((float) $invoice->discount_amount > 0)
        <tr><td>Discount</td><td>- {{ number_format($invoice->discount_amount, 2) }}</td></tr>
        @endif
        @if((float) $invoice->tax_amount > 0)
        <tr><td>Tax</td><td>{{ number_format($invoice->tax_amount, 2) }}</td></tr>
        @endif
        <tr><td><strong>Total</strong></td><td><strong>{{ number_format($invoice->total_amount, 2) }}</strong></td></tr>
        <tr><td>Paid</td><td>{{ number_format($invoice->paid_amount, 2) }}</td></tr>
        <tr><td><strong>Balance Due</strong></td><td><strong>{{ number_format($invoice->balance_due, 2) }}</strong></td></tr>
    </table>
    <p><strong>Status:</strong> {{ $invoice->status }}</p>
    @if($invoice->notes)
    <p><strong>Notes:</strong> {{ $invoice->notes }}</p>
    @endif
    <div class="footer">
        <p>Thank you for your business.</p>
    </div>
</body>
</html>
