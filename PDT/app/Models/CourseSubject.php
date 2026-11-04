<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSubject extends Model
{
    protected $table = 'course_subjects';

    protected $fillable = [
        'code',
        'name',
        'description',
        'credit_hours',
        'grade_level',
        'subject_type',
        'max_score',
        'credit',
        'class_configs',
    ];

    protected $casts = [
        'class_configs' => 'array',
        'max_score' => 'float',
        'credit' => 'float',
        'credit_hours' => 'integer',
    ];

    /**
     * Get Max Score for a specific class or grade (e.g. '11D', '11', '10A', '7')
     */
    public function getMaxScoreForClass(?string $class): float
    {
        if (empty($class) || empty($this->class_configs)) {
            return (float) ($this->max_score ?: 50.0);
        }

        $configs = is_array($this->class_configs) ? $this->class_configs : json_decode($this->class_configs, true);
        if (!$configs) {
            return (float) ($this->max_score ?: 50.0);
        }

        // 1. Direct class match (e.g. '11D')
        if (isset($configs[$class]['max_score'])) {
            return (float) $configs[$class]['max_score'];
        }

        // 2. Grade level prefix match (e.g. '11' from '11D' or '7' from '7A')
        if (preg_match('/^(\d+)/', $class, $matches)) {
            $grade = $matches[1];
            if (isset($configs[$grade]['max_score'])) {
                return (float) $configs[$grade]['max_score'];
            }
        }

        return (float) ($this->max_score ?: 50.0);
    }

    /**
     * Get Credit / Coefficient divisor for a specific class or grade
     */
    public function getCreditForClass(?string $class): float
    {
        if (empty($class) || empty($this->class_configs)) {
            return (float) ($this->credit ?: 1.0);
        }

        $configs = is_array($this->class_configs) ? $this->class_configs : json_decode($this->class_configs, true);
        if (!$configs) {
            return (float) ($this->credit ?: 1.0);
        }

        // 1. Direct class match
        if (isset($configs[$class]['credit'])) {
            return (float) $configs[$class]['credit'];
        }

        // 2. Grade level prefix match
        if (preg_match('/^(\d+)/', $class, $matches)) {
            $grade = $matches[1];
            if (isset($configs[$grade]['credit'])) {
                return (float) $configs[$grade]['credit'];
            }
        }

        return (float) ($this->credit ?: 1.0);
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'course_subject_id');
    }

    public function scores()
    {
        return $this->hasMany(ExaminationScore::class, 'course_subject_id');
    }
}
