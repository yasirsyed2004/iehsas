<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->route('staff.login');
        }

        $user = Auth::guard('web')->user();

        if ($user->role !== 'staff') {
            Auth::guard('web')->logout();
            return redirect()->route('staff.login')->with('error', 'Unauthorized access.');
        }

        if (!$user->is_active) {
            Auth::guard('web')->logout();
            return redirect()->route('staff.login')->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
