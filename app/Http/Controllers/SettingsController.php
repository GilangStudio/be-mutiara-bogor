<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        return view('pages.settings.index');
    }

    /**
     * Update user profile (name, username, email)
     */
    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::user()->id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ], [
            'name.required' => 'Name is required',
            'name.max' => 'Name cannot exceed 255 characters',
            'username.required' => 'Username is required',
            'username.max' => 'Username cannot exceed 255 characters',
            'username.unique' => 'Username is already taken',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'Email is already taken',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
            ]);

            return redirect()->route('settings.index')
                           ->with('success', 'Profile updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('settings.index')
                           ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = User::find(Auth::user()->id);
        
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Current password is required',
            'password.required' => 'New password is required',
            'password.confirmed' => 'Password confirmation does not match',
            'password.min' => 'Password must be at least 8 characters',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('settings.index')
                           ->with('error', 'Current password is incorrect')
                           ->withInput();
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('settings.index')
                           ->with('success', 'Password updated successfully');

        } catch (\Exception $e) {
            return redirect()->route('settings.index')
                           ->with('error', 'Failed to update password: ' . $e->getMessage());
        }
    }
}