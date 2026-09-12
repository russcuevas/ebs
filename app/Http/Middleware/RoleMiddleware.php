<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if student email is verified
        if ($user->role === 'student' && !$user->email_verified_at) {
            if (!$request->is('verify-otp*') && !$request->is('logout')) {
                return redirect()->route('verification.notice');
            }
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Unauthorized access based on role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorized access.');
        } elseif ($user->role === 'staff') {
            return redirect()->route('staff.dashboard')->with('error', 'Unauthorized access.');
        } else {
            return redirect()->route('student.dashboard')->with('error', 'Unauthorized access.');
        }
    }
}
