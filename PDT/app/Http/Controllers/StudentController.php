<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Role;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\SchoolParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\StudentApprovedMail;
use Carbon\Carbon;

class StudentController extends Controller
{
    private function ensureCanViewStudents(): void
    {
        abort_unless(auth()->user()?->canViewStudents(), 403, 'Unauthorized action.');
    }

    private function ensureCanManageStudents(): void
    {
        abort_unless(auth()->user()?->canManageStudents(), 403, 'Unauthorized action.');
    }

    /**
     * Display a listing of students with filters and pagination.
     */
    public function index(Request $request)
    {
        $this->ensureCanViewStudents();

        $search = trim((string) $request->input('search', ''));
        $gradeFilter = $request->input('grade');
        $statusFilter = $request->input('status');

        $query = Student::with('user', 'academicYear')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('student_id_card', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($gradeFilter, fn ($q) => $q->where('grade', $gradeFilter))
            ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter));

        $students = $query->latest()->paginate(10)->withQueryString();

        $availableGrades = SchoolClass::orderedNames();
        $classes = $availableGrades;
        $academicYears = AcademicYear::where('status', 'active')->get();
        $status = $statusFilter ?? '';
        $grade = $gradeFilter ?? '';

        return view('student.index', compact('students', 'search', 'gradeFilter', 'grade', 'statusFilter', 'status', 'classes', 'availableGrades', 'academicYears'));
    }

    /**
     * View complete Student Profile with 6 Tabs.
     */
    public function show(Student $student)
    {
        $this->ensureCanViewStudents();

        $student->load([
            'user',
            'academicYear',
            'parents',
            'attendances' => fn ($q) => $q->latest('date')->take(20),
            'examinationScores.examination',
            'invoices.payments',
            'bookBorrows.book',
        ]);

        return view('student.show', compact('student'));
    }

    /**
     * View / Print Student ID Card.
     */
    public function idCard(Student $student)
    {
        $this->ensureCanViewStudents();
        $student->load('user', 'academicYear');
        return view('student.id_card', compact('student'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $this->ensureCanManageStudents();

        $availableGrades = SchoolClass::orderedNames();
        $academicYears = AcademicYear::where('status', 'active')->get();

        return view('student.create', compact('availableGrades', 'academicYears'));
    }

    /**
     * Store a newly created student along with linked user account.
     */
    public function store(Request $request)
    {
        $this->ensureCanManageStudents();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'grade' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,graduated,transferred,suspended',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'enrollment_date' => 'nullable|date',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'phone' => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:50',
            'village' => 'nullable|string|max:255',
            'commune' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'high_school' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:50',
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Auto-generate student ID card code
        $nextId = Student::max('id') + 1;
        $studentIdCard = sprintf('STU-%s-%03d', date('Y'), $nextId);

        // Handle Photo Upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students/photos', 'public');
        }

        // Create User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign student role
        $studentRole = Role::where('name', 'student')->first();
        if ($studentRole) {
            $user->roles()->syncWithoutDetaching([$studentRole->id]);
        }

        // Create Student linked to user
        $student = Student::create([
            'user_id' => $user->id,
            'student_id_card' => $studentIdCard,
            'grade' => $validated['grade'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'photo' => $photoPath,
            'academic_year_id' => $validated['academic_year_id'] ?? null,
            'enrollment_date' => $validated['enrollment_date'] ?? Carbon::today(),
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'village' => $validated['village'] ?? null,
            'commune' => $validated['commune'] ?? null,
            'district' => $validated['district'] ?? null,
            'province' => $validated['province'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'high_school' => $validated['high_school'] ?? null,
            'mother_name' => $validated['mother_name'] ?? null,
            'mother_phone' => $validated['mother_phone'] ?? null,
            'father_name' => $validated['father_name'] ?? null,
            'father_phone' => $validated['father_phone'] ?? null,
        ]);

        // Auto-create parent link if parent information provided
        if (!empty($validated['father_name']) || !empty($validated['mother_name'])) {
            $parentName = !empty($validated['father_name']) ? $validated['father_name'] : $validated['mother_name'];
            $parentPhone = !empty($validated['father_phone']) ? $validated['father_phone'] : $validated['mother_phone'];
            $rel = !empty($validated['father_name']) ? 'ឪពុក' : 'ម្តាយ';

            $parent = SchoolParent::create([
                'name' => $parentName,
                'phone' => $parentPhone,
                'address' => $validated['province'] ?? null,
                'relationship' => $rel,
            ]);
            $student->parents()->syncWithoutDetaching([$parent->id => ['relationship' => $rel]]);
        }

        return redirect()->route('students.index')->with('success', "បានបន្ថែមសិស្ស {$user->name} (អត្តលេខ: {$studentIdCard}) ដោយជោគជ័យ!");
    }

    /**
     * Show the form for editing a student.
     */
    public function edit(Student $student)
    {
        $this->ensureCanManageStudents();

        $availableGrades = SchoolClass::orderedNames();
        $academicYears = AcademicYear::where('status', 'active')->get();

        return view('student.edit', compact('student', 'availableGrades', 'academicYears'));
    }

    /**
     * Update the specified student and linked user.
     */
    public function update(Request $request, Student $student)
    {
        $this->ensureCanManageStudents();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'password' => 'nullable|string|min:6',
            'grade' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,graduated,transferred,suspended',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'enrollment_date' => 'nullable|date',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:50',
            'village' => 'nullable|string|max:255',
            'commune' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'high_school' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:50',
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:50',
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
        $student->user->update($userData);

        // Photo Upload
        $photoPath = $student->photo;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students/photos', 'public');
        }

        // Update Student
        $student->update([
            'grade' => $validated['grade'] ?? $student->grade,
            'status' => $validated['status'] ?? $student->status,
            'photo' => $photoPath,
            'academic_year_id' => $validated['academic_year_id'] ?? $student->academic_year_id,
            'enrollment_date' => $validated['enrollment_date'] ?? $student->enrollment_date,
            'emergency_contact' => $validated['emergency_contact'] ?? $student->emergency_contact,
            'date_of_birth' => $validated['date_of_birth'] ?? $student->date_of_birth,
            'gender' => $validated['gender'] ?? $student->gender,
            'village' => $validated['village'] ?? $student->village,
            'commune' => $validated['commune'] ?? $student->commune,
            'district' => $validated['district'] ?? $student->district,
            'province' => $validated['province'] ?? $student->province,
            'phone' => $validated['phone'] ?? $student->phone,
            'high_school' => $validated['high_school'] ?? $student->high_school,
            'mother_name' => $validated['mother_name'] ?? $student->mother_name,
            'mother_phone' => $validated['mother_phone'] ?? $student->mother_phone,
            'father_name' => $validated['father_name'] ?? $student->father_name,
            'father_phone' => $validated['father_phone'] ?? $student->father_phone,
        ]);

        return redirect()->route('students.index')->with('success', 'បានកែប្រែព័ត៌មានសិស្សដោយជោគជ័យ!');
    }

    /**
     * Remove the specified student and linked user.
     */
    public function destroy(Student $student)
    {
        $this->ensureCanManageStudents();

        $name = $student->user->name ?? 'សិស្ស';
        $student->user?->delete();
        $student->delete();

        return redirect()->route('students.index')->with('success', "បានលុបសិស្ស {$name} ដោយជោគជ័យ!");
    }

    /**
     * Export students to CSV
     */
    public function export()
    {
        $this->ensureCanViewStudents();

        $students = Student::with('user', 'academicYear')->get();

        $filename = 'students_export_' . date('Y_m_d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($students) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Khmer support

            fputcsv($handle, ['អត្តលេខសិស្ស', 'ឈ្មោះ', 'ភេទ', 'ថ្ងៃខែឆ្នាំកំណើត', 'ថ្នាក់', 'ស្ថានភាព', 'អ៊ីមែល', 'លេខទូរស័ព្ទ', 'អាសយដ្ឋាន']);

            foreach ($students as $stu) {
                fputcsv($handle, [
                    $stu->student_id_card ?? 'STU-' . $stu->id,
                    $stu->user->name ?? '',
                    $stu->gender ?? '',
                    $stu->date_of_birth ? $stu->date_of_birth->format('Y-m-d') : '',
                    $stu->grade ?? '',
                    $stu->status ?? 'active',
                    $stu->user->email ?? '',
                    $stu->phone ?? '',
                    trim("{$stu->village} {$stu->commune} {$stu->district} {$stu->province}"),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Approve a pending student, create their login user, and email credentials via Brevo.
     */
    public function approve(Request $request, Student $student)
    {
        $this->ensureCanManageStudents();

        $validated = $request->validate([
            'grade' => 'nullable|string',
            'student_id_card' => 'nullable|string',
        ]);

        // 1. Auto-generate official Student ID (STU-YYYY-XXXX) if needed
        $studentIdCard = $validated['student_id_card'] ?? $student->student_id_card;
        if (empty($studentIdCard) || $studentIdCard === 'STU-រង់ចាំ') {
            $year = date('Y');
            $lastStudent = Student::whereNotNull('student_id_card')
                ->where('student_id_card', 'like', "STU-{$year}-%")
                ->orderBy('id', 'desc')
                ->first();
            
            $nextNum = 1;
            if ($lastStudent && preg_match('/STU-\d{4}-(\d+)/', $lastStudent->student_id_card, $matches)) {
                $nextNum = intval($matches[1]) + 1;
            } else {
                $nextNum = Student::whereNotNull('student_id_card')->count() + 1;
            }
            $studentIdCard = sprintf('STU-%s-%04d', $year, $nextNum);
        }

        // 2. Generate secure temporary plain-text password (Pdt@ + last 4 digits of phone)
        $cleanPhone = preg_replace('/\D/', '', (string) ($student->phone ?: '1234'));
        $last4Phone = strlen($cleanPhone) >= 4 ? substr($cleanPhone, -4) : str_pad((string)rand(100, 9999), 4, '0', STR_PAD_LEFT);
        $plainPassword = 'Pdt@' . $last4Phone;

        // 3. Determine login email
        $loginEmail = $student->email;
        if (empty($loginEmail)) {
            $loginEmail = strtolower(str_replace([' ', '-'], '', $studentIdCard)) . '@school.edu.kh';
            $student->email = $loginEmail;
        }

        // 4. Create or update User record
        $user = null;
        if ($student->user_id) {
            $user = User::find($student->user_id);
        }
        if (!$user) {
            $user = User::where('email', $loginEmail)->first();
        }
        if (!$user) {
            $user = new User();
        }

        $baseName = $student->name_khmer ?: ($student->name_latin ?: 'Student');
        $user->name = $baseName;
        $user->email = $loginEmail;
        $user->password = Hash::make($plainPassword);
        $user->save();

        $studentRole = Role::where('name', 'student')->first();
        if ($studentRole) {
            $user->roles()->syncWithoutDetaching([$studentRole->id]);
        }

        $student->user_id = $user->id;
        if (!empty($validated['grade'])) {
            $student->grade = $validated['grade'];
        }
        $student->student_id_card = $studentIdCard;
        $student->status = 'enrolled';
        $student->enrollment_date = Carbon::today();
        $student->save();

        // 5. Brevo SMTP Email Sending with try/catch
        $emailSent = false;
        if (!empty($student->email)) {
            try {
                Mail::to($student->email)
                    ->send(new StudentApprovedMail($student, $plainPassword, route('login')));
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::error('Brevo SMTP failed to send StudentApprovedMail: ' . $e->getMessage());
            }
        }

        if ($emailSent) {
            return back()->with('success', "បានអនុម័តសិស្ស {$student->display_name} (អត្តលេខ: {$studentIdCard}) ជោគជ័យ! ព័ត៌មានគណនី និងពាក្យសម្ងាត់បណ្តោះអាសន្ន ({$plainPassword}) ត្រូវបានផ្ញើទៅកាន់អ៊ីមែល {$student->email} រួចរាល់។");
        } else {
            return back()->with('warning', "បានអនុម័តសិស្ស {$student->display_name} (អត្តលេខ: {$studentIdCard}) ជោគជ័យ! (ចំណាំ៖ មិនអាចផ្ញើអ៊ីមែលបានទេ! ពាក្យសម្ងាត់បណ្តោះអាសន្នគឺ៖ {$plainPassword} សូមចម្លងជូនសិស្សដោយផ្ទាល់)។");
        }
    }
}
