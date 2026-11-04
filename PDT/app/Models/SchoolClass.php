<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'school_classes';

    protected $fillable = [
        'name',
        'grade',
        'section',
        'room_number',
        'capacity',
        'teacher_id',
        'academic_year_id',
        'description',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'grade', 'name');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'class_name', 'name');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_name', 'name');
    }

    public static function orderedNames()
    {
        return self::all()->sortBy(function ($c) {
            preg_match('/^(\d+)([a-zA-Z]*)/', $c->name, $matches);
            $num = isset($matches[1]) ? (int)$matches[1] : 999;
            $letter = $matches[2] ?? '';
            return sprintf('%04d_%s', $num, $letter);
        })->pluck('name')->values();
    }
}
