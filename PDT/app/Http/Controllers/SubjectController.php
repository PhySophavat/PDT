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

        if (!$user || !$user->canManageScores() || !$user->canAccessClass((string) $student->grade)) {
            abort(403, 'Unauthorized action.');
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

        if (!$user || !$user->canManageScores() || !$user->canAccessClass((string) $student->grade)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
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
        ]);

        $editableFields = $user->editableSubjectFields();

        if ($editableFields === []) {
            return redirect()->back()->with('error', 'No subject permissions assigned to this user.');
        }

        $dataToSave = [];
        foreach (self::SCORE_FIELDS as $field) {
            if (in_array($field, $editableFields, true)) {
                $dataToSave[$field] = $validated[$field] ?? null;
            }
        }

        $subject = Subject::firstOrNew(['student_id' => $studentId]);
        $wasRecentlyCreated = !$subject->exists;
        $subject->fill($dataToSave);
        $subject->student_id = $studentId;
        $subject->save();

        $message = $wasRecentlyCreated
            ? 'áž–áž·áž“áŸ’áž‘áž»ážáŸ’ážšáž¼ážœáž”áž¶áž“ážšáž€áŸ’ážŸáž¶áž‘áž»áž€ážŠáŸ„áž™áž‡áŸ„áž‚áž‡áŸáž™!'
            : 'áž–áž·áž“áŸ’áž‘áž»ážáŸ’ážšáž¼ážœáž”áž¶áž“áž€áŸ‚áž”áŸ’ážšáŸ‚ážŠáŸ„áž™áž‡áŸ„áž‚áž‡áŸáž™!';

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
            abort(403, 'Unauthorized action.');
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
            abort(403, 'Unauthorized action.');
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
            abort(403, 'Unauthorized action.');
        }

        $subject = Subject::findOrFail($subjectId);
        $subject->delete();

        return redirect()->back()->with('success', 'áž–áž·áž“áŸ’áž‘áž»աժáŸ’ážšáž¼ážœáž”áž¶áž“áž›áž»áž”ážŠáŸ„áž™áž‡áŸ„áž‚áž‡áŸáž™!');
    }
}
