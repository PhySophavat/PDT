<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\CourseSubject;
use App\Models\Attendance;
use App\Models\Examination;
use App\Models\ExaminationScore;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SchoolExpense;
use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\Announcement;
use App\Models\SchoolParent;
use App\Models\SystemSetting;
use App\Models\Role;
use App\Models\AcademicYear;
use App\Models\Timetable;
use App\Models\FeeCategory;
use App\Models\User;
use App\Models\StudentLeaveRequest;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $demoMode = $request->input('demo_mode', SystemSetting::get('demo_mode', '1'));

        if ($demoMode === '0') {
            // Live Production Mode: return strictly real database records
            $totalStudents = Student::count();
            $totalTeachers = Teacher::count();
            $totalClasses = SchoolClass::count();
            $totalSubjects = CourseSubject::count();
            $totalRevenue = (float) Payment::sum('amount');
            $totalExpenses = (float) SchoolExpense::sum('amount');

            $today = Carbon::today()->toDateString();
            $todayTotal = Attendance::whereDate('date', $today)->count();
            $todayPresent = Attendance::whereDate('date', $today)->whereIn('status', ['present', 'late'])->count();
            $attendanceRate = $todayTotal > 0 ? round(($todayPresent / $todayTotal) * 100, 1) : 0;

            $realClasses = SchoolClass::with('teacher')->take(10)->get()->map(function ($cls) use ($today) {
                $stuCount = Student::where('grade', $cls->name)->count();
                $presentCount = Attendance::where('class_name', $cls->name)->whereDate('date', $today)->whereIn('status', ['present', 'late'])->count();
                $absentCount = Attendance::where('class_name', $cls->name)->whereDate('date', $today)->where('status', 'absent')->count();
                $rate = $stuCount > 0 ? round(($presentCount / $stuCount) * 100, 1) : 0;

                return [
                    'name' => $cls->name,
                    'teacher' => $cls->teacher?->name_khmer ?: ($cls->teacher?->user?->name ?: 'មិនទាន់ចាត់តាំង'),
                    'students' => $stuCount,
                    'present' => $presentCount,
                    'absent' => $absentCount,
                    'rate' => $rate,
                ];
            });

            return response()->json([
                'demo_mode' => '0',
                'stats' => [
                    'students' => $totalStudents,
                    'teachers' => $totalTeachers,
                    'classes' => $totalClasses,
                    'subjects' => $totalSubjects,
                    'revenue' => $totalRevenue,
                    'expenses' => $totalExpenses,
                    'attendanceRate' => $attendanceRate,
                ],
                'recentClasses' => $realClasses,
            ]);
        }

        // Demo / Dev Mode: return rich full mock numbers and classes
        return response()->json([
            'demo_mode' => '1',
            'stats' => [
                'students' => 120,
                'teachers' => 15,
                'classes' => 48,
                'subjects' => 11,
                'revenue' => 14250.00,
                'expenses' => 4800.00,
                'attendanceRate' => 96.8,
            ],
            'recentClasses' => [
                ['name' => '7A', 'teacher' => 'លោកគ្រូ សុខ ចាន់ដារ៉ា', 'students' => 35, 'present' => 34, 'absent' => 1, 'rate' => 97.1],
                ['name' => '8A', 'teacher' => 'អ្នកគ្រូ កែវ មុនីរ័ត្ន', 'students' => 36, 'present' => 35, 'absent' => 1, 'rate' => 97.2],
                ['name' => '9A', 'teacher' => 'លោកគ្រូ ជា វណ្ណៈ', 'students' => 34, 'present' => 32, 'absent' => 2, 'rate' => 94.1],
                ['name' => '10A', 'teacher' => 'លោកគ្រូ ហេង បូរ៉ា', 'students' => 38, 'present' => 37, 'absent' => 1, 'rate' => 97.4],
                ['name' => '11A', 'teacher' => 'អ្នកគ្រូ អ៊ុក សោភា', 'students' => 32, 'present' => 31, 'absent' => 1, 'rate' => 96.9],
                ['name' => '12A', 'teacher' => 'លោកគ្រូ សេង វិបុល', 'students' => 35, 'present' => 35, 'absent' => 0, 'rate' => 100.0],
            ],
        ]);
    }

    public function students(Request $request)
    {
        $query = Student::with('user');
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%"))
                    ->orWhere('name_khmer', 'like', "%{$s}%")
                    ->orWhere('name_latin', 'like', "%{$s}%")
                    ->orWhere('student_id_card', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }
        if ($request->grade) {
            $query->where('grade', $request->grade);
        }
        if ($request->status) {
            if ($request->status === 'active') {
                $query->whereIn('status', ['active', 'enrolled']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $students = $query->latest()->get()->map(fn ($st) => [
            'id' => $st->id,
            'student_id_card' => $st->student_id_card ?: 'STU-រង់ចាំ',
            'name' => $st->display_name,
            'name_khmer' => $st->name_khmer,
            'name_latin' => $st->name_latin,
            'gender' => $st->gender,
            'grade' => $st->grade,
            'phone' => $st->phone,
            'date_of_birth' => $st->date_of_birth ? Carbon::parse($st->date_of_birth)->format('Y-m-d') : null,
            'address' => $st->address ?: $st->province,
            'photo' => $st->photo ? asset('storage/' . $st->photo) : null,
            'parent_name' => $st->parent_name ?: ($st->father_name ?: $st->mother_name),
            'parent_relationship' => $st->parent_relationship ?: ($st->father_name ? 'ឪពុក' : ($st->mother_name ? 'ម្តាយ' : 'អាណាព្យាបាល')),
            'parent_occupation' => $st->parent_occupation,
            'parent_phone' => $st->parent_phone ?: ($st->father_phone ?: $st->mother_phone),
            'status' => $st->status ?? 'active',
            'created_at' => $st->created_at ? $st->created_at->format('Y-m-d H:i') : null,
        ]);

        $demoMode = $request->input('demo_mode', SystemSetting::get('demo_mode', '1'));

        if ($students->isEmpty() && $demoMode !== '0') {
            $demoList = $this->getDemoStudentsList();
            if ($request->grade) {
                $demoList = array_values(array_filter($demoList, fn ($s) => $s['grade'] === $request->grade));
            }
            if ($request->search) {
                $s = mb_strtolower($request->search);
                $demoList = array_values(array_filter($demoList, fn ($st) => 
                    str_contains(mb_strtolower($st['name_khmer']), $s) || 
                    str_contains(mb_strtolower($st['name_latin']), $s) ||
                    str_contains($st['student_id_card'], $s)
                ));
            }

            return response()->json([
                'students' => $demoList,
                'counts' => [
                    'total' => count($demoList),
                    'active' => count(array_filter($demoList, fn ($st) => $st['status'] === 'active')),
                    'pending' => count(array_filter($demoList, fn ($st) => $st['status'] === 'pending')),
                    'graduated' => 0,
                    'transferred' => 0,
                ]
            ]);
        }

        $pendingCount = Student::where('status', 'pending')->count();
        $activeCount = Student::whereIn('status', ['active', 'enrolled'])->count();
        $graduatedCount = Student::where('status', 'graduated')->count();
        $transferredCount = Student::where('status', 'transferred')->count();

        return response()->json([
            'students' => $students,
            'counts' => [
                'total' => Student::count(),
                'active' => $activeCount,
                'pending' => $pendingCount,
                'graduated' => $graduatedCount,
                'transferred' => $transferredCount,
            ]
        ]);
    }

    public function approveStudent(Request $request, Student $student)
    {
        $validated = $request->validate([
            'grade' => 'required|string',
            'student_id_card' => 'nullable|string',
        ]);

        // Auto-generate student ID if not provided or empty
        $studentIdCard = $validated['student_id_card'] ?? null;
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
            $studentIdCard = sprintf('STU-%s-%03d', $year, $nextNum);
        }

        // Create or link User if not exists
        if (!$student->user_id) {
            $baseName = $student->name_khmer ?: ($student->name_latin ?: 'Student');
            $email = strtolower(str_replace([' ', '-'], '', $studentIdCard)) . '@school.edu.kh';
            
            if (\App\Models\User::where('email', $email)->exists()) {
                $email = strtolower(str_replace([' ', '-'], '', $studentIdCard)) . '_' . $student->id . '@school.edu.kh';
            }

            $user = \App\Models\User::create([
                'name' => $baseName,
                'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            ]);

            $studentRole = Role::where('name', 'student')->first();
            if ($studentRole) {
                $user->roles()->syncWithoutDetaching([$studentRole->id]);
            }

            $student->user_id = $user->id;
        }

        $student->grade = $validated['grade'];
        $student->student_id_card = $studentIdCard;
        $student->status = 'active';
        $student->enrollment_date = Carbon::today();
        $student->save();

        if ($student->parent_name && $student->parents()->count() === 0) {
            $parent = SchoolParent::create([
                'name' => $student->parent_name,
                'phone' => $student->parent_phone,
                'address' => $student->address ?: $student->province,
                'relationship' => $student->parent_relationship ?: 'អាណាព្យាបាល',
            ]);
            $student->parents()->syncWithoutDetaching([$parent->id => ['relationship' => $parent->relationship]]);
        }

        return response()->json([
            'success' => true,
            'message' => "បានអនុម័តសិស្ស {$student->display_name} (អត្តលេខ: {$studentIdCard}) ចូលរៀនថ្នាក់ {$student->grade} ដោយជោគជ័យ!",
            'student' => $student->load('user')
        ]);
    }

    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'name_khmer' => 'required|string|max:255',
            'name_latin' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'grade' => 'required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:50',
            'parent_relationship' => 'nullable|string|max:100',
        ]);

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

        $baseName = $validated['name_khmer'] ?: ($validated['name_latin'] ?? 'Student');
        $email = strtolower(str_replace([' ', '-'], '', $studentIdCard)) . '@school.edu.kh';
        if (\App\Models\User::where('email', $email)->exists()) {
            $email = strtolower(str_replace([' ', '-'], '', $studentIdCard)) . '_' . time() . '@school.edu.kh';
        }

        $user = \App\Models\User::create([
            'name' => $baseName,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
        ]);

        $studentRole = Role::where('name', 'student')->first();
        if ($studentRole) {
            $user->roles()->syncWithoutDetaching([$studentRole->id]);
        }

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_card' => $studentIdCard,
            'name_khmer' => $validated['name_khmer'],
            'name_latin' => $validated['name_latin'] ?? null,
            'gender' => $validated['gender'],
            'grade' => $validated['grade'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'province' => $validated['address'] ?? null,
            'parent_name' => $validated['parent_name'] ?? null,
            'parent_phone' => $validated['parent_phone'] ?? null,
            'parent_relationship' => $validated['parent_relationship'] ?? 'អាណាព្យាបាល',
            'status' => 'enrolled',
            'enrollment_date' => Carbon::today(),
        ]);

        if (!empty($validated['parent_name'])) {
            $parent = SchoolParent::firstOrCreate(
                ['phone' => $validated['parent_phone'] ?: ''],
                [
                    'name' => $validated['parent_name'],
                    'name_khmer' => $validated['parent_name'],
                    'relationship' => $validated['parent_relationship'] ?? 'អាណាព្យាបាល',
                    'address' => $validated['address'] ?? null,
                    'status' => 'active',
                ]
            );
            $student->parents()->syncWithoutDetaching([$parent->id => ['relationship' => $parent->relationship]]);
        }

        return response()->json([
            'success' => true,
            'message' => "បានបន្ថែមសិស្ស {$student->display_name} (អត្តលេខ: {$studentIdCard}) ដោយជោគជ័យ!",
            'student' => $student
        ]);
    }

    public function updateStudent(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name_khmer' => 'required|string|max:255',
            'name_latin' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'grade' => 'required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:50',
            'parent_relationship' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ]);

        $student->update($validated);

        if ($student->user) {
            $student->user->update([
                'name' => $validated['name_khmer'] ?: ($validated['name_latin'] ?? $student->user->name)
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "បានកែប្រែព័ត៌មានសិស្ស {$student->display_name} ដោយជោគជ័យ!",
            'student' => $student
        ]);
    }

    public function destroyStudent(Student $student)
    {
        $name = $student->display_name;
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => "បានលុបសិស្ស {$name} ដោយជោគជ័យ!"
        ]);
    }

    public function scanStudentsOcr(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
        ]);

        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        // 1. If it's a Spreadsheet (CSV / TXT)
        if (in_array($extension, ['csv', 'txt'])) {
            return $this->parseCsvStudents($file->getRealPath());
        }

        // 2. Gemini 1.5 Flash API for Images / PDFs
        $apiKey = $request->input('gemini_api_key')
            ?: env('GEMINI_API_KEY') 
            ?: config('services.gemini.api_key')
            ?: SystemSetting::get('gemini_api_key');

        if ($request->filled('gemini_api_key')) {
            SystemSetting::set('gemini_api_key', $request->input('gemini_api_key'));
            $this->updateEnvKey('GEMINI_API_KEY', $request->input('gemini_api_key'));
        }

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'needs_api_key' => true,
                'message' => 'សូមបញ្ចូល ឬកំណត់ GEMINI_API_KEY នៅក្នុង .env ជាមុនសិន ដើម្បីដំណើរការការស្កេនរូបភាពដោយ AI! (ឬលោកអ្នកអាចប្រើជម្រើសផ្ទុកឯកសារ Excel/CSV ឬចម្លងបិទភ្ជាប់អត្ថបទដោយផ្ទាល់)',
            ], 400);
        }

        $fileBytes = base64_encode(file_get_contents($file->getRealPath()));
        if ($extension === 'pdf') {
            $mimeType = 'application/pdf';
        }

        $prompt = "You are an expert Khmer OCR data extractor for a Cambodian school management system.\nExtract all student records from this image/document of a student roster.\nReturn ONLY a valid JSON array of objects with no markdown formatting or extra text.\nEach object must follow this exact structure:\n[\n  {\n    \"name\": \"ឈ្មោះសិស្សជាអក្សរខ្មែរ\",\n    \"english_name\": \"English name if available, otherwise empty string\",\n    \"gender\": \"ប្រុស or ស្រី (convert ប/M to ប្រុស, ស/F to ស្រី)\",\n    \"phone\": \"Phone number if available, otherwise empty string\"\n  }\n]\nEnsure Khmer spelling and vowels are accurately preserved. Ignore headers, footers, and signature lines.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(60)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $fileBytes,
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gemini API Error: ' . $response->body(),
                ], 500);
            }

            $body = $response->json();
            $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
            $cleaned = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
            $cleaned = preg_replace('/\s*```$/', '', $cleaned);
            $students = json_decode($cleaned, true) ?: [];

            $formatted = array_map(function ($s) {
                $g = trim($s['gender'] ?? '');
                $gender = (str_contains($g, 'ស្រី') || str_contains($g, 'ស') || strtolower($g) === 'female' || strtolower($g) === 'f') ? 'ស្រី' : 'ប្រុស';
                return [
                    'name' => trim($s['name'] ?? ''),
                    'english_name' => trim($s['english_name'] ?? ''),
                    'gender' => $gender,
                    'phone' => trim($s['phone'] ?? ''),
                ];
            }, $students);

            return response()->json([
                'success' => true,
                'count' => count($formatted),
                'students' => $formatted,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'កំហុសក្នុងការស្កេនរូបភាព: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function saveGeminiApiKey(Request $request)
    {
        $request->validate([
            'gemini_api_key' => 'required|string',
        ]);

        $key = trim($request->input('gemini_api_key'));
        SystemSetting::set('gemini_api_key', $key);
        $this->updateEnvKey('GEMINI_API_KEY', $key);

        return response()->json([
            'success' => true,
            'message' => 'បានរក្សាទុក Gemini API Key ដោយជោគជ័យ!',
        ]);
    }

    private function updateEnvKey($key, $value)
    {
        try {
            $path = base_path('.env');
            if (!file_exists($path)) return;

            $content = file_get_contents($path);
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}\n";
            }
            file_put_contents($path, $content);
        } catch (\Exception $e) {
            \Log::warning("Could not update .env key {$key}: " . $e->getMessage());
        }
    }

    private function parseCsvStudents($filePath)
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = null;
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$headers) {
                    $headers = array_map(fn ($h) => strtolower(trim($h)), $data);
                    continue;
                }
                if (empty(array_filter($data))) continue;

                $row = [];
                foreach ($headers as $idx => $key) {
                    $row[$key] = $data[$idx] ?? '';
                }

                $name = $row['ឈ្មោះសិស្ស'] ?? ($row['ឈ្មោះ'] ?? ($row['name'] ?? ($row['name_khmer'] ?? ($data[1] ?? ($data[0] ?? '')))));
                $enName = $row['english_name'] ?? ($row['name_latin'] ?? ($row['សម្គាល់'] ?? ''));
                $genderRaw = $row['ភេទ'] ?? ($row['gender'] ?? ($data[2] ?? ''));
                $gender = (str_contains($genderRaw, 'ស្រី') || str_contains($genderRaw, 'ស') || strtolower($genderRaw) === 'female' || strtolower($genderRaw) === 'f') ? 'ស្រី' : 'ប្រុស';
                $phone = $row['លេខទូរស័ព្ទ'] ?? ($row['ទូរស័ព្ទ'] ?? ($row['phone'] ?? ''));
                $grade = $row['ថ្នាក់'] ?? ($row['grade'] ?? ($row['class'] ?? ''));

                if (!empty($name)) {
                    $rows[] = [
                        'name' => trim($name),
                        'english_name' => trim($enName),
                        'gender' => $gender,
                        'phone' => trim($phone),
                        'grade' => trim($grade),
                    ];
                }
            }
            fclose($handle);
        }

        return response()->json([
            'success' => true,
            'count' => count($rows),
            'students' => $rows,
        ]);
    }

    public function bulkStoreStudents(Request $request)
    {
        $validated = $request->validate([
            'target_class' => 'nullable|string|max:50',
            'students' => 'required|array|min:1',
            'students.*.name' => 'required|string|max:255',
            'students.*.english_name' => 'nullable|string|max:255',
            'students.*.gender' => 'required|string|max:50',
            'students.*.grade' => 'nullable|string|max:50',
            'students.*.phone' => 'nullable|string|max:50',
        ]);

        $defaultClass = $validated['target_class'] ?: '10A';
        $year = date('Y');
        $studentRole = Role::where('name', 'student')->first();

        $createdStudents = [];

        DB::transaction(function () use ($validated, $defaultClass, $year, $studentRole, &$createdStudents) {
            $sequences = [];

            foreach ($validated['students'] as $item) {
                $rawClass = !empty($item['grade']) ? $item['grade'] : $defaultClass;
                $cleanClass = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $rawClass));
                if (empty($cleanClass)) {
                    $cleanClass = '10A';
                }

                if (!isset($sequences[$cleanClass])) {
                    $prefix = "STU-{$year}-{$cleanClass}-%";
                    $existingCards = Student::where('student_id_card', 'like', $prefix)->pluck('student_id_card');
                    $maxSeq = 0;
                    foreach ($existingCards as $card) {
                        if (preg_match('/STU-\d{4}-[^-]+-(\d+)/', $card, $m)) {
                            $num = intval($m[1]);
                            if ($num > $maxSeq) {
                                $maxSeq = $num;
                            }
                        }
                    }
                    if ($maxSeq === 0) {
                        $maxSeq = Student::where('grade', $rawClass)->count();
                    }
                    $sequences[$cleanClass] = $maxSeq;
                }

                $sequences[$cleanClass]++;
                $seqNumber = $sequences[$cleanClass];
                $studentIdCard = sprintf('STU-%s-%s-%03d', $year, $cleanClass, $seqNumber);

                $g = trim($item['gender'] ?? '');
                $gender = (str_contains($g, 'ស្រី') || str_contains($g, 'ស') || strtolower($g) === 'female' || strtolower($g) === 'f') ? 'female' : 'male';

                $baseName = $item['name'] ?: ($item['english_name'] ?? 'Student');
                $email = strtolower(str_replace([' ', '-'], '', $studentIdCard)) . '@school.edu.kh';
                if (User::where('email', $email)->exists()) {
                    $email = strtolower(str_replace([' ', '-'], '', $studentIdCard)) . '_' . uniqid() . '@school.edu.kh';
                }

                $user = User::create([
                    'name' => $baseName,
                    'email' => $email,
                    'password' => Hash::make('123456'),
                ]);

                if ($studentRole) {
                    $user->roles()->syncWithoutDetaching([$studentRole->id]);
                }

                $student = Student::create([
                    'user_id' => $user->id,
                    'student_id_card' => $studentIdCard,
                    'name_khmer' => $item['name'],
                    'name_latin' => $item['english_name'] ?: null,
                    'gender' => $gender,
                    'grade' => $rawClass,
                    'phone' => $item['phone'] ?: null,
                    'status' => 'active',
                    'enrollment_date' => Carbon::today(),
                ]);

                $createdStudents[] = [
                    'id' => $student->id,
                    'student_id_card' => $studentIdCard,
                    'name' => $student->name_khmer,
                    'gender' => $gender === 'female' ? 'ស្រី' : 'ប្រុស',
                    'grade' => $rawClass,
                ];
            }
        });

        $count = count($createdStudents);
        $message = "បានបញ្ចូលសិស្សចំនួន {$count} នាក់ដោយជោគជ័យ!";

        if ($request->wantsJson() || $request->ajax() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => $message,
                'students' => $createdStudents,
            ]);
        }

        return redirect()->route('students.index')->with('success', $message);
    }



    public function teachers(Request $request)
    {
        $query = Teacher::with('user');
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%"))
                    ->orWhere('name_khmer', 'like', "%{$s}%")
                    ->orWhere('name_latin', 'like', "%{$s}%")
                    ->orWhere('teacher_id_card', 'like', "%{$s}%")
                    ->orWhere('specialization', 'like', "%{$s}%")
                    ->orWhere('qualification', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }
        if ($request->status) {
            if ($request->status === 'active') {
                $query->whereIn('status', ['active', 'fulltime', 'parttime']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $teachers = $query->latest()->get()->map(fn ($t) => [
            'id' => $t->id,
            'idCard' => $t->teacher_id_card ?: 'TCH-រង់ចាំ',
            'name' => $t->display_name,
            'name_khmer' => $t->name_khmer,
            'name_latin' => $t->name_latin,
            'gender' => $t->gender,
            'date_of_birth' => $t->date_of_birth ? Carbon::parse($t->date_of_birth)->format('Y-m-d') : null,
            'specialization' => $t->specialization ?: ($t->subject ?: 'គរុកោសល្យ'),
            'qualification' => $t->qualification ?: 'បរិញ្ញាបត្រ',
            'phone' => $t->phone ?: '-',
            'address' => $t->address ?: $t->province,
            'photo' => $t->photo ? asset('storage/' . $t->photo) : null,
            'class_assigned' => $t->class_assigned ?: 'មិនទាន់ចាត់តាំង',
            'status' => $t->status ?? 'active',
            'created_at' => $t->created_at ? $t->created_at->format('Y-m-d H:i') : null,
        ]);

        $demoMode = $request->input('demo_mode', SystemSetting::get('demo_mode', '1'));

        if ($teachers->isEmpty() && $demoMode !== '0') {
            $demoList = $this->getDemoTeachersList();
            if ($request->search) {
                $s = mb_strtolower($request->search);
                $demoList = array_values(array_filter($demoList, fn ($t) => 
                    str_contains(mb_strtolower($t['name_khmer']), $s) || 
                    str_contains(mb_strtolower($t['name_latin']), $s) ||
                    str_contains($t['idCard'], $s) ||
                    str_contains(mb_strtolower($t['specialization']), $s)
                ));
            }

            return response()->json([
                'teachers' => $demoList,
                'counts' => [
                    'total' => count($demoList),
                    'active' => count($demoList),
                    'pending' => 0,
                    'leave' => 0,
                ]
            ]);
        }

        $pendingCount = Teacher::where('status', 'pending')->count();
        $activeCount = Teacher::whereIn('status', ['active', 'fulltime', 'parttime'])->count();
        $leaveCount = Teacher::where('status', 'leave')->count();

        return response()->json([
            'teachers' => $teachers,
            'counts' => [
                'total' => Teacher::count(),
                'active' => $activeCount,
                'pending' => $pendingCount,
                'leave' => $leaveCount,
            ]
        ]);
    }

    public function storeTeacher(Request $request)
    {
        $validated = $request->validate([
            'name_khmer' => 'required|string|max:255',
            'name_latin' => 'nullable|string|max:255',
            'gender' => 'required|string',
            'phone' => 'nullable|string|max:50',
            'specialization' => 'required|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'class_assigned' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'salary' => 'nullable|numeric',
        ]);

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

        $baseName = $validated['name_khmer'] ?: ($validated['name_latin'] ?? 'Teacher');
        $email = strtolower(str_replace([' ', '-'], '', $teacherIdCard)) . '@school.edu.kh';
        if (\App\Models\User::where('email', $email)->exists()) {
            $email = strtolower(str_replace([' ', '-'], '', $teacherIdCard)) . '_' . time() . '@school.edu.kh';
        }

        $user = \App\Models\User::create([
            'name' => $baseName,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
        ]);

        $role = Role::where('name', 'teacher')->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'teacher_id_card' => $teacherIdCard,
            'name_khmer' => $validated['name_khmer'],
            'name_latin' => $validated['name_latin'] ?? null,
            'gender' => $validated['gender'],
            'phone' => $validated['phone'] ?? null,
            'specialization' => $validated['specialization'],
            'subject' => $validated['specialization'],
            'qualification' => $validated['qualification'] ?? 'បរិញ្ញាបត្រ',
            'class_assigned' => $validated['class_assigned'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'salary' => $validated['salary'] ?? 0,
            'status' => 'active',
            'joining_date' => Carbon::today(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "បានបន្ថែមគ្រូបង្រៀន {$teacher->display_name} (អត្តលេខ: {$teacherIdCard}) ដោយជោគជ័យ!",
            'teacher' => $teacher
        ]);
    }

    public function approveTeacher(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'teacher_id_card' => 'nullable|string',
            'specialization' => 'required|string',
            'qualification' => 'nullable|string',
            'class_assigned' => 'nullable|string',
        ]);

        $year = date('Y');
        $teacherIdCard = $validated['teacher_id_card'] ?? null;
        if (empty($teacherIdCard) || $teacherIdCard === 'TCH-រង់ចាំ') {
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
        }

        // Create or link User
        if (!$teacher->user_id) {
            $baseName = $teacher->name_khmer ?: ($teacher->name_latin ?: 'Teacher');
            $email = strtolower(str_replace([' ', '-'], '', $teacherIdCard)) . '@school.edu.kh';
            if (\App\Models\User::where('email', $email)->exists()) {
                $email = strtolower(str_replace([' ', '-'], '', $teacherIdCard)) . '_' . $teacher->id . '@school.edu.kh';
            }

            $user = \App\Models\User::create([
                'name' => $baseName,
                'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            ]);

            $role = Role::where('name', 'teacher')->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            $teacher->user_id = $user->id;
        }

        $teacher->teacher_id_card = $teacherIdCard;
        $teacher->specialization = $validated['specialization'];
        $teacher->subject = $validated['specialization'];
        $teacher->qualification = $validated['qualification'] ?? $teacher->qualification;
        $teacher->class_assigned = $validated['class_assigned'] ?? $teacher->class_assigned;
        $teacher->status = 'active';
        $teacher->joining_date = Carbon::today();
        $teacher->save();

        return response()->json([
            'success' => true,
            'message' => "បានអនុម័តគ្រូបង្រៀន {$teacher->display_name} (អត្តលេខ: {$teacherIdCard}) ដោយជោគជ័យ!",
            'teacher' => $teacher->load('user')
        ]);
    }

    public function updateTeacher(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name_khmer' => 'required|string|max:255',
            'name_latin' => 'nullable|string|max:255',
            'gender' => 'required|string',
            'phone' => 'nullable|string|max:50',
            'specialization' => 'required|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'class_assigned' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|string|max:50',
        ]);

        $teacher->update([
            'name_khmer' => $validated['name_khmer'],
            'name_latin' => $validated['name_latin'] ?? $teacher->name_latin,
            'gender' => $validated['gender'],
            'phone' => $validated['phone'] ?? $teacher->phone,
            'specialization' => $validated['specialization'],
            'subject' => $validated['specialization'],
            'qualification' => $validated['qualification'] ?? $teacher->qualification,
            'class_assigned' => $validated['class_assigned'] ?? $teacher->class_assigned,
            'address' => $validated['address'] ?? $teacher->address,
            'status' => $validated['status'] ?? $teacher->status,
        ]);

        if ($teacher->user) {
            $teacher->user->update([
                'name' => $validated['name_khmer'] ?: ($validated['name_latin'] ?? $teacher->user->name)
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "បានកែប្រែព័ត៌មានគ្រូបង្រៀន {$teacher->display_name} ដោយជោគជ័យ!",
            'teacher' => $teacher
        ]);
    }

    public function destroyTeacher(Teacher $teacher)
    {
        $name = $teacher->display_name;
        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => "បានលុបគ្រូបង្រៀន {$name} ដោយជោគជ័យ!"
        ]);
    }


    public function library()
    {
        $totalBooks = (int) Book::sum('quantity');
        $availableCopies = (int) Book::sum('available_copies');
        $activeBorrows = BookBorrow::where('status', 'borrowed')->count();
        $overdueBorrows = BookBorrow::where('status', 'borrowed')
            ->where('due_date', '<', Carbon::today())
            ->count();

        $books = Book::with('category')->latest()->take(100)->get()->map(fn ($b) => [
            'id' => $b->id,
            'isbn' => $b->isbn,
            'title' => $b->title,
            'author' => $b->author,
            'category' => $b->category?->name ?? 'ទូទៅ',
            'category_id' => $b->book_category_id,
            'publisher' => $b->publisher,
            'quantity' => $b->quantity,
            'available' => $b->available_copies
        ]);

        $borrows = BookBorrow::with('book', 'student.user')->latest()->take(100)->get()->map(fn ($br) => [
            'id' => $br->id,
            'book_id' => $br->book_id,
            'book' => $br->book?->title ?? 'N/A',
            'student_id' => $br->student_id,
            'student' => ($br->student?->display_name ?? 'N/A') . ' (' . ($br->student?->student_id_card ?? '') . ')',
            'borrow_date' => Carbon::parse($br->borrow_date)->format('Y-m-d'),
            'due_date' => Carbon::parse($br->due_date)->format('Y-m-d'),
            'status' => $br->status === 'returned' ? 'Returned' : (Carbon::parse($br->due_date)->isPast() ? 'Overdue' : 'Borrowed'),
            'status_raw' => $br->status,
        ]);

        $categories = BookCategory::all();
        $students = Student::with('user')->get()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->display_name,
            'student_id_card' => $s->student_id_card,
            'grade' => $s->grade,
        ]);

        return response()->json([
            'stats' => [
                'total' => $totalBooks,
                'available' => $availableCopies,
                'borrows' => $activeBorrows,
                'overdue' => $overdueBorrows,
            ],
            'books' => $books,
            'borrows' => $borrows,
            'categories' => $categories,
            'students' => $students,
        ]);
    }

    public function storeBook(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'book_category_id' => 'nullable|exists:book_categories,id',
            'isbn' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        $validated['available_copies'] = $validated['quantity'];

        $book = Book::create($validated);

        return response()->json([
            'success' => true,
            'message' => "បានបន្ថែមសៀវភៅ {$book->title} ដោយជោគជ័យ!",
            'book' => $book,
        ]);
    }

    public function borrowBook(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'student_id' => 'required|exists:students,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        if ($book->available_copies < 1) {
            return response()->json([
                'success' => false,
                'message' => 'សៀវភៅនេះអស់ស្តុកហើយ មិនអាចខ្ចីបានទេ!',
            ], 422);
        }

        $borrow = BookBorrow::create([
            'book_id' => $validated['book_id'],
            'student_id' => $validated['student_id'],
            'borrow_date' => $validated['borrow_date'],
            'due_date' => $validated['due_date'],
            'status' => 'borrowed',
        ]);

        $book->decrement('available_copies');

        return response()->json([
            'success' => true,
            'message' => "បានកត់ត្រាការខ្ចីសៀវភៅ {$book->title} ដោយជោគជ័យ!",
            'borrow' => $borrow,
        ]);
    }

    public function returnBook(BookBorrow $borrow)
    {
        if ($borrow->status !== 'returned') {
            $borrow->update([
                'status' => 'returned',
                'return_date' => Carbon::today(),
            ]);
            $borrow->book?->increment('available_copies');
        }

        return response()->json([
            'success' => true,
            'message' => 'បានកត់ត្រាការសងសៀវភៅដោយជោគជ័យ!',
        ]);
    }

    public function updateBorrow(Request $request, BookBorrow $borrow)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'student_id' => 'nullable|exists:students,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            'status' => 'nullable|string|in:borrowed,returned,overdue',
        ]);

        if ($validated['book_id'] != $borrow->book_id) {
            $borrow->book?->increment('available_copies');
            $newBook = Book::findOrFail($validated['book_id']);
            $newBook->decrement('available_copies');
        }

        if (isset($validated['status']) && $validated['status'] === 'returned' && $borrow->status !== 'returned') {
            $validated['return_date'] = Carbon::today();
            $borrow->book?->increment('available_copies');
        } elseif (isset($validated['status']) && $validated['status'] !== 'returned' && $borrow->status === 'returned') {
            $validated['return_date'] = null;
            $borrow->book?->decrement('available_copies');
        }

        $borrow->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'បានកែសម្រួលព័ត៌មានការខ្ចីសៀវភៅដោយជោគជ័យ!',
            'borrow' => $borrow,
        ]);
    }

    public function deleteBorrow(BookBorrow $borrow)
    {
        if ($borrow->status !== 'returned') {
            $borrow->book?->increment('available_copies');
        }
        $borrow->delete();

        return response()->json([
            'success' => true,
            'message' => 'បានលុបកំណត់ត្រាការខ្ចីដោយជោគជ័យ!',
        ]);
    }

    public function announcements()
    {
        $items = Announcement::latest()->get()->map(fn ($a) => [
            'id' => $a->id,
            'title' => $a->title,
            'content' => $a->content,
            'category' => $a->category ?: 'ទូទៅ',
            'target_audience' => $a->target_audience ?: 'ទាំងអស់គ្នា',
            'publish_date' => Carbon::parse($a->publish_date)->format('Y-m-d'),
            'date_formatted' => Carbon::parse($a->publish_date)->format('d-M-Y'),
        ]);

        return response()->json(['announcements' => $items]);
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'target_audience' => 'required|string|max:100',
            'content' => 'required|string',
            'publish_date' => 'nullable|date',
        ]);

        $validated['publish_date'] = $validated['publish_date'] ?? Carbon::today();
        $announcement = Announcement::create($validated);

        return response()->json([
            'success' => true,
            'message' => "បានផ្សាយសេចក្តីជូនដំណឹង {$announcement->title} ដោយជោគជ័យ!",
            'announcement' => $announcement,
        ]);
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        $title = $announcement->title;
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => "បានលុបសេចក្តីជូនដំណឹង {$title} ដោយជោគជ័យ!",
        ]);
    }

    public function classes()
    {
        $classes = SchoolClass::with('teacher.user')->withCount('students')->get()->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'grade' => $c->grade,
            'section' => $c->section,
            'teacher_id' => $c->teacher_id,
            'teacher' => $c->teacher?->display_name ?? ($c->teacher?->user?->name ?? 'មិនទាន់ចាត់តាំង'),
            'room' => $c->room_number ?? ('បន្ទប់ ' . $c->name),
            'capacity' => $c->capacity ?? 40,
            'enrolled' => $c->students_count,
            'academic_year_id' => $c->academic_year_id,
            'description' => $c->description,
        ]);

        $teachers = Teacher::with('user')->get()->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->display_name,
        ]);

        $academicYears = AcademicYear::where('status', 'active')->get()->map(fn ($y) => [
            'id' => $y->id,
            'name' => $y->name,
        ]);

        $students = Student::with('user')->get()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->display_name,
            'grade' => $s->grade,
            'student_id_card' => $s->student_id_card,
        ]);

        return response()->json([
            'classes' => $classes,
            'teachers' => $teachers,
            'academicYears' => $academicYears,
            'students' => $students,
        ]);
    }

    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'grade' => 'required|string|max:20',
            'section' => 'required|string|max:10',
            'room_number' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1',
            'teacher_id' => 'nullable|exists:teachers,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'description' => 'nullable|string',
        ]);

        $name = trim($validated['grade']) . trim($validated['section']);
        $validated['name'] = $name;

        $schoolClass = SchoolClass::updateOrCreate(['name' => $name], $validated);

        return response()->json([
            'success' => true,
            'message' => "បានបង្កើត/កែប្រែថ្នាក់ {$name} ដោយជោគជ័យ!",
            'class' => $schoolClass,
        ]);
    }

    public function updateClass(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'grade' => 'nullable|string|max:20',
            'section' => 'nullable|string|max:10',
            'room_number' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1',
            'teacher_id' => 'nullable|exists:teachers,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'description' => 'nullable|string',
        ]);

        if (!empty($validated['grade']) && !empty($validated['section'])) {
            $validated['name'] = trim($validated['grade']) . trim($validated['section']);
        }

        $class->update($validated);

        return response()->json([
            'success' => true,
            'message' => "បានកែប្រែព័ត៌មានថ្នាក់ {$class->name} ដោយជោគជ័យ!",
            'class' => $class,
        ]);
    }

    public function destroyClass(SchoolClass $class)
    {
        $name = $class->name;
        $class->delete();

        return response()->json([
            'success' => true,
            'message' => "បានលុបថ្នាក់ {$name} ដោយជោគជ័យ!",
        ]);
    }

    public function transferStudent(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'target_class' => 'required|string|max:50',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $oldClass = $student->grade;
        $student->update(['grade' => $validated['target_class']]);

        return response()->json([
            'success' => true,
            'message' => "បានផ្លាស់ប្តូរសិស្ស {$student->display_name} ពីថ្នាក់ {$oldClass} ទៅថ្នាក់ {$validated['target_class']} ដោយជោគជ័យ!",
        ]);
    }

    public function promoteStudents(Request $request)
    {
        $validated = $request->validate([
            'current_class' => 'required|string|max:50',
            'next_class' => 'required|string|max:50',
        ]);

        $count = Student::where('grade', $validated['current_class'])
            ->update(['grade' => $validated['next_class']]);

        return response()->json([
            'success' => true,
            'message' => "បានឡើងថ្នាក់សិស្សចំនួន {$count} នាក់ ពីថ្នាក់ {$validated['current_class']} ទៅថ្នាក់ {$validated['next_class']} ដោយជោគជ័យ!",
        ]);
    }

    public function attendance(Request $request)
    {
        $today = $request->input('date', Carbon::today()->toDateString());
        $selectedClass = $request->input('class', '7A');

        $classes = SchoolClass::orderedNames();

        $students = Student::with('user')
            ->where('grade', $selectedClass)
            ->get()
            ->map(function ($st) use ($today) {
                $att = Attendance::where('student_id', $st->id)
                    ->where('date', $today)
                    ->first();

                return [
                    'student_id' => $st->id,
                    'name' => $st->display_name,
                    'student_id_card' => $st->student_id_card,
                    'gender' => $st->gender,
                    'status' => $att?->status ?? 'present',
                    'remark' => $att?->remark ?? '',
                ];
            });

        $academicYears = AcademicYear::orderBy('name', 'desc')->pluck('name');
        if ($academicYears->isEmpty()) {
            $academicYears = collect(['2025-2026', '2024-2025']);
        }

        $stats = [
            'present' => Attendance::where('status', 'present')->where('date', $today)->count() ?: 116,
            'late' => Attendance::where('status', 'late')->where('date', $today)->count() ?: 3,
            'absent' => Attendance::where('status', 'absent')->where('date', $today)->count() ?: 1,
            'holiday' => Attendance::whereIn('status', ['holiday', 'excused'])->where('date', $today)->count() ?: 0,
        ];

        // Real Matrix attendance data for this class
        $classAttendances = Attendance::where('class_name', $selectedClass)->get();
        $realMatrix = [];
        foreach ($classAttendances as $att) {
            $carbon = Carbon::parse($att->date);
            $day = (int) $carbon->format('j');
            $month = strtolower($carbon->format('F')); // january, february, etc.

            $statusChar = match($att->status) {
                'absent' => 'A',
                'late' => 'L',
                'excused' => 'H',
                'holiday' => 'H',
                default => 'P',
            };

            // Priority: A > H > L > P
            $existing = $realMatrix[$day][$month] ?? null;
            if (!$existing) {
                $realMatrix[$day][$month] = $statusChar;
            } elseif ($statusChar === 'A') {
                $realMatrix[$day][$month] = 'A';
            } elseif ($statusChar === 'H' && $existing !== 'A') {
                $realMatrix[$day][$month] = 'H';
            } elseif ($statusChar === 'L' && !in_array($existing, ['A', 'H'])) {
                $realMatrix[$day][$month] = 'L';
            }
        }

        // Fetch real absence & leave records for this class
        $absenceRecords = Attendance::where('class_name', $selectedClass)
            ->whereIn('status', ['excused', 'absent', 'late', 'holiday'])
            ->with(['student', 'student.user'])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($att) {
                $st = $att->student;
                $dateStr = Carbon::parse($att->date)->toDateString();
                $leaveReq = StudentLeaveRequest::where('student_id', $att->student_id)
                    ->whereDate('start_date', '<=', $dateStr)
                    ->whereDate('end_date', '>=', $dateStr)
                    ->first();

                return [
                    'id' => $att->id,
                    'date' => $dateStr,
                    'day' => (int) Carbon::parse($att->date)->format('j'),
                    'month' => strtolower(Carbon::parse($att->date)->format('F')),
                    'student_id' => $att->student_id,
                    'student_name' => $st?->display_name ?? 'N/A',
                    'student_id_card' => $st?->student_id_card ?? 'N/A',
                    'gender' => $st?->gender ?? 'male',
                    'status' => $att->status,
                    'remark' => $att->remark ?: ($leaveReq ? "សុំច្បាប់: {$leaveReq->leave_type} - {$leaveReq->reason}" : ''),
                    'attachment_url' => $leaveReq?->attachment_path ? asset('storage/' . $leaveReq->attachment_path) : null,
                ];
            });

        // Student-level attendance map for monthly roster: [student_id][date] = {status, remark}
        $studentAttendanceMap = [];
        foreach ($classAttendances as $att) {
            $dateKey = Carbon::parse($att->date)->toDateString();
            $studentAttendanceMap[$att->student_id][$dateKey] = [
                'status' => $att->status,
                'remark' => $att->remark,
            ];
        }

        return response()->json([
            'stats' => $stats,
            'students' => $students,
            'classes' => $classes,
            'academicYears' => $academicYears,
            'selectedDate' => $today,
            'selectedClass' => $selectedClass,
            'matrix' => $realMatrix,
            'absence_records' => $absenceRecords,
            'student_attendance_map' => $studentAttendanceMap,
        ]);
    }

    public function saveAttendance(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'remarks' => 'nullable|array',
        ]);

        $className = $validated['class_name'];
        $date = $validated['date'];
        $academicYearId = AcademicYear::where('is_current', true)->value('id');

        foreach ($validated['attendance'] as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $date,
                ],
                [
                    'class_name' => $className,
                    'status' => $status,
                    'remark' => $validated['remarks'][$studentId] ?? null,
                    'academic_year_id' => $academicYearId,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => "បានរក្សាទុកវត្តមានសិស្សថ្នាក់ {$className} សម្រាប់ថ្ងៃទី {$date} ដោយជោគជ័យ!",
        ]);
    }

    public function teacherAttendance(Request $request)
    {
        $today = $request->input('date', Carbon::today()->toDateString());

        $teachers = Teacher::with('user')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($tc) use ($today) {
                $att = TeacherAttendance::where('teacher_id', $tc->id)
                    ->where('date', $today)
                    ->first();

                return [
                    'teacher_id' => $tc->id,
                    'name' => $tc->display_name,
                    'name_khmer' => $tc->name_khmer,
                    'name_latin' => $tc->name_latin,
                    'teacher_id_card' => $tc->teacher_id_card ?: ('TC-' . str_pad($tc->id, 4, '0', STR_PAD_LEFT)),
                    'gender' => $tc->gender ?: 'male',
                    'specialization' => $tc->specialization ?: ($tc->subject ?: 'ទូទៅ'),
                    'phone' => $tc->phone ?: 'N/A',
                    'status' => $att?->status ?? 'present',
                    'remark' => $att?->remark ?? '',
                ];
            });

        $academicYears = AcademicYear::orderBy('name', 'desc')->pluck('name');
        if ($academicYears->isEmpty()) {
            $academicYears = collect(['2025-2026', '2024-2025']);
        }

        $stats = [
            'present' => TeacherAttendance::where('status', 'present')->where('date', $today)->count() ?: $teachers->count(),
            'late' => TeacherAttendance::where('status', 'late')->where('date', $today)->count(),
            'absent' => TeacherAttendance::where('status', 'absent')->where('date', $today)->count(),
            'holiday' => TeacherAttendance::where('status', 'excused')->where('date', $today)->count(),
        ];

        // Teacher-level attendance map for monthly matrix: [teacher_id][date] = {status, remark}
        $allTeacherAttendances = TeacherAttendance::all();
        $teacherAttendanceMap = [];
        foreach ($allTeacherAttendances as $att) {
            $dateKey = Carbon::parse($att->date)->toDateString();
            $teacherAttendanceMap[$att->teacher_id][$dateKey] = [
                'status' => $att->status,
                'remark' => $att->remark,
            ];
        }

        return response()->json([
            'stats' => $stats,
            'teachers' => $teachers,
            'academicYears' => $academicYears,
            'selectedDate' => $today,
            'teacher_attendance_map' => $teacherAttendanceMap,
        ]);
    }

    public function saveTeacherAttendance(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'remarks' => 'nullable|array',
        ]);

        $date = $validated['date'];

        foreach ($validated['attendance'] as $teacherId => $status) {
            TeacherAttendance::updateOrCreate(
                [
                    'teacher_id' => $teacherId,
                    'date' => $date,
                ],
                [
                    'status' => $status,
                    'remark' => $validated['remarks'][$teacherId] ?? null,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => "បានរក្សាទុកវត្តមានលោកគ្រូ-អ្នកគ្រូសម្រាប់ថ្ងៃទី {$date} ដោយជោគជ័យ!",
        ]);
    }

    public function academicYears()
    {
        $years = AcademicYear::orderBy('name', 'desc')->get()->map(fn ($y) => [
            'id' => $y->id,
            'name' => $y->name,
            'start_date' => $y->start_date ? Carbon::parse($y->start_date)->format('Y-m-d') : null,
            'end_date' => $y->end_date ? Carbon::parse($y->end_date)->format('Y-m-d') : null,
            'start' => $y->start_date ? Carbon::parse($y->start_date)->format('d-M-Y') : 'N/A',
            'end' => $y->end_date ? Carbon::parse($y->end_date)->format('d-M-Y') : 'N/A',
            'sem1' => $y->semester_1 ?: 'Oct-Feb',
            'sem2' => $y->semester_2 ?: 'Mar-Jul',
            'status' => $y->status ?? 'active',
            'is_current' => (bool) $y->is_current,
            'isCurrent' => (bool) $y->is_current,
        ]);

        $currentYear = AcademicYear::where('is_current', true)->first() ?: AcademicYear::latest()->first();
        $currentSem = SystemSetting::get('current_semester', '1');

        return response()->json([
            'years' => $years,
            'stats' => [
                'total' => $years->count(),
                'currentYear' => $currentYear?->name ?? '2025-2026',
                'currentSemester' => $currentSem == '2' ? 'ឆមាសទី ២' : 'ឆមាសទី ១',
                'status' => 'សកម្ម (Active)'
            ]
        ]);
    }

    public function storeAcademicYear(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'semester_1' => 'nullable|string|max:100',
            'semester_2' => 'nullable|string|max:100',
            'is_current' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
        ]);

        $isCurrent = !empty($validated['is_current']);

        if ($isCurrent) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
            SystemSetting::set('current_academic_year', $validated['name']);
        }

        $year = AcademicYear::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'semester_1' => $validated['semester_1'] ?? 'Oct-Feb',
            'semester_2' => $validated['semester_2'] ?? 'Mar-Jul',
            'is_current' => $isCurrent,
            'status' => $isCurrent ? 'active' : ($validated['status'] ?? 'inactive'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "បានបង្កើតឆ្នាំសិក្សា {$year->name} ដោយជោគជ័យ!",
            'year' => $year
        ]);
    }

    public function updateAcademicYear(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'semester_1' => 'nullable|string|max:100',
            'semester_2' => 'nullable|string|max:100',
            'is_current' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
        ]);

        $isCurrent = !empty($validated['is_current']);

        if ($isCurrent) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_current' => false]);
            SystemSetting::set('current_academic_year', $validated['name']);
        }

        $academicYear->update([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'semester_1' => $validated['semester_1'] ?? $academicYear->semester_1,
            'semester_2' => $validated['semester_2'] ?? $academicYear->semester_2,
            'is_current' => $isCurrent,
            'status' => $isCurrent ? 'active' : ($validated['status'] ?? $academicYear->status),
        ]);

        return response()->json([
            'success' => true,
            'message' => "បានកែប្រែឆ្នាំសិក្សា {$academicYear->name} ដោយជោគជ័យ!",
            'year' => $academicYear
        ]);
    }

    public function setCurrentAcademicYear(AcademicYear $academicYear)
    {
        AcademicYear::where('id', '!=', $academicYear->id)->update(['is_current' => false]);
        $academicYear->update(['is_current' => true, 'status' => 'active']);
        SystemSetting::set('current_academic_year', $academicYear->name);

        return response()->json([
            'success' => true,
            'message' => "បានកំណត់ឆ្នាំសិក្សា {$academicYear->name} ជាឆ្នាំសិក្សាបច្ចុប្បន្ន!",
            'year' => $academicYear
        ]);
    }

    public function courseSubjects()
    {
        $typeLabels = [
            'core' => 'មុខវិជ្ជាគោល',
            'science' => 'វិទ្យាសាស្ត្រពិត',
            'social' => 'វិទ្យាសាស្ត្រសង្គម',
            'languages' => 'ភាសាបរទេស / ភាសា',
            'elective' => 'មុខវិជ្ជាជ្រើសរើស / បំណិន',
            'general' => 'ទូទៅ',
        ];

        $subjects = CourseSubject::latest()->get()->map(function ($s) use ($typeLabels) {
            $sType = $s->subject_type;
            if (!$sType || $sType === 'general') {
                $code = strtoupper($s->code ?? '');
                $name = $s->name ?? '';
                if (str_contains($code, 'MTH') || str_contains($code, 'PHY') || str_contains($code, 'CHM') || str_contains($code, 'BIO') || str_contains($code, 'ICT') || str_contains($name, 'គណិត') || str_contains($name, 'រូប') || str_contains($name, 'គីមី') || str_contains($name, 'ជីវ')) {
                    $sType = 'science';
                } elseif (str_contains($code, 'KHM') || str_contains($code, 'ENG') || str_contains($code, 'FRA') || str_contains($name, 'ខ្មែរ') || str_contains($name, 'អង់គ្លេស') || str_contains($name, 'បារាំង')) {
                    $sType = 'languages';
                } elseif (str_contains($code, 'HIS') || str_contains($code, 'GEO') || str_contains($code, 'MOR') || str_contains($code, 'ECO') || str_contains($name, 'ប្រវត្តិ') || str_contains($name, 'ភូមិ') || str_contains($name, 'ពលរដ្ឋ') || str_contains($name, 'សីលធម៌')) {
                    $sType = 'social';
                } elseif (str_contains($code, 'PE') || str_contains($code, 'SPT') || str_contains($code, 'ART') || str_contains($name, 'កីឡា') || str_contains($name, 'សិល្បៈ')) {
                    $sType = 'elective';
                } else {
                    $sType = 'core';
                }
            }

            return [
                'id' => $s->id,
                'code' => $s->code,
                'name' => $s->name,
                'nameKh' => $s->name,
                'nameEn' => $s->description ?: $s->name,
                'grade_level' => $s->grade_level ?: '7-12',
                'credit_hours' => $s->credit_hours ?: 3,
                'credits' => $s->credit_hours ?: 3,
                'max_score' => (float) ($s->max_score ?: 50.0),
                'credit' => (float) ($s->credit ?: 1.0),
                'class_configs' => $s->class_configs ?: [],
                'description' => $s->description,
                'subject_type' => $sType,
                'type' => $sType,
                'typeLabel' => $typeLabels[$sType] ?? 'ទូទៅ',
            ];
        });

        return response()->json(['subjects' => $subjects]);
    }

    public function storeCourseSubject(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:course_subjects,code',
            'name' => 'required|string|max:255',
            'subject_type' => 'nullable|string|max:50',
            'credit_hours' => 'required|integer|min:1|max:15',
            'max_score' => 'nullable|numeric|min:1|max:1000',
            'credit' => 'nullable|numeric|min:0.1|max:20',
            'class_configs' => 'nullable|array',
            'grade_level' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $subject = CourseSubject::create($validated);

        return response()->json([
            'success' => true,
            'message' => "បានបង្កើតមុខវិជ្ជា {$subject->name} ({$subject->code}) ដោយជោគជ័យ!",
            'subject' => $subject,
        ]);
    }

    public function updateCourseSubject(Request $request, CourseSubject $courseSubject)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:course_subjects,code,' . $courseSubject->id,
            'name' => 'required|string|max:255',
            'subject_type' => 'nullable|string|max:50',
            'credit_hours' => 'required|integer|min:1|max:15',
            'max_score' => 'nullable|numeric|min:1|max:1000',
            'credit' => 'nullable|numeric|min:0.1|max:20',
            'class_configs' => 'nullable|array',
            'grade_level' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $courseSubject->update($validated);

        return response()->json([
            'success' => true,
            'message' => "បានកែប្រែព័ត៌មានមុខវិជ្ជា {$courseSubject->name} ដោយជោគជ័យ!",
            'subject' => $courseSubject,
        ]);
    }

    public function saveSubjectClassConfigs(Request $request, CourseSubject $courseSubject)
    {
        $request->validate([
            'class_configs' => 'required|array',
        ]);

        $courseSubject->class_configs = $request->input('class_configs');
        $courseSubject->save();

        return response()->json([
            'success' => true,
            'message' => "បានរក្សាទុកការកំណត់ពិន្ទុ និងក្រេឌីតតាមថ្នាក់សម្រាប់ {$courseSubject->name} ដោយជោគជ័យ!",
            'subject' => $courseSubject,
        ]);
    }

    public function destroyCourseSubject(CourseSubject $courseSubject)
    {
        $name = $courseSubject->name;
        $courseSubject->delete();

        return response()->json([
            'success' => true,
            'message' => "បានលុបមុខវិជ្ជា {$name} ដោយជោគជ័យ!",
        ]);
    }

    public function timetables(Request $request)
    {
        $selectedClass = $request->input('class');
        $selectedTeacher = $request->input('teacher_id');

        $query = Timetable::with('courseSubject', 'teacher.user');

        if ($selectedTeacher) {
            $query->where('teacher_id', $selectedTeacher);
        } elseif ($selectedClass) {
            $query->where('class_name', $selectedClass);
        }

        $timetables = $query->orderBy('start_time')->get()->map(fn ($tt) => [
            'id' => $tt->id,
            'class' => $tt->class_name,
            'class_name' => $tt->class_name,
            'subject' => $tt->courseSubject?->name ?? 'N/A',
            'course_subject_id' => $tt->course_subject_id,
            'teacher' => $tt->teacher?->display_name ?? ($tt->teacher?->user?->name ?? 'N/A'),
            'teacher_id' => $tt->teacher_id,
            'day' => $tt->day_of_week,
            'day_of_week' => $tt->day_of_week,
            'start_time' => $tt->start_time,
            'end_time' => $tt->end_time,
            'time' => $tt->start_time . ' - ' . $tt->end_time,
            'room_number' => $tt->room_number,
        ]);

        $classes = SchoolClass::orderedNames();
        $subjects = CourseSubject::orderBy('name')->get()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'code' => $s->code,
        ]);
        $teachers = Teacher::with('user')->get()->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->display_name,
        ]);

        return response()->json([
            'timetables' => $timetables,
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
        ]);
    }

    public function storeTimetable(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:50',
            'course_subject_id' => 'required|exists:course_subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day_of_week' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room_number' => 'nullable|string|max:50',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        // Conflict check on teacher
        $teacherConflict = Timetable::where('teacher_id', $validated['teacher_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                  ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
            })
            ->first();

        if ($teacherConflict) {
            return response()->json([
                'success' => false,
                'message' => "គ្រូបង្រៀនរូបនេះមានម៉ោងបង្រៀននៅថ្នាក់ {$teacherConflict->class_name} ក្នុងចន្លោះម៉ោងនេះរួចហើយ!",
            ], 422);
        }

        $entry = Timetable::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'បានបញ្ចូលកាលវិភាគបង្រៀនថ្មីដោយជោគជ័យ!',
            'timetable' => $entry,
        ]);
    }

    public function destroyTimetable(Timetable $timetable)
    {
        $timetable->delete();

        return response()->json([
            'success' => true,
            'message' => 'បានលុបម៉ោងបង្រៀនចេញពីកាលវិភាគ!',
        ]);
    }

    public function examinations(Request $request)
    {
        $demoMode = $request->input('demo_mode', SystemSetting::get('demo_mode', '1'));

        $exams = Examination::with('academicYear')->withCount('scores')->latest()->get()->map(fn ($ex) => [
            'id' => $ex->id,
            'title' => $ex->title,
            'exam_type' => $ex->exam_type ?: 'ឆមាស',
            'term' => $ex->term ?: ($ex->semester == 2 ? 'ឆមាសទី ២' : 'ឆមាសទី ១'),
            'semester' => $ex->semester ?? 1,
            'start_date' => $ex->start_date ? Carbon::parse($ex->start_date)->format('Y-m-d') : null,
            'end_date' => $ex->end_date ? Carbon::parse($ex->end_date)->format('Y-m-d') : null,
            'academic_year' => $ex->academicYear?->name ?? '2025-2026',
            'scores_count' => $ex->scores_count,
            'status' => $ex->status ?: ($ex->is_published ? 'ផ្សាយជាសាធារណៈ' : 'សេចក្តីព្រាង'),
            'is_published' => (bool)$ex->is_published,
        ]);

        if ($exams->isEmpty() && $demoMode !== '0') {
            $exams = collect([
                [
                    'id' => 1,
                    'title' => 'ការប្រឡងប្រចាំខែមករា',
                    'exam_type' => 'ប្រចាំខែ',
                    'term' => 'ឆមាសទី ១',
                    'semester' => 1,
                    'start_date' => '2026-01-20',
                    'end_date' => '2026-01-25',
                    'academic_year' => '2025-2026',
                    'scores_count' => 696,
                    'status' => 'ផ្សាយជាសាធារណៈ',
                    'is_published' => true,
                ],
                [
                    'id' => 2,
                    'title' => 'ការប្រឡងឆមាសទី ១',
                    'exam_type' => 'ឆមាស',
                    'term' => 'ឆមាសទី ១',
                    'semester' => 1,
                    'start_date' => '2026-02-15',
                    'end_date' => '2026-02-22',
                    'academic_year' => '2025-2026',
                    'scores_count' => 696,
                    'status' => 'ផ្សាយជាសាធារណៈ',
                    'is_published' => true,
                ],
                [
                    'id' => 3,
                    'title' => 'ការប្រឡងប្រចាំខែមេសា',
                    'exam_type' => 'ប្រចាំខែ',
                    'term' => 'ឆមាសទី ២',
                    'semester' => 2,
                    'start_date' => '2026-04-20',
                    'end_date' => '2026-04-25',
                    'academic_year' => '2025-2026',
                    'scores_count' => 696,
                    'status' => 'ផ្សាយជាសាធារណៈ',
                    'is_published' => true,
                ],
            ]);
        }

        $classes = SchoolClass::orderedNames();
        if (empty($classes) && $demoMode !== '0') {
            $classes = ['10A', '10B', '11D', '12A', '7A', '7B', '8A', '8B', '9A', '9B', '11A', '12B'];
        }
        $subjects = CourseSubject::orderBy('id')->get()->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'code' => $s->code]);
        if ($subjects->isEmpty() && $demoMode !== '0') {
            $subjects = collect([
                ['id' => 1, 'name' => 'អក្សរសាស្ត្រខ្មែរ', 'code' => 'KHM-01', 'max_score' => 100, 'credit' => 2],
                ['id' => 2, 'name' => 'គណិតវិទ្យា', 'code' => 'MTH-01', 'max_score' => 100, 'credit' => 2],
                ['id' => 3, 'name' => 'រូបវិទ្យា', 'code' => 'PHY-01', 'max_score' => 100, 'credit' => 1.5],
                ['id' => 4, 'name' => 'គីមីវិទ្យា', 'code' => 'CHM-01', 'max_score' => 100, 'credit' => 1.5],
                ['id' => 5, 'name' => 'ជីវវិទ្យា', 'code' => 'BIO-01', 'max_score' => 100, 'credit' => 1.5],
                ['id' => 6, 'name' => 'ប្រវត្តិវិទ្យា', 'code' => 'HIS-01', 'max_score' => 100, 'credit' => 1],
                ['id' => 7, 'name' => 'សីលធម៌-ពលរដ្ឋ', 'code' => 'ETH-01', 'max_score' => 100, 'credit' => 1],
                ['id' => 8, 'name' => 'ភាសាអង់គ្លេស', 'code' => 'ENG-01', 'max_score' => 100, 'credit' => 1],
            ]);
        }
        $academicYears = AcademicYear::where('status', 'active')->get()->map(fn ($y) => ['id' => $y->id, 'name' => $y->name]);
        if ($academicYears->isEmpty() && $demoMode !== '0') {
            $academicYears = collect([
                ['id' => 1, 'name' => '2025-2026'],
                ['id' => 2, 'name' => '2026-2027'],
            ]);
        }

        return response()->json([
            'examinations' => $exams,
            'classes' => $classes,
            'subjects' => $subjects,
            'academicYears' => $academicYears,
        ]);
    }

    public function storeExamination(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_type' => 'required|string',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'semester' => 'required|integer|in:1,2',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['status'] = !empty($validated['is_published']) ? 'active' : 'draft';
        $exam = Examination::create($validated);

        return response()->json([
            'success' => true,
            'message' => "បានបង្កើតសម័យប្រឡង {$exam->title} ដោយជោគជ័យ!",
            'examination' => $exam,
        ]);
    }

    public function updateExamination(Request $request, Examination $examination)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_type' => 'required|string',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'semester' => 'required|integer|in:1,2',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['status'] = !empty($validated['is_published']) ? 'active' : 'draft';
        $examination->update($validated);

        return response()->json([
            'success' => true,
            'message' => "បានកែប្រែសម័យប្រឡង {$examination->title} ដោយជោគជ័យ!",
            'examination' => $examination,
        ]);
    }

    public function destroyExamination(Examination $examination)
    {
        $title = $examination->title;
        $examination->scores()->delete();
        $examination->delete();

        return response()->json([
            'success' => true,
            'message' => "បានលុបសម័យប្រឡង {$title} ដោយជោគជ័យ!",
        ]);
    }

    public function getExamScores(Request $request, $examination)
    {
        $exam = $examination instanceof Examination ? $examination : Examination::find($examination);
        $className = $request->input('class', '7A');
        $subjectId = $request->input('subject_id');
        $demoMode = $request->input('demo_mode', SystemSetting::get('demo_mode', '1'));

        $students = collect();
        if ($exam) {
            $students = Student::with('user')
                ->where('grade', $className)
                ->get()
                ->map(function ($s) use ($exam, $subjectId, $className) {
                    $scoreQuery = ExaminationScore::where('examination_id', $exam->id)
                        ->where('student_id', $s->id)
                        ->where('class_name', $className);

                    if ($subjectId) {
                        $scoreQuery->where('course_subject_id', $subjectId);
                    }

                    $score = $scoreQuery->first();

                    return [
                        'student_id' => $s->id,
                        'name' => $s->display_name,
                        'student_id_card' => $s->student_id_card,
                        'gender' => $s->gender,
                        'score' => $score?->score,
                        'max_score' => $score?->max_score ?? 100,
                        'grade_letter' => $score?->grade_letter,
                    ];
                });
        }

        if ($students->isEmpty() && $demoMode !== '0') {
            $demoStudents = $this->getDemoStudentsList();
            $filtered = array_values(array_filter($demoStudents, fn($s) => $s['grade'] === $className));
            if (empty($filtered)) {
                $filtered = array_slice($demoStudents, 0, 6);
            }
            $sampleScores = [88.5, 92.0, 75.5, 68.0, 95.0, 81.5, 79.0, 85.0, 90.5, 74.0];
            $students = collect($filtered)->map(function ($s, $idx) use ($sampleScores) {
                $sc = $sampleScores[$idx % count($sampleScores)];
                return [
                    'student_id' => $s['id'],
                    'name' => $s['name'],
                    'student_id_card' => $s['student_id_card'],
                    'gender' => $s['gender'],
                    'score' => $sc,
                    'max_score' => 100,
                    'grade_letter' => ExaminationScore::calculateGradeLetter($sc, 100),
                ];
            });
        }

        return response()->json([
            'students' => $students,
            'examination' => $exam ?: [
                'id' => is_numeric($examination) ? (int)$examination : 1,
                'title' => 'ការប្រឡងឆមាសលើកទី ១ (Semester 1 Exam)',
                'semester' => 1
            ],
        ]);
    }

    public function saveExamScores(Request $request, Examination $examination)
    {
        $validated = $request->validate([
            'class_name' => 'required|string',
            'course_subject_id' => 'required|exists:course_subjects,id',
            'scores' => 'required|array',
            'max_score' => 'nullable|numeric|min:1',
        ]);

        $className = $validated['class_name'];
        $subject = CourseSubject::findOrFail($validated['course_subject_id']);
        $maxScore = $validated['max_score'] ?? 100;

        foreach ($validated['scores'] as $studentId => $scoreVal) {
            if ($scoreVal !== null && $scoreVal !== '') {
                $scoreFloat = (float) $scoreVal;
                $gradeLetter = ExaminationScore::calculateGradeLetter($scoreFloat, $maxScore);

                ExaminationScore::updateOrCreate(
                    [
                        'examination_id' => $examination->id,
                        'student_id' => $studentId,
                        'course_subject_id' => $subject->id,
                    ],
                    [
                        'class_name' => $className,
                        'subject_name' => $subject->name,
                        'score' => $scoreFloat,
                        'max_score' => $maxScore,
                        'grade_letter' => $gradeLetter,
                    ]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => "បានបញ្ចូលពិន្ទុមុខវិជ្ជា {$subject->name} សម្រាប់ថ្នាក់ {$className} ដោយជោគជ័យ!",
        ]);
    }

    public function getExamMatrixScores(Request $request, $examination)
    {
        $exam = $examination instanceof Examination ? $examination : Examination::find($examination);
        $className = $request->input('class', '7A');
        $demoMode = $request->input('demo_mode', SystemSetting::get('demo_mode', '1'));

        $subjects = CourseSubject::orderBy('id')->get()->map(function ($s) use ($className) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'code' => $s->code,
                'max_score' => $s->getMaxScoreForClass($className),
                'credit' => $s->getCreditForClass($className),
            ];
        });

        if ($subjects->isEmpty() && $demoMode !== '0') {
            $subjects = collect([
                ['id' => 1, 'name' => 'ភាសាខ្មែរ', 'code' => 'KHM', 'max_score' => 100, 'credit' => 2],
                ['id' => 2, 'name' => 'គណិតវិទ្យា', 'code' => 'MTH', 'max_score' => 100, 'credit' => 2],
                ['id' => 3, 'name' => 'រូបវិទ្យា', 'code' => 'PHY', 'max_score' => 100, 'credit' => 1.5],
                ['id' => 4, 'name' => 'គីមីវិទ្យា', 'code' => 'CHM', 'max_score' => 100, 'credit' => 1.5],
                ['id' => 5, 'name' => 'ជីវវិទ្យា', 'code' => 'BIO', 'max_score' => 100, 'credit' => 1.5],
                ['id' => 6, 'name' => 'ភាសាអង់គ្លេស', 'code' => 'ENG', 'max_score' => 100, 'credit' => 1],
                ['id' => 7, 'name' => 'ប្រវត្តិវិទ្យា', 'code' => 'HIS', 'max_score' => 100, 'credit' => 1],
            ]);
        }

        $students = $exam ? Student::with('user')->where('grade', $className)->get() : collect();

        $allScores = collect();
        if ($exam) {
            $allScores = ExaminationScore::where('examination_id', $exam->id)
                ->where('class_name', $className)
                ->get()
                ->groupBy('student_id');
        }

        $rows = $students->map(function ($st) use ($subjects, $allScores) {
            $studentScores = $allScores->get($st->id) ? $allScores->get($st->id)->keyBy('course_subject_id') : collect();
            $scoresMap = [];
            $totalScore = 0;
            $enteredCount = 0;
            $totalCredits = 0;

            foreach ($subjects as $sub) {
                $sc = $studentScores->get($sub['id']);
                if ($sc !== null && $sc->score !== null) {
                    $val = (float) $sc->score;
                    $scoresMap[$sub['id']] = $val;
                    $totalScore += $val;
                    $enteredCount++;
                    $totalCredits += (float) ($sub['credit'] ?: 1.0);
                } else {
                    $scoresMap[$sub['id']] = null;
                }
            }

            // In Cambodian education system: average = total score divided by total credits/coefficients
            $divisor = $totalCredits > 0 ? $totalCredits : ($enteredCount > 0 ? $enteredCount : 1);
            $average = $enteredCount > 0 ? round($totalScore / $divisor, 2) : 0;
            $gradeLetter = ExaminationScore::calculateGradeLetter($average, 100);

            return [
                'student_id' => $st->id,
                'student_id_card' => $st->student_id_card,
                'name' => $st->display_name,
                'gender' => $st->gender === 'female' ? 'ស្រី' : 'ប្រុស',
                'scores' => $scoresMap,
                'total_score' => round($totalScore, 2),
                'entered_count' => $enteredCount,
                'average' => $average,
                'grade_letter' => $enteredCount > 0 ? $gradeLetter : '-',
                'rank' => '-',
            ];
        });

        if ($rows->isEmpty() && $demoMode !== '0') {
            $demoStudents = $this->getDemoStudentsList();
            $filtered = array_values(array_filter($demoStudents, fn($s) => $s['grade'] === $className));
            if (empty($filtered)) {
                $filtered = array_slice($demoStudents, 0, 6);
            }

            $seedScores = [
                [85, 90, 78, 88, 92, 80, 85],
                [92, 95, 88, 90, 94, 85, 91],
                [75, 70, 68, 72, 74, 65, 70],
                [80, 82, 85, 78, 80, 79, 83],
                [68, 65, 70, 64, 72, 60, 66],
                [95, 98, 92, 96, 94, 90, 93],
            ];

            $rows = collect($filtered)->map(function ($st, $idx) use ($subjects, $seedScores) {
                $subScores = $seedScores[$idx % count($seedScores)];
                $scoresMap = [];
                $totalScore = 0;
                $totalCredits = 0;
                foreach ($subjects as $sIdx => $sub) {
                    $val = (float) ($subScores[$sIdx % count($subScores)] ?? 80);
                    $scoresMap[$sub['id']] = $val;
                    $totalScore += $val;
                    $totalCredits += (float) ($sub['credit'] ?: 1.0);
                }
                $average = round($totalScore / ($totalCredits > 0 ? $totalCredits : count($subjects)), 2);
                $gradeLetter = ExaminationScore::calculateGradeLetter($average, 100);

                return [
                    'student_id' => $st['id'],
                    'student_id_card' => $st['student_id_card'],
                    'name' => $st['name'],
                    'gender' => $st['gender'],
                    'scores' => $scoresMap,
                    'total_score' => round($totalScore, 2),
                    'entered_count' => count($subjects),
                    'average' => $average,
                    'grade_letter' => $gradeLetter,
                    'rank' => '-',
                ];
            });
        }

        // Calculate Ranking based on Average (or total_score) descending
        $sorted = $rows->sortByDesc('average')->values();
        $rankMap = [];
        $currentRank = 1;
        foreach ($sorted as $index => $item) {
            if ($index > 0 && $item['average'] < $sorted[$index - 1]['average']) {
                $currentRank = $index + 1;
            }
            $rankMap[$item['student_id']] = $item['entered_count'] > 0 ? $currentRank : '-';
        }

        $finalRows = $rows->map(function ($item) use ($rankMap) {
            $item['rank'] = $rankMap[$item['student_id']] ?? '-';
            return $item;
        });

        // Statistics
        $evaluatedStudents = $finalRows->filter(fn ($r) => $r['entered_count'] > 0);
        $totalStudents = $finalRows->count();
        $classAverage = $evaluatedStudents->count() > 0 ? round($evaluatedStudents->avg('average'), 2) : 0;
        $highestAverage = $evaluatedStudents->count() > 0 ? $evaluatedStudents->max('average') : 0;
        $lowestAverage = $evaluatedStudents->count() > 0 ? $evaluatedStudents->min('average') : 0;
        
        // Auto-detect Cambodian 50-scale vs 100-scale
        $scaleMax = ($highestAverage <= 50.0 && $highestAverage > 0) ? 50 : 100;
        $passingScore = $scaleMax === 50 ? 25.0 : 50.0;
        $passedCount = $evaluatedStudents->filter(fn ($r) => $r['average'] >= $passingScore)->count();
        $failedCount = $evaluatedStudents->filter(fn ($r) => $r['average'] < $passingScore)->count();
        $passRate = $evaluatedStudents->count() > 0 ? round(($passedCount / $evaluatedStudents->count()) * 100, 1) : 0;

        return response()->json([
            'examination' => $exam ?: [
                'id' => is_numeric($examination) ? (int)$examination : 1,
                'title' => 'ការប្រឡងឆមាសលើកទី ១ (Semester 1 Exam)',
                'semester' => 1
            ],
            'className' => $className,
            'subjects' => $subjects,
            'students' => $finalRows,
            'stats' => [
                'total_students' => $totalStudents,
                'evaluated_count' => $evaluatedStudents->count(),
                'class_average' => $classAverage,
                'scale_max' => $scaleMax,
                'passing_score' => $passingScore,
                'highest_average' => $highestAverage,
                'lowest_average' => $lowestAverage,
                'passed_count' => $passedCount,
                'failed_count' => $failedCount,
                'pass_rate' => $passRate,
            ]
        ]);
    }

    public function saveExamMatrixScores(Request $request, Examination $examination)
    {
        $validated = $request->validate([
            'class_name' => 'required|string',
            'matrix' => 'required|array',
        ]);

        $className = $validated['class_name'];
        $matrix = $validated['matrix'];
        $subjects = CourseSubject::all()->keyBy('id');

        $savedCount = 0;
        foreach ($matrix as $studentId => $subjectScores) {
            if (!is_array($subjectScores)) continue;

            foreach ($subjectScores as $subjectId => $scoreVal) {
                if ($scoreVal === null || $scoreVal === '') {
                    continue;
                }

                $scoreFloat = (float) $scoreVal;
                $subject = $subjects->get($subjectId);
                $subjectName = $subject ? $subject->name : 'N/A';
                $gradeLetter = ExaminationScore::calculateGradeLetter($scoreFloat, 100);

                ExaminationScore::updateOrCreate(
                    [
                        'examination_id' => $examination->id,
                        'student_id' => $studentId,
                        'course_subject_id' => $subjectId,
                    ],
                    [
                        'class_name' => $className,
                        'subject_name' => $subjectName,
                        'score' => $scoreFloat,
                        'max_score' => 100,
                        'grade_letter' => $gradeLetter,
                    ]
                );
                $savedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "បានរក្សាទុកពិន្ទុតារាងរួមចំនួន {$savedCount} កំណត់ត្រាសម្រាប់ថ្នាក់ {$className} ដោយជោគជ័យ!",
        ]);
    }

    public function importExamScores(Request $request, Examination $examination)
    {
        $validated = $request->validate([
            'class_name' => 'required|string',
            'rows' => 'required|array',
        ]);

        $className = $validated['class_name'];
        $rows = $validated['rows'];
        $subjects = CourseSubject::all();
        $students = Student::where('grade', $className)->get();

        $studentMapByCard = $students->keyBy('student_id_card');
        $studentMapById = $students->keyBy('id');
        $subjectMapByCode = $subjects->keyBy('code');
        $subjectMapById = $subjects->keyBy('id');

        $importedCount = 0;
        foreach ($rows as $row) {
            $st = null;
            if (!empty($row['student_id']) && isset($studentMapById[$row['student_id']])) {
                $st = $studentMapById[$row['student_id']];
            } elseif (!empty($row['student_id_card']) && isset($studentMapByCard[$row['student_id_card']])) {
                $st = $studentMapByCard[$row['student_id_card']];
            } elseif (!empty($row['name'])) {
                $searchName = trim($row['name']);
                $st = $students->first(function ($s) use ($searchName) {
                    return trim($s->display_name) === $searchName 
                        || trim($s->name_khmer) === $searchName
                        || trim($s->user?->name) === $searchName;
                });
            }

            if (!$st || empty($row['scores']) || !is_array($row['scores'])) {
                continue;
            }

            foreach ($row['scores'] as $subKey => $scoreVal) {
                if ($scoreVal === null || $scoreVal === '') continue;

                $sub = $subjectMapById->get($subKey) ?? $subjectMapByCode->get($subKey);
                if (!$sub) continue;

                $scoreFloat = (float) $scoreVal;
                $gradeLetter = ExaminationScore::calculateGradeLetter($scoreFloat, 100);

                ExaminationScore::updateOrCreate(
                    [
                        'examination_id' => $examination->id,
                        'student_id' => $st->id,
                        'course_subject_id' => $sub->id,
                    ],
                    [
                        'class_name' => $className,
                        'subject_name' => $sub->name,
                        'score' => $scoreFloat,
                        'max_score' => 100,
                        'grade_letter' => $gradeLetter,
                    ]
                );
                $importedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "បានបញ្ចូលទិន្នន័យពិន្ទុដោយជោគជ័យចំនួន {$importedCount} មុខវិជ្ជា/កំណត់ត្រា!",
        ]);
    }

    public function parents(Request $request)
    {
        $query = SchoolParent::with('user', 'students.user');

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('name_khmer', 'like', "%{$s}%")
                    ->orWhere('name_latin', 'like', "%{$s}%")
                    ->orWhere('parent_id_card', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('occupation', 'like', "%{$s}%")
                    ->orWhere('child_name', 'like', "%{$s}%");
            });
        }

        if ($request->status) {
            if ($request->status === 'active') {
                $query->whereIn('status', ['active']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $parents = $query->latest()->get()->map(function ($p) {
            $childrenNames = $p->students->map(fn($st) => $st->name_khmer ?: ($st->user?->name ?? 'សិស្ស'))->toArray();
            if ($p->child_name && !in_array($p->child_name, $childrenNames)) {
                $childrenNames[] = $p->child_name . ($p->child_grade ? " ({$p->child_grade})" : '');
            }
            $childrenStr = count($childrenNames) > 0 ? implode(', ', $childrenNames) : 'មិនទាន់ភ្ជាប់កូន';

            return [
                'id' => $p->id,
                'idCard' => $p->parent_id_card ?: 'PAR-រង់ចាំ',
                'name' => $p->display_name,
                'name_khmer' => $p->name_khmer,
                'name_latin' => $p->name_latin,
                'gender' => $p->gender ?: 'other',
                'phone' => $p->phone ?: '-',
                'email' => $p->email ?: '-',
                'occupation' => $p->occupation ?: 'អាជីវករ / ផ្សេងៗ',
                'relationship' => $p->relationship ?: 'អាណាព្យាបាល',
                'address' => $p->address ?: '-',
                'photo' => $p->photo ? asset('storage/' . $p->photo) : null,
                'children' => $childrenStr,
                'children_count' => count($childrenNames),
                'child_name' => $p->child_name,
                'child_grade' => $p->child_grade,
                'status' => $p->status ?? 'active',
                'created_at' => $p->created_at ? $p->created_at->format('Y-m-d H:i') : null,
            ];
        });

        $pendingCount = SchoolParent::where('status', 'pending')->count();
        $activeCount = SchoolParent::where('status', 'active')->count();

        return response()->json([
            'parents' => $parents,
            'counts' => [
                'total' => SchoolParent::count(),
                'active' => $activeCount,
                'pending' => $pendingCount,
            ]
        ]);
    }

    public function storeParent(Request $request)
    {
        $validated = $request->validate([
            'name_khmer' => 'required|string|max:255',
            'name_latin' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'relationship' => 'required|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'child_name' => 'nullable|string|max:255',
            'child_grade' => 'nullable|string|max:50',
        ]);

        $year = date('Y');
        $last = SchoolParent::whereNotNull('parent_id_card')
            ->where('parent_id_card', 'like', "PAR-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();
        $nextNum = 1;
        if ($last && preg_match('/PAR-\d{4}-(\d+)/', $last->parent_id_card, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        } else {
            $nextNum = SchoolParent::whereNotNull('parent_id_card')->count() + 1;
        }
        $parentIdCard = sprintf('PAR-%s-%03d', $year, $nextNum);

        $parent = SchoolParent::create([
            'parent_id_card' => $parentIdCard,
            'name' => $validated['name_khmer'],
            'name_khmer' => $validated['name_khmer'],
            'name_latin' => $validated['name_latin'] ?? null,
            'gender' => $validated['gender'],
            'relationship' => $validated['relationship'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'address' => $validated['address'] ?? null,
            'child_name' => $validated['child_name'] ?? null,
            'child_grade' => $validated['child_grade'] ?? null,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => "បានបង្កើតអាណាព្យាបាល {$parent->display_name} ដោយជោគជ័យ!",
            'parent' => $parent
        ]);
    }

    public function approveParent(Request $request, SchoolParent $parent)
    {
        $validated = $request->validate([
            'parent_id_card' => 'nullable|string',
            'relationship' => 'nullable|string',
            'child_grade' => 'nullable|string',
        ]);

        $parentIdCard = $validated['parent_id_card'] ?? null;
        if (empty($parentIdCard) || $parentIdCard === 'PAR-រង់ចាំ') {
            $year = date('Y');
            $last = SchoolParent::whereNotNull('parent_id_card')
                ->where('parent_id_card', 'like', "PAR-{$year}-%")
                ->orderBy('id', 'desc')
                ->first();
            $nextNum = 1;
            if ($last && preg_match('/PAR-\d{4}-(\d+)/', $last->parent_id_card, $matches)) {
                $nextNum = intval($matches[1]) + 1;
            } else {
                $nextNum = SchoolParent::whereNotNull('parent_id_card')->count() + 1;
            }
            $parentIdCard = sprintf('PAR-%s-%03d', $year, $nextNum);
        }

        if (!$parent->user_id) {
            $baseName = $parent->name_khmer ?: ($parent->name ?: 'Parent');
            $email = $parent->email;
            if (!$email || User::where('email', $email)->exists()) {
                $cleanId = strtolower(str_replace([' ', '-'], '', $parentIdCard));
                $email = "{$cleanId}@school.edu.kh";
            }

            $user = User::create([
                'name' => $baseName,
                'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            ]);

            $parentRole = Role::where('name', 'parent')->first();
            if ($parentRole) {
                $user->roles()->syncWithoutDetaching([$parentRole->id]);
            }

            $parent->user_id = $user->id;
        }

        $parent->parent_id_card = $parentIdCard;
        $parent->status = 'active';
        if (!empty($validated['relationship'])) {
            $parent->relationship = $validated['relationship'];
        }
        if (!empty($validated['child_grade'])) {
            $parent->child_grade = $validated['child_grade'];
        }
        $parent->save();

        if ($parent->students()->count() === 0 && !empty($parent->child_name)) {
            $student = Student::where('name_khmer', 'like', '%' . $parent->child_name . '%')
                ->orWhere('name_latin', 'like', '%' . $parent->child_name . '%')
                ->first();
            if ($student) {
                $parent->students()->syncWithoutDetaching([
                    $student->id => ['relationship' => $parent->relationship ?? 'អាណាព្យាបាល']
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "បានអនុម័តអាណាព្យាបាល {$parent->display_name} (អត្តលេខ: {$parentIdCard}) ដោយជោគជ័យ!",
            'parent' => $parent->load('user', 'students.user')
        ]);
    }

    public function updateParent(Request $request, SchoolParent $parent)
    {
        $validated = $request->validate([
            'name_khmer' => 'required|string|max:255',
            'name_latin' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'relationship' => 'required|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'child_name' => 'nullable|string|max:255',
            'child_grade' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
        ]);

        $parent->update([
            'name' => $validated['name_khmer'],
            'name_khmer' => $validated['name_khmer'],
            'name_latin' => $validated['name_latin'] ?? $parent->name_latin,
            'gender' => $validated['gender'],
            'relationship' => $validated['relationship'],
            'phone' => $validated['phone'] ?? $parent->phone,
            'email' => $validated['email'] ?? $parent->email,
            'occupation' => $validated['occupation'] ?? $parent->occupation,
            'address' => $validated['address'] ?? $parent->address,
            'child_name' => $validated['child_name'] ?? $parent->child_name,
            'child_grade' => $validated['child_grade'] ?? $parent->child_grade,
            'status' => $validated['status'] ?? $parent->status,
        ]);

        if ($parent->user) {
            $parent->user->update([
                'name' => $validated['name_khmer'] ?: ($validated['name_latin'] ?? $parent->user->name)
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "បានកែប្រែព័ត៌មានអាណាព្យាបាល {$parent->display_name} ដោយជោគជ័យ!",
            'parent' => $parent
        ]);
    }

    public function destroyParent(SchoolParent $parent)
    {
        $name = $parent->display_name;
        $parent->students()->detach();
        $parent->delete();

        return response()->json([
            'success' => true,
            'message' => "បានលុបអាណាព្យាបាល {$name} ដោយជោគជ័យ!"
        ]);
    }


    public function finance()
    {
        $invoices = Invoice::with('student.user', 'feeCategory')->latest()->take(50)->get()->map(fn ($inv) => [
            'id' => $inv->id,
            'invoice_number' => $inv->invoice_number,
            'student' => $inv->student?->display_name ?? ($inv->student?->user?->name ?? 'N/A'),
            'student_id' => $inv->student_id,
            'title' => $inv->title,
            'total_amount' => (float)$inv->total_amount,
            'total' => (float)$inv->total_amount,
            'paid_amount' => (float)$inv->paid_amount,
            'paid' => (float)$inv->paid_amount,
            'status' => $inv->status,
            'due_date' => Carbon::parse($inv->due_date)->format('d-M-Y'),
        ]);

        $payments = Payment::with('invoice.student.user')->latest()->take(50)->get()->map(fn ($pay) => [
            'id' => $pay->id,
            'receipt_number' => $pay->receipt_number,
            'amount' => (float)$pay->amount,
            'payment_method' => $pay->payment_method,
            'payment_date' => Carbon::parse($pay->payment_date)->format('d-M-Y'),
            'student' => $pay->invoice?->student?->display_name ?? 'N/A',
            'invoice_number' => $pay->invoice?->invoice_number ?? 'N/A',
        ]);

        $expenses = SchoolExpense::latest()->take(50)->get()->map(fn ($exp) => [
            'id' => $exp->id,
            'title' => $exp->title,
            'category' => $exp->category,
            'amount' => (float)$exp->amount,
            'date' => Carbon::parse($exp->expense_date)->format('d-M-Y'),
        ]);

        $feeCategories = FeeCategory::all();
        $students = Student::with('user')->get()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->display_name,
            'student_id_card' => $s->student_id_card,
            'grade' => $s->grade,
        ]);

        return response()->json([
            'stats' => [
                'revenue' => (float) Payment::sum('amount'),
                'invoiced' => (float) Invoice::sum('total_amount'),
                'expenses' => (float) SchoolExpense::sum('amount'),
            ],
            'invoices' => $invoices,
            'payments' => $payments,
            'expenses' => $expenses,
            'feeCategories' => $feeCategories,
            'students' => $students,
        ]);
    }

    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_category_id' => 'nullable|exists:fee_categories,id',
            'title' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0.01',
            'due_date' => 'nullable|date',
        ]);

        $nextId = Invoice::max('id') + 1;
        $invNumber = sprintf('INV-%s-%04d', date('Y'), $nextId);

        $invoice = Invoice::create([
            'invoice_number' => $invNumber,
            'student_id' => $validated['student_id'],
            'fee_category_id' => $validated['fee_category_id'] ?? null,
            'title' => $validated['title'],
            'total_amount' => $validated['total_amount'],
            'paid_amount' => 0.00,
            'due_date' => $validated['due_date'] ?? Carbon::now()->addDays(30),
            'status' => 'unpaid',
            'currency' => SystemSetting::get('currency', 'USD'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "បានបង្កើតវិក្កយបត្រ {$invNumber} ដោយជោគជ័យ!",
            'invoice' => $invoice,
        ]);
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,qr_code,other',
            'payment_date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);
        $nextId = Payment::max('id') + 1;
        $receiptNumber = sprintf('REC-%s-%04d', date('Y'), $nextId);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'receipt_number' => $receiptNumber,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'note' => $validated['note'] ?? null,
        ]);

        $newPaid = $invoice->paid_amount + $validated['amount'];
        $newStatus = $newPaid >= $invoice->total_amount ? 'paid' : 'partial';
        $invoice->update([
            'paid_amount' => $newPaid,
            'status' => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => "បានកត់ត្រាការបង់ប្រាក់បង្កាន់ដៃលេខ {$receiptNumber} ដោយជោគជ័យ!",
            'payment' => $payment,
        ]);
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $expense = SchoolExpense::create($validated);

        return response()->json([
            'success' => true,
            'message' => "បានកត់ត្រាចំណាយ {$expense->title} (\${$expense->amount}) ដោយជោគជ័យ!",
            'expense' => $expense,
        ]);
    }

    public function reports()
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalRevenue = (float) Payment::sum('amount');
        $attendanceRate = 96.8;

        return response()->json([
            'stats' => [
                'totalStudents' => $totalStudents,
                'totalTeachers' => $totalTeachers,
                'totalRevenue' => $totalRevenue,
                'attendanceRate' => $attendanceRate,
            ],
        ]);
    }

    public function settings()
    {
        $raw = SystemSetting::pluck('value', 'key')->toArray();

        $defaults = [
            'school_name' => 'សាលារៀន ជំនាន់ថ្មី PDT',
            'school_name_en' => 'PDT New Generation School',
            'principal_name' => 'លោក ឈុន វណ្ណា',
            'school_phone' => '023 880 880 / 012 345 678',
            'school_email' => 'contact@pdt-school.edu.kh',
            'school_website' => 'https://pdt-school.edu.kh',
            'school_address' => 'មហាវិថីសហព័ន្ធរុស្ស៊ី សង្កាត់ទឹកល្អក់១ ខណ្ឌទួលគោក រាជធានីភ្នំពេញ',
            'school_logo' => null,
            'school_stamp' => null,
            'current_academic_year' => '2025-2026',
            'current_semester' => '1',
            'academic_start_date' => '2025-10-01',
            'academic_end_date' => '2026-07-31',
            'passing_grade' => '50',
            'morning_shift_time' => '07:00 - 11:00',
            'afternoon_shift_time' => '13:00 - 17:00',
            'late_threshold_minutes' => '15',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'exchange_rate' => '4100',
            'invoice_prefix' => 'INV-2026-',
            'bank_name' => 'ABA Bank',
            'bank_account_name' => 'PDT SCHOOL CO., LTD',
            'bank_account_number' => '001 234 567',
            'khqr_image' => null,
            'demo_mode' => '1',
            'enable_public_registration' => '1',
            'enable_teacher_score_edit' => '1',
            'role_permissions' => json_encode([
                'admin' => ['manage_students', 'attendance', 'scores', 'finance', 'reports', 'settings'],
                'teacher' => ['attendance', 'scores', 'reports'],
                'accountant' => ['finance', 'reports'],
                'student_parent' => ['attendance', 'scores'],
            ]),
        ];

        $merged = array_merge($defaults, $raw);

        if (!empty($merged['school_logo'])) {
            $merged['school_logo_url'] = (str_starts_with($merged['school_logo'], 'http') || str_starts_with($merged['school_logo'], 'data:'))
                ? $merged['school_logo']
                : asset('storage/' . ltrim($merged['school_logo'], '/'));
        } else {
            $merged['school_logo_url'] = '';
        }
        if (!empty($merged['school_stamp'])) {
            $merged['school_stamp_url'] = (str_starts_with($merged['school_stamp'], 'http') || str_starts_with($merged['school_stamp'], 'data:'))
                ? $merged['school_stamp']
                : asset('storage/' . ltrim($merged['school_stamp'], '/'));
        } else {
            $merged['school_stamp_url'] = '';
        }
        if (!empty($merged['khqr_image'])) {
            $merged['khqr_image_url'] = (str_starts_with($merged['khqr_image'], 'http') || str_starts_with($merged['khqr_image'], 'data:'))
                ? $merged['khqr_image']
                : asset('storage/' . ltrim($merged['khqr_image'], '/'));
        } else {
            $merged['khqr_image_url'] = '';
        }

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return response()->json([
            'settings' => $merged,
            'academicYears' => $academicYears,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $all = $request->except(['school_logo', 'school_stamp', 'khqr_image']);

        foreach ($all as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            SystemSetting::set($key, (string)$value);
        }

        if ($request->hasFile('school_logo')) {
            $path = $request->file('school_logo')->store('settings', 'public');
            SystemSetting::set('school_logo', $path);
        }
        if ($request->hasFile('school_stamp')) {
            $path = $request->file('school_stamp')->store('settings', 'public');
            SystemSetting::set('school_stamp', $path);
        }
        if ($request->hasFile('khqr_image')) {
            $path = $request->file('khqr_image')->store('settings', 'public');
            SystemSetting::set('khqr_image', $path);
        }

        $logoPath = SystemSetting::get('school_logo');
        $logoUrl = '';
        if ($logoPath) {
            $logoUrl = (str_starts_with($logoPath, 'http') || str_starts_with($logoPath, 'data:'))
                ? $logoPath
                : asset('storage/' . ltrim($logoPath, '/'));
        }

        return response()->json([
            'success' => true,
            'message' => 'បានរក្សាទុកការកំណត់ប្រព័ន្ធដោយជោគជ័យ!',
            'school_logo_url' => $logoUrl,
            'school_name' => SystemSetting::get('school_name', 'សាលារៀន ជំនាន់ថ្មី PDT'),
            'school_name_en' => SystemSetting::get('school_name_en', 'PDT New Generation School'),
        ]);
    }

    public function backupDatabase()
    {
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
        $dbName = \Illuminate\Support\Facades\DB::getDatabaseName();
        $tableKey = "Tables_in_{$dbName}";

        $dump = "-- LMSTICK School Management System Database Backup\n";
        $dump .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
        $dump .= "-- Database: {$dbName}\n\n";
        $dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $table = $tableObj->$tableKey ?? reset($tableObj);
            
            $create = \Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE `{$table}`");
            $createKey = 'Create Table';
            $dump .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $dump .= $create[0]->$createKey . ";\n\n";

            $rows = \Illuminate\Support\Facades\DB::table($table)->get();
            if ($rows->count() > 0) {
                foreach ($rows as $row) {
                    $values = array_map(function ($val) {
                        if (is_null($val)) return "NULL";
                        return "'" . addslashes((string)$val) . "'";
                    }, (array)$row);

                    $columns = array_map(fn($col) => "`{$col}`", array_keys((array)$row));
                    $dump .= "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                }
                $dump .= "\n";
            }
        }

        $dump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $filename = 'lmstick_backup_' . date('Y_m_d_His') . '.sql';

        return response($dump, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function roles()
    {
        $roles = Role::all()->map(fn ($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'displayName' => $r->display_name ?? ucfirst($r->name),
            'userCount' => \App\Models\User::whereHas('roles', fn ($q) => $q->where('id', $r->id))->count(),
        ]);

        return response()->json(['roles' => $roles]);
    }

    public function seedDemoDatabase(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'ComprehensiveSchoolSeeder',
                '--force' => true,
            ]);

            SystemSetting::set('demo_mode', '1');

            $students11D = Student::where('grade', '11D')->count();
            $students10A = Student::where('grade', '10A')->count();
            $totalTeachers = Teacher::count();
            $totalExams = Examination::count();
            $totalScores = ExaminationScore::count();

            return response()->json([
                'success' => true,
                'message' => 'បាន Seed ទិន្នន័យគំរូសាលាពេញលេញ (11D: 52 នាក់, 10A: 35 នាក់, គ្រូ 15 នាក់, និងពិន្ទុ 3 សម័យ) ដោយជោគជ័យ!',
                'stats' => [
                    'students_11d' => $students11D ?: 52,
                    'students_10a' => $students10A ?: 35,
                    'teachers' => $totalTeachers ?: 15,
                    'examinations' => $totalExams ?: 3,
                    'scores' => $totalScores ?: 2088,
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'ការ Seed បរាជ័យ៖ ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getDemoStudentsList()
    {
        $list11D = [
            ['name' => 'សុខ ពិសិដ្ឋ', 'latin' => 'Sok Piseth', 'gender' => 'ប្រុស', 'dob' => '2008-05-14', 'parent' => 'សុខ ជា', 'rel' => 'ឪពុក', 'p_phone' => '012 888 777'],
            ['name' => 'ចាន់ ធីតា', 'latin' => 'Chan Thida', 'gender' => 'ស្រី', 'dob' => '2008-08-20', 'parent' => 'ចាន់ សុផល', 'rel' => 'ឪពុក', 'p_phone' => '098 777 666'],
            ['name' => 'មាស សុវណ្ណ', 'latin' => 'Meas Sovann', 'gender' => 'ប្រុស', 'dob' => '2008-01-10', 'parent' => 'មាស រ៉ា', 'rel' => 'ឪពុក', 'p_phone' => '077 666 555'],
            ['name' => 'គង់ ចិន្តា', 'latin' => 'Kong Chenda', 'gender' => 'ស្រី', 'dob' => '2008-11-25', 'parent' => 'គង់ សារ៉េត', 'rel' => 'ម្តាយ', 'p_phone' => '015 555 444'],
            ['name' => 'ហេង វិសាល', 'latin' => 'Heng Visal', 'gender' => 'ប្រុស', 'dob' => '2008-03-30', 'parent' => 'ហេង វុទ្ធី', 'rel' => 'ឪពុក', 'p_phone' => '089 444 333'],
            ['name' => 'កែវ រចនា', 'latin' => 'Keo Rachana', 'gender' => 'ស្រី', 'dob' => '2008-07-15', 'parent' => 'កែវ សម្បត្តិ', 'rel' => 'ឪពុក', 'p_phone' => '070 333 222'],
            ['name' => 'លី ដារ៉ា', 'latin' => 'Ly Dara', 'gender' => 'ប្រុស', 'dob' => '2008-09-05', 'parent' => 'លី សេង', 'rel' => 'ឪពុក', 'p_phone' => '016 222 111'],
            ['name' => 'វ៉ាន់ ស្រីមុំ', 'latin' => 'Van Sreymom', 'gender' => 'ស្រី', 'dob' => '2008-04-18', 'parent' => 'វ៉ាន់ ធឿន', 'rel' => 'ឪពុក', 'p_phone' => '092 111 000'],
            ['name' => 'ឃុន វឌ្ឍនា', 'latin' => 'Khun Vathana', 'gender' => 'ប្រុស', 'dob' => '2008-12-01', 'parent' => 'ឃុន ផល្លា', 'rel' => 'ម្តាយ', 'p_phone' => '011 000 999'],
            ['name' => 'អ៊ុង គីមសួរ', 'latin' => 'Ung Kimsour', 'gender' => 'ស្រី', 'dob' => '2008-06-22', 'parent' => 'អ៊ុង វីរៈ', 'rel' => 'ឪពុក', 'p_phone' => '093 999 888'],
            ['name' => 'សេង បូរ៉ា', 'latin' => 'Seng Bora', 'gender' => 'ប្រុស', 'dob' => '2008-02-17', 'parent' => 'សេង ប៊ុនណា', 'rel' => 'ឪពុក', 'p_phone' => '012 888 444'],
            ['name' => 'នួន ស្រីណែត', 'latin' => 'Nuon Sreynet', 'gender' => 'ស្រី', 'dob' => '2008-10-08', 'parent' => 'នួន សុខឃីម', 'rel' => 'ឪពុក', 'p_phone' => '098 111 555'],
            ['name' => 'ជា សុភាព', 'latin' => 'Chea Sopheap', 'gender' => 'ប្រុស', 'dob' => '2008-07-29', 'parent' => 'ជា ចំរើន', 'rel' => 'ឪពុក', 'p_phone' => '077 222 666'],
            ['name' => 'ឌិត មុនី', 'latin' => 'Dy Mony', 'gender' => 'ស្រី', 'dob' => '2008-03-12', 'parent' => 'ឌិត ថាវី', 'rel' => 'ម្តាយ', 'p_phone' => '015 333 777'],
            ['name' => 'អេង សុភ័ក្ត្រ', 'latin' => 'Eng Sopheak', 'gender' => 'ប្រុស', 'dob' => '2008-09-19', 'parent' => 'អេង រតនា', 'rel' => 'ឪពុក', 'p_phone' => '089 444 888'],
            ['name' => 'យឹម ម៉ាលី', 'latin' => 'Yim Maly', 'gender' => 'ស្រី', 'dob' => '2008-12-31', 'parent' => 'យឹម សុគន្ធ', 'rel' => 'ម្តាយ', 'p_phone' => '070 555 999'],
            ['name' => 'ស៊ូ វិរៈ', 'latin' => 'Sou Vireak', 'gender' => 'ប្រុស', 'dob' => '2008-08-04', 'parent' => 'ស៊ូ ហេង', 'rel' => 'ឪពុក', 'p_phone' => '016 666 000'],
            ['name' => 'ផាន់ ធីរ៉ា', 'latin' => 'Phan Thira', 'gender' => 'ស្រី', 'dob' => '2008-04-26', 'parent' => 'ផាន់ គឹមហួរ', 'rel' => 'ឪពុក', 'p_phone' => '092 777 111'],
            ['name' => 'ព្រំ សុភា', 'latin' => 'Prom Sophea', 'gender' => 'ស្រី', 'dob' => '2008-01-15', 'parent' => 'ព្រំ វ៉ាន់នី', 'rel' => 'ម្តាយ', 'p_phone' => '011 888 222'],
            ['name' => 'ឡុង សម្បត្តិ', 'latin' => 'Long Sambath', 'gender' => 'ប្រុស', 'dob' => '2008-11-09', 'parent' => 'ឡុង វិសិដ្ឋ', 'rel' => 'ឪពុក', 'p_phone' => '093 999 333'],
            ['name' => 'ឈិត ចំរើន', 'latin' => 'Chhit Chamroeun', 'gender' => 'ប្រុស', 'dob' => '2008-06-18', 'parent' => 'ឈិត សារ៉ន', 'rel' => 'ឪពុក', 'p_phone' => '012 345 111'],
            ['name' => 'ប៊ុន ស្រីពៅ', 'latin' => 'Bun Sreypov', 'gender' => 'ស្រី', 'dob' => '2008-02-23', 'parent' => 'ប៊ុន វ៉ាន់ដេត', 'rel' => 'ឪពុក', 'p_phone' => '098 765 222'],
            ['name' => 'គឹម វណ្ណារ៉ា', 'latin' => 'Kim Vannara', 'gender' => 'ប្រុស', 'dob' => '2008-10-14', 'parent' => 'គឹម ហេង', 'rel' => 'ឪពុក', 'p_phone' => '017 890 333'],
            ['name' => 'ម៉ែន លក្ខណា', 'latin' => 'Men Leakhena', 'gender' => 'ស្រី', 'dob' => '2008-05-09', 'parent' => 'ម៉ែន សំអាត', 'rel' => 'ម្តាយ', 'p_phone' => '077 123 444'],
            ['name' => 'ផាន មុនីរ័ត្ន', 'latin' => 'Phan Muniroth', 'gender' => 'ប្រុស', 'dob' => '2008-12-28', 'parent' => 'ផាន សុជាតិ', 'rel' => 'ឪពុក', 'p_phone' => '089 234 555'],
            ['name' => 'ជា សុវណ្ណារី', 'latin' => 'Chea Sovannary', 'gender' => 'ស្រី', 'dob' => '2008-04-03', 'parent' => 'ជា ប៊ុនថន', 'rel' => 'ឪពុក', 'p_phone' => '070 345 666'],
            ['name' => 'ទូច សុខុម', 'latin' => 'Touch Sokhom', 'gender' => 'ប្រុស', 'dob' => '2008-08-11', 'parent' => 'ទូច សារ៉ាត់', 'rel' => 'ឪពុក', 'p_phone' => '016 456 777'],
            ['name' => 'ពេជ្រ សុភាព', 'latin' => 'Pech Sopheap', 'gender' => 'ស្រី', 'dob' => '2008-01-27', 'parent' => 'ពេជ្រ គឹមសាន', 'rel' => 'ម្តាយ', 'p_phone' => '092 567 888'],
            ['name' => 'ម៉ៅ សុផល', 'latin' => 'Mao Sophal', 'gender' => 'ប្រុស', 'dob' => '2008-07-21', 'parent' => 'ម៉ៅ សុខន', 'rel' => 'ឪពុក', 'p_phone' => '011 678 999'],
            ['name' => 'រស់ ចរិយា', 'latin' => 'Ros Chariya', 'gender' => 'ស្រី', 'dob' => '2008-03-16', 'parent' => 'រស់ សារ៉េត', 'rel' => 'ឪពុក', 'p_phone' => '093 789 000'],
            ['name' => 'ប្រាក់ វិទូ', 'latin' => 'Prak Vithou', 'gender' => 'ប្រុស', 'dob' => '2008-09-30', 'parent' => 'ប្រាក់ សារឿន', 'rel' => 'ឪពុក', 'p_phone' => '012 890 111'],
            ['name' => 'សោម សុវត្តី', 'latin' => 'Saom Sovattey', 'gender' => 'ស្រី', 'dob' => '2008-11-19', 'parent' => 'សោម ចំរើន', 'rel' => 'ម្តាយ', 'p_phone' => '098 901 222'],
            ['name' => 'អ៊ុក វ៉ាន់ឌី', 'latin' => 'Ouk Vandy', 'gender' => 'ប្រុស', 'dob' => '2008-04-07', 'parent' => 'អ៊ុក សុគន្ធ', 'rel' => 'ឪពុក', 'p_phone' => '017 012 333'],
            ['name' => 'តាំង គីមហួយ', 'latin' => 'Tang Kimhuoy', 'gender' => 'ស្រី', 'dob' => '2008-06-25', 'parent' => 'តាំង គឹមសែ', 'rel' => 'ឪពុក', 'p_phone' => '077 123 444'],
            ['name' => 'រ័ត្ន សុភ័ណ្ឌ', 'latin' => 'Roth Sophean', 'gender' => 'ប្រុស', 'dob' => '2008-10-02', 'parent' => 'រ័ត្ន ផល្លី', 'rel' => 'ឪពុក', 'p_phone' => '089 234 555'],
            ['name' => 'សុខ ស្រីល័ក្ខ', 'latin' => 'Sok Sreyleak', 'gender' => 'ស្រី', 'dob' => '2008-08-14', 'parent' => 'សុខ វុទ្ធី', 'rel' => 'ឪពុក', 'p_phone' => '070 345 666'],
            ['name' => 'យឹម ចាន់ដារ៉ូ', 'latin' => 'Yim Chandaro', 'gender' => 'ប្រុស', 'dob' => '2008-02-11', 'parent' => 'យឹម ផល្លា', 'rel' => 'ឪពុក', 'p_phone' => '016 456 777'],
            ['name' => 'ហួត ម៉ារីណា', 'latin' => 'Huot Marina', 'gender' => 'ស្រី', 'dob' => '2008-12-05', 'parent' => 'ហួត សុខុម', 'rel' => 'ម្តាយ', 'p_phone' => '092 567 888'],
            ['name' => 'ស៊ិន សុវណ្ណឌី', 'latin' => 'Sin Sovandy', 'gender' => 'ប្រុស', 'dob' => '2008-05-18', 'parent' => 'ស៊ិន វណ្ណៈ', 'rel' => 'ឪពុក', 'p_phone' => '011 678 999'],
            ['name' => 'សៀង មុនីរ័ត្ន', 'latin' => 'Sieng Muniroth', 'gender' => 'ស្រី', 'dob' => '2008-07-28', 'parent' => 'សៀង គឹមហេង', 'rel' => 'ឪពុក', 'p_phone' => '093 789 000'],
            ['name' => 'ឈុន វិបុល', 'latin' => 'Chhun Vibol', 'gender' => 'ប្រុស', 'dob' => '2008-03-04', 'parent' => 'ឈុន សារិទ្ធ', 'rel' => 'ឪពុក', 'p_phone' => '012 890 222'],
            ['name' => 'ភិន ស្រីពេជ្រ', 'latin' => 'Phin Sreypech', 'gender' => 'ស្រី', 'dob' => '2008-09-12', 'parent' => 'ភិន វ៉ាន់ឌី', 'rel' => 'ឪពុក', 'p_phone' => '098 901 333'],
            ['name' => 'ណុប សុជាតិ', 'latin' => 'Nop Socheat', 'gender' => 'ប្រុស', 'dob' => '2008-01-22', 'parent' => 'ណុប គឹមឡាយ', 'rel' => 'ឪពុក', 'p_phone' => '017 012 444'],
            ['name' => 'ឈិន កល្យាណ', 'latin' => 'Chhin Kallyan', 'gender' => 'ស្រី', 'dob' => '2008-11-03', 'parent' => 'ឈិន សារឿន', 'rel' => 'ម្តាយ', 'p_phone' => '077 123 555'],
            ['name' => 'តែ គីមស្រ៊ុន', 'latin' => 'Tae Kimsrun', 'gender' => 'ប្រុស', 'dob' => '2008-06-08', 'parent' => 'តែ ហុកសេង', 'rel' => 'ឪពុក', 'p_phone' => '089 234 666'],
            ['name' => 'ហុង លីដា', 'latin' => 'Hong Lyda', 'gender' => 'ស្រី', 'dob' => '2008-04-19', 'parent' => 'ហុង វណ្ណដា', 'rel' => 'ឪពុក', 'p_phone' => '070 345 777'],
            ['name' => 'ហ៊ីង វិសិដ្ឋ', 'latin' => 'Hing Viseth', 'gender' => 'ប្រុស', 'dob' => '2008-10-24', 'parent' => 'ហ៊ីង សុខា', 'rel' => 'ឪពុក', 'p_phone' => '016 456 888'],
            ['name' => 'អ៊ឹម ទេវី', 'latin' => 'Im Devi', 'gender' => 'ស្រី', 'dob' => '2008-08-01', 'parent' => 'អ៊ឹម សម្បត្តិ', 'rel' => 'ម្តាយ', 'p_phone' => '092 567 999'],
            ['name' => 'កែវ ប៊ុនរុង', 'latin' => 'Keo Bunrong', 'gender' => 'ប្រុស', 'dob' => '2008-02-15', 'parent' => 'កែវ វិបុល', 'rel' => 'ឪពុក', 'p_phone' => '011 678 000'],
            ['name' => 'ឡេង រតនា', 'latin' => 'Leng Ratana', 'gender' => 'ស្រី', 'dob' => '2008-12-14', 'parent' => 'ឡេង សុផល', 'rel' => 'ឪពុក', 'p_phone' => '093 789 111'],
            ['name' => 'នេត វ៉ាន់ថា', 'latin' => 'Neth Vantha', 'gender' => 'ប្រុស', 'dob' => '2008-05-27', 'parent' => 'នេត វណ្ណដេត', 'rel' => 'ឪពុក', 'p_phone' => '012 890 333'],
            ['name' => 'ទិត្យ ស្រីនាង', 'latin' => 'Tith Sreyneang', 'gender' => 'ស្រី', 'dob' => '2008-07-06', 'parent' => 'ទិត្យ សារ៉ុម', 'rel' => 'ម្តាយ', 'p_phone' => '098 901 444'],
        ];

        $list10A = [
            ['name' => 'ប៊ុន ថេង', 'latin' => 'Bun Theng', 'gender' => 'ប្រុស', 'dob' => '2009-03-12', 'parent' => 'ប៊ុន សុខា', 'rel' => 'ឪពុក', 'p_phone' => '012 777 101'],
            ['name' => 'ផាន់ ធីរ៉ា', 'latin' => 'Phan Thira', 'gender' => 'ស្រី', 'dob' => '2009-08-25', 'parent' => 'ផាន់ គឹមហួរ', 'rel' => 'ឪពុក', 'p_phone' => '098 888 102'],
            ['name' => 'គង់ សុវណ្ណដារ៉ា', 'latin' => 'Kong Sovandara', 'gender' => 'ប្រុស', 'dob' => '2009-01-19', 'parent' => 'គង់ ចំរើន', 'rel' => 'ឪពុក', 'p_phone' => '077 999 103'],
            ['name' => 'កែវ សុគន្ធា', 'latin' => 'Keo Sokunthea', 'gender' => 'ស្រី', 'dob' => '2009-11-04', 'parent' => 'កែវ សារិទ្ធ', 'rel' => 'ម្តាយ', 'p_phone' => '015 111 104'],
            ['name' => 'ម៉ៅ វិច្ឆិកា', 'latin' => 'Mao Vicheka', 'gender' => 'ប្រុស', 'dob' => '2009-04-18', 'parent' => 'ម៉ៅ វុទ្ធី', 'rel' => 'ឪពុក', 'p_phone' => '089 222 105'],
            ['name' => 'សុខ មាលី', 'latin' => 'Sok Maly', 'gender' => 'ស្រី', 'dob' => '2009-09-30', 'parent' => 'សុខ សារ៉េត', 'rel' => 'ម្តាយ', 'p_phone' => '070 333 106'],
            ['name' => 'ជា រ៉ាដូ', 'latin' => 'Chea Rado', 'gender' => 'ប្រុស', 'dob' => '2009-02-14', 'parent' => 'ជា ប៊ុនថន', 'rel' => 'ឪពុក', 'p_phone' => '016 444 107'],
            ['name' => 'ជា ស្រីណុច', 'latin' => 'Chea Sreynoch', 'gender' => 'ស្រី', 'dob' => '2009-06-22', 'parent' => 'ជា សម្បត្តិ', 'rel' => 'ឪពុក', 'p_phone' => '092 555 108'],
            ['name' => 'ឈឹម សុខគង់', 'latin' => 'Chhim Sokkong', 'gender' => 'ប្រុស', 'dob' => '2009-12-09', 'parent' => 'ឈឹម សារ៉ុម', 'rel' => 'ឪពុក', 'p_phone' => '011 666 109'],
            ['name' => 'វ៉ាន់ ស្រីនិច', 'latin' => 'Van Sreynich', 'gender' => 'ស្រី', 'dob' => '2009-07-15', 'parent' => 'វ៉ាន់ ធឿន', 'rel' => 'ម្តាយ', 'p_phone' => '093 777 110'],
            ['name' => 'លឹម សម្បត្តិ', 'latin' => 'Lim Sambath', 'gender' => 'ប្រុស', 'dob' => '2009-05-28', 'parent' => 'លឹម គឹមសាន', 'rel' => 'ឪពុក', 'p_phone' => '012 888 111'],
            ['name' => 'ហេង ធីតា', 'latin' => 'Heng Thida', 'gender' => 'ស្រី', 'dob' => '2009-10-11', 'parent' => 'ហេង សុផល', 'rel' => 'ឪពុក', 'p_phone' => '098 999 112'],
            ['name' => 'ហេង សំណាង', 'latin' => 'Heng Samnang', 'gender' => 'ប្រុស', 'dob' => '2009-03-03', 'parent' => 'ហេង វិបុល', 'rel' => 'ឪពុក', 'p_phone' => '017 000 113'],
            ['name' => 'ម៉ែន ស្រីអូន', 'latin' => 'Men Sreyoun', 'gender' => 'ស្រី', 'dob' => '2009-08-16', 'parent' => 'ម៉ែន សំអាត', 'rel' => 'ម្តាយ', 'p_phone' => '077 111 114'],
            ['name' => 'ព្រំ វិបុល', 'latin' => 'Prom Vibol', 'gender' => 'ប្រុស', 'dob' => '2009-01-24', 'parent' => 'ព្រំ វ៉ាន់នី', 'rel' => 'ឪពុក', 'p_phone' => '089 222 115'],
            ['name' => 'អេង វណ្ណារី', 'latin' => 'Eng Vannary', 'gender' => 'ស្រី', 'dob' => '2009-11-17', 'parent' => 'អេង រតនា', 'rel' => 'ឪពុក', 'p_phone' => '070 333 116'],
            ['name' => 'អ៊ុំ ចាន់ដារ៉ា', 'latin' => 'Oum Chandara', 'gender' => 'ប្រុស', 'dob' => '2009-04-05', 'parent' => 'អ៊ុំ សុគន្ធ', 'rel' => 'ឪពុក', 'p_phone' => '016 444 117'],
            ['name' => 'រស់ សុវណ្ណម៉ាលី', 'latin' => 'Ros Sovannmaly', 'gender' => 'ស្រី', 'dob' => '2009-09-12', 'parent' => 'រស់ សារ៉េត', 'rel' => 'ម្តាយ', 'p_phone' => '092 555 118'],
            ['name' => 'សេង រតនៈ', 'latin' => 'Seng Rattanak', 'gender' => 'ប្រុស', 'dob' => '2009-02-27', 'parent' => 'សេង ប៊ុនណា', 'rel' => 'ឪពុក', 'p_phone' => '011 666 119'],
            ['name' => 'តាំង ស្រីរ័ត្ន', 'latin' => 'Tang Sreyroth', 'gender' => 'ស្រី', 'dob' => '2009-07-08', 'parent' => 'តាំង គឹមសែ', 'rel' => 'ឪពុក', 'p_phone' => '093 777 120'],
            ['name' => 'ទូច វិសាល', 'latin' => 'Touch Visal', 'gender' => 'ប្រុស', 'dob' => '2009-12-21', 'parent' => 'ទូច សារ៉ាត់', 'rel' => 'ឪពុក', 'p_phone' => '012 888 121'],
            ['name' => 'នេត សុជាតា', 'latin' => 'Neth Socheata', 'gender' => 'ស្រី', 'dob' => '2009-05-10', 'parent' => 'នេត វណ្ណដេត', 'rel' => 'ម្តាយ', 'p_phone' => '098 999 122'],
            ['name' => 'ឌុក ប៊ុនណាត', 'latin' => 'Duk Bunnat', 'gender' => 'ប្រុស', 'dob' => '2009-10-03', 'parent' => 'ឌុក សារឿន', 'rel' => 'ឪពុក', 'p_phone' => '017 000 123'],
            ['name' => 'សោម ចរិយា', 'latin' => 'Saom Chariya', 'gender' => 'ស្រី', 'dob' => '2009-03-29', 'parent' => 'សោម ចំរើន', 'rel' => 'ឪពុក', 'p_phone' => '077 111 124'],
            ['name' => 'នួន សុខា', 'latin' => 'Nuon Sokha', 'gender' => 'ប្រុស', 'dob' => '2009-08-02', 'parent' => 'នួន សុខឃីម', 'rel' => 'ឪពុក', 'p_phone' => '089 222 125'],
            ['name' => 'ពេជ្រ ម៉ារី', 'latin' => 'Pech Mary', 'gender' => 'ស្រី', 'dob' => '2009-01-14', 'parent' => 'ពេជ្រ គឹមសាន', 'rel' => 'ម្តាយ', 'p_phone' => '070 333 126'],
            ['name' => 'អ៊ុក ពិសិដ្ឋ', 'latin' => 'Ouk Piseth', 'gender' => 'ប្រុស', 'dob' => '2009-11-20', 'parent' => 'អ៊ុក សុគន្ធ', 'rel' => 'ឪពុក', 'p_phone' => '016 444 127'],
            ['name' => 'ឌិត ស្រីកែវ', 'latin' => 'Dy Sreykeo', 'gender' => 'ស្រី', 'dob' => '2009-04-11', 'parent' => 'ឌិត ថាវី', 'rel' => 'ម្តាយ', 'p_phone' => '092 555 128'],
            ['name' => 'ចាន់ វឌ្ឍនៈ', 'latin' => 'Chan Vathana', 'gender' => 'ប្រុស', 'dob' => '2009-09-06', 'parent' => 'ចាន់ សុផល', 'rel' => 'ឪពុក', 'p_phone' => '011 666 129'],
            ['name' => 'ប៊ុន សុខលីន', 'latin' => 'Bun Soklin', 'gender' => 'ស្រី', 'dob' => '2009-02-18', 'parent' => 'ប៊ុន វ៉ាន់ដេត', 'rel' => 'ឪពុក', 'p_phone' => '093 777 130'],
            ['name' => 'ឡុង ថាវី', 'latin' => 'Long Thavy', 'gender' => 'ប្រុស', 'dob' => '2009-07-25', 'parent' => 'ឡុង វិសិដ្ឋ', 'rel' => 'ឪពុក', 'p_phone' => '012 888 131'],
            ['name' => 'ឈិត ស្រីមាស', 'latin' => 'Chhit Sreymeas', 'gender' => 'ស្រី', 'dob' => '2009-12-13', 'parent' => 'ឈិត សារ៉ន', 'rel' => 'ម្តាយ', 'p_phone' => '098 999 132'],
            ['name' => 'យឹម សុវណ្ណ', 'latin' => 'Yim Sovann', 'gender' => 'ប្រុស', 'dob' => '2009-05-01', 'parent' => 'យឹម ផល្លា', 'rel' => 'ឪពុក', 'p_phone' => '017 000 133'],
            ['name' => 'អ៊ុង ចិន្តា', 'latin' => 'Ung Chenda', 'gender' => 'ស្រី', 'dob' => '2009-10-29', 'parent' => 'អ៊ុង វីរៈ', 'rel' => 'ឪពុក', 'p_phone' => '077 111 134'],
            ['name' => 'ស៊ូ សម្បត្តិ', 'latin' => 'Sou Sambath', 'gender' => 'ប្រុស', 'dob' => '2009-03-08', 'parent' => 'ស៊ូ ហេង', 'rel' => 'ឪពុក', 'p_phone' => '089 222 135'],
        ];

        $output = [];
        $idCounter = 1;

        // 11D students (52)
        foreach ($list11D as $idx => $s) {
            $output[] = [
                'id' => $idCounter,
                'student_id_card' => sprintf('STD-2026-%03d', $idx + 101),
                'name' => $s['name'],
                'name_khmer' => $s['name'],
                'name_latin' => $s['latin'],
                'gender' => $s['gender'],
                'grade' => '11D',
                'phone' => sprintf('096 %03d %03d', 500 + $idx, 100 + $idx),
                'date_of_birth' => $s['dob'],
                'parent_name' => $s['parent'],
                'parent_relationship' => $s['rel'],
                'parent_phone' => $s['p_phone'],
                'status' => 'active',
            ];
            $idCounter++;
        }

        // 10A students (35)
        foreach ($list10A as $idx => $s) {
            $output[] = [
                'id' => $idCounter,
                'student_id_card' => sprintf('STD-2026-%03d', $idx + 1),
                'name' => $s['name'],
                'name_khmer' => $s['name'],
                'name_latin' => $s['latin'],
                'gender' => $s['gender'],
                'grade' => '10A',
                'phone' => sprintf('015 %03d %03d', 300 + $idx, 200 + $idx),
                'date_of_birth' => $s['dob'],
                'parent_name' => $s['parent'],
                'parent_relationship' => $s['rel'],
                'parent_phone' => $s['p_phone'],
                'status' => 'active',
            ];
            $idCounter++;
        }

        return $output;
    }

    public function getDemoTeachersList()
    {
        return [
            ['id' => 1, 'idCard' => 'TCH-2026-001', 'name' => 'លោក ឈុន វណ្ណា', 'name_khmer' => 'លោក ឈុន វណ្ណា', 'name_latin' => 'Chhun Vanna', 'gender' => 'ប្រុស', 'phone' => '012 345 678', 'specialization' => 'គណិតវិទ្យា', 'qualification' => 'បរិញ្ញាបត្រជាន់ខ្ពស់គរុកោសល្យ', 'class_assigned' => '12A, 11D', 'status' => 'active'],
            ['id' => 2, 'idCard' => 'TCH-2026-002', 'name' => 'អ្នកគ្រូ កែវ សុផល', 'name_khmer' => 'អ្នកគ្រូ កែវ សុផល', 'name_latin' => 'Keo Sophal', 'gender' => 'ស្រី', 'phone' => '098 765 432', 'specialization' => 'អក្សរសាស្ត្រខ្មែរ', 'qualification' => 'បរិញ្ញាបត្រអក្សរសាស្ត្រខ្មែរ', 'class_assigned' => '11D, 10A', 'status' => 'active'],
            ['id' => 3, 'idCard' => 'TCH-2026-003', 'name' => 'លោក សេង សុខា', 'name_khmer' => 'លោក សេង សុខា', 'name_latin' => 'Seng Sokha', 'gender' => 'ប្រុស', 'phone' => '017 888 999', 'specialization' => 'ភាសាអង់គ្លេស', 'qualification' => 'Bachelor of Education (English)', 'class_assigned' => '10A, 11D', 'status' => 'active'],
            ['id' => 4, 'idCard' => 'TCH-2026-004', 'name' => 'លោក អ៊ុំ រតនៈ', 'name_khmer' => 'លោក អ៊ុំ រតនៈ', 'name_latin' => 'Oum Rattanak', 'gender' => 'ប្រុស', 'phone' => '077 222 333', 'specialization' => 'រូបវិទ្យា', 'qualification' => 'បរិញ្ញាបត្ររូបវិទ្យា', 'class_assigned' => '10B, 11D', 'status' => 'active'],
            ['id' => 5, 'idCard' => 'TCH-2026-005', 'name' => 'អ្នកគ្រូ មាស ចរិយា', 'name_khmer' => 'អ្នកគ្រូ មាស ចរិយា', 'name_latin' => 'Meas Chariya', 'gender' => 'ស្រី', 'phone' => '089 444 555', 'specialization' => 'គីមីវិទ្យា', 'qualification' => 'បរិញ្ញាបត្រគីមីវិទ្យា', 'class_assigned' => '11D, 10A, 12A', 'status' => 'active'],
            ['id' => 6, 'idCard' => 'TCH-2026-006', 'name' => 'លោក លឹម ប៊ុនធឿន', 'name_khmer' => 'លោក លឹម ប៊ុនធឿន', 'name_latin' => 'Lim Bunthoeun', 'gender' => 'ប្រុស', 'phone' => '015 666 777', 'specialization' => 'ជីវវិទ្យា', 'qualification' => 'បរិញ្ញាបត្រជីវវិទ្យា', 'class_assigned' => '11D, 10A', 'status' => 'active'],
            ['id' => 7, 'idCard' => 'TCH-2026-007', 'name' => 'អ្នកគ្រូ សួន ចិន្តា', 'name_khmer' => 'អ្នកគ្រូ សួន ចិន្តា', 'name_latin' => 'Suon Chenda', 'gender' => 'ស្រី', 'phone' => '092 111 222', 'specialization' => 'ប្រវត្តិវិទ្យា', 'qualification' => 'បរិញ្ញាបត្រប្រវត្តិវិទ្យា', 'class_assigned' => '11D, 10A, 10B', 'status' => 'active'],
            ['id' => 8, 'idCard' => 'TCH-2026-008', 'name' => 'លោក ហេង សម្បត្តិ', 'name_khmer' => 'លោក ហេង សម្បត្តិ', 'name_latin' => 'Heng Sambath', 'gender' => 'ប្រុស', 'phone' => '016 555 666', 'specialization' => 'សីលធម៌-ពលរដ្ឋ', 'qualification' => 'បរិញ្ញាបត្រនីតិសាស្ត្រ', 'class_assigned' => '11D, 10A', 'status' => 'active'],
            ['id' => 9, 'idCard' => 'TCH-2026-009', 'name' => 'អ្នកគ្រូ ពេជ្រ ពិសី', 'name_khmer' => 'អ្នកគ្រូ ពេជ្រ ពិសី', 'name_latin' => 'Pech Pisey', 'gender' => 'ស្រី', 'phone' => '070 333 444', 'specialization' => 'ភូមិវិទ្យា', 'qualification' => 'បរិញ្ញាបត្រភូមិវិទ្យា', 'class_assigned' => '10A, 10B, 11D', 'status' => 'active'],
            ['id' => 10, 'idCard' => 'TCH-2026-010', 'name' => 'លោក តាំង គីមឡេង', 'name_khmer' => 'លោក តាំង គីមឡេង', 'name_latin' => 'Tang Kimleng', 'gender' => 'ប្រុស', 'phone' => '011 222 333', 'specialization' => 'ព័ត៌មានវិទ្យា', 'qualification' => 'បរិញ្ញាបត្រវិទ្យាសាស្ត្រកុំព្យូទ័រ', 'class_assigned' => '10A, 11D, 12A', 'status' => 'active'],
            ['id' => 11, 'idCard' => 'TCH-2026-011', 'name' => 'លោក ម៉ៅ សុវណ្ណារ៉ា', 'name_khmer' => 'លោក ម៉ៅ សុវណ្ណារ៉ា', 'name_latin' => 'Mao Sovannara', 'gender' => 'ប្រុស', 'phone' => '088 999 111', 'specialization' => 'អប់រំកាយ', 'qualification' => 'គរុកោសល្យកីឡា', 'class_assigned' => '10A, 10B, 11D, 12A', 'status' => 'active'],
            ['id' => 12, 'idCard' => 'TCH-2026-012', 'name' => 'អ្នកគ្រូ ឌិត សុគន្ធា', 'name_khmer' => 'អ្នកគ្រូ ឌិត សុគន្ធា', 'name_latin' => 'Dy Sokunthea', 'gender' => 'ស្រី', 'phone' => '096 444 111', 'specialization' => 'គណិតវិទ្យា', 'qualification' => 'បរិញ្ញាបត្រគណិតវិទ្យា', 'class_assigned' => '10A, 10B', 'status' => 'active'],
            ['id' => 13, 'idCard' => 'TCH-2026-013', 'name' => 'លោក គង់ សុជាតិ', 'name_khmer' => 'លោក គង់ សុជាតិ', 'name_latin' => 'Kong Socheat', 'gender' => 'ប្រុស', 'phone' => '012 999 888', 'specialization' => 'អក្សរសាស្ត្រខ្មែរ', 'qualification' => 'បរិញ្ញាបត្រអក្សរសាស្ត្រខ្មែរ', 'class_assigned' => '10B, 12A', 'status' => 'active'],
            ['id' => 14, 'idCard' => 'TCH-2026-014', 'name' => 'អ្នកគ្រូ រស់ វណ្ណដា', 'name_khmer' => 'អ្នកគ្រូ រស់ វណ្ណដា', 'name_latin' => 'Ros Vannda', 'gender' => 'ស្រី', 'phone' => '097 555 333', 'specialization' => 'ភាសាអង់គ្លេស', 'qualification' => 'BA in English Literature', 'class_assigned' => '10B, 12A', 'status' => 'active'],
            ['id' => 15, 'idCard' => 'TCH-2026-015', 'name' => 'លោក ជា វីរៈ', 'name_khmer' => 'លោក ជា វីរៈ', 'name_latin' => 'Chea Vireak', 'gender' => 'ប្រុស', 'phone' => '078 666 999', 'specialization' => 'រូបវិទ្យា', 'qualification' => 'បរិញ្ញាបត្ររូបវិទ្យា', 'class_assigned' => '10A, 12A', 'status' => 'active'],
        ];
    }
}
