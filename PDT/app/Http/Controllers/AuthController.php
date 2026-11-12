<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Role;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if ($request->has('logout') || $request->has('logged_out')) {
            try {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            } catch (\Throwable $e) {}
        }

        if (Auth::check()) {
            return redirect()->intended($this->defaultRedirectPath(Auth::user()));
        }

        return response()->view('auth.login')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Direct 1-Click Magic Login from Email Link
     */
    public function directLogin(Request $request)
    {
        $token = trim((string) $request->query('token'));
        if (empty($token)) {
            return redirect()->route('login');
        }

        // 1. Look up user by cache token or remember_token in database
        $userId = \Illuminate\Support\Facades\Cache::get('direct_login_' . $token);
        $user = null;
        if ($userId) {
            $user = User::find($userId);
        }

        if (!$user) {
            $user = User::where('remember_token', $token)->first();
        }

        if (!$user) {
            return redirect()->route('login')->with('error', 'តំណភ្ជាប់ចូលប្រព័ន្ធស្វ័យប្រវត្តិនេះបានផុតកំណត់ហើយ សូមបញ្ចូលពាក្យសម្ងាត់ដើម្បីចូល។');
        }

        // 2. Perform instant login & regenerate session
        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->put('login_time', now()->toIso8601String());

        $loginTimeKh = now()->format('H:i');
        $loginDateKh = now()->format('d/m/Y');

        ActivityLogger::log(
            action: 'LOGIN',
            module: 'ចូលប្រើប្រព័ន្ធ',
            targetId: $user->id,
            description: "បានចូលប្រើប្រព័ន្ធស្វ័យប្រវត្តិតាមរយៈ Magic Login Link ({$user->email}) [ម៉ោង {$loginTimeKh} ថ្ងៃទី {$loginDateKh}]",
            oldValues: null,
            newValues: [
                'login_at' => now()->toIso8601String(),
                'login_time' => $loginTimeKh,
                'login_date' => $loginDateKh,
                'method' => 'magic_link',
            ],
            user: $user
        );

        return redirect()->to($this->defaultRedirectPath($user))
            ->with('success', "សូមស្វាគមន៍ប្អូន {$user->name}! អ្នកបានចូលប្រើប្រព័ន្ធដោយជោគជ័យ។");
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែល (Email)',
            'email.email' => 'ទម្រង់អ៊ីមែលមិនត្រឹមត្រូវឡើយ (ឧ. name@example.com)',
            'password.required' => 'សូមបញ្ចូលពាក្យសម្ងាត់',
        ]);

        $loginInput = strtolower(trim((string) $request->input('email')));
        $passwordInput = trim((string) $request->input('password'));
        $remember = $request->boolean('remember', true);

        // Brute-force rate limiting: Max 5 failed attempts per 5 minutes per email and IP
        $throttleKey = Str::transliterate($loginInput . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey) ?: 300;
            if ($seconds > 300) {
                $seconds = 300;
            }
            $minutes = ceil($seconds / 60);
            return back()
                ->withInput($request->only('email'))
                ->with('lockout_seconds', $seconds)
                ->with('error', 'អ្នកបានព្យាយាមចូលខុស ៥ ដងហើយ។ សូមរង់ចាំ ' . $seconds . ' វិនាទី (ប្រហែល ' . $minutes . ' នាទី) ទៀត ទើបអាចសាកល្បងម្តងទៀត។');
        }

        // Find user by email (only email)
        $user = User::where('email', $loginInput)->first();

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
            // Check account status & approval
            if ($user->status === 'pending' || $user->is_approved === false) {
                return back()->withInput($request->only('email'))
                    ->with('error', 'សំណើចុះឈ្មោះរបស់អ្នកកំពុងស្ថិតក្នុងការរង់ចាំការអនុម័តពីគណៈគ្រប់គ្រងសាលា (Account Pending Approval)។ សូមរង់ចាំការពិនិត្យ និងអនុម័ត។');
            }

            if ($user->status === 'inactive' || $user->status === 'suspended') {
                return back()->withInput($request->only('email'))
                    ->with('error', 'គណនីរបស់អ្នកត្រូវបានផ្អាកដំណើរការជាបណ្តោះអាសន្ន (Account Suspended)។ សូមទាក់ទងរដ្ឋបាលសាលា។');
            }

            RateLimiter::clear($throttleKey);
            Auth::login($user, $remember);
            $request->session()->regenerate();
            $request->session()->put('login_time', now()->toIso8601String());

            $loginTimeKh = now()->format('H:i');
            $loginDateKh = now()->format('d/m/Y');
            $displayName = ActivityLogger::determineUserName($user);

            ActivityLogger::log(
                action: 'LOGIN',
                module: 'ចូលប្រើប្រព័ន្ធ',
                targetId: $user->id,
                description: "{$displayName} បានចូលប្រើប្រព័ន្ធដោយជោគជ័យ [ម៉ោង {$loginTimeKh} ថ្ងៃទី {$loginDateKh}] ({$loginInput})",
                oldValues: null,
                newValues: [
                    'user_name' => $displayName,
                    'login_at' => now()->toIso8601String(),
                    'login_time' => $loginTimeKh,
                    'login_date' => $loginDateKh,
                    'identifier' => $loginInput,
                ],
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
            description: "ការប៉ុនប៉ងចូលប្រើប្រព័ន្ធមិនជោគជ័យ (Login Email: {$loginInput}) - ពាក្យសម្ងាត់មិនត្រឹមត្រូវ"
        );

        RateLimiter::hit($throttleKey, 300);

        return back()->withInput($request->only('email'))->with('error', 'អ៊ីមែល ឬពាក្យសម្ងាត់មិនត្រឹមត្រូវឡើយ (Invalid credentials)។ សូមពិនិត្យមើលអក្សរធំ-តូចនៃពាក្យសម្ងាត់ឡើងវិញ។');
    }

    public function logout(Request $request)
    {
        try {
            if ($user = Auth::user()) {
                $loginAtIso = $request->session()->get('login_time');
                $durationText = 'តិចជាង ១ នាទី';
                $durationMinutes = 0;
                $loginTimeKh = null;
                $loginDateKh = null;

                if ($loginAtIso) {
                    try {
                        $loginAt = \Carbon\Carbon::parse($loginAtIso);
                        $loginTimeKh = $loginAt->format('H:i');
                        $loginDateKh = $loginAt->format('d/m/Y');
                        $diffMinutes = (int) $loginAt->diffInMinutes(now());
                        $durationMinutes = max(1, $diffMinutes);
                        $hours = intdiv($durationMinutes, 60);
                        $mins = $durationMinutes % 60;
                        if ($hours > 0) {
                            $durationText = "{$hours} ម៉ោង {$mins} នាទី";
                        } else {
                            $durationText = "{$mins} នាទី";
                        }
                    } catch (\Throwable $e) {}
                }

                $logoutTimeKh = now()->format('H:i');
                $logoutDateKh = now()->format('d/m/Y');
                $displayName = ActivityLogger::determineUserName($user);

                $description = "{$displayName} បានចាកចេញពីប្រព័ន្ធ";
                if ($loginTimeKh) {
                    $description .= " (ចូល: {$loginTimeKh} • ចេញ: {$logoutTimeKh} • រយៈពេលប្រើប្រាស់: {$durationText})";
                } else {
                    $description .= " (ចេញម៉ោង: {$logoutTimeKh} ថ្ងៃទី {$logoutDateKh})";
                }

                ActivityLogger::log(
                    action: 'LOGOUT',
                    module: 'ចូលប្រើប្រព័ន្ធ',
                    targetId: $user->id,
                    description: $description,
                    oldValues: null,
                    newValues: [
                        'user_name' => $displayName,
                        'login_at' => $loginAtIso,
                        'login_time' => $loginTimeKh,
                        'login_date' => $loginDateKh,
                        'logout_at' => now()->toIso8601String(),
                        'logout_time' => $logoutTimeKh,
                        'logout_date' => $logoutDateKh,
                        'duration_minutes' => $durationMinutes,
                        'duration_text' => $durationText,
                    ],
                    user: $user
                );
            }
        } catch (\Throwable $e) {}

        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Throwable $e) {}

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'បានចាកចេញពីប្រព័ន្ធដោយជោគជ័យ',
                'redirect' => route('login')
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
              ->header('Pragma', 'no-cache');
        }

        $sessionCookieName = config('session.cookie') ?: 'laravel_session';
        return redirect()->route('login')
            ->with('success', 'បានចាកចេញពីប្រព័ន្ធដោយជោគជ័យ។')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Clear-Site-Data', '"cache", "storage"')
            ->withCookie(\Illuminate\Support\Facades\Cookie::forget($sessionCookieName));
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
