<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    Use HasFactory;

    protected $fillable = [
        'student_id',
        'khmer',
        'math',
        'english',
        'history',
        'geography',
        'chemistry',
        'physics',
        'biology',
        'ethics',
        'sports',

        
    ];
      public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scores()
{
    return $this->hasMany(Score::class);
}
 public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
        public function student()
    {
        return $this->belongsTo(Student::class);
    }
 public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'class_subject');
    }
    


}
