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
     * Cambodian MoEYS Standard: 1 Credit = 50 Maximum Points
     */
    public function getMaxScoreForClass(?string $class): float
    {
        $defaultMax = (float) ($this->max_score ?: (($this->credit ?: 1.0) * 50.0));

        if (empty($class) || empty($this->class_configs)) {
            return $defaultMax;
        }

        $configs = is_array($this->class_configs) ? $this->class_configs : json_decode($this->class_configs, true);
        if (!$configs) {
            return $defaultMax;
        }

        // 1. Direct class match (e.g. '11D')
        if (isset($configs[$class])) {
            if (isset($configs[$class]['max_score']) && is_numeric($configs[$class]['max_score'])) {
                return (float) $configs[$class]['max_score'];
            }
            if (isset($configs[$class]['credit']) && is_numeric($configs[$class]['credit'])) {
                return (float) $configs[$class]['credit'] * 50.0;
            }
        }

        // 2. Grade level prefix match (e.g. '11' from '11D' or '7' from '7A')
        if (preg_match('/^(\d+)/', $class, $matches)) {
            $grade = $matches[1];
            if (isset($configs[$grade])) {
                if (isset($configs[$grade]['max_score']) && is_numeric($configs[$grade]['max_score'])) {
                    return (float) $configs[$grade]['max_score'];
                }
                if (isset($configs[$grade]['credit']) && is_numeric($configs[$grade]['credit'])) {
                    return (float) $configs[$grade]['credit'] * 50.0;
                }
            }
        }

        return $defaultMax;
    }

    /**
     * Get Credit / Coefficient divisor for a specific class or grade
     */
    public function getCreditForClass(?string $class): float
    {
        $defaultCredit = (float) ($this->credit ?: 1.0);

        if (empty($class) || empty($this->class_configs)) {
            return $defaultCredit;
        }

        $configs = is_array($this->class_configs) ? $this->class_configs : json_decode($this->class_configs, true);
        if (!$configs) {
            return $defaultCredit;
        }

        // 1. Direct class match
        if (isset($configs[$class])) {
            if (isset($configs[$class]['credit']) && is_numeric($configs[$class]['credit'])) {
                return (float) $configs[$class]['credit'];
            }
            if (isset($configs[$class]['max_score']) && is_numeric($configs[$class]['max_score'])) {
                return round((float) $configs[$class]['max_score'] / 50.0, 2);
            }
        }

        // 2. Grade level prefix match
        if (preg_match('/^(\d+)/', $class, $matches)) {
            $grade = $matches[1];
            if (isset($configs[$grade])) {
                if (isset($configs[$grade]['credit']) && is_numeric($configs[$grade]['credit'])) {
                    return (float) $configs[$grade]['credit'];
                }
                if (isset($configs[$grade]['max_score']) && is_numeric($configs[$grade]['max_score'])) {
                    return round((float) $configs[$grade]['max_score'] / 50.0, 2);
                }
            }
        }

        return $defaultCredit;
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
