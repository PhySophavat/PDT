<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Examination extends Model
{
    use LogsActivity;

    protected string $activityModule = 'ការប្រឡង';

    protected $fillable = [
        'title',
        'exam_type',
        'academic_year_id',
        'semester',
        'start_date',
        'end_date',
        'is_published',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_published' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scores()
    {
        return $this->hasMany(ExaminationScore::class);
    }
}
