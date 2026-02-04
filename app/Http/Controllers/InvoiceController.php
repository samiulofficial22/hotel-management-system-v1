<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    /**
     * Download invoice as PDF.
     */
    public function pdf(Invoice $invoice): Response
    {
        $invoice->load(['guest', 'booking.room.roomType', 'items', 'payments']);
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';
        return $pdf->download($filename);
    }
}
