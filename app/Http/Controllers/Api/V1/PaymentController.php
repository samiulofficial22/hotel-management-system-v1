<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class PaymentController extends ApiBaseController
{
    public function __construct(
        protected PaymentService $service
    ) {}

    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $payments = $this->service->paginate($request->integer('per_page', 15));
        return PaymentResource::collection($payments)->additional(['success' => true, 'message' => 'Payments']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->service->rules());
        try {
            $payment = $this->service->create($validated);
            return $this->success(new PaymentResource($payment->load(['invoice', 'booking'])), 'Payment created', 201);
        } catch (ValidationException $e) {
            return $this->error($e->getMessage(), 422, $e->errors());
        }
    }

    public function show(Payment $payment): JsonResponse
    {
        $payment->load(['invoice', 'booking', 'receivedBy']);
        return $this->success(new PaymentResource($payment), 'Payment');
    }

    public function update(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'reference' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,completed,failed,refunded,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);
        $this->service->repository->update($payment, $validated);
        return $this->success(new PaymentResource($payment->fresh()), 'Payment updated');
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $this->service->delete($payment);
        return $this->success(null, 'Payment deleted');
    }
}
