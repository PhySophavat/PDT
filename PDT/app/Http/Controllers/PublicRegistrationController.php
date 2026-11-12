<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Mail\StudentRegistrationReceivedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
        // 1. Abuse Prevention: Invisible Honeypot Field Trap
        // If a spam bot filled the hidden honeypot field, silently reject or delay and redirect
        if ($request->filled('hp_website_trap')) {
            Log::warning('Honeypot spam bot submission detected from IP: ' . $request->ip());
            usleep(random_int(300000, 600000)); // Delay 300-600ms to consume bot resources
            return redirect()->route('public.register-student')->with([
                'registration_success' => true,
                'registered_name' => 'សិស្ស',
                'registered_grade' => '---',
                'registered_phone' => '---',
                'registered_email' => '---',
            ]);
        }

        // Normalize student ID card if provided
        if ($request->filled('student_id_card')) {
            $request->merge([
                'student_id_card' => strtoupper(trim((string) $request->input('student_id_card'))),
            ]);
        }

        // Normalize Khmer digits (០-៩) to Latin digits (0-9) and trim spaces for phone numbers
        $normalizeKhmerNumbers = function ($str) {
            if (!is_string($str)) return $str;
            $khmer = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
            $latin = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $cleaned = str_replace($khmer, $latin, $str);
            return preg_replace('/[\s\-\.]/', '', $cleaned);
        };

        if ($request->filled('phone')) {
            $request->merge(['phone' => $normalizeKhmerNumbers($request->input('phone'))]);
        }
        if ($request->filled('parent_phone')) {
            $request->merge(['parent_phone' => $normalizeKhmerNumbers($request->input('parent_phone'))]);
        }

        // 2. Strict Server-Side Input Validation & Type Enforcement
        $validated = $request->validate([
            'student_id_card' => 'nullable|string|max:50|unique:students,student_id_card',
            'name_khmer' => 'required|string|min:2|max:100',
            'name_latin' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s\.\-\']+$/'],
            'gender' => 'required|in:male,female',
            'date_of_birth' => ['required', 'date', 'before_or_equal:' . Carbon::now()->subYears(10)->format('Y-m-d')],
            'phone' => ['required', 'string', 'regex:/^0[0-9]{8,9}$/'],
            'email' => 'required|string|email:rfc,filter|max:255|unique:students,email|unique:users,email',
            'grade' => 'required|string|max:50',
            'address' => 'required|string|min:3|max:500',
            'photo' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|mimetypes:image/jpeg,image/png,image/webp|max:3072',
            'parent_name' => 'required|string|min:2|max:100',
            'parent_relationship' => 'required|string|max:100',
            'parent_occupation' => 'nullable|string|max:150',
            'parent_phone' => ['required', 'string', 'regex:/^0[0-9]{8,9}$/'],
        ], [
            'student_id_card.unique' => 'អត្តលេខសិស្សនេះមានក្នុងប្រព័ន្ធរួចហើយ សូមពិនិត្យ ឬជ្រើសរើសអត្តលេខផ្សេង',
            'student_id_card.max' => 'អត្តលេខសិស្សមិនត្រូវលើសពី ៥០ តួអក្សរឡើយ',
            'name_khmer.required' => 'សូមបញ្ចូលឈ្មោះពេញជាភាសាខ្មែរ',
            'name_khmer.min' => 'ឈ្មោះភាសាខ្មែរត្រូវមានយ៉ាងតិច ២ តួអក្សរ',
            'name_khmer.max' => 'ឈ្មោះភាសាខ្មែរមិនត្រូវលើសពី ១០០ តួអក្សរឡើយ',
            'name_latin.required' => 'សូមបញ្ចូលឈ្មោះជាអក្សរឡាតាំង',
            'name_latin.min' => 'ឈ្មោះជាអក្សរឡាតាំងត្រូវមានយ៉ាងតិច ២ តួអក្សរ',
            'name_latin.max' => 'ឈ្មោះជាអក្សរឡាតាំងមិនត្រូវលើសពី ១០០ តួអក្សរឡើយ',
            'name_latin.regex' => 'ឈ្មោះជាអក្សរឡាតាំងត្រូវតែជាអក្សរឡាតាំង (A-Z) គ្មានលេខ ឬនិមិត្តសញ្ញាពិសេសឡើយ',
            'gender.required' => 'សូមជ្រើសរើសភេទ',
            'gender.in' => 'ភេទត្រូវតែជា ប្រុស ឬ ស្រី',
            'date_of_birth.required' => 'សូមជ្រើសរើសថ្ងៃខែឆ្នាំកំណើត',
            'date_of_birth.date' => 'ទម្រង់ថ្ងៃខែឆ្នាំកំណើតមិនត្រឹមត្រូវ',
            'date_of_birth.before_or_equal' => 'សិស្សត្រូវមានអាយុយ៉ាងតិច ១០ ឆ្នាំឡើងទៅ (ថ្ងៃខែឆ្នាំកំណើតត្រូវកើតនៅត្រឹម ឬមុនថ្ងៃទី ' . Carbon::now()->subYears(10)->format('d/m/Y') . ')',
            'phone.required' => 'សូមបញ្ចូលលេខទូរស័ព្ទសិស្ស',
            'phone.regex' => 'លេខទូរស័ព្ទសិស្សត្រូវតែជាលេខជាភាសាអង់គ្លេស (0-9) ចាប់ផ្ដើមដោយលេខ 0 មាន 9 ទៅ 10 ខ្ទង់ (ឧ. 012345678 មិនប្រើលេខខ្មែរ ០-៩ ឡើយ)',
            'email.required' => 'សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែល (Email) របស់សិស្ស',
            'email.email' => 'ទម្រង់អ៊ីមែលមិនត្រឹមត្រូវឡើយ (ឧ. name@example.com)',
            'email.max' => 'អ៊ីមែលមិនត្រូវលើសពី ២៥៥ តួអក្សរឡើយ',
            'email.unique' => 'អ៊ីមែលនេះមានក្នុងប្រព័ន្ធរួចហើយ សូមប្រើប្រាស់អ៊ីមែលផ្សេង',
            'grade.required' => 'សូមជ្រើសរើសកម្រិតថ្នាក់ដែលចង់ស្នើសុំចូលរៀន',
            'address.required' => 'សូមបញ្ចូលអាសយដ្ឋានបច្ចុប្បន្ន',
            'address.min' => 'អាសយដ្ឋានត្រូវមានយ៉ាងតិច ៣ តួអក្សរ',
            'address.max' => 'អាសយដ្ឋានមិនត្រូវលើសពី ៥០០ តួអក្សរឡើយ',
            'photo.file' => 'ឯកសារភ្ជាប់ត្រូវតែជា file រូបភាពត្រឹមត្រូវ',
            'photo.image' => 'ឯកសាររូបថតត្រូវតែជាប្រភេទរូបភាព (JPG, PNG, WebP)',
            'photo.mimes' => 'រូបថតទទួលស្គាល់តែប្រភេទ JPG, JPEG, PNG ឬ WebP ប៉ុណ្ណោះ',
            'photo.mimetypes' => 'ប្រភេទ Content (MIME) របស់ឯកសារត្រូវតែជារូបភាពពិតប្រាកដ',
            'photo.max' => 'ទំហំរូបថតមិនត្រូវលើសពី 3MB ឡើយ ដើម្បីការពារសុវត្ថិភាពម៉ាស៊ីនបម្រើ',
            'parent_name.required' => 'សូមបញ្ចូលឈ្មោះឪពុក/ម្តាយ/អាណាព្យាបាល',
            'parent_name.min' => 'ឈ្មោះអាណាព្យាបាលត្រូវមានយ៉ាងតិច ២ តួអក្សរ',
            'parent_name.max' => 'ឈ្មោះអាណាព្យាបាលមិនត្រូវលើសពី ១០០ តួអក្សរឡើយ',
            'parent_relationship.required' => 'សូមបញ្ជាក់ត្រូវជាអ្វីនឹងសិស្ស',
            'parent_occupation.max' => 'មុខរបរមិនត្រូវលើសពី ១៥០ តួអក្សរឡើយ',
            'parent_phone.required' => 'សូមបញ្ចូលលេខទូរស័ព្ទអាណាព្យាបាល',
            'parent_phone.regex' => 'លេខទូរស័ព្ទអាណាព្យាបាលត្រូវតែជាលេខជាភាសាអង់គ្លេស (0-9) ចាប់ផ្ដើមដោយលេខ 0 មាន 9 ទៅ 10 ខ្ទង់ (ឧ. 012345678 មិនប្រើលេខខ្មែរ ០-៩ ឡើយ)',
        ]);

        // 3. File Upload Security: Extension & MIME Verification + UUID Renaming
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');
            if ($photoFile->isValid()) {
                $ext = strtolower($photoFile->getClientOriginalExtension());
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $ext = 'jpg';
                }
                // Generate safe random UUID filename to prevent arbitrary code execution and directory traversal
                $safeFilename = Str::uuid()->toString() . '.' . $ext;
                $photoPath = $photoFile->storeAs('students/photos', $safeFilename, 'public');
            }
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

        // 4. Data Sanitization & XSS Prevention: Strip HTML tags and trim
        $cleanKhmerName = strip_tags(trim($validated['name_khmer']));
        $cleanLatinName = strip_tags(trim($validated['name_latin']));
        $cleanAddress = strip_tags(trim($validated['address']));
        $cleanParentName = strip_tags(trim($validated['parent_name']));
        $cleanRelationship = strip_tags(trim($validated['parent_relationship']));
        $cleanOccupation = !empty($validated['parent_occupation']) ? strip_tags(trim($validated['parent_occupation'])) : null;
        $cleanPhone = preg_replace('/[^\d]/', '', trim($validated['phone']));
        $cleanParentPhone = preg_replace('/[^\d]/', '', trim($validated['parent_phone']));

        $studentData = [
            'student_id_card' => $studentIdCard,
            'name_khmer' => $cleanKhmerName,
            'name_latin' => $cleanLatinName,
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'phone' => $cleanPhone,
            'email' => strtolower(trim($validated['email'])),
            'grade' => $validated['grade'],
            'address' => $cleanAddress,
            'province' => $cleanAddress,
            'photo' => $photoPath,
            'parent_name' => $cleanParentName,
            'parent_relationship' => $cleanRelationship,
            'parent_occupation' => $cleanOccupation,
            'parent_phone' => $cleanParentPhone,
            'status' => 'pending',
            'academic_year_id' => $activeYear?->id,
            'enrollment_date' => Carbon::today(),
            'emergency_contact' => $cleanParentPhone,
        ];

        if ($cleanRelationship === 'ឪពុក') {
            $studentData['father_name'] = $cleanParentName;
            $studentData['father_phone'] = $cleanParentPhone;
        } elseif ($cleanRelationship === 'ម្តាយ') {
            $studentData['mother_name'] = $cleanParentName;
            $studentData['mother_phone'] = $cleanParentPhone;
        } else {
            $studentData['father_name'] = $cleanParentName;
            $studentData['father_phone'] = $cleanParentPhone;
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
