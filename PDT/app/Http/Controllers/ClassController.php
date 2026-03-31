<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()?->canAccessClassesOverview(), 403, 'Unauthorized action.');

       
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

   public function show(Request $request, $className)
{
    abort_unless(Auth::user()?->canAccessClass($className), 403, 'Unauthorized action.');

    $search = trim((string) $request->input('search', ''));

  
    $students = \App\Models\Student::with('user')
        ->where('grade', $className)
        ->searchByUserName($search)
        ->get();

    return view('class.show', compact('students', 'className', 'search'));
}

}
