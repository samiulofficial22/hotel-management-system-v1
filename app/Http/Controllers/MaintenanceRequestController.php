<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Notifications\MaintenanceAssignedNotification;
use App\Notifications\MaintenanceCompletedNotification;
use App\Notifications\NewMaintenanceRequestNotification;
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
        $users = User::role('Housekeeper')->orderBy('name')->get(['id', 'name']);
        $departments = $this->departmentService->all(true);
        return view('maintenance.form', ['rooms' => $rooms, 'users' => $users, 'departments' => $departments, 'request' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'department_id' => 'nullable|exists:departments,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'resolution_notes' => 'nullable|string|max:2000',
        ]);
        $data = $request->only(['room_id', 'title', 'description', 'priority']);
        $data['department_id'] = $request->input('department_id') ?: null;
        $data['reported_by'] = auth()->id();
        $data['status'] = MaintenanceRequest::STATUS_OPEN;
        $data['assigned_to'] = $request->input('assigned_to') ?: null;
        $data['resolution_notes'] = $request->input('resolution_notes') ?: null;
        $maintenanceRequest = $this->repository->create($data);
        $maintenanceRequest->load('reportedBy', 'room');
        if ($maintenanceRequest->assigned_to) {
            try {
                User::find($maintenanceRequest->assigned_to)?->notify(new MaintenanceAssignedNotification($maintenanceRequest));
            } catch (\Throwable $e) {
                report($e);
            }
        }
        $admins = User::permission('maintenance.manage')->get();
        foreach ($admins as $user) {
            try {
                $user->notify(new NewMaintenanceRequestNotification($maintenanceRequest));
            } catch (\Throwable $e) {
                report($e);
            }
        }
        return redirect()->route('maintenance.index')->with('success', __('Request created.'));
    }

    public function show(MaintenanceRequest $maintenance_request): View
    {
        $maintenance_request->load(['room', 'reportedBy', 'assignedTo', 'resolvedBy']);
        return view('maintenance.show', ['request' => $maintenance_request]);
    }

    /** Allow assigned user or maintenance.manage to start the request (status → in_progress). */
    public function start(MaintenanceRequest $maintenance_request): RedirectResponse
    {
        $isAssigned = (int) $maintenance_request->assigned_to === (int) auth()->id();
        if (! $isAssigned && ! auth()->user()?->can('maintenance.manage')) {
            abort(403, __('You can only start requests assigned to you.'));
        }
        if ($maintenance_request->status !== MaintenanceRequest::STATUS_OPEN) {
            return redirect()->route('maintenance.show', $maintenance_request)->with('info', __('Already started or resolved.'));
        }
        $this->repository->update($maintenance_request, [
            'status' => MaintenanceRequest::STATUS_IN_PROGRESS,
            'started_at' => $maintenance_request->started_at ?? now(),
        ]);
        return redirect()->route('maintenance.show', $maintenance_request)->with('success', __('messages.Work started.'));
    }

    /** Allow assigned user or maintenance.manage to complete with notes (status → resolved). */
    public function complete(Request $request, MaintenanceRequest $maintenance_request): RedirectResponse
    {
        $isAssigned = (int) $maintenance_request->assigned_to === (int) auth()->id();
        if (! $isAssigned && ! auth()->user()?->can('maintenance.manage')) {
            abort(403, __('You can only complete requests assigned to you.'));
        }
        $request->validate(['notes' => 'nullable|string|max:2000']);
        $notes = $request->input('notes', '');
        if ($maintenance_request->status === MaintenanceRequest::STATUS_RESOLVED) {
            return redirect()->route('maintenance.show', $maintenance_request)->with('info', __('Already resolved.'));
        }
        $this->repository->update($maintenance_request, [
            'status' => MaintenanceRequest::STATUS_RESOLVED,
            'resolution_notes' => $notes,
            'resolved_at' => $maintenance_request->resolved_at ?? now(),
            'resolved_by' => $maintenance_request->resolved_by ?? auth()->id(),
        ]);
        $maintenance_request->refresh()->load(['room', 'resolvedBy']);
        $completedByName = $maintenance_request->resolvedBy?->name ?? auth()->user()->name;
        $admins = User::permission('maintenance.manage')->get();
        foreach ($admins as $user) {
            if ((int) $user->id === (int) auth()->id()) {
                continue;
            }
            try {
                $user->notify(new MaintenanceCompletedNotification($maintenance_request, $completedByName));
            } catch (\Throwable $e) {
                report($e);
            }
        }
        return redirect()->route('maintenance.show', $maintenance_request)->with('success', __('messages.Work completed.'));
    }

    /** Allow assigned user or maintenance.manage to update resolution notes after complete. */
    public function updateNotes(Request $request, MaintenanceRequest $maintenance_request): RedirectResponse
    {
        $isAssigned = (int) $maintenance_request->assigned_to === (int) auth()->id();
        if (! $isAssigned && ! auth()->user()?->can('maintenance.manage')) {
            abort(403, __('You can only edit notes for requests assigned to you.'));
        }
        $request->validate(['notes' => 'nullable|string|max:2000']);
        $this->repository->update($maintenance_request, ['resolution_notes' => $request->input('notes')]);
        return redirect()->route('maintenance.show', $maintenance_request)->with('success', __('Notes updated.'));
    }

    public function edit(MaintenanceRequest $maintenance_request): View
    {
        $rooms = $this->roomRepository->all(false);
        $users = User::role('Housekeeper')->orderBy('name')->get(['id', 'name']);
        if ($maintenance_request->assigned_to && ! $users->contains('id', $maintenance_request->assigned_to)) {
            $current = User::find($maintenance_request->assigned_to, ['id', 'name']);
            if ($current) {
                $users = $users->push($current)->sortBy('name')->values();
            }
        }
        $departments = $this->departmentService->all(true);
        return view('maintenance.form', ['request' => $maintenance_request, 'rooms' => $rooms, 'users' => $users, 'departments' => $departments]);
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
        $previousAssignedTo = $maintenance_request->assigned_to;
        $this->repository->update($maintenance_request, $data);
        $maintenance_request->refresh();
        if (! empty($maintenance_request->assigned_to) && $maintenance_request->assigned_to != $previousAssignedTo) {
            $assignedUser = User::find($maintenance_request->assigned_to);
            if ($assignedUser) {
                try {
                    $assignedUser->notify(new MaintenanceAssignedNotification($maintenance_request));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
        return redirect()->route('maintenance.show', $maintenance_request)->with('success', __('Updated.'));
    }
}
