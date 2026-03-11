<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'phone',
        'date_of_birth',
        'gender',
        'village',
        'commune',
        'district',
        'province',
        'class_assigned',
    ];

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
}