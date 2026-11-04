<?php

namespace App\Http\Controllers;

use App\Models\CourseSubject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class CourseSubjectController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $subjects = CourseSubject::when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12);

        $teachers = Teacher::with('user')->get();

        return view('course_subject.index', compact('subjects', 'search', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:course_subjects,code',
            'name' => 'required|string|max:255',
            'subject_type' => 'nullable|string|max:50',
            'credit_hours' => 'required|integer|min:1|max:10',
            'grade_level' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        CourseSubject::create($validated);

        return redirect()->route('course-subjects.index')->with('success', 'បានបន្ថែមមុខវិជ្ជាថ្មីដោយជោគជ័យ!');
    }

    public function update(Request $request, CourseSubject $courseSubject)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:course_subjects,code,' . $courseSubject->id,
            'name' => 'required|string|max:255',
            'subject_type' => 'nullable|string|max:50',
            'credit_hours' => 'required|integer|min:1|max:10',
            'grade_level' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $courseSubject->update($validated);

        return redirect()->route('course-subjects.index')->with('success', 'បានកែប្រែព័ត៌មានមុខវិជ្ជាដោយជោគជ័យ!');
    }

    public function destroy(CourseSubject $courseSubject)
    {
        $courseSubject->delete();

        return redirect()->route('course-subjects.index')->with('success', 'បានលុបមុខវិជ្ជាដោយជោគជ័យ!');
    }
}
