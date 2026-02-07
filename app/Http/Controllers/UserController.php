<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $searchStaff = trim($request->input('q_staff', ''));
        $searchGuests = trim($request->input('q_guests', ''));
        $likeStaff = $searchStaff ? '%' . $searchStaff . '%' : null;
        $likeGuests = $searchGuests ? '%' . $searchGuests . '%' : null;

        $guestsQuery = User::role('Guest')->with(['roles', 'guest'])->orderByDesc('created_at');
        if ($likeGuests) {
            $guestsQuery->where(function ($q) use ($likeGuests) {
                $q->where('name', 'like', $likeGuests)->orWhere('email', 'like', $likeGuests);
            });
        }
        $guests = $guestsQuery->paginate(15, ['*'], 'guests_page')->withQueryString();

        $staffQuery = User::whereDoesntHave('roles', fn ($q) => $q->where('name', 'Guest'))
            ->with('roles')
            ->orderBy('name');
        if ($likeStaff) {
            $staffQuery->where(function ($q) use ($likeStaff) {
                $q->where('name', 'like', $likeStaff)->orWhere('email', 'like', $likeStaff);
            });
        }
        $officeStaff = $staffQuery->paginate(15, ['*'], 'staff_page')->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'staff_rows' => view('users.partials.staff-rows', compact('officeStaff'))->render(),
                'staff_pagination' => $officeStaff->links()->render(),
                'guests_rows' => view('users.partials.guests-rows', compact('guests'))->render(),
                'guests_pagination' => $guests->links()->render(),
            ]);
        }

        return view('users.index', compact('guests', 'officeStaff', 'searchStaff', 'searchGuests'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->pluck('name', 'id');
        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (! empty($data['roles'])) {
            $user->syncRoles(Role::whereIn('id', $data['roles'])->pluck('name')->all());
        }

        return redirect()->route('users.index')->with('success', __('User created.'));
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->pluck('name', 'id');
        $assignedRoles = $user->roles()->pluck('id')->all();

        return view('users.edit', compact('user', 'roles', 'assignedRoles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $update = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];
        if (! empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        $roleIds = $data['roles'] ?? [];
        $user->syncRoles(Role::whereIn('id', $roleIds)->pluck('name')->all());

        return redirect()->route('users.index')->with('success', __('User updated.'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('success', __('Cannot delete your own user.'));
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', __('User deleted.'));
    }
}

