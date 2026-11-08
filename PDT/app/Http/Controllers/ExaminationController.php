<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\ExaminationScore;
use App\Models\Student;
use App\Models\CourseSubject;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class ExaminationController extends Controller
{
    public function index()
    {
        $examinations = Examination::with('academicYear')
            ->withCount('scores')
            ->latest()
            ->paginate(10);

        $academicYears = AcademicYear::where('status', 'active')->get();

        return view('examination.index', compact('examinations', 'academicYears'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user && ($user->hasRole('student') || strtolower($user->role ?? '') === 'student')) {
            abort(403, 'Unauthorized. Students cannot create examinations.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_type' => 'required|string',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'semester' => 'required|integer|in:1,2',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_published' => 'nullable|boolean',
        ]);

        Examination::create($validated);

        return redirect()->route('examinations.index')->with('success', 'បានបង្កើតការប្រឡងថ្មីដោយជោគជ័យ!');
    }

    /**
     * Enter or view scores for an exam and class.
     */
    public function scores(Request $request, Examination $examination)
    {
        $selectedClass = $request->input('class', '7A');

        $classes = SchoolClass::orderedNames();
        $subjects = CourseSubject::orderBy('name')->get();
        $rawStudents = Student::with('user')->where('grade', $selectedClass)->get();
        if (class_exists('Collator')) {
            $collator = new \Collator('km');
            $students = $rawStudents->sort(function ($a, $b) use ($collator) {
                $nameA = $a->name_khmer ?: ($a->display_name ?? '');
                $nameB = $b->name_khmer ?: ($b->display_name ?? '');
                $cmp = $collator->compare((string) $nameA, (string) $nameB);
                return $cmp !== 0 ? $cmp : strnatcmp((string) $a->student_id_card, (string) $b->student_id_card);
            })->values();
        } else {
            $students = $rawStudents->sortBy(fn ($s) => $s->name_khmer ?: $s->display_name)->values();
        }

        $scores = ExaminationScore::where('examination_id', $examination->id)
            ->where('class_name', $selectedClass)
            ->get()
            ->groupBy('student_id');

        return view('examination.scores', compact('examination', 'selectedClass', 'classes', 'subjects', 'students', 'scores'));
    }

    /**
     * Store student scores batch for exam.
     */
    public function storeScores(Request $request, Examination $examination)
    {
        $user = auth()->user();
        if ($user && ($user->hasRole('student') || strtolower($user->role ?? '') === 'student')) {
            abort(403, 'Unauthorized. Students cannot enter or save examination scores.');
        }

        $validated = $request->validate([
            'class_name' => 'required|string',
            'course_subject_id' => 'required|exists:course_subjects,id',
            'scores' => 'required|array',
            'max_score' => 'nullable|numeric|min:1',
        ]);

        $className = $validated['class_name'];
        $subject = CourseSubject::findOrFail($validated['course_subject_id']);
        $maxScore = $validated['max_score'] ?? 100;

        $now = now();
        $records = [];
        foreach ($validated['scores'] as $studentId => $scoreVal) {
            if ($scoreVal !== null && $scoreVal !== '') {
                $scoreFloat = (float) $scoreVal;
                $gradeLetter = ExaminationScore::calculateSubjectGrade($scoreFloat, $maxScore);

                $records[] = [
                    'examination_id' => $examination->id,
                    'student_id' => $studentId,
                    'course_subject_id' => $subject->id,
                    'class_name' => $className,
                    'subject_name' => $subject->name,
                    'score' => $scoreFloat,
                    'max_score' => $maxScore,
                    'grade_letter' => $gradeLetter,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($records)) {
            ExaminationScore::upsert(
                $records,
                ['examination_id', 'student_id', 'course_subject_id'],
                ['class_name', 'subject_name', 'score', 'max_score', 'grade_letter', 'updated_at']
            );
        }

        return back()->with('success', "បានបញ្ចូលពិន្ទុមុខវិជ្ជា {$subject->name} សម្រាប់ថ្នាក់ {$className} ដោយជោគជ័យ!");
    }

    /**
     * Generate / Print Student Report Card.
     */
    public function reportCard(Examination $examination, Student $student)
    {
        $authUser = auth()->user();
        if ($authUser && ($authUser->hasRole('student') || strtolower($authUser->role ?? '') === 'student')) {
            $studentProfile = $authUser->student;
            if (!$studentProfile || $studentProfile->id !== $student->id) {
                abort(403, 'អ្នកមិនមានសិទ្ធិមើលព្រឹត្តិបត្រពិន្ទុរបស់អ្នកដទៃឡើយ។');
            }
        }

        $student->load('user', 'academicYear');

        $scores = ExaminationScore::with('courseSubject')
            ->where('examination_id', $examination->id)
            ->where('student_id', $student->id)
            ->get();

        $totalRawScore = 0;
        $totalCredits = 0;
        $subjectCount = $scores->count();
        foreach ($scores as $sc) {
            $subMax = (float)($sc->courseSubject?->getMaxScoreForClass($student->grade) ?: ($sc->max_score ?: 100));
            $sc->max_score = $subMax;
            $sc->grade_letter = ExaminationScore::calculateSubjectGrade($sc->score, $subMax);

            $cr = (float)($sc->courseSubject?->getCreditForClass($student->grade) ?: ($sc->courseSubject?->credit ?: 1.0));
            // MoEYS standard: 1 Credit = 50 Max Points. Total score is the direct sum of raw scores
            $totalRawScore += (float)$sc->score;
            $totalCredits += $cr;
        }

        $totalScore = round($totalRawScore, 2);
        $average = $totalCredits > 0 ? round($totalRawScore / $totalCredits, 2) : 0;
        $overallGrade = ExaminationScore::calculateAverageGrade($average);

        // Calculate student rank within class
        $allClassScores = ExaminationScore::where('examination_id', $examination->id)
            ->where('class_name', $student->grade)
            ->get()
            ->groupBy('student_id')
            ->map(fn ($items) => $items->sum('score'))
            ->sortDesc()
            ->values();

        $studentRank = $allClassScores->search(function ($val) use ($totalScore) {
            return $val == $totalScore;
        });
        $rank = $studentRank !== false ? $studentRank + 1 : 1;

        $schoolName = SystemSetting::get('school_name', 'សាលារៀន ជំនាន់ថ្មី');
        $schoolAddress = SystemSetting::get('school_address', 'រាជធានីភ្នំពេញ');
        $schoolPhone = SystemSetting::get('school_phone', '012 345 678');

        return view('examination.report_card', compact(
            'examination',
            'student',
            'scores',
            'totalScore',
            'average',
            'overallGrade',
            'rank',
            'schoolName',
            'schoolAddress',
            'schoolPhone'
        ));
    }

    /**
     * Generate / Print Official Class Monthly Ranking Sheet (2-Column Cambodian High School Format).
     */
    public function rankingSheet(Request $request, Examination $examination)
    {
        $className = $request->input('class', '11D');

        $authUser = auth()->user();
        if ($authUser && ($authUser->hasRole('student') || strtolower($authUser->role ?? '') === 'student')) {
            $studentProfile = $authUser->student;
            if ($studentProfile && $studentProfile->grade) {
                $className = $studentProfile->grade;
            }
        }

        $students = Student::with('user')
            ->where('grade', $className)
            ->get();

        $allScores = ExaminationScore::where('examination_id', $examination->id)
            ->where('class_name', $className)
            ->get()
            ->groupBy('student_id');

        $subjects = CourseSubject::all()->keyBy('id');

        $rows = $students->map(function ($st) use ($allScores, $subjects, $className) {
            $scores = $allScores->get($st->id) ?? collect();
            $subjectCount = $scores->count();
            
            $totalRawScore = 0;
            $totalCredits = 0;
            foreach ($scores as $sc) {
                $sub = $subjects->get($sc->course_subject_id);
                $cr = (float)($sub ? $sub->getCreditForClass($className) : 1.0);
                // MoEYS standard: 1 Credit = 50 Max Points. Total score is the direct sum of raw scores
                $totalRawScore += (float)$sc->score;
                $totalCredits += $cr;
            }
            $totalScore = round($totalRawScore, 2);
            $average = $totalCredits > 0 ? round($totalRawScore / $totalCredits, 2) : 0;
            $gradeLetter = ExaminationScore::calculateAverageGrade($average);

            // Separate surname and firstname
            $rawName = trim($st->name_khmer ?: ($st->user?->name ?: $st->display_name));
            $nameParts = preg_split('/\s+/', $rawName, 2);
            $surname = $nameParts[0] ?? '';
            $firstname = $nameParts[1] ?? '';

            return [
                'id' => $st->id,
                'student_id_card' => $st->student_id_card,
                'name' => $rawName,
                'surname' => $surname,
                'firstname' => $firstname,
                'gender' => $st->gender === 'female' ? 'ស្រី' : 'ប្រុស',
                'is_female' => $st->gender === 'female',
                'total_score' => $totalScore,
                'average' => $average,
                'grade_letter' => $gradeLetter,
            ];
        });

        // Sort descending by average / total score
        $sorted = $rows->sortByDesc('average')->values();

        // Assign ranks with ties handling
        $ranked = collect();
        $currentRank = 1;
        foreach ($sorted as $idx => $item) {
            if ($idx > 0 && $item['average'] < $sorted[$idx - 1]['average']) {
                $currentRank = $idx + 1;
            }
            $item['rank'] = $currentRank;
            $ranked->push($item);
        }

        $totalCount = $ranked->count();
        $femaleCount = $ranked->where('is_female', true)->count();

        // Grade Statistics
        $grades = ['A', 'B', 'C', 'D', 'E', 'F'];
        $gradeStats = [];
        foreach ($grades as $g) {
            $inGrade = $ranked->where('grade_letter', $g);
            $count = $inGrade->count();
            $female = $inGrade->where('is_female', true)->count();
            $pct = $totalCount > 0 ? round(($count / $totalCount) * 100, 2) : 0;
            $gradeStats[$g] = [
                'count' => $count,
                'female' => $female,
                'pct' => $pct,
            ];
        }

        // Split into Left (half 1) and Right (half 2)
        $half = (int) ceil($totalCount / 2);
        $leftRows = $ranked->slice(0, $half)->values();
        $rightRows = $ranked->slice($half)->values();

        $districtOffice = $request->input('district', SystemSetting::get('district_office', 'ការិយាល័យអប់រំស្រុក ស៊ីធរកណ្តាល'));
        $schoolName = $request->input('school', SystemSetting::get('school_name', 'វិទ្យាល័យ ព្រៃដើមថ្នឹង'));
        $academicYearName = $request->input('academic_year', $examination->academicYear?->name ?? '២០២៥ - ២០២៦');
        $principalName = $request->input('principal', SystemSetting::get('principal_name', 'សន វ៉ែន'));
        $teacherName = $request->input('teacher', SystemSetting::get('homeroom_teacher_' . $className, SystemSetting::get('teacher_name', 'ប៊ុន សុភា')));
        $sheetDate = $request->input('date', 'ថ្ងៃសៅរ៍ ទី២៨ ខែកុម្ភៈ ឆ្នាំ២០២៦');

        return view('examination.ranking_sheet', compact(
            'examination',
            'className',
            'leftRows',
            'rightRows',
            'totalCount',
            'femaleCount',
            'gradeStats',
            'districtOffice',
            'schoolName',
            'academicYearName',
            'principalName',
            'teacherName',
            'sheetDate'
        ));
    }

    /**
     * Save official ranking sheet customizable settings.
     */
    public function saveRankingSettings(Request $request)
    {
        $validated = $request->validate([
            'district_office' => 'nullable|string|max:255',
            'school_name' => 'nullable|string|max:255',
            'principal_name' => 'nullable|string|max:255',
            'teacher_name' => 'nullable|string|max:255',
            'class_name' => 'nullable|string|max:50',
        ]);

        if (!empty($validated['district_office'])) {
            SystemSetting::set('district_office', $validated['district_office']);
        }
        if (!empty($validated['school_name'])) {
            SystemSetting::set('school_name', $validated['school_name']);
        }
        if (!empty($validated['principal_name'])) {
            SystemSetting::set('principal_name', $validated['principal_name']);
        }
        if (!empty($validated['teacher_name'])) {
            if (!empty($validated['class_name'])) {
                SystemSetting::set('homeroom_teacher_' . $validated['class_name'], $validated['teacher_name']);
            }
            SystemSetting::set('teacher_name', $validated['teacher_name']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'បានរក្សាទុកព័ត៌មានដោយជោគជ័យ'
        ]);
    }
}
