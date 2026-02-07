<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Services\GuestService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuestController extends Controller
{
    public function __construct(
        protected GuestService $service
    ) {}

    public function index(Request $request): View
    {
        $guests = $this->service->paginateWithSearch(
            $request->integer('per_page', 15),
            $request->input('q')
        );
        return view('guests.index', compact('guests'));
    }

    public function create(): View
    {
        return view('guests.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = array_merge($this->service->rules(), [
            'email' => ['nullable', 'email', 'max:255', Rule::unique('guests', 'email')],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('guests', 'phone')],
            'nid_photo_front' => ['nullable', 'file', 'image', 'max:2048'],
            'nid_photo_back' => ['nullable', 'file', 'image', 'max:2048'],
            'portal_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        $validated = $request->validate($rules);
        $photoFront = $request->file('nid_photo_front');
        $photoBack = $request->file('nid_photo_back');
        unset($validated['nid_photo_front'], $validated['nid_photo_back']);
        $guest = $this->service->create($validated);
        $this->storeNidPhotos($guest, $photoFront, $photoBack);

        // NEW – SAFE ADDITION: Optional portal login creation when requested by admin.
        if ($request->boolean('create_portal_user')) {
            $this->ensurePortalUserForGuest(
                $guest,
                $request->input('portal_password') ?: null,
                $request->boolean('portal_send_reset')
            );
        }

        return redirect()->route('guests.index')->with('success', __('Guest created.'));
    }

    public function show(Guest $guest): View
    {
        $guest->load(['bookings.room.roomType', 'invoices']);
        return view('guests.show', compact('guest'));
    }

    public function edit(Guest $guest): View
    {
        return view('guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest): RedirectResponse
    {
        $rules = array_merge($this->service->rules(), [
            'email' => ['nullable', 'email', 'max:255', Rule::unique('guests', 'email')->ignore($guest->id)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('guests', 'phone')->ignore($guest->id)],
            'nid_photo_front' => ['nullable', 'file', 'image', 'max:2048'],
            'nid_photo_back' => ['nullable', 'file', 'image', 'max:2048'],
            'portal_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        $validated = $request->validate($rules);
        $photoFront = $request->file('nid_photo_front');
        $photoBack = $request->file('nid_photo_back');
        unset($validated['nid_photo_front'], $validated['nid_photo_back']);
        $this->service->update($guest, $validated);
        $this->storeNidPhotos($guest, $photoFront, $photoBack);

        // NEW – SAFE ADDITION: Optional portal login creation/linking on update.
        // Also allow updating portal password/reset even if the checkbox was already enabled before.
        if (
            $request->boolean('create_portal_user') ||
            $request->filled('portal_password') ||
            $request->boolean('portal_send_reset')
        ) {
            $this->ensurePortalUserForGuest(
                $guest,
                $request->input('portal_password') ?: null,
                $request->boolean('portal_send_reset')
            );
        }

        return redirect()->route('guests.show', $guest)->with('success', __('Guest updated.'));
    }

    protected function storeNidPhotos(Guest $guest, $photoFront, $photoBack): void
    {
        $dir = 'guest-nid/' . $guest->id;
        if ($photoFront) {
            if ($guest->nid_photo_front) {
                Storage::disk('public')->delete($guest->nid_photo_front);
            }
            $path = $photoFront->store($dir, 'public');
            $guest->update(['nid_photo_front' => $path]);
        }
        if ($photoBack) {
            if ($guest->nid_photo_back) {
                Storage::disk('public')->delete($guest->nid_photo_back);
            }
            $path = $photoBack->store($dir, 'public');
            $guest->update(['nid_photo_back' => $path]);
        }
    }

    public function destroy(Guest $guest): RedirectResponse
    {
        $this->service->delete($guest);
        return redirect()->route('guests.index')->with('success', __('Guest deleted.'));
    }

    /**
     * NEW – SAFE ADDITION:
     * Ensure there is a linked User with Guest role for this guest.
     * - Reuses existing user by email when possible.
     * - Does not remove any existing roles.
     * - Does not affect admin/reception flows.
     */
    protected function ensurePortalUserForGuest(Guest $guest, ?string $plainPassword = null, bool $sendReset = false): void
    {
        // Already linked to a user – just ensure Guest role is present.
        if ($guest->user) {
            if (! $guest->user->hasRole('Guest')) {
                $guest->user->assignRole('Guest');
            }
            // Admin-specified password update for existing linked user (always store hash in DB).
            if ($plainPassword) {
                $guest->user->password = Hash::make($plainPassword);
                $guest->user->save();
            }
            if ($sendReset && $guest->email) {
                Password::sendResetLink(['email' => $guest->email]);
            }
            return;
        }

        // Try to reuse an existing user with same email.
        $user = null;
        if (! empty($guest->email)) {
            $user = User::where('email', $guest->email)->first();
        }

        if (! $user) {
            // Create a new minimal user for portal access. Password always stored as hash in DB.
            $email = $guest->email ?: 'guest+' . $guest->id . '@example.invalid';
            $user = User::create([
                'name' => $guest->full_name,
                'email' => $email,
                'password' => Hash::make($plainPassword ?: Str::random(12)),
            ]);
        } else {
            // Reusing existing user by email – update password if provided (always store hash).
            if ($plainPassword) {
                $user->password = Hash::make($plainPassword);
                $user->save();
            }
        }

        if (! $user->hasRole('Guest')) {
            $user->assignRole('Guest');
        }

        $guest->user_id = $user->id;
        $guest->save();

        if ($sendReset && $guest->email) {
            Password::sendResetLink(['email' => $guest->email]);
        }
    }

    /**
     * NEW – SAFE ADDITION:
     * Remove portal access for this guest (unlink user and remove Guest role),
     * without affecting other admin logic.
     */
    public function revokePortal(Guest $guest): RedirectResponse
    {
        if ($guest->user) {
            $user = $guest->user;
            if ($user->hasRole('Guest')) {
                $user->removeRole('Guest');
            }
        }

        $guest->user_id = null;
        $guest->save();

        return redirect()->route('guests.show', $guest)->with('success', __('Guest portal access revoked.'));
    }
}
