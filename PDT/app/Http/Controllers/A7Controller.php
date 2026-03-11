<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\A7;
use App\Models\Student;
use App\Models\User;
class A7Controller extends Controller
{
   
public function index()
{
     // Get all students with grade 7A and load their user
    $students = Student::with('user')   // eager load user
                        ->where('grade', '7A')
                        ->get();

    return view('class.7A.index', compact('students'));
}
}

