<?php

namespace App\Repositories;

use App\Models\BanquetBooking;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BanquetBookingRepository
{
    public function __construct(protected BanquetBooking $model) {}

    public function find(int $id): ?BanquetBooking
    {
        return $this->model->with(['banquetVenue'])->find($id);
    }

    public function paginate(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with('banquetVenue')->orderByDesc('id');
        if ($status !== null) {
            $q->where('status', $status);
        }
        return $q->paginate($perPage);
    }

    /** Paginate with optional search (event name, contact name, venue) and status filter. */
    public function paginateWithSearch(int $perPage = 15, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with('banquetVenue')->orderByDesc('id');
        if ($search !== null && trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $q->where(function ($query) use ($term) {
                $query->where('event_name', 'like', $term)
                    ->orWhere('contact_name', 'like', $term)
                    ->orWhere('contact_phone', 'like', $term)
                    ->orWhere('contact_email', 'like', $term)
                    ->orWhereHas('banquetVenue', fn ($q) => $q->where('name', 'like', $term));
            });
        }
        if ($status !== null && $status !== '') {
            $q->where('status', $status);
        }
        return $q->paginate($perPage)->withQueryString();
    }

    public function byVenueAndDate(int $venueId, Carbon $date): Collection
    {
        return $this->model->newQuery()
            ->where('banquet_venue_id', $venueId)
            ->where('event_date', $date->toDateString())
            ->whereNotIn('status', [BanquetBooking::STATUS_CANCELLED])
            ->orderBy('start_time')
            ->get();
    }

    public function create(array $data): BanquetBooking
    {
        return $this->model->create($data);
    }

    public function update(BanquetBooking $booking, array $data): BanquetBooking
    {
        $booking->update($data);
        return $booking->fresh();
    }
}
