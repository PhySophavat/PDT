<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class ExaminationScore extends Model
{
    use LogsActivity;

    protected string $activityModule = 'ពិន្ទុប្រឡង';

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

    /**
     * Calculate weighted score for a subject based on MoEYS standards:
     * raw_score is out of 100, 1 credit = 50 points.
     * weighted_score = (raw_score / 2) * credit
     */
    public static function calculateWeightedScore($rawScore, $credit = 1.0): float
    {
        if ($rawScore === null || $rawScore === '' || !is_numeric($rawScore)) return 0.0;
        $credit = (float)($credit > 0 ? $credit : 1.0);
        return round(((float)$rawScore / 2.0) * $credit, 2);
    }

    /**
     * Calculate grade for individual subject score based on percentage (MoEYS standard).
     * percentage = (raw_score / max_score) * 100
     * A: 90.00 - 100.00% (ល្អប្រសើរ)
     * B: 80.00 - 89.99%  (ល្អណាស់)
     * C: 70.00 - 79.99%  (ល្អ)
     * D: 60.00 - 69.99%  (ល្អបង្គួរ)
     * E: 50.00 - 59.99%  (មធ្យម)
     * F: < 50.00%        (ខ្សោយ / ធ្លាក់)
     */
    public static function calculateSubjectGrade($score, $maxScore = 100)
    {
        if ($score === null || $score === '' || !is_numeric($score)) return '-';
        $score = (float)$score;
        $maxScore = (float)($maxScore > 0 ? $maxScore : 100);

        $pct = ($score / $maxScore) * 100.0;
        if ($pct >= 90.0) return 'A';
        if ($pct >= 80.0) return 'B';
        if ($pct >= 70.0) return 'C';
        if ($pct >= 60.0) return 'D';
        if ($pct >= 50.0) return 'E';
        return 'F';
    }

    /**
     * Calculate grade for overall student average on Cambodian MoEYS 50-scale (passing score is 25.0).
     * 45.00 - 50.00 => A (ល្អប្រសើរ / ជាប់)
     * 40.00 - 44.99 => B (ល្អណាស់ / ជាប់)
     * 35.00 - 39.99 => C (ល្អ / ជាប់)
     * 30.00 - 34.99 => D (ល្អបង្គួរ / ជាប់)
     * 25.00 - 29.99 => E (មធ្យម / ជាប់)
     * < 25.00       => F (ខ្សោយ / ធ្លាក់)
     */
    public static function calculateAverageGrade($average)
    {
        if ($average === null || $average === '' || !is_numeric($average) || $average === '-') return '-';
        $avg = (float)$average;

        if ($avg >= 45.0) return 'A';
        if ($avg >= 40.0) return 'B';
        if ($avg >= 35.0) return 'C';
        if ($avg >= 30.0) return 'D';
        if ($avg >= 25.0) return 'E';
        return 'F';
    }

    /**
     * Determine pass/fail result based on average (out of 50).
     */
    public static function calculateAverageResult($average): string
    {
        if ($average === null || $average === '' || !is_numeric($average) || $average === '-') return '-';
        return ((float)$average >= 25.0) ? 'ជាប់' : 'ធ្លាក់';
    }

    /**
     * General grade calculator for a subject.
     */
    public static function calculateGradeLetter($score, $maxScore = 100)
    {
        if ($score === null || $score === '' || !is_numeric($score)) return '-';
        return self::calculateSubjectGrade($score, $maxScore);
    }
}
