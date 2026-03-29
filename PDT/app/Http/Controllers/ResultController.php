<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()?->canAccessResultsOverview(), 403, 'Unauthorized action.');

        // Example class structure: dynamic & reusable
        $grades = [
            7 => ['A','B','C','D','E','F','G','H'],
            8 => ['A','B','C','D','E','F','G','H'],
            9 => ['A','B','C','D','E','F','G','H'],
            10 => ['A','B','C','D','E','F','G','H'],
            11 => ['A','B','C','D','E','F','G','H'],
            12 => ['A','B','C','D','E','F','G','H'],
        ];

        return view('result.index', compact('grades'));
    }

    public function show($className)
    {
        abort_unless(Auth::user()?->canAccessResultClass($className), 403, 'Unauthorized action.');

        $students = Student::with(['user', 'latestSubject'])
            ->where('grade', $className)
            ->get();

        $subjectLabels = Subject::scoreLabels();

        return view('result.show', compact('students', 'className', 'subjectLabels'));
    }
}
