<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    /**
     * Show the form for creating/editing scores for a student.
     */
    public function create($studentId)
    {
        // Check permission
        $user = Auth::user();
        if (!$user || (! $user->hasPermission('add-scores') && 
            ! $user->hasRole('teacher') && 
            ! $user->hasRole('admin'))) {
            abort(403, 'អ្នកមិនមានសិទ្ធិក្នុងការបញ្ចូលពិន្ទុទេ។');
        }

        $student = Student::with('user', 'latestSubject')->findOrFail($studentId);
        
        return view('subject.create', compact('student'));
    }

    /**
     * Store or update scores for a student.
     * Note: This REPLACES existing scores, does not add to them.
     */
    public function store(Request $request, $studentId)
    {
        // Check permission
        $user = Auth::user();
        if (!$user || (! $user->hasPermission('add-scores') && 
            ! $user->hasRole('teacher') && 
            ! $user->hasRole('admin'))) {
            return redirect()->back()->with('error', 'អ្នកមិនមានសិទ្ធិក្នុងការបញ្ចូលពិន្ទុទេ។');
        }

        $student = Student::findOrFail($studentId);

        // Validate input
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

        // Calculate total and average (only from entered scores)
        $scores = array_filter($validated, fn($value) => !is_null($value) && $value !== '');
        $total = array_sum($scores);
        $average = count($scores) > 0 ? $total / count($scores) : 0;

        // Determine rank based on average
        $rank = $this->calculateRank($average);

        // Prepare data to save (replace all fields, including nulls)
        $dataToSave = [
            'math' => $validated['math'] ?? null,
            'khmer' => $validated['khmer'] ?? null,
            'english' => $validated['english'] ?? null,
            'history' => $validated['history'] ?? null,
            'geography' => $validated['geography'] ?? null,
            'chemistry' => $validated['chemistry'] ?? null,
            'physics' => $validated['physics'] ?? null,
            'biology' => $validated['biology'] ?? null,
            'ethics' => $validated['ethics'] ?? null,
            'sports' => $validated['sports'] ?? null,
            'total' => $total,
            'average' => round($average, 2),
            'rank' => $rank,
        ];

        // Check if subject record exists for this student
        $subject = Subject::where('student_id', $studentId)->first();

        if ($subject) {
            // REPLACE existing scores completely (not add to them)
            $subject->update($dataToSave);
            $message = 'ពិន្ទុត្រូវបានកែប្រែដោយជោគជ័យ! (ពិន្ទុចាស់ត្រូវបានជំនួស)';
        } else {
            // Create new record
            Subject::create(array_merge($dataToSave, [
                'student_id' => $studentId,
            ]));
            $message = 'ពិន្ទុត្រូវបានរក្សាទុកដោយជោគជ័យ!';
        }

        return redirect()->route('subject.create', $studentId)
            ->with('success', $message);
    }

    /**
     * Calculate rank based on average score.
     */
    private function calculateRank($average)
    {
        if ($average >= 90) {
            return 'A';
        } elseif ($average >= 80) {
            return 'B';
        } elseif ($average >= 70) {
            return 'C';
        } elseif ($average >= 60) {
            return 'D';
        } elseif ($average >= 50) {
            return 'E';
        } else {
            return 'F';
        }
    }

    /**
     * Display all subjects/scores.
     */
    public function index()
    {
        // Check permission
        $user = Auth::user();
        if (!$user || (! $user->hasPermission('view-scores') && 
            ! $user->hasRole('teacher') && 
            ! $user->hasRole('admin'))) {
            abort(403, 'អ្នកមិនមានសិទ្ធិមើលពិន្ទុទេ។');
        }

        $subjects = Subject::with('student.user')->get();
        
        return view('subject.index', compact('subjects'));
    }

    /**
     * Show a specific student's scores.
     */
    public function show($studentId)
    {
        // Check permission
        $user = Auth::user();
        if (!$user || (! $user->hasPermission('view-scores') && 
            ! $user->hasRole('teacher') && 
            ! $user->hasRole('admin'))) {
            abort(403, 'អ្នកមិនមានសិទ្ធិមើលពិន្ទុទេ។');
        }

        $student = Student::with('user', 'subjects')->findOrFail($studentId);
        
        return view('subject.show', compact('student'));
    }

    /**
     * Delete a subject/scores (admins only).
     */
    public function destroy($subjectId)
    {
        // Only admins can delete scores
        $user = Auth::user();
        if (!$user || ! $user->hasRole('admin')) {
            abort(403, 'មានតែអ្នកគ្រប់គ្រងទេដែលអាចលុបពិន្ទុបាន។');
        }

        $subject = Subject::findOrFail($subjectId);
        $subject->delete();

        return redirect()->back()->with('success', 'ពិន្ទុត្រូវបានលុបដោយជោគជ័យ!');
    }
}