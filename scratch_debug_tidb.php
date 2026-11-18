<?php
require 'PDT/vendor/autoload.php';
$app = require_once 'PDT/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\Attendance;
use App\Models\StudentLeaveRequest;

echo "=== STUDENTS in 12A ===\n";
$students = Student::where('grade', 'like', '%12A%')->get(['id', 'student_id_card', 'name_khmer', 'name_latin', 'grade', 'phone', 'parent_phone']);
foreach ($students as $s) {
    echo "ID: {$s->id} | Card: {$s->student_id_card} | Khmer: {$s->name_khmer} | Latin: {$s->name_latin} | Grade: {$s->grade}\n";
}

echo "\n=== ATTENDANCES on 2026-11-18 ===\n";
$atts = Attendance::where('date', '2026-11-18')->get();
foreach ($atts as $a) {
    echo "ID: {$a->id} | StudentID: {$a->student_id} | Class: {$a->class_name} | Date: {$a->date} | Status: {$a->status} | Remark: {$a->remark}\n";
}

echo "\n=== LEAVE REQUESTS for 12A ===\n";
$leaves = StudentLeaveRequest::where('class_name', 'like', '%12A%')->orWhereIn('student_id', $students->pluck('id'))->get();
foreach ($leaves as $l) {
    echo "ID: {$l->id} | StudentID: {$l->student_id} | Name: {$l->student_name} | Class: {$l->class_name} | Start: {$l->start_date} | End: {$l->end_date} | Status: {$l->status}\n";
}
