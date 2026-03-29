<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    Use HasFactory;

    public const SCORE_FIELDS = [
        'math',
        'khmer',
        'english',
        'history',
        'geography',
        'chemistry',
        'physics',
        'biology',
        'ethics',
        'sports',
    ];

    public const SCORE_LABELS = [
        'math' => 'គណិតវិទ្យា',
        'khmer' => 'ភាសាខ្មែរ',
        'english' => 'ភាសាអង់គ្លេស',
        'history' => 'ប្រវត្តិវិទ្យា',
        'geography' => 'ភូមិវិទ្យា',
        'chemistry' => 'គីមីវិទ្យា',
        'physics' => 'រូបវិទ្យា',
        'biology' => 'ជីវវិទ្យា',
        'ethics' => 'សីលធម៌',
        'sports' => 'កីឡា',
    ];

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
        'coefficient',

        
    ];

    protected $casts = [
        'coefficient' => 'float',
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

    public static function scoreFields(): array
    {
        return self::SCORE_FIELDS;
    }

    public static function scoreLabels(): array
    {
        return self::SCORE_LABELS;
    }

    public function totalScore(): float
    {
        return collect(self::SCORE_FIELDS)
            ->sum(fn (string $field): float => (float) ($this->{$field} ?? 0));
    }

    public function coefficientValue(): float
    {
        return $this->coefficient && $this->coefficient > 0
            ? (float) $this->coefficient
            : 1.0;
    }

    public function averageScore(): float
    {
        $coefficient = $this->coefficientValue();

        return $coefficient > 0
            ? round($this->totalScore() / $coefficient, 2)
            : 0.0;
    }

}
