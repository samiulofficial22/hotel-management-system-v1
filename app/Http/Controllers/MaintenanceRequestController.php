<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Repositories\MaintenanceRequestRepository;
use App\Repositories\RoomRepository;
use App\Services\DepartmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceRequestController extends Controller
{
    public function __construct(
        protected MaintenanceRequestRepository $repository,
        protected RoomRepository $roomRepository,
        protected DepartmentService $departmentService
    ) {}

    public function index(Request $request): View
    {
        $requests = $this->repository->paginate(15, $request->input('status'));
        return view('maintenance.index', compact('requests'));
    }

    public function create(): View
    {
        $rooms = $this->roomRepository->all(false);
        $departments = $this->departmentService->all(true);
        return view('maintenance.create', compact('rooms', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['room_id' => 'nullable|exists:rooms,id', 'department_id' => 'nullable|exists:departments,id', 'title' => 'required|string|max:150', 'description' => 'nullable|string', 'priority' => 'nullable|in:low,normal,high,urgent']);
        $data = $request->only(['room_id', 'title', 'description', 'priority']);
        $data['department_id'] = $request->input('department_id') ?: null;
        $data['reported_by'] = auth()->id();
        $data['status'] = MaintenanceRequest::STATUS_OPEN;
        $this->repository->create($data);
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
        $departments = $this->departmentService->all(true);
        return view('maintenance.edit', ['request' => $maintenance_request, 'rooms' => $rooms, 'users' => $users, 'departments' => $departments]);
    }

    public function update(Request $request, MaintenanceRequest $maintenance_request): RedirectResponse
    {
        $request->validate([
            'status' => 'nullable|in:open,in_progress,resolved,cancelled',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'resolution_notes' => 'nullable|string',
        ]);
        $data = $request->only(['status', 'priority', 'assigned_to', 'resolution_notes']);
        $data['department_id'] = $request->input('department_id') ?: null;
        if (($data['status'] ?? null) === MaintenanceRequest::STATUS_RESOLVED) {
            $data['resolved_at'] = $maintenance_request->resolved_at ?? now();
            $data['resolved_by'] = $maintenance_request->resolved_by ?? auth()->id();
        }
        $this->repository->update($maintenance_request, $data);
        return redirect()->route('maintenance.show', $maintenance_request)->with('success', __('Updated.'));
    }
}
