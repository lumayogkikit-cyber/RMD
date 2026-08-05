<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access the system.');
        }

        $user = Auth::user();

        if (!$user->isActive()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended. Please contact the Super Admin.');
        }

        // If user is super_admin, grant full access
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user's role matches any of the required roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        return redirect()->route('scaling.index')->with('error', 'Unauthorized access! You do not have permission for this section.');
    }
}
