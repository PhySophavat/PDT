<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolParent extends Model
{
    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'parent_id_card',
        'name',
        'name_khmer',
        'name_latin',
        'gender',
        'phone',
        'email',
        'occupation',
        'address',
        'photo',
        'relationship',
        'child_name',
        'child_grade',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
                    ->withPivot('relationship')
                    ->withTimestamps();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name_khmer ?: ($this->name ?: ($this->name_latin ?: 'អាណាព្យាបាល'));
    }
}
