<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    private const SCORE_FIELDS = [
        'math',
        'khmer',
        'english',
        'history',
        'geography',
        'chemistry',
        'physics',
        'biology',
        'ethics',
        'sports',
    ];

    /**
     * Show the form for creating/editing scores for a student.
     */
    public function create($studentId)
    {
        $user = Auth::user();
        $student = Student::with('user', 'latestSubject')->findOrFail($studentId);

        if (
            !$user
            || !$user->canAccessClass((string) $student->grade)
            || !$user->canEditAnySubjectField()
        ) {
            abort(403, 'មិនមានសិទ្ធិចូលប្រើប្រាស់ទំព័រនេះទេ។');
        }

        return view('subject.create', compact('student'));
    }

    /**
     * Store or update scores for a student.
     */
    public function store(Request $request, $studentId)
    {
        $user = Auth::user();
        $student = Student::findOrFail($studentId);

        if (
            !$user
            || !$user->canAccessClass((string) $student->grade)
            || !$user->canEditAnySubjectField()
        ) {
            abort(403, 'មិនមានសិទ្ធិចូលប្រើប្រាស់ទំព័រនេះទេ។');
        }

        $validated = $request->validate(
            [
                'math' => 'nullable|numeric|min:0|max:100',
                'khmer' => 'nullable|numeric|min:0|max:100',
                'english' => 'nullable|numeric|min:0|max:100',
                'history' => 'nullable|numeric|min:0|max:100',
                'geography' => 'nullable|numeric|min:0|max:100',
                'chemistry' => 'nullable|numeric|min:0|max:100',
                'physics' => 'nullable|numeric|min:0|max:100',
                'biology' => 'nullable|numeric|min:0|max:100',
                'ethics' => 'nullable|numeric|min:0|max:100',
                'sports' => 'nullable|numeric|min:0|max:100',
                'coefficient' => 'nullable|numeric|min:0.01|max:9999',
                'coefficient_changed' => 'nullable|boolean',
            ],
            [
                'coefficient.numeric' => 'មេគុណត្រូវតែជាលេខ។',
                'coefficient.min' => 'មេគុណត្រូវតែធំជាង 0។',
                'coefficient.max' => 'មេគុណធំពេកហើយ។',
            ]
        );

        $editableFields = $user->editableSubjectFields();

        if ($editableFields === []) {
            return redirect()->back()->with('error', 'មិនទាន់មានសិទ្ធិមុខវិជ្ជាដែលអាចកែបានទេ។');
        }

        $subject = Subject::firstOrNew(['student_id' => $studentId]);
        $dataToSave = [];

        foreach (self::SCORE_FIELDS as $field) {
            if (in_array($field, $editableFields, true)) {
                $dataToSave[$field] = $validated[$field] ?? null;
            }
        }

        $coefficientWasChanged = $request->boolean('coefficient_changed');
        $existingCoefficient = ($subject->coefficient && $subject->coefficient > 0)
            ? (float) $subject->coefficient
            : 1.0;

        if (!$subject->exists && !$coefficientWasChanged) {
            $dataToSave['coefficient'] = $request->filled('coefficient')
                ? (float) $validated['coefficient']
                : 1.0;
        } elseif ($coefficientWasChanged) {
            $dataToSave['coefficient'] = $request->filled('coefficient')
                ? (float) $validated['coefficient']
                : 1.0;
        } else {
            $dataToSave['coefficient'] = $existingCoefficient;
        }

        $wasRecentlyCreated = !$subject->exists;
        $subject->fill($dataToSave);
        $subject->student_id = $studentId;
        $subject->save();

        $message = $wasRecentlyCreated
            ? 'បានរក្សាទុកពិន្ទុដោយជោគជ័យ។'
            : 'បានកែប្រែពិន្ទុដោយជោគជ័យ។';

        return redirect()->route('subject.create', $studentId)
            ->with('success', $message);
    }

    /**
     * Display all subjects/scores.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user || !$user->canViewAllScoreRecords()) {
            abort(403, 'មិនមានសិទ្ធិចូលប្រើប្រាស់ទំព័រនេះទេ។');
        }

        $subjects = Subject::with('student.user')->get();

        return view('subject.index', compact('subjects'));
    }

    /**
     * Show a specific student's scores.
     */
    public function show($studentId)
    {
        $student = Student::with('user', 'subjects')->findOrFail($studentId);
        $user = Auth::user();

        if (!$user || (!$user->canViewAllScoreRecords() && !$user->canAccessResultClass((string) $student->grade))) {
            abort(403, 'មិនមានសិទ្ធិចូលប្រើប្រាស់ទំព័រនេះទេ។');
        }

        return view('subject.show', compact('student'));
    }

    /**
     * Delete a subject/scores (admins only).
     */
    public function destroy($subjectId)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'មិនមានសិទ្ធិចូលប្រើប្រាស់ទំព័រនេះទេ।');
        }

        $subject = Subject::findOrFail($subjectId);
        $subject->delete();

        return redirect()->back()->with('success', 'បានលុបកំណត់ត្រាពិន្ទុដោយជោគជ័យ។');
    }
}
