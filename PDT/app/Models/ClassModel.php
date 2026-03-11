<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'grade'];

    // One class has many students
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject');
    }

public function show($classId)
{
    $class = ClassModel::with(['students.user', 'subjects'])->findOrFail($classId);

    return view('subject.show', compact('class'));
}
}
