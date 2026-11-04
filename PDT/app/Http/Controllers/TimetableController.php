<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use App\Models\SchoolClass;
use App\Models\CourseSubject;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $selectedClass = $request->input('class', '7A');
        $selectedTeacher = $request->input('teacher_id');

        $classes = SchoolClass::orderBy('name')->pluck('name');
        $teachers = Teacher::with('user')->get();
        $subjects = CourseSubject::orderBy('name')->get();
        $academicYears = AcademicYear::where('status', 'active')->get();

        $days = ['ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'];

        $query = Timetable::with('courseSubject', 'teacher.user');

        if ($selectedTeacher) {
            $query->where('teacher_id', $selectedTeacher);
        } else {
            $query->where('class_name', $selectedClass);
        }

        $timetables = $query->orderBy('start_time')->get()->groupBy('day_of_week');

        return view('timetable.index', compact(
            'timetables',
            'classes',
            'teachers',
            'subjects',
            'academicYears',
            'selectedClass',
            'selectedTeacher',
            'days'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:50',
            'course_subject_id' => 'required|exists:course_subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day_of_week' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room_number' => 'nullable|string|max:50',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        // 1. Conflict Check: Teacher schedule conflict on the same day & overlapping time
        $teacherConflict = Timetable::where('teacher_id', $validated['teacher_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                  ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                  ->orWhere(function ($sub) use ($validated) {
                      $sub->where('start_time', '<=', $validated['start_time'])
                          ->where('end_time', '>=', $validated['end_time']);
                  });
            })
            ->first();

        if ($teacherConflict) {
            return back()->with('error', "គ្រូបង្រៀនរូបនេះមានម៉ោងបង្រៀននៅថ្នាក់ {$teacherConflict->class_name} ក្នុងចន្លោះម៉ោងនេះរួចហើយ!");
        }

        // 2. Conflict Check: Classroom schedule conflict
        if (!empty($validated['room_number'])) {
            $roomConflict = Timetable::where('room_number', $validated['room_number'])
                ->where('day_of_week', $validated['day_of_week'])
                ->where(function ($q) use ($validated) {
                    $q->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
                })
                ->first();

            if ($roomConflict) {
                return back()->with('error', "បន្ទប់ {$validated['room_number']} ត្រូវបានប្រើប្រាស់ដោយថ្នាក់ {$roomConflict->class_name} ក្នុងចន្លោះម៉ោងនេះរួចហើយ!");
            }
        }

        Timetable::create($validated);

        return back()->with('success', 'បានបញ្ចូលកាលវិភាគបង្រៀនថ្មីដោយជោគជ័យ!');
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();

        return back()->with('success', 'បានលុបម៉ោងបង្រៀនចេញពីកាលវិភាគ!');
    }
}
