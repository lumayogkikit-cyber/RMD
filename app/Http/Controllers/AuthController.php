<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('scaling.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is suspended. Please contact management.',
                ]);
            }

            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'User Login',
                'details' => "Logged in successfully as {$user->role}.",
                'ip_address' => $request->ip(),
            ]);

            if ($user->isSuperAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', "Welcome Super Admin Master, {$user->name}!");
            }

            return redirect()->intended(route('scaling.index'))
                ->with('success', "Welcome, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'Invalid credentials provided.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'User Logout',
                'details' => 'Logged out of the system.',
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
