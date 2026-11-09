<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\CourseSubject;
use App\Models\TeacherLeaveRequest;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TeacherController extends Controller
{
    private function ensureCanManageTeachers(): void
    {
        abort_unless(Auth::user()?->canManageTeachers(), 403, 'Unauthorized action.');
    }

    /**
     * Show public teacher registration form.
     */
    public function showPublicRegistration()
    {
        $subjects = CourseSubject::orderBy('name')->pluck('name');
        if ($subjects->isEmpty()) {
            $subjects = collect([
                'គណិតវិទ្យា', 'ភាសាខ្មែរ', 'រូបវិទ្យា', 'គីមីវិទ្យា', 'ជីវវិទ្យា',
                'ប្រវត្តិវិទ្យា', 'ភូមិវិទ្យា', 'ភាសាអង់គ្លេស', 'ព័ត៌មានវិទ្យា (ICT)',
                'សីលធម៌ និងពលរដ្ឋវិជ្ជា', 'ផែនដី និងបរិស្ថានវិទ្យា', 'អប់រំកាយ និងកីឡា'
            ]);
        }

        $qualifications = [
            'គរុកោសល្យបឋម (Primary Pedagogy)',
            'គរុកោសល្យមធ្យម (Lower Secondary Pedagogy)',
            'បរិញ្ញាបត្រ (Bachelor Degree)',
            'បរិញ្ញាបត្រជាន់ខ្ពស់ (Master Degree)',
            'បណ្ឌិត (PhD / Doctorate)',
            'វិញ្ញាបនបត្រគរុកោសល្យជាន់ខ្ពស់ (Higher Education Pedagogy)'
        ];

        return view('public.register_teacher', compact('subjects', 'qualifications'));
    }

    /**
     * Handle public teacher registration submission.
     */
    public function submitPublicRegistration(Request $request)
    {
        $validated = $request->validate([
            'name_khmer' => 'required|string|max:255',
            'name_latin' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string|max:50',
            'specialization' => 'required|string|max:255',
            'qualification' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'photo' => 'nullable|image|max:3072',
        ], [
            'name_khmer.required' => 'សូមបញ្ចូលឈ្មោះពេញជាភាសាខ្មែរ',
            'name_latin.required' => 'សូមបញ្ចូលឈ្មោះជាអក្សរឡាតាំង',
            'gender.required' => 'សូមជ្រើសរើសភេទ',
            'date_of_birth.required' => 'សូមជ្រើសរើសថ្ងៃខែឆ្នាំកំណើត',
            'phone.required' => 'សូមបញ្ចូលលេខទូរស័ព្ទ',
            'specialization.required' => 'សូមជ្រើសរើស ឬបញ្ចូលជំនាញឯកទេស',
            'qualification.required' => 'សូមជ្រើសរើសកម្រិតវប្បធម៌',
            'address.required' => 'សូមបញ្ចូលអាសយដ្ឋានបច្ចុប្បន្ន',
            'photo.image' => 'ឯកសាររូបថតត្រូវតែជាប្រភេទរូបភាព (JPG, PNG, WebP)',
            'photo.max' => 'រូបថតមិនត្រូវលើសពី 3MB ឡើយ',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('teachers/photos', 'public');
        }

        $year = date('Y');
        $last = Teacher::whereNotNull('teacher_id_card')
            ->where('teacher_id_card', 'like', "TCH-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();
        $nextNum = 1;
        if ($last && preg_match('/TCH-\d{4}-(\d+)/', $last->teacher_id_card, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        } else {
            $nextNum = Teacher::whereNotNull('teacher_id_card')->count() + 1;
        }
        $teacherIdCard = sprintf('TCH-%s-%03d', $year, $nextNum);

        $teacher = Teacher::create([
            'teacher_id_card' => $teacherIdCard,
            'name_khmer' => $validated['name_khmer'],
            'name_latin' => $validated['name_latin'],
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'phone' => $validated['phone'],
            'specialization' => $validated['specialization'],
            'subject' => $validated['specialization'],
            'qualification' => $validated['qualification'],
            'address' => $validated['address'],
            'province' => $validated['address'],
            'photo' => $photoPath,
            'status' => 'pending',
            'joining_date' => Carbon::today(),
            'salary' => 0,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'ការចុះឈ្មោះគ្រូបង្រៀនត្រូវបានបញ្ជូនដោយជោគជ័យ! សូមរង់ចាំការត្រួតពិនិត្យពីសាលា។',
                'teacher_id' => $teacher->id
            ]);
        }

        return redirect()->route('public.register-teacher')->with('registration_success', true);
    }

    /**
     * Show public teacher leave request form.
     */
    public function showPublicLeaveForm(Request $request)
    {
        $teachers = Teacher::with('user')
            ->where('status', 'active')
            ->get()
            ->sortBy(fn($t) => $t->name_khmer ?: ($t->user->name ?? ''))
            ->values();

        return view('public.teacher_leave_request', compact('teachers'));
    }

    /**
     * Submit public teacher leave request.
     */
    public function submitPublicLeaveForm(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'teacher_name' => 'required|string|max:255',
            'teacher_code' => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|string|max:100',
            'reason' => 'required|string|max:1000',
            'phone' => 'required|string|max:50',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], [
            'teacher_id.required' => 'សូមជ្រើសរើសលោកគ្រូ-អ្នកគ្រូ',
            'start_date.required' => 'សូមជ្រើសរើសកាលបរិច្ឆេទចាប់ផ្ដើម',
            'end_date.required' => 'សូមជ្រើសរើសកាលបរិច្ឆេទបញ្ចប់',
            'end_date.after_or_equal' => 'កាលបរិច្ឆេទបញ្ចប់ត្រូវតែស្មើ ឬក្រោយកាលបរិច្ឆេទចាប់ផ្ដើម',
            'leave_type.required' => 'សូមជ្រើសរើសប្រភេទច្បាប់',
            'reason.required' => 'សូមបញ្ចូលមូលហេតុនៃការសុំច្បាប់',
            'phone.required' => 'សូមបញ្ចូលលេខទូរស័ព្ទទំនាក់ទំនង',
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $daysCount = $start->diffInDays($end) + 1;

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('teacher_leaves/attachments', 'public');
        }

        $leave = TeacherLeaveRequest::create([
            'teacher_id' => $validated['teacher_id'],
            'teacher_name' => $validated['teacher_name'],
            'teacher_code' => $validated['teacher_code'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'days_count' => $daysCount,
            'leave_type' => $validated['leave_type'],
            'reason' => $validated['reason'],
            'phone' => $validated['phone'],
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'សំណើសុំច្បាប់ត្រូវបានបញ្ជូនដោយជោគជ័យ! សូមរង់ចាំការអនុម័តពីគណៈគ្រប់គ្រងសាលា។',
                'leave_id' => $leave->id,
            ]);
        }

        return redirect()->route('public.teacher-leave-request')->with('leave_success', [
            'teacher_name' => $leave->teacher_name,
            'specialization' => $leave->specialization,
            'start_date' => $leave->start_date->format('d/m/Y'),
            'end_date' => $leave->end_date->format('d/m/Y'),
            'days_count' => $leave->days_count,
            'leave_type' => $leave->leave_type,
        ]);
    }

    /**
     * API: Get Teacher Leave Requests.
     */
    public function getLeaveRequests(Request $request)
    {
        $status = $request->input('status', 'all');
        $date = $request->input('date');

        $query = TeacherLeaveRequest::query()->latest();

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
                'teacher_id' => $l->teacher_id,
                'teacher_name' => $l->teacher_name,
                'teacher_code' => $l->teacher_code,
                'specialization' => $l->specialization,
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

        $pendingCount = TeacherLeaveRequest::where('status', 'pending')->count();

        return response()->json([
            'requests' => $requests,
            'pending_count' => $pendingCount,
        ]);
    }

    /**
     * API: Approve Teacher Leave Request.
     */
    public function approveLeaveRequest(Request $request, $id)
    {
        $leave = TeacherLeaveRequest::findOrFail($id);
        $leave->status = 'approved';
        $leave->approved_by = auth()->id();
        $leave->save();

        // Automatically mark teacher attendance as 'leave' (H) for each day of approved leave
        if ($leave->teacher_id) {
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);

            for ($curr = $start->copy(); $curr->lte($end); $curr->addDay()) {
                if ($curr->isSunday()) continue;

                TeacherAttendance::updateOrCreate(
                    [
                        'teacher_id' => $leave->teacher_id,
                        'date' => $curr->toDateString(),
                    ],
                    [
                        'status' => 'leave', // leave = ច្បាប់ (H)
                        'remark' => "សុំច្បាប់: {$leave->leave_type} - {$leave->reason}",
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
            'message' => "បានអនុម័តសំណើសុំច្បាប់របស់លោកគ្រូ-អ្នកគ្រូ {$leave->teacher_name} និងកត់ត្រាវត្តមានជា «មានច្បាប់» ដោយជោគជ័យ!",
        ]);
    }

    /**
     * API: Reject Teacher Leave Request.
     */
    public function rejectLeaveRequest(Request $request, $id)
    {
        $leave = TeacherLeaveRequest::findOrFail($id);
        $leave->status = 'rejected';
        $leave->rejection_reason = $request->input('reason', 'ពុំមានមូលហេតុសមរម្យ');
        $leave->approved_by = auth()->id();
        $leave->save();

        try {
            Cache::increment('notif_version');
            Cache::forget('pending_notifications_payload');
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => "បានបដិសេធសំណើសុំច្បាប់របស់លោកគ្រូ-អ្នកគ្រូ {$leave->teacher_name}!",
        ]);
    }

    /**
     * API: Restore a rejected teacher leave request back to pending.
     */
    public function restoreLeaveRequest(Request $request, $id)
    {
        $leave = TeacherLeaveRequest::findOrFail($id);
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
            'message' => "បានស្ដារសំណើសុំច្បាប់របស់លោកគ្រូ-អ្នកគ្រូ {$leave->teacher_name} មកជា «រង់ចាំពិនិត្យ» វិញដោយជោគជ័យ!",
            'leave' => $leave,
        ]);
    }

    /**
     * Display a listing of teachers.
     */
    public function index(Request $request)
    {
        $this->ensureCanManageTeachers();

        $search = trim((string) $request->input('search', ''));
        $statusFilter = $request->input('status');

        $teachers = Teacher::with('user')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('teacher_id_card', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('teacher.index', compact('teachers', 'search', 'statusFilter'));
    }

    /**
     * Show Teacher Profile.
     */
    public function show(Teacher $teacher)
    {
        $this->ensureCanManageTeachers();

        $teacher->load([
            'user',
            'schoolClasses',
            'timetables.courseSubject',
            'attendances' => fn ($q) => $q->latest('date')->take(20),
        ]);

        return view('teacher.show', compact('teacher'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        $this->ensureCanManageTeachers();

        $classes = SchoolClass::orderedNames();
        $subjects = CourseSubject::orderBy('name')->pluck('name');

        return view('teacher.create', compact('classes', 'subjects'));
    }

    /**
     * Store a newly created teacher along with linked user.
     */
    public function store(Request $request)
    {
        $this->ensureCanManageTeachers();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'subject' => 'required|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave',
            'gender' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:50',
            'classes' => 'nullable|array',
            'village' => 'nullable|string|max:255',
            'commune' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        $nextId = Teacher::max('id') + 1;
        $teacherIdCard = sprintf('TCH-%s-%03d', date('Y'), $nextId);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('teachers/photos', 'public');
        }

        // Create User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign teacher role
        $teacherRole = Role::where('name', 'teacher')->first();
        if ($teacherRole) {
            $user->roles()->syncWithoutDetaching([$teacherRole->id]);
        }

        // Create Teacher profile
        $classesStr = !empty($validated['classes']) ? implode(',', $validated['classes']) : null;

        Teacher::create([
            'user_id' => $user->id,
            'teacher_id_card' => $teacherIdCard,
            'subject' => $validated['subject'],
            'qualification' => $validated['qualification'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'joining_date' => $validated['joining_date'] ?? Carbon::today(),
            'salary' => $validated['salary'] ?? 0,
            'status' => $validated['status'] ?? 'active',
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'photo' => $photoPath,
            'village' => $validated['village'] ?? null,
            'commune' => $validated['commune'] ?? null,
            'district' => $validated['district'] ?? null,
            'province' => $validated['province'] ?? null,
            'class_assigned' => $classesStr,
        ]);

        return redirect()->route('teacher.index')->with('success', "បានបន្ថែមគ្រូបង្រៀន {$user->name} (អត្តលេខ: {$teacherIdCard}) ដោយជោគជ័យ!");
    }

    /**
     * Show the form for editing a teacher.
     */
    public function edit(Teacher $teacher)
    {
        $this->ensureCanManageTeachers();

        $classes = SchoolClass::orderedNames();
        $subjects = CourseSubject::orderBy('name')->pluck('name');
        $assignedClasses = $teacher->getAssignedClassesArray();

        return view('teacher.edit', compact('teacher', 'classes', 'subjects', 'assignedClasses'));
    }

    /**
     * Update the specified teacher.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $this->ensureCanManageTeachers();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'password' => 'nullable|string|min:6',
            'subject' => 'required|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave',
            'gender' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:50',
            'classes' => 'nullable|array',
            'village' => 'nullable|string|max:255',
            'commune' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Update User
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }
        $teacher->user->update($userData);

        $photoPath = $teacher->photo;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('teachers/photos', 'public');
        }

        $classesStr = !empty($validated['classes']) ? implode(',', $validated['classes']) : null;

        $teacher->update([
            'subject' => $validated['subject'],
            'qualification' => $validated['qualification'] ?? $teacher->qualification,
            'specialization' => $validated['specialization'] ?? $teacher->specialization,
            'joining_date' => $validated['joining_date'] ?? $teacher->joining_date,
            'salary' => $validated['salary'] ?? $teacher->salary,
            'status' => $validated['status'] ?? $teacher->status,
            'phone' => $validated['phone'] ?? $teacher->phone,
            'date_of_birth' => $validated['date_of_birth'] ?? $teacher->date_of_birth,
            'gender' => $validated['gender'] ?? $teacher->gender,
            'photo' => $photoPath,
            'village' => $validated['village'] ?? $teacher->village,
            'commune' => $validated['commune'] ?? $teacher->commune,
            'district' => $validated['district'] ?? $teacher->district,
            'province' => $validated['province'] ?? $teacher->province,
            'class_assigned' => $classesStr,
        ]);

        return redirect()->route('teacher.index')->with('success', 'បានកែប្រែព័ត៌មានគ្រូបង្រៀនដោយជោគជ័យ!');
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(Teacher $teacher)
    {
        $this->ensureCanManageTeachers();

        $name = $teacher->user->name ?? 'គ្រូ';
        $teacher->user?->delete();
        $teacher->delete();

        return redirect()->route('teacher.index')->with('success', "បានលុបគ្រូបង្រៀន {$name} ដោយជោគជ័យ!");
    }
}
