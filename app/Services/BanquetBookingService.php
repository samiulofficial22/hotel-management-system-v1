<?php

namespace App\Services;

use App\Models\BanquetBooking;
use App\Repositories\BanquetBookingRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class BanquetBookingService
{
    public function __construct(
        protected BanquetBookingRepository $repository,
        protected LedgerEntryService $ledgerService,
        protected ChartOfAccountService $chartService
    ) {}

    public function rules(bool $forUpdate = false): array
    {
        return [
            'banquet_venue_id' => ['required', 'exists:banquet_venues,id'],
            'event_name' => ['required', 'string', 'max:150'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'guest_count' => ['required', 'integer', 'min:1'],
            'package_name' => ['nullable', 'string', 'max:100'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:pending,confirmed,cancelled,completed'],
            'payment_method' => ['nullable', 'string'],
            'payment_status' => ['nullable', 'in:unpaid,paid'],
            'contact_name' => ['required', 'string', 'max:100'],
            'contact_phone' => ['required', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function find(int $id): ?BanquetBooking
    {
        return $this->repository->find($id);
    }

    public function paginate(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $status);
    }

    public function paginateWithSearch(int $perPage = 15, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return $this->repository->paginateWithSearch($perPage, $search, $status);
    }

    public function byVenueAndDate(int $venueId, Carbon $date): Collection
    {
        return $this->repository->byVenueAndDate($venueId, $date);
    }

    /** @throws ValidationException */
    public function create(array $data, ?int $userId = null): BanquetBooking
    {
        $this->checkOverlap($data);
        $data['created_by'] = $userId;
        $data['status'] = $data['status'] ?? BanquetBooking::STATUS_PENDING;
        $data['total_amount'] = $data['total_amount'] ?? 0;
        $booking = $this->repository->create($data);
        
        if (in_array($booking->status, [BanquetBooking::STATUS_CONFIRMED, BanquetBooking::STATUS_COMPLETED])) {
            $this->postToLedger($booking);
        }
        
        return $booking;
    }

    /** @throws ValidationException */
    public function update(BanquetBooking $booking, array $data): BanquetBooking
    {
        $oldStatus = $booking->status;
        $this->checkOverlap($data, $booking->id);
        $updated = $this->repository->update($booking, $data);
        
        if ($oldStatus !== $updated->status) {
            $this->postToLedger($updated);
        }
        
        return $updated;
    }

    public function postToLedger(BanquetBooking $booking): void
    {
        $amount = (float) $booking->total_amount;
        if ($amount <= 0) return;

        $ar = $this->chartService->findByCode('AR');
        $banquetRev = $this->chartService->findByCode('BANQUET_REV');
        $cash = $this->chartService->findByCode(strtoupper($booking->payment_method ?? 'CASH') === 'CASH' ? 'CASH' : 'BANK');

        if ($booking->status === BanquetBooking::STATUS_CONFIRMED) {
            if ($ar && $banquetRev) {
                $this->ledgerService->createDoubleEntry(
                    $ar->id, $banquetRev->id, $amount, $booking->event_date->toDateString(),
                    'Banquet Revenue - ' . $booking->event_name,
                    'banquet_booking', $booking->id, auth()->id()
                );
            }
        } elseif ($booking->status === BanquetBooking::STATUS_COMPLETED) {
            if ($cash && $ar) {
                $this->ledgerService->createDoubleEntry(
                    $cash->id, $ar->id, $amount, now()->toDateString(),
                    'Banquet Payment - ' . $booking->event_name,
                    'banquet_booking', $booking->id, auth()->id()
                );
                $booking->update(['payment_status' => 'paid']);
            }
        }
    }

    public function delete(BanquetBooking $booking): void
    {
        $booking->delete();
    }

    protected function checkOverlap(array $data, ?int $ignoreId = null): void
    {
        $exists = BanquetBooking::where('banquet_venue_id', $data['banquet_venue_id'])
            ->where('event_date', $data['event_date'])
            ->where(function ($q) use ($data) {
                $q->where(function ($sub) use ($data) {
                    $sub->where('start_time', '<=', $data['start_time'])
                        ->where('end_time', '>', $data['start_time']);
                })->orWhere(function ($sub) use ($data) {
                    $sub->where('start_time', '<', $data['end_time'])
                        ->where('end_time', '>=', $data['end_time']);
                })->orWhere(function ($sub) use ($data) {
                    $sub->where('start_time', '>=', $data['start_time'])
                        ->where('end_time', '<=', $data['end_time']);
                });
            })
            ->where('status', '!=', BanquetBooking::STATUS_CANCELLED);

        if ($ignoreId) {
            $exists->where('id', '!=', $ignoreId);
        }

        if ($exists->exists()) {
            throw ValidationException::withMessages([
                'start_time' => [__('This venue is already booked for the selected date and time.')],
            ]);
        }
    }
}
