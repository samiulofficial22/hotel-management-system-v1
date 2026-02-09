<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            ->with('roles');
        if ($likeStaff) {
            $staffQuery->where(function ($q) use ($likeStaff) {
                $q->where('name', 'like', $likeStaff)->orWhere('email', 'like', $likeStaff);
            });
        }
        $officeStaffList = $staffQuery->orderBy('name')->get();
        $roleOrder = ['Admin', 'Manager', 'Receptionist', 'Housekeeping', 'Accountant', 'Other'];
        $staffByRole = $officeStaffList->groupBy(function ($user) use ($roleOrder) {
            $roleNames = $user->roles->pluck('name');
            foreach ($roleOrder as $r) {
                if ($r === 'Other') {
                    continue;
                }
                if ($roleNames->contains($r)) {
                    return $r;
                }
            }
            return 'Other';
        });
        foreach ($roleOrder as $r) {
            if (! $staffByRole->has($r)) {
                $staffByRole->put($r, collect());
            }
        }
        $staffByRole = $staffByRole->sortKeysUsing(function ($a, $b) use ($roleOrder) {
            $posA = array_search($a, $roleOrder);
            $posB = array_search($b, $roleOrder);
            return ($posA !== false ? $posA : 999) <=> ($posB !== false ? $posB : 999);
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'staff_rows' => view('users.partials.staff-rows', compact('staffByRole', 'roleOrder'))->render(),
                'staff_pagination' => '',
                'guests_rows' => view('users.partials.guests-rows', compact('guests'))->render(),
                'guests_pagination' => $guests->links()->render(),
            ]);
        }

        return view('users.index', compact('guests', 'staffByRole', 'roleOrder', 'searchStaff', 'searchGuests'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->pluck('name', 'id');
        $canAssignRole = auth()->user()?->can('roles.assign');
        return view('users.create', compact('roles', 'canAssignRole'));
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

        if (auth()->user()?->can('roles.assign') && ! empty($data['roles'] ?? [])) {
            $user->syncRoles(Role::whereIn('id', $data['roles'])->pluck('name')->all());
        }

        return redirect()->route('users.index')->with('success', __('User created.'));
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->pluck('name', 'id');
        $assignedRoles = $user->roles()->pluck('id')->all();
        $canAssignRole = auth()->user()?->can('roles.assign');

        return view('users.edit', compact('user', 'roles', 'assignedRoles', 'canAssignRole'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_pic' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
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
        if ($request->hasFile('profile_pic')) {
            if ($user->profile_pic) {
                Storage::disk('public')->delete($user->profile_pic);
            }
            $update['profile_pic'] = $request->file('profile_pic')->store('profile-pics', 'public');
        }

        $user->update($update);

        if (auth()->user()?->can('roles.assign')) {
            $roleIds = $data['roles'] ?? [];
            $user->syncRoles(Role::whereIn('id', $roleIds)->pluck('name')->all());
        }

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

