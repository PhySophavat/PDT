<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
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
    // Example: "7A", "9B", etc.
    $students = \App\Models\Student::with('user')
        ->where('grade', $className)
        ->get();

    return view('result.show', compact('students', 'className'));
}
}
