<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationScore extends Model
{
    protected $fillable = [
        'examination_id',
        'student_id',
        'class_name',
        'course_subject_id',
        'subject_name',
        'score',
        'max_score',
        'grade_letter',
    ];

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function courseSubject()
    {
        return $this->belongsTo(CourseSubject::class, 'course_subject_id');
    }

    public static function calculateGradeLetter($score, $maxScore = 100)
    {
        if ($maxScore <= 0) return 'F';

        // Auto-detect Cambodian high school 50-scale vs standard 100-scale:
        // In Cambodian general education, monthly exam average is scored out of 50 points (passing is 25.0).
        if ($maxScore == 50 || ($maxScore == 100 && $score > 0 && $score <= 50.0)) {
            if ($score >= 45.0) return 'A';
            if ($score >= 40.0) return 'B';
            if ($score >= 35.0) return 'C';
            if ($score >= 30.0) return 'D';
            if ($score >= 25.0) return 'E';
            return 'F';
        }

        $pct = ($score / $maxScore) * 100;
        if ($pct >= 90) return 'A';
        if ($pct >= 80) return 'B';
        if ($pct >= 70) return 'C';
        if ($pct >= 60) return 'D';
        if ($pct >= 50) return 'E';
        return 'F';
    }
}
