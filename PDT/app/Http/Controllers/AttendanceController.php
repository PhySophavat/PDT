<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\TeacherAttendance;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\StudentLeaveRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AttendanceController extends Controller
{
    /**
     * Student Attendance sheet and recording.
     */
    public function index(Request $request)
    {
        $selectedClass = $request->input('class', '7A');
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $academicYearId = $request->input('academic_year_id');

        $classes = SchoolClass::orderedNames();
        $academicYears = AcademicYear::where('status', 'active')->get();

        $students = Student::with('user')
            ->where('grade', $selectedClass)
            ->where('status', 'active')
            ->get();

        // Load existing attendance records for the date and class
        $attendances = Attendance::where('class_name', $selectedClass)
            ->where('date', $selectedDate)
            ->get()
            ->keyBy('student_id');

        // Summary metrics
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $excusedCount = $attendances->where('status', 'excused')->count();
        $totalRecorded = $attendances->count();

        return view('attendance.index', compact(
            'students',
            'classes',
            'academicYears',
            'selectedClass',
            'selectedDate',
            'attendances',
            'presentCount',
            'absentCount',
            'lateCount',
            'excusedCount',
            'totalRecorded'
        ));
    }

    /**
     * Store student attendance batch.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'remarks' => 'nullable|array',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $className = $validated['class_name'];
        $date = $validated['date'];
        $academicYearId = $validated['academic_year_id'] ?? AcademicYear::where('is_current', true)->value('id');

        $remarks = $validated['remarks'] ?? [];
        $now = now();
        $records = [];
        foreach ($validated['attendance'] as $studentId => $status) {
            $records[] = [
                'student_id' => $studentId,
                'date' => $date,
                'class_name' => $className,
                'status' => $status,
                'remark' => $remarks[$studentId] ?? null,
                'academic_year_id' => $academicYearId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($records)) {
            Attendance::upsert(
                $records,
                ['student_id', 'date'],
                ['class_name', 'status', 'remark', 'academic_year_id', 'updated_at']
            );
        }

        return back()->with('success', "បានរក្សាទុកវត្តមានសិស្សថ្នាក់ {$className} សម្រាប់ថ្ងៃទី {$date} ដោយជោគជ័យ!");
    }

    /**
     * Teacher attendance management.
     */
    public function teachers(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $teachers = Teacher::with('user')->where('status', 'active')->get();

        $attendances = TeacherAttendance::where('date', $selectedDate)
            ->get()
            ->keyBy('teacher_id');

        return view('attendance.teachers', compact('teachers', 'selectedDate', 'attendances'));
    }

    /**
     * Store teacher attendance batch.
     */
    public function storeTeachers(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'remarks' => 'nullable|array',
        ]);

        $date = $validated['date'];

        $remarks = $validated['remarks'] ?? [];
        $now = now();
        $records = [];
        foreach ($validated['attendance'] as $teacherId => $status) {
            $records[] = [
                'teacher_id' => $teacherId,
                'date' => $date,
                'status' => $status,
                'remark' => $remarks[$teacherId] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($records)) {
            TeacherAttendance::upsert(
                $records,
                ['teacher_id', 'date'],
                ['status', 'remark', 'updated_at']
            );
        }

        return back()->with('success', "បានរក្សាទុកវត្តមានលោកគ្រូ អ្នកគ្រូ សម្រាប់ថ្ងៃទី {$date} ដោយជោគជ័យ!");
    }

    /**
     * Show public student leave form.
     */
    public function showPublicLeaveForm(Request $request)
    {
        $selectedClass = $request->input('class', '');
        $classes = SchoolClass::orderedNames();
        if ($classes->isEmpty()) {
            $classes = collect(['7A', '7B', '8A', '8B', '9A', '9B', '10A', '10B', '11A', '11B', '12A', '12B']);
        }

        $students = collect();
        if ($selectedClass) {
            $students = Student::where('grade', $selectedClass)
                ->where('status', '!=', 'graduated')
                ->orderBy('name_khmer')
                ->get()
                ->map(fn($st) => [
                    'id' => $st->id,
                    'name' => $st->display_name,
                    'student_code' => $st->student_id_card,
                ]);
        }

        return view('public.leave_request', compact('classes', 'selectedClass', 'students'));
    }

    /**
     * Fetch students by class for public leave request form.
     */
    public function getPublicStudentsByClass(Request $request)
    {
        $class = $request->input('class');
        if (!$class) {
            return response()->json(['students' => []]);
        }

        $students = Student::where('grade', $class)
            ->where('status', '!=', 'graduated')
            ->orderBy('name_khmer')
            ->get()
            ->map(fn($st) => [
                'id' => $st->id,
                'name' => $st->display_name,
                'student_code' => $st->student_id_card,
                'phone' => $st->phone ?: $st->parent_phone,
            ]);

        return response()->json(['students' => $students]);
    }

    /**
     * Submit public student leave request.
     */
    public function submitPublicLeaveForm(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string',
            'student_id' => 'nullable|exists:students,id',
            'student_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'nullable|string',
            'reason' => 'required|string|max:1000',
            'phone' => 'nullable|string|max:50',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ], [
            'class_name.required' => 'សូមជ្រើសរើសថ្នាក់រៀន',
            'student_name.required' => 'សូមជ្រើសរើស ឬបញ្ចូលឈ្មោះសិស្ស',
            'start_date.required' => 'សូមជ្រើសរើសកាលបរិច្ឆេទចាប់ផ្ដើមសុំច្បាប់',
            'end_date.required' => 'សូមជ្រើសរើសកាលបរិច្ឆេទបញ្ចប់',
            'end_date.after_or_equal' => 'កាលបរិច្ឆេទបញ្ចប់ត្រូវតែស្មើ ឬក្រោយថ្ងៃចាប់ផ្ដើម',
            'reason.required' => 'សូមបញ្ជាក់មូលហេតុនៃការសុំច្បាប់',
            'attachment.mimes' => 'ឯកសារភ្ជាប់ត្រូវតែជា JPG, PNG, WebP ឬ PDF',
            'attachment.max' => 'ឯកសារភ្ជាប់មិនត្រូវលើសពី 5MB ឡើយ',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $daysCount = $startDate->diffInDays($endDate) + 1;
        if ($request->filled('days_count') && is_numeric($request->input('days_count'))) {
            $daysCount = (float) $request->input('days_count');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leaves/attachments', 'public');
        }

        $studentCode = null;
        if (!empty($validated['student_id'])) {
            $student = Student::find($validated['student_id']);
            $studentCode = $student?->student_id_card;
        }

        $leave = StudentLeaveRequest::create([
            'student_id' => $validated['student_id'] ?? null,
            'student_name' => $validated['student_name'],
            'student_code' => $studentCode,
            'class_name' => $validated['class_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_count' => $daysCount,
            'leave_type' => $validated['leave_type'] ?? 'ច្បាប់',
            'reason' => $validated['reason'],
            'phone' => $validated['phone'] ?? null,
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        try {
            Cache::increment('notif_version');
            Cache::forget('pending_notifications_payload');
        } catch (\Throwable $e) {}

        if ($request->wantsJson() || $request->ajax() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'សំណើសុំច្បាប់ត្រូវបានបញ្ជូនដោយជោគជ័យ! សាលានឹងពិនិត្យ និងអនុម័តជូនឆាប់ៗនេះ។',
                'leave' => $leave,
            ]);
        }

        return back()->with('leave_success', [
            'message' => 'សំណើសុំច្បាប់ត្រូវបានបញ្ជូនដោយជោគជ័យ! សាលានឹងពិនិត្យ និងអនុម័តជូនឆាប់ៗនេះ។',
            'student_name' => $leave->student_name,
            'class_name' => $leave->class_name,
            'dates' => "{$leave->start_date->format('d/m/Y')} ដល់ {$leave->end_date->format('d/m/Y')} ({$leave->days_count} ថ្ងៃ)",
            'leave_type' => $leave->leave_type,
        ]);
    }

    /**
     * API: Get Leave Requests list with filtering.
     */
    public function getLeaveRequests(Request $request)
    {
        $className = $request->input('class');
        $status = $request->input('status', 'all');
        $date = $request->input('date');

        $query = StudentLeaveRequest::query()->latest();

        $authUser = auth()->user();
        $isStudentUser = ($authUser && ($authUser->hasRole('student') || strtolower($authUser->role ?? '') === 'student'));

        if ($isStudentUser) {
            $studentProfile = $authUser->student ?? \App\Models\Student::where('email', $authUser->email)->first();
            $studentId = $studentProfile?->id;
            $studentName = $studentProfile?->name_khmer ?: ($studentProfile?->name_latin ?: $authUser->name);
            $query->where(function ($q) use ($studentId, $studentName, $authUser) {
                if ($studentId) {
                    $q->where('student_id', $studentId);
                }
                $q->orWhere('student_name', $studentName)
                  ->orWhere('student_name', $authUser->name);
            });

            $pendingCount = StudentLeaveRequest::where(function ($q) use ($studentId, $studentName, $authUser) {
                if ($studentId) {
                    $q->where('student_id', $studentId);
                }
                $q->orWhere('student_name', $studentName)
                  ->orWhere('student_name', $authUser->name);
            })->where('status', 'pending')->count();
        } else {
            if ($className && $className !== 'all') {
                $query->where('class_name', $className);
            }
            $pendingCount = StudentLeaveRequest::where('status', 'pending')
                ->when($className && $className !== 'all', fn($q) => $q->where('class_name', $className))
                ->count();
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($date) {
            $query->where(function ($q) use ($date) {
                $q->where('start_date', '<=', $date)
                  ->where('end_date', '>=', $date);
            });
        }

        $requests = $query->get()->map(function ($l) {
            return [
                'id' => $l->id,
                'student_id' => $l->student_id,
                'student_name' => $l->student_name,
                'student_code' => $l->student_code,
                'class_name' => $l->class_name,
                'start_date' => $l->start_date ? $l->start_date->toDateString() : '',
                'end_date' => $l->end_date ? $l->end_date->toDateString() : '',
                'days_count' => $l->days_count,
                'leave_type' => $l->leave_type,
                'reason' => $l->reason,
                'phone' => $l->phone,
                'attachment_url' => $l->attachment_path ? asset('storage/' . $l->attachment_path) : null,
                'status' => $l->status,
                'created_at' => $l->created_at ? $l->created_at->format('Y-m-d H:i') : '',
            ];
        });

        return response()->json([
            'requests' => $requests,
            'pending_count' => $pendingCount,
        ]);
    }

    /**
     * API: Approve Leave Request and mark attendance as excused (E/H).
     */
    public function approveLeaveRequest(Request $request, $id)
    {
        $leave = StudentLeaveRequest::findOrFail($id);
        $leave->status = 'approved';
        $leave->approved_by = auth()->id();
        $leave->save();

        // Update attendance records for each day of leave
        if ($leave->student_id) {
            $academicYearId = AcademicYear::where('is_current', true)->value('id');
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);

            for ($curr = $start->copy(); $curr->lte($end); $curr->addDay()) {
                // Skip Sundays if needed, or record all dates
                if ($curr->isSunday()) continue;

                Attendance::updateOrCreate(
                    [
                        'student_id' => $leave->student_id,
                        'date' => $curr->toDateString(),
                    ],
                    [
                        'class_name' => $leave->class_name,
                        'status' => 'excused', // excused = ច្បាប់
                        'remark' => "សុំច្បាប់: {$leave->leave_type} - {$leave->reason}",
                        'academic_year_id' => $academicYearId,
                    ]
                );
            }
        }

        try {
            Cache::increment('notif_version');
            Cache::forget('pending_notifications_payload');
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => "បានអនុម័តសំណើសុំច្បាប់របស់សិស្ស {$leave->student_name} និងកត់ត្រាវត្តមានជា «មានច្បាប់» ដោយជោគជ័យ!",
        ]);
    }

    /**
     * API: Reject Leave Request.
     */
    public function rejectLeaveRequest(Request $request, $id)
    {
        $leave = StudentLeaveRequest::findOrFail($id);
        $leave->status = 'rejected';
        $leave->rejection_reason = $request->input('reason', 'ពុំមានមូលហេតុគ្រប់គ្រាន់');
        $leave->approved_by = auth()->id();
        $leave->save();

        try {
            Cache::increment('notif_version');
            Cache::forget('pending_notifications_payload');
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => "បានបដិសេធសំណើសុំច្បាប់របស់សិស្ស {$leave->student_name}!",
        ]);
    }

    /**
     * API: Restore a rejected leave request back to pending.
     */
    public function restoreLeaveRequest(Request $request, $id)
    {
        $leave = StudentLeaveRequest::findOrFail($id);
        $leave->status = 'pending';
        $leave->rejection_reason = null;
        $leave->approved_by = null;
        $leave->save();

        try {
            Cache::increment('notif_version');
            Cache::forget('pending_notifications_payload');
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => "បានស្ដារសំណើសុំច្បាប់របស់សិស្ស {$leave->student_name} មកជា «រង់ចាំពិនិត្យ» វិញដោយជោគជ័យ!",
            'leave' => $leave,
        ]);
    }
}
