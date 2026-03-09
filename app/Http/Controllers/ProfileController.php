<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'profile_pic' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];

        if ($user->hasRole('Admin')) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id];
        }
        else {
            $rules['email'] = ['required', 'string', 'email', 'in:' . $user->email];
        }

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        $validated = $request->validate($rules);

        if (!$user->hasRole('Admin')) {
            $validated['email'] = $user->email;
        }

        if ($request->hasFile('profile_pic')) {
            if ($user->profile_pic) {
                Storage::disk('public')->delete($user->profile_pic);
            }
            $validated['profile_pic'] = $request->file('profile_pic')->store('profile-pics', 'public');
        }
        else {
            unset($validated['profile_pic']);
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update(array_filter($validated));

        return redirect()->route('profile.edit')->with('success', __('Profile updated.'));
    }
}
