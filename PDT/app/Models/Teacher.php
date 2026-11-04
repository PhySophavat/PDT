<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'teacher_id_card',
        'subject',
        'qualification',
        'specialization',
        'joining_date',
        'salary',
        'status',
        'phone',
        'date_of_birth',
        'gender',
        'photo',
        'village',
        'commune',
        'district',
        'province',
        'class_assigned',
        'name_khmer',
        'name_latin',
        'address',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function getDisplayNameAttribute()
    {
        return $this->name_khmer ?: ($this->user?->name ?: ($this->name_latin ?: 'N/A'));
    }

    // Each teacher belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get array of assigned classes
     * 
     * @return array
     */
    public function getAssignedClassesArray()
    {
        if (empty($this->class_assigned)) {
            return [];
        }
        
        return array_map('trim', explode(',', $this->class_assigned));
    }

    /**
     * Check if teacher is assigned to a specific class
     * 
     * @param string $className
     * @return bool
     */
    public function isAssignedToClass($className)
    {
        return in_array($className, $this->getAssignedClassesArray());
    }

    public function schoolClasses()
    {
        return $this->hasMany(SchoolClass::class, 'teacher_id');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'teacher_id');
    }

    public function attendances()
    {
        return $this->hasMany(TeacherAttendance::class, 'teacher_id');
    }
}