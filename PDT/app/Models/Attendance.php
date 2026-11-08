<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Attendance extends Model
{
    use LogsActivity;

    protected string $activityModule = 'វត្តមានសិស្ស';

    protected $fillable = [
        'student_id',
        'class_name',
        'date',
        'status',
        'remark',
        'academic_year_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
