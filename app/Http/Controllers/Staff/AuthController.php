<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->role === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        return view('staff.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
            $user = Auth::guard('web')->user();

            if ($user->role !== 'staff') {
                Auth::guard('web')->logout();
                return back()->withErrors(['email' => 'Unauthorized. This login is for staff members only.'])->withInput();
            }

            if (!$user->is_active) {
                Auth::guard('web')->logout();
                return back()->withErrors(['email' => 'Your account has been deactivated.'])->withInput();
            }

            $request->session()->regenerate();
            return redirect()->intended(route('staff.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login');
    }

    public function profile()
    {
        $user = Auth::guard('web')->user();
        return view('staff.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? $user->phone;

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
