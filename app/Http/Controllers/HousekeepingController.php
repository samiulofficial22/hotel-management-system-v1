<?php

namespace App\Http\Controllers;

use App\Models\HousekeepingAssignment;
use App\Models\Room;
use App\Models\User;
use App\Notifications\RoomAssignedForCleaningNotification;
use App\Notifications\RoomCleaningCompletedNotification;
use App\Services\HousekeepingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HousekeepingController extends Controller
{
    public function __construct(protected HousekeepingService $service) {}

    public function index(Request $request): View
    {
        $date = $request->has('date') ? Carbon::parse($request->date) : today();
        $roomId = $request->filled('room_id') ? (int) $request->room_id : null;
        $canAssignOthers = auth()->user()?->can('housekeeping.manage') && ! auth()->user()?->hasRole('Housekeeping');
        $userId = $canAssignOthers ? null : auth()->id();
        $assignments = $this->service->assignmentsForDate($date, $userId, $roomId);
        $rooms = $this->service->allRooms();
        $users = User::role('Housekeeping')->orderBy('name')->get(['id', 'name']);
        return view('housekeeping.index', compact('assignments', 'rooms', 'date', 'users', 'roomId'));
    }

    public function assign(Request $request): RedirectResponse
    {
        $rules = ['room_id' => 'required|exists:rooms,id', 'date' => 'required|date'];
        $canAssignOthers = auth()->user()?->can('housekeeping.manage') && ! auth()->user()?->hasRole('Housekeeping');
        if ($canAssignOthers) {
            $housekeeperIds = User::role('Housekeeping')->pluck('id')->implode(',');
            $rules['assigned_to'] = ['required', 'exists:users,id', 'in:' . $housekeeperIds];
        }
        $validated = $request->validate($rules);

        if (! $canAssignOthers) {
            if ($request->filled('assigned_to') && (int) $request->assigned_to !== (int) auth()->id()) {
                abort(403, __('You can only assign rooms to yourself.'));
            }
        }

        $assignedTo = $canAssignOthers ? (int) $validated['assigned_to'] : (int) auth()->id();
        $assignment = $this->service->assignRoom(
            (int) $request->room_id,
            Carbon::parse($request->date),
            $assignedTo
        );
        $assignment->load(['room', 'assignedTo']);
        $assignment->assignedTo?->notify(new RoomAssignedForCleaningNotification($assignment));
        return redirect()->route('housekeeping.index', ['date' => $request->date])->with('success', __('Room assigned.'));
    }

    public function edit(HousekeepingAssignment $assignment): View
    {
        if (auth()->user()?->hasRole('Housekeeping')) {
            abort(403, __('Only admin or manager can edit assignments.'));
        }
        $assignment->load(['room.roomType', 'assignedTo']);
        $rooms = $this->service->allRooms();
        $users = User::role('Housekeeping')->orderBy('name')->get(['id', 'name']);
        return view('housekeeping.edit', compact('assignment', 'rooms', 'users'));
    }

    public function update(Request $request, HousekeepingAssignment $assignment): RedirectResponse
    {
        if (auth()->user()?->hasRole('Housekeeping')) {
            abort(403, __('Only admin or manager can update assignments.'));
        }
        $housekeeperIds = User::role('Housekeeping')->pluck('id')->implode(',');
        $validated = $request->validate([
            'room_id' => [
                'required',
                'exists:rooms,id',
                Rule::unique('housekeeping_assignments')
                    ->where('date', $request->input('date'))
                    ->ignore($assignment->id),
            ],
            'date' => 'required|date',
            'assigned_to' => ['required', 'exists:users,id', 'in:' . $housekeeperIds],
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);
        $newStatus = $validated['status'];
        $update = [
            'room_id' => (int) $validated['room_id'],
            'date' => $validated['date'],
            'assigned_to' => (int) $validated['assigned_to'],
            'status' => $newStatus,
        ];
        if ($newStatus === HousekeepingAssignment::STATUS_COMPLETED && ! $assignment->completed_at) {
            $update['completed_at'] = now();
        }
        if ($newStatus !== HousekeepingAssignment::STATUS_COMPLETED) {
            $update['completed_at'] = null;
        }
        $assignment->update($update);
        $room = $assignment->room;
        if ($room) {
            if ($newStatus === HousekeepingAssignment::STATUS_IN_PROGRESS) {
                $room->update(['status' => Room::STATUS_CLEANING]);
            } elseif ($newStatus === HousekeepingAssignment::STATUS_COMPLETED) {
                $room->update(['status' => Room::STATUS_AVAILABLE]);
            }
        }
        $assignment->load(['room', 'assignedTo']);
        $assignment->assignedTo?->notify(new RoomAssignedForCleaningNotification($assignment->fresh()));
        return redirect()->route('housekeeping.index', ['date' => $assignment->date->format('Y-m-d')])->with('success', __('Assignment updated.'));
    }

    public function destroy(HousekeepingAssignment $assignment): RedirectResponse
    {
        if (auth()->user()?->hasRole('Housekeeping')) {
            abort(403, __('Only admin or manager can delete assignments.'));
        }
        $date = $assignment->date->format('Y-m-d');
        $assignment->delete();
        return redirect()->route('housekeeping.index', ['date' => $date])->with('success', __('Assignment deleted.'));
    }

    public function reassign(Request $request, HousekeepingAssignment $assignment): RedirectResponse
    {
        if (auth()->user()?->hasRole('Housekeeping')) {
            abort(403, __('You cannot reassign rooms. Only admin or manager can.'));
        }
        $request->validate([
            'assigned_to' => ['required', 'exists:users,id', 'in:' . User::role('Housekeeping')->pluck('id')->implode(',')],
        ]);
        $this->service->reassign($assignment, (int) $request->assigned_to);
        $assignment->load(['room', 'assignedTo']);
        $assignment->assignedTo?->notify(new RoomAssignedForCleaningNotification($assignment));
        return redirect()->route('housekeeping.index', ['date' => $assignment->date->format('Y-m-d')])->with('success', __('Reassigned.'));
    }

    public function start(HousekeepingAssignment $assignment): RedirectResponse
    {
        $this->service->startAssignment($assignment);
        return redirect()->route('housekeeping.index', ['date' => $assignment->date->format('Y-m-d')])->with('success', __('Started.'));
    }

    public function complete(Request $request, HousekeepingAssignment $assignment): RedirectResponse
    {
        $assignment = $this->service->completeAssignment($assignment, $request->notes);
        $assignment->load(['room', 'assignedTo']);
        $completedByUserId = $assignment->assigned_to;
        $adminsAndManagers = User::permission('housekeeping.manage')->get();
        foreach ($adminsAndManagers as $user) {
            if ((int) $user->id !== (int) $completedByUserId) {
                try {
                    $user->notify(new RoomCleaningCompletedNotification($assignment));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
        return redirect()->route('housekeeping.index', ['date' => $assignment->date->format('Y-m-d')])->with('success', __('Completed.'));
    }

    public function updateNotes(Request $request, HousekeepingAssignment $assignment): RedirectResponse
    {
        $isAssigned = (int) $assignment->assigned_to === (int) auth()->id();
        $canEditOthers = auth()->user()?->can('housekeeping.manage') && ! auth()->user()?->hasRole('Housekeeping');
        if (! $isAssigned && ! $canEditOthers) {
            abort(403, __('You can only edit notes for your own assignments.'));
        }
        $request->validate(['notes' => 'nullable|string|max:1000']);
        $assignment->update(['notes' => $request->input('notes')]);
        return redirect()->route('housekeeping.index', ['date' => $assignment->date->format('Y-m-d')])->with('success', __('Notes updated.'));
    }
}
