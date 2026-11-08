<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Student extends Model
{
    use LogsActivity;

    protected string $activityModule = 'ព័ត៌មានសិស្ស';

    protected $fillable = [
        'user_id',
        'email',
        'student_id_card',
        'grade',
        'status',
        'photo',
        'academic_year_id',
        'enrollment_date',
        'emergency_contact',
        'date_of_birth',
        'gender',
        'village',
        'commune',
        'district',
        'province',
        'phone',
        'high_school',
        'mother_name',
        'mother_phone',
        'father_name',
        'father_phone',
        'name_khmer',
        'name_latin',
        'address',
        'parent_name',
        'parent_relationship',
        'parent_occupation',
        'parent_phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
    ];

    public function getDisplayNameAttribute()
    {
        return $this->name_khmer ?: ($this->user?->name ?: ($this->name_latin ?: 'N/A'));
    }

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with all subjects (scores)
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get the latest subject (most recent score entry)
     */
    public function latestSubject()
    {
        return $this->hasOne(Subject::class)->latestOfMany();
    }

    public function scopeSearchByUserName(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->whereHas('user', function (Builder $userQuery) use ($search) {
            $userQuery->where('name', 'like', '%' . $search . '%');
        });
    }

    /**
     * Alternative: Get the current active subject (if you only want one record per student)
     */
    public function subject()
    {
        return $this->hasOne(Subject::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function examinationScores()
    {
        return $this->hasMany(ExaminationScore::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function parents()
    {
        return $this->belongsToMany(SchoolParent::class, 'parent_student', 'student_id', 'parent_id')
                    ->withPivot('relationship')
                    ->withTimestamps();
    }

    public function bookBorrows()
    {
        return $this->hasMany(BookBorrow::class);
    }
}
