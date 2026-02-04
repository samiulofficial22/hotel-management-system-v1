<?php

namespace App\Http\Controllers;

use App\Models\HousekeepingAssignment;
use App\Models\User;
use App\Services\HousekeepingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HousekeepingController extends Controller
{
    public function __construct(protected HousekeepingService $service) {}

    public function index(Request $request): View
    {
        $date = $request->has('date') ? Carbon::parse($request->date) : today();
        $userId = auth()->user()?->can('housekeeping.manage') ? null : auth()->id();
        $assignments = $this->service->assignmentsForDate($date, $userId);
        $rooms = $this->service->allRooms();
        $users = User::orderBy('name')->get(['id', 'name']);
        return view('housekeeping.index', compact('assignments', 'rooms', 'date', 'users'));
    }

    public function assign(Request $request): RedirectResponse
    {
        $rules = ['room_id' => 'required|exists:rooms,id', 'date' => 'required|date'];
        if (auth()->user()?->can('housekeeping.manage')) {
            $rules['assigned_to'] = 'required|exists:users,id';
        }
        $validated = $request->validate($rules);
        $assignedTo = isset($validated['assigned_to']) ? (int) $validated['assigned_to'] : (int) auth()->id();
        $this->service->assignRoom(
            (int) $request->room_id,
            Carbon::parse($request->date),
            $assignedTo
        );
        return redirect()->route('housekeeping.index', ['date' => $request->date])->with('success', __('Room assigned.'));
    }

    public function reassign(Request $request, HousekeepingAssignment $assignment): RedirectResponse
    {
        $request->validate(['assigned_to' => 'required|exists:users,id']);
        $this->service->reassign($assignment, (int) $request->assigned_to);
        return redirect()->route('housekeeping.index', ['date' => $assignment->date->format('Y-m-d')])->with('success', __('Reassigned.'));
    }

    public function start(HousekeepingAssignment $assignment): RedirectResponse
    {
        $this->service->startAssignment($assignment);
        return redirect()->route('housekeeping.index', ['date' => $assignment->date->format('Y-m-d')])->with('success', __('Started.'));
    }

    public function complete(Request $request, HousekeepingAssignment $assignment): RedirectResponse
    {
        $this->service->completeAssignment($assignment, $request->notes);
        return redirect()->route('housekeeping.index', ['date' => $assignment->date->format('Y-m-d')])->with('success', __('Completed.'));
    }
}
