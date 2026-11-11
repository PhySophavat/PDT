<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Mail\PasswordResetMail;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Show the Request Password Reset form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle the request to send OTP via Brevo Email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែល (Email)',
            'email.email' => 'ទម្រង់អ៊ីមែលមិនត្រឹមត្រូវឡើយ (ឧ. name@example.com)',
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $ip = $request->ip();

        // 1. Resend Countdown Protection (60s minimum interval between OTP requests)
        $existingRecord = DB::table('password_reset_tokens')->where('email', $email)->first();
        if ($existingRecord && $existingRecord->created_at) {
            $diffInSeconds = Carbon::parse($existingRecord->created_at)->diffInSeconds(Carbon::now());
            if ($diffInSeconds < 60) {
                $secondsLeft = 60 - $diffInSeconds;
                return back()
                    ->withInput()
                    ->with('error', "សូមរង់ចាំ {$secondsLeft} វិនាទីទៀត ទើបអាចស្នើសុំផ្ញើលេខកូដ OTP ថ្មីម្តងទៀតបាន (Resend in 60s)!");
            }
        }

        // 2. Rate Limiting on Forgot Password (Abuse Prevention & Mail Flooding Protection)
        // Max 5 requests per hour per IP / Email
        $throttleKey = 'forgot-password:' . Str::transliterate($email . '|' . $ip);
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey) ?: 60;
            $minutes = ceil($seconds / 60);
            return back()
                ->withInput()
                ->with('error', "លោកអ្នកបានស្នើសុំច្រើនដងពេកហើយ។ ដើម្បីសុវត្ថិភាព សូមរង់ចាំប្រហែល {$minutes} នាទីទៀត ({$seconds} វិនាទី) ទើបអាចស្នើសុំម្តងទៀតបាន។");
        }
        RateLimiter::hit($throttleKey, 3600); // 1-hour window

        // 3. Generic Success Response Message (User Enumeration Protection)
        $genericSuccessMessage = 'ប្រសិនបើគណនី ឬអ៊ីមែលនេះមានក្នុងប្រព័ន្ធ យើងបានផ្ញើតំណភ្ជាប់ និងលេខកូដ OTP ទៅកាន់ប្រអប់សាររបស់អ្នករួចរាល់ហើយ។ សូមពិនិត្យមើលប្រអប់សំបុត្រ ឬ Spam/Junk។';

        // 4. Lookup User by Email
        $user = User::where('email', $email)->first();

        // Even if account does not exist, return generic message so attackers cannot enumerate users
        if (!$user) {
            return back()->with('status', $genericSuccessMessage);
        }

        $targetEmail = $user->email;
        $recipientName = $user->name;

        // 5. Generate 6-digit numeric OTP code (e.g. 489201)
        $otpCode = sprintf('%06d', random_int(100000, 999999));
        $hashedOtp = hash('sha256', $otpCode);

        // Generate high-entropy reset token for direct link
        $rawToken = Str::random(64);
        $hashedToken = hash('sha256', $rawToken);

        // Save OTP with 3-minute expiration time and reset attempts to 0
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => strtolower($targetEmail)],
            [
                'token' => $hashedToken,
                'otp' => $hashedOtp,
                'attempts' => 0,
                'expires_at' => Carbon::now()->addMinutes(3),
                'created_at' => Carbon::now(),
            ]
        );

        // Construct one-time direct reset URL (valid for 3 minutes)
        $resetUrl = route('password.reset', [
            'token' => $rawToken,
            'email' => strtolower($targetEmail),
        ]);

        $sysSettings = SystemSetting::allSettings();
        $schoolName = $sysSettings['school_name'] ?? 'សាលារៀន ជំនាន់ថ្មី';

        // 6. Send OTP Email via Brevo SMTP / Mailer
        try {
            Mail::to($targetEmail)->send(new PasswordResetMail(
                recipientName: $recipientName,
                resetUrl: $resetUrl,
                otpCode: $otpCode,
                expireMinutes: 3,
                schoolName: $schoolName
            ));
        } catch (\Throwable $e) {
            Log::error('Password reset email sending failed to ' . $targetEmail . ': ' . $e->getMessage());
        }

        // Redirect directly to the OTP verification screen
        return redirect()->route('password.otp.show', ['email' => $targetEmail])
            ->with('status', "លេខកូដសម្ងាត់ OTP ៦ ខ្ទង់ ត្រូវបានផ្ញើទៅកាន់ {$targetEmail} រួចរាល់ហើយ។ សូមពិនិត្យប្រអប់សំបុត្រ ឬ Spam/Junk។");
    }

    /**
     * Show the 6-digit OTP Verification Screen.
     */
    public function showOtpForm(Request $request)
    {
        $email = strtolower(trim((string) $request->query('email', old('email', ''))));
        if (empty($email)) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Handle OTP verification submission.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ], [
            'email.required' => 'សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែល',
            'otp.required' => 'សូមបញ្ចូលលេខកូដ OTP ៦ ខ្ទង់',
            'otp.size' => 'លេខកូដ OTP ត្រូវតែមាន ៦ ខ្ទង់គត់',
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $otpInput = trim((string) $request->input('otp'));
        $hashedOtpInput = hash('sha256', $otpInput);

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            return back()->withInput()->with('error', 'រកមិនឃើញសំណើ OTP សម្រាប់អ៊ីមែលនេះឡើយ។ សូមស្នើសុំលេខកូដថ្មី។');
        }

        // 1. Check Expiration Time (3 minutes limit)
        $expiresAt = $record->expires_at ? Carbon::parse($record->expires_at) : Carbon::parse($record->created_at)->addMinutes(3);
        if (Carbon::now()->isAfter($expiresAt)) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')->with('error', 'លេខកូដ OTP នេះបានផុតកំណត់សុពលភាពហើយ (លើសពី ៣ នាទី)។ សូមស្នើសុំលេខកូដថ្មីម្តងទៀត។');
        }

        // 2. Check Max Attempts (Max 5 failed attempts allowed)
        if ($record->attempts >= 5) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')->with('error', 'អ្នកបានវាយបញ្ចូលលេខកូដខុសលើសពី ៥ ដងហើយ។ ដើម្បីសុវត្ថិភាព លេខកូដនេះត្រូវបានបោះបង់ចោល សូមស្នើសុំលេខកូដ OTP ថ្មីម្តងទៀត។');
        }

        // 3. Verify OTP Match
        if (!hash_equals($record->otp ?? '', $hashedOtpInput)) {
            DB::table('password_reset_tokens')->where('email', $email)->increment('attempts');
            $remainingAttempts = 5 - ($record->attempts + 1);

            if ($remainingAttempts <= 0) {
                DB::table('password_reset_tokens')->where('email', $email)->delete();
                return redirect()->route('password.request')->with('error', 'អ្នកបានវាយបញ្ចូលលេខកូដខុសលើសពី ៥ ដងហើយ។ លេខកូដនេះត្រូវបានបោះបង់ចោល សូមស្នើសុំកូដថ្មីម្តងទៀត។');
            }

            return back()->withInput()->with('error', "លេខកូដ OTP មិនត្រឹមត្រូវឡើយ! អ្នកនៅសល់ឱកាស {$remainingAttempts} ដងទៀតក្នុងការផ្ទៀងផ្ទាត់។");
        }

        // 4. OTP is Valid! Generate single-use verified reset token
        $verifiedToken = Str::random(64);
        DB::table('password_reset_tokens')->where('email', $email)->update([
            'token' => hash('sha256', $verifiedToken),
            'otp' => null, // clear OTP so it cannot be reused
            'attempts' => 0,
        ]);

        return redirect()->route('password.reset', ['token' => $verifiedToken, 'email' => $email])
            ->with('success', '✓ ការផ្ទៀងផ្ទាត់លេខកូដ OTP ទទួលបានជោគជ័យ! សូមកំណត់ពាក្យសម្ងាត់ថ្មីរបស់អ្នក។');
    }

    /**
     * Show the Reset Password form when user clicks the email link or successfully verifies OTP.
     */
    public function showResetForm(Request $request, $token = null)
    {
        $token = $token ?: $request->query('token');
        $email = $request->query('email', old('email', ''));

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Handle the actual password reset submission.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'token.required' => 'កូដសម្គាល់សុវត្ថិភាព (Token) មិនត្រឹមត្រូវឡើយ',
            'email.required' => 'សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែលរបស់អ្នក',
            'email.email' => 'ទម្រង់អ៊ីមែលមិនត្រឹមត្រូវ',
            'password.required' => 'សូមបញ្ចូលពាក្យសម្ងាត់ថ្មី',
            'password.min' => 'ពាក្យសម្ងាត់ថ្មីត្រូវមានយ៉ាងតិច ៨ ខ្ទង់ឡើងទៅ',
            'password.confirmed' => 'ការបញ្ជាក់ពាក្យសម្ងាត់ទាំង ២ ដងមិនត្រូវគ្នាឡើយ សូមពិនិត្យឡើងវិញ',
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $token = trim((string) $request->input('token'));
        $hashedInputToken = hash('sha256', $token);

        // 1. Verify Token from Database
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || !hash_equals($record->token, $hashedInputToken)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'តំណភ្ជាប់សម្រាប់កំណត់ពាក្យសម្ងាត់ថ្មីនេះមិនត្រឹមត្រូវ ឬត្រូវបានប្រើប្រាស់រួចហើយ។ សូមស្នើសុំតំណភ្ជាប់ថ្មីម្តងទៀត។']);
        }

        // 2. Check Token Expiration (3 Minutes limit)
        $expiresAt = $record->expires_at ? Carbon::parse($record->expires_at) : Carbon::parse($record->created_at)->addMinutes(3);
        if (Carbon::now()->isAfter($expiresAt)) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'តំណភ្ជាប់នេះបានផុតកំណត់សុពលភាពហើយ (លើសពី ៣ នាទី)។ សូមស្នើសុំតំណភ្ជាប់ថ្មីម្តងទៀត។']);
        }

        // 3. Find User & Update Password securely with Bcrypt
        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'រកមិនឃើញគណនីដែលត្រូវគ្នានឹងអ៊ីមែលនេះឡើយ។']);
        }

        // Hash password securely with Bcrypt
        $user->password = Hash::make($request->input('password'));
        $user->remember_token = Str::random(60);
        $user->save();

        // 4. Invalidate Token Immediately (Single-Use Enforcement)
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // 5. Log Security Activity
        try {
            ActivityLogger::log(
                action: 'កំណត់ពាក្យសម្ងាត់ថ្មីជោគជ័យតាមរយៈ Password Reset OTP',
                module: 'auth',
                details: [
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
                user: $user
            );
        } catch (\Throwable $e) {}

        // 6. Redirect to Login Page with Success Toast
        return redirect()->route('login')->with('success', '🎉 ពាក្យសម្ងាត់របស់អ្នកត្រូវបានផ្លាស់ប្តូរដោយជោគជ័យ! សូមចូលប្រើប្រព័ន្ធជាមួយពាក្យសម្ងាត់ថ្មីរបស់អ្នក។');
    }
}
