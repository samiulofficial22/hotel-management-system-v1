<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends ApiBaseController
{
    public function __construct(
        protected InvoiceService $service
    ) {}

    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $status = $request->input('status');
        $invoices = $this->service->paginate($request->integer('per_page', 15), $status);
        return InvoiceResource::collection($invoices)->additional(['success' => true, 'message' => 'Invoices']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
        $booking = \App\Models\Booking::findOrFail($validated['booking_id']);
        $invoice = $this->service->createFromBooking(
            $booking,
            (float) ($validated['discount_amount'] ?? 0),
            (float) ($validated['tax_rate'] ?? 0)
        );
        return $this->success(new InvoiceResource($invoice->load('items')), 'Invoice created', 201);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['guest', 'booking', 'items', 'payments']);
        return $this->success(new InvoiceResource($invoice), 'Invoice');
    }

    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $validated = $request->validate([
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);
        $this->service->repository->update($invoice, $validated);
        return $this->success(new InvoiceResource($invoice->fresh()->load('items')), 'Invoice updated');
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $this->service->repository->delete($invoice);
        return $this->success(null, 'Invoice deleted');
    }
}
