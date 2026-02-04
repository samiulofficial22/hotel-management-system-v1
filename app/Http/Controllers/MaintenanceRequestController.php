<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Repositories\MaintenanceRequestRepository;
use App\Repositories\RoomRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceRequestController extends Controller
{
    public function __construct(
        protected MaintenanceRequestRepository $repository,
        protected RoomRepository $roomRepository
    ) {}

    public function index(Request $request): View
    {
        $requests = $this->repository->paginate(15, $request->input('status'));
        return view('maintenance.index', compact('requests'));
    }

    public function create(): View
    {
        $rooms = $this->roomRepository->all(false);
        return view('maintenance.create', compact('rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['room_id' => 'nullable|exists:rooms,id', 'title' => 'required|string|max:150', 'description' => 'nullable|string', 'priority' => 'nullable|in:low,normal,high,urgent']);
        $this->repository->create($request->only(['room_id', 'title', 'description', 'priority']) + ['reported_by' => auth()->id(), 'status' => MaintenanceRequest::STATUS_OPEN]);
        return redirect()->route('maintenance.index')->with('success', __('Request created.'));
    }

    public function show(MaintenanceRequest $maintenance_request): View
    {
        $maintenance_request->load(['room', 'reportedBy', 'assignedTo', 'resolvedBy']);
        return view('maintenance.show', ['request' => $maintenance_request]);
    }

    public function edit(MaintenanceRequest $maintenance_request): View
    {
        $rooms = $this->roomRepository->all(false);
        $users = User::orderBy('name')->get(['id', 'name']);
        return view('maintenance.edit', ['request' => $maintenance_request, 'rooms' => $rooms, 'users' => $users]);
    }

    public function update(Request $request, MaintenanceRequest $maintenance_request): RedirectResponse
    {
        $request->validate([
            'status' => 'nullable|in:open,in_progress,resolved,cancelled',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'resolution_notes' => 'nullable|string',
        ]);
        $data = $request->only(['status', 'priority', 'assigned_to', 'resolution_notes']);
        if (($data['status'] ?? null) === MaintenanceRequest::STATUS_RESOLVED) {
            $data['resolved_at'] = $maintenance_request->resolved_at ?? now();
            $data['resolved_by'] = $maintenance_request->resolved_by ?? auth()->id();
        }
        $this->repository->update($maintenance_request, $data);
        return redirect()->route('maintenance.show', $maintenance_request)->with('success', __('Updated.'));
    }
}
