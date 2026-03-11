<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Count totals
        $users = User::count();
        $students = Student::count();
        $teachers = Teacher::count();

        // Count students by gender (ប្រុស/ស្រី)
        $maleStudents = Student::where('gender', 'ប្រុស')->count();
        $femaleStudents = Student::where('gender', 'ស្រី')->count();

        return view('dashboard', compact(
            'users',
            'students',
            'teachers',
            'maleStudents',
            'femaleStudents'
        ));
    }

    public function profile()
    {
        return view('auth.profile');
    }
}
