<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\CourseSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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

        $classes = SchoolClass::orderBy('name')->pluck('name');
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

        $classes = SchoolClass::orderBy('name')->pluck('name');
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
