<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if ($this->shouldForgetIntendedUrl($request->user(), $request->session()->get('url.intended'))) {
                $request->session()->forget('url.intended');
            }

            return redirect()->intended($this->defaultRedirectPath($request->user()));
        }

        return back()->with('error', 'Invalid credentials.');
    }


    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'បានចាកចេញពីប្រព័ន្ធដោយជោគជ័យ',
                'redirect' => route('login')
            ]);
        }

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    protected function shouldForgetIntendedUrl(User $user, ?string $intendedUrl): bool
    {
        if ($user->hasRole('admin')) {
            return false;
        }

        $intendedPath = trim((string) parse_url((string) $intendedUrl, PHP_URL_PATH), '/');

        return $intendedPath === 'dashboard';
    }

    protected function defaultRedirectPath(User $user): string
    {
        if ($user->hasRole('admin')) {
            return route('dashboard');
        }

        if ($user->hasRole('teacher')) {
            return route('class.index');
        }

        if ($user->hasRole('student')) {
            return route('result.index');
        }

        if ($user->canManageTeachers()) {
            return route('teacher.index');
        }

        if ($user->canViewStudents()) {
            return route('students.index');
        }

        if ($user->canAccessClassesOverview()) {
            return route('class.index');
        }

        if ($user->canAccessResultsOverview()) {
            return route('result.index');
        }

        return route('auth.profile');
    }
}
