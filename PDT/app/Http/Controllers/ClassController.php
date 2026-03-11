<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
       
        $grades = [
            7 => ['A','B','C','D','E','F','G','H'],
            8 => ['A','B','C','D','E','F','G','H'],
            9 => ['A','B','C','D','E','F','G','H'],
            10 => ['A','B','C','D','E','F','G','H'],
            11 => ['A','B','C','D','E','F','G','H'],
            12 => ['A','B','C','D','E','F','G','H'],
        ];

        return view('class.index', compact('grades'));
    }

   public function show($className)
{
  
    $students = \App\Models\Student::with('user')
        ->where('grade', $className)
        ->get();

    return view('class.show', compact('students', 'className'));
}

}
