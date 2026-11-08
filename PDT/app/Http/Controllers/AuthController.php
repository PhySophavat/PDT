<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->intended($this->defaultRedirectPath(Auth::user()));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'សូមបញ្ចូលអ៊ីមែល ឬអត្តលេខសិស្ស (Student ID)',
            'password.required' => 'សូមបញ្ចូលពាក្យសម្ងាត់',
        ]);

        $loginInput = trim((string) $request->input('email'));
        $passwordInput = trim((string) $request->input('password'));
        $remember = $request->boolean('remember', true);

        // Find user by email (case-insensitive) or by Student ID card
        $user = User::where('email', strtolower($loginInput))->first();
        if (!$user) {
            $student = Student::where('student_id_card', strtoupper($loginInput))
                ->orWhere('student_id_card', $loginInput)
                ->first();
            if ($student && $student->user_id) {
                $user = User::find($student->user_id);
            }
        }

        // Verify password with case flexibility (e.g. Pdt@ vs pdt@)
        $passwordValid = false;
        if ($user) {
            if (Hash::check($passwordInput, $user->password)) {
                $passwordValid = true;
            } elseif (Hash::check(strtolower($passwordInput), $user->password)) {
                $passwordValid = true;
            } elseif (Hash::check(ucfirst($passwordInput), $user->password)) {
                $passwordValid = true;
            }
        }

        if ($user && $passwordValid) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            ActivityLogger::log(
                action: 'LOGIN',
                module: 'ចូលប្រើប្រព័ន្ធ',
                targetId: $user->id,
                description: "បានចូលប្រើប្រព័ន្ធដោយជោគជ័យ ({$loginInput})",
                user: $user
            );

            if ($this->shouldForgetIntendedUrl($user, $request->session()->get('url.intended'))) {
                $request->session()->forget('url.intended');
            }

            return redirect()->intended($this->defaultRedirectPath($user));
        }

        ActivityLogger::log(
            action: 'FAILED_LOGIN',
            module: 'ចូលប្រើប្រព័ន្ធ',
            targetId: null,
            description: "ការប៉ុនប៉ងចូលប្រើប្រព័ន្ធមិនជោគជ័យ (Login: {$loginInput}) - ពាក្យសម្ងាត់មិនត្រឹមត្រូវ"
        );

        return back()->withInput($request->only('email'))->with('error', 'អ៊ីមែល/អត្តលេខ ឬពាក្យសម្ងាត់មិនត្រឹមត្រូវឡើយ (Invalid credentials)។ សូមពិនិត្យមើលអក្សរធំ-តូចនៃពាក្យសម្ងាត់ឡើងវិញ។');
    }

    public function logout(Request $request)
    {
        if ($user = Auth::user()) {
            ActivityLogger::log(
                action: 'LOGOUT',
                module: 'ចូលប្រើប្រព័ន្ធ',
                targetId: $user->id,
                description: "បានចាកចេញពីប្រព័ន្ធ",
                user: $user
            );
        }

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
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            return route('dashboard');
        }

        if ($user->hasRole('teacher')) {
            return route('dashboard');
        }

        if ($user->hasRole('student')) {
            return route('dashboard');
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
            return route('examinations.index');
        }

        return route('dashboard');
    }
}
