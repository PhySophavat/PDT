<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index()
    {
        $students = Student::with('user')->get();
        return view('student.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('student.create');
    }

    /**
     * Store a newly created student along with linked user account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'grade' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'nullable|string',
            'village' => 'nullable|string',
            'commune' => 'nullable|string',
            'district' => 'nullable|string',
            'province' => 'nullable|string',
            'phone' => 'nullable|string',
            'high_school' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'mother_phone' => 'nullable|string',
            'father_name' => 'nullable|string',
            'father_phone' => 'nullable|string',
        ]);

        // Create User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign student role
        $studentRole = Role::where('name', 'student')->first();
        if ($studentRole) {
            $user->roles()->syncWithoutDetaching([$studentRole->id]);
        }

        // Create Student linked to user
        Student::create([
            'user_id' => $user->id,
            'grade' => $validated['grade'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'village' => $validated['village'] ?? null,
            'commune' => $validated['commune'] ?? null,
            'district' => $validated['district'] ?? null,
            'province' => $validated['province'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'high_school' => $validated['high_school'] ?? null,
            'mother_name' => $validated['mother_name'] ?? null,
            'mother_phone' => $validated['mother_phone'] ?? null,
            'father_name' => $validated['father_name'] ?? null,
            'father_phone' => $validated['father_phone'] ?? null,
        ]);

        return redirect()->route('students.index')->with('success', 'Student added successfully!');
    }

    /**
     * Show the form for editing a student.
     */
    public function edit(Student $student)
    {
        return view('student.edit', compact('student'));
    }

    /**
     * Update the specified student and linked user.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'password' => 'nullable|string|min:6',
            'grade' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
            'village' => 'nullable|string',
            'commune' => 'nullable|string',
            'district' => 'nullable|string',
            'province' => 'nullable|string',
            'phone' => 'nullable|string',
            'high_school' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'mother_phone' => 'nullable|string',
            'father_name' => 'nullable|string',
            'father_phone' => 'nullable|string',
        ]);

        // Update User
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }
        $student->user->update($userData);

        // Update Student
        $student->update([
            'grade' => $validated['grade'] ?? $student->grade,
            'date_of_birth' => $validated['date_of_birth'] ?? $student->date_of_birth,
            'gender' => $validated['gender'] ?? $student->gender,
            'village' => $validated['village'] ?? $student->village,
            'commune' => $validated['commune'] ?? $student->commune,
            'district' => $validated['district'] ?? $student->district,
            'province' => $validated['province'] ?? $student->province,
            'phone' => $validated['phone'] ?? $student->phone,
            'high_school' => $validated['high_school'] ?? $student->high_school,
            'mother_name' => $validated['mother_name'] ?? $student->mother_name,
            'mother_phone' => $validated['mother_phone'] ?? $student->mother_phone,
            'father_name' => $validated['father_name'] ?? $student->father_name,
            'father_phone' => $validated['father_phone'] ?? $student->father_phone,
        ]);

        return redirect()->route('students.index')->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified student and linked user.
     */
    public function destroy(Student $student)
    {
        $student->user->delete();
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully!');
    }
}
