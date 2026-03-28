<?php

namespace App\Http\Controllers;

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

    // Example: "7A", "9B", etc.
    $students = \App\Models\Student::with('user')
        ->where('grade', $className)
        ->get();

    return view('result.show', compact('students', 'className'));
}
}
