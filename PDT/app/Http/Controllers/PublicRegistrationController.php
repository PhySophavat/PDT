<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Mail\StudentRegistrationReceivedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PublicRegistrationController extends Controller
{
    /**
     * Show the public mobile-friendly student registration form.
     */
    public function show()
    {
        $classes = SchoolClass::orderedNames();
        if ($classes->isEmpty()) {
            $classes = collect([
                '7A',
                '7B',
                '7C',
                '7D',
                '8A',
                '8B',
                '8C',
                '8D',
                '9A',
                '9B',
                '9C',
                '9D',
                '10A',
                '10B',
                '10C',
                '10D',
                '11A',
                '11B',
                '11C',
                '11D',
                '12A',
                '12B',
                '12C',
                '12D'
            ]);
        }

        return view('public.register_student', compact('classes'));
    }

    /**
     * Handle public registration form submission.
     */
    public function store(Request $request)
    {
        if ($request->filled('student_id_card')) {
            $request->merge([
                'student_id_card' => strtoupper(trim((string) $request->input('student_id_card'))),
            ]);
        }

        $validated = $request->validate([
            'student_id_card' => 'nullable|string|max:50|unique:students,student_id_card',
            'name_khmer' => 'required|string|max:255',
            'name_latin' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:students,email|unique:users,email',
            'grade' => 'required|string|max:50',
            'address' => 'required|string|max:500',
            'photo' => 'nullable|image|max:3072',
            'parent_name' => 'required|string|max:255',
            'parent_relationship' => 'required|string|max:100',
            'parent_occupation' => 'nullable|string|max:255',
            'parent_phone' => 'required|string|max:50',
        ], [
            'student_id_card.unique' => 'អត្តលេខសិស្សនេះមានក្នុងប្រព័ន្ធរួចហើយ សូមពិនិត្យ ឬជ្រើសរើសអត្តលេខផ្សេង',
            'student_id_card.max' => 'អត្តលេខសិស្សមិនត្រូវលើសពី ៥០ តួអក្សរឡើយ',
            'name_khmer.required' => 'សូមបញ្ចូលឈ្មោះពេញជាភាសាខ្មែរ',
            'name_latin.required' => 'សូមបញ្ចូលឈ្មោះជាអក្សរឡាតាំង',
            'gender.required' => 'សូមជ្រើសរើសភេទ',
            'date_of_birth.required' => 'សូមជ្រើសរើសថ្ងៃខែឆ្នាំកំណើត',
            'phone.required' => 'សូមបញ្ចូលលេខទូរស័ព្ទសិស្ស',
            'email.required' => 'សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែល (Email) របស់សិស្ស',
            'email.email' => 'ទម្រង់អ៊ីមែលមិនត្រឹមត្រូវឡើយ (ឧ. name@example.com)',
            'email.unique' => 'អ៊ីមែលនេះមានក្នុងប្រព័ន្ធរួចហើយ សូមប្រើប្រាស់អ៊ីមែលផ្សេង',
            'grade.required' => 'សូមជ្រើសរើសកម្រិតថ្នាក់ដែលចង់ស្នើសុំចូលរៀន',
            'address.required' => 'សូមបញ្ចូលអាសយដ្ឋានបច្ចុប្បន្ន',
            'photo.image' => 'ឯកសាររូបថតត្រូវតែជាប្រភេទរូបភាព (JPG, PNG, WebP)',
            'photo.max' => 'រូបថតមិនត្រូវលើសពី 3MB ឡើយ',
            'parent_name.required' => 'សូមបញ្ចូលឈ្មោះឪពុក/ម្តាយ/អាណាព្យាបាល',
            'parent_relationship.required' => 'សូមបញ្ជាក់ត្រូវជាអ្វីនឹងសិស្ស',
            'parent_phone.required' => 'សូមបញ្ចូលលេខទូរស័ព្ទអាណាព្យាបាល',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students/photos', 'public');
        }

        $activeYear = AcademicYear::where('status', 'active')->first();

        // Use custom student ID card or auto-generate if blank
        $studentIdCard = !empty($validated['student_id_card']) ? trim($validated['student_id_card']) : null;
        if (!$studentIdCard) {
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
            $studentIdCard = sprintf('STU-%s-%03d', $year, $nextNum);
        }

        $studentData = [
            'student_id_card' => $studentIdCard,
            'name_khmer' => $validated['name_khmer'],
            'name_latin' => $validated['name_latin'],
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'phone' => $validated['phone'],
            'email' => strtolower(trim($validated['email'])),
            'grade' => $validated['grade'],
            'address' => $validated['address'],
            'province' => $validated['address'],
            'photo' => $photoPath,
            'parent_name' => $validated['parent_name'],
            'parent_relationship' => $validated['parent_relationship'],
            'parent_occupation' => $validated['parent_occupation'] ?? null,
            'parent_phone' => $validated['parent_phone'],
            'status' => 'pending',
            'academic_year_id' => $activeYear?->id,
            'enrollment_date' => Carbon::today(),
            'emergency_contact' => $validated['parent_phone'],
        ];

        if ($validated['parent_relationship'] === 'ឪពុក') {
            $studentData['father_name'] = $validated['parent_name'];
            $studentData['father_phone'] = $validated['parent_phone'];
        } elseif ($validated['parent_relationship'] === 'ម្តាយ') {
            $studentData['mother_name'] = $validated['parent_name'];
            $studentData['mother_phone'] = $validated['parent_phone'];
        } else {
            $studentData['father_name'] = $validated['parent_name'];
            $studentData['father_phone'] = $validated['parent_phone'];
        }

        $student = Student::create($studentData);

        // Send acknowledgement email to student via Brevo SMTP
        if (!empty($student->email)) {
            try {
                Mail::to($student->email)->send(new StudentRegistrationReceivedMail($student));
            } catch (\Throwable $e) {
                Log::error('Brevo SMTP failed to send StudentRegistrationReceivedMail to ' . $student->email . ': ' . $e->getMessage());
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'ការចុះឈ្មោះរបស់អ្នកត្រូវបានបញ្ជូនដោយជោគជ័យ! យើងបានផ្ញើសារបញ្ជាក់ទៅកាន់អ៊ីមែល ' . $student->email . ' រួចរាល់ហើយ។',
                'student_id' => $student->id
            ]);
        }

        return redirect()->route('public.register-student')->with([
            'registration_success' => true,
            'registered_id_card' => $student->student_id_card,
            'registered_name' => $student->name_khmer ?: $student->name_latin,
            'registered_name_latin' => $student->name_latin,
            'registered_email' => $student->email,
            'registered_phone' => $student->phone,
            'registered_grade' => $student->grade,
            'registered_gender' => $student->gender === 'female' ? 'ស្រី' : 'ប្រុស',
            'registered_dob' => Carbon::parse($student->date_of_birth)->format('d/m/Y'),
            'registered_date' => Carbon::now()->format('d/m/Y - h:i A'),
        ]);
    }
}
