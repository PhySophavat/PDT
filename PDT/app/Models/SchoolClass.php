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

    public function scopeOrdered($query)
    {
        $driver = $query->getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            return $query->orderByRaw('CAST(grade AS INTEGER) ASC')
                         ->orderBy('section', 'asc')
                         ->orderBy('name', 'asc');
        }

        // MySQL / TiDB / MariaDB use CAST(... AS UNSIGNED)
        return $query->orderByRaw('CAST(grade AS UNSIGNED) ASC')
                     ->orderBy('section', 'asc')
                     ->orderBy('name', 'asc');
    }

    public static function orderedList()
    {
        return self::ordered()->get();
    }

    public static function orderedNames()
    {
        return self::ordered()->pluck('name')->values();
    }
}
