<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\AcademicYear;

class ClassController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()?->canAccessClassesOverview(), 403, 'Unauthorized action.');

        $gradesList = [7, 8, 9, 10, 11, 12];
        $sectionsList = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        // Pull structured classes from database
        $dbClasses = SchoolClass::with('teacher.user')
            ->withCount('students')
            ->get()
            ->keyBy('name');

        $grades = [];
        $classes = [];
        foreach ($gradesList as $grade) {
            $grades[$grade] = $sectionsList;
            foreach ($sectionsList as $section) {
                $classes[] = $grade . $section;
            }
        }

        // Additional custom classes if any
        $customClasses = SchoolClass::whereNotIn('grade', [7, 8, 9, 10, 11, 12])
            ->orWhereNotIn('section', $sectionsList)
            ->get();

        foreach ($customClasses as $c) {
            if (!in_array($c->name, $classes)) {
                $classes[] = $c->name;
            }
        }

        $studentCounts = Student::select('grade', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->whereNotNull('grade')
            ->groupBy('grade')
            ->pluck('count', 'grade')
            ->toArray();

        $teachers = Teacher::with('user')->get();
        $academicYears = AcademicYear::where('status', 'active')->get();

        return view('class.index', compact('grades', 'classes', 'studentCounts', 'dbClasses', 'customClasses', 'teachers', 'academicYears'));
    }

    public function show(Request $request, $className)
    {
        abort_unless(Auth::user()?->canAccessClass($className), 403, 'Unauthorized action.');

        $search = trim((string) $request->input('search', ''));

        $classInfo = SchoolClass::where('name', $className)->with('teacher.user', 'academicYear')->first();

        $students = Student::with('user')
            ->where('grade', $className)
            ->searchByUserName($search)
            ->get();

        $allClasses = SchoolClass::orderBy('name')->pluck('name');
        $teachers = Teacher::with('user')->get();

        return view('class.show', compact('students', 'className', 'classInfo', 'allClasses', 'teachers', 'search'));
    }

    public function store(Request $request)
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

        SchoolClass::updateOrCreate(['name' => $name], $validated);

        return redirect()->route('class.index')->with('success', "បានបង្កើត/កែប្រែថ្នាក់ {$name} ដោយជោគជ័យ!");
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'room_number' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1',
            'teacher_id' => 'nullable|exists:teachers,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'description' => 'nullable|string',
        ]);

        $class->update($validated);

        return back()->with('success', 'បានកែប្រែព័ត៌មានថ្នាក់ដោយជោគជ័យ!');
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

        return back()->with('success', "បានផ្លាស់ប្តូរសិស្ស {$student->user->name} ពីថ្នាក់ {$oldClass} ទៅថ្នាក់ {$validated['target_class']} ដោយជោគជ័យ!");
    }

    public function promoteStudents(Request $request)
    {
        $validated = $request->validate([
            'current_class' => 'required|string|max:50',
            'next_class' => 'required|string|max:50',
        ]);

        $count = Student::where('grade', $validated['current_class'])
            ->update(['grade' => $validated['next_class']]);

        return back()->with('success', "បានឡើងថ្នាក់សិស្សចំនួន {$count} នាក់ ពីថ្នាក់ {$validated['current_class']} ទៅថ្នាក់ {$validated['next_class']}!");
    }
}
