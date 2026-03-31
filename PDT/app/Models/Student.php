<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'grade',
        'date_of_birth',
        'gender',
        'village',
        'commune',
        'district',
        'province',
        'phone',
        'high_school',
        'mother_name',
        'mother_phone',
        'father_name',
        'father_phone',
    ];

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with all subjects (scores)
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get the latest subject (most recent score entry)
     */
    public function latestSubject()
    {
        return $this->hasOne(Subject::class)->latestOfMany();
    }

    public function scopeSearchByUserName(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->whereHas('user', function (Builder $userQuery) use ($search) {
            $userQuery->where('name', 'like', '%' . $search . '%');
        });
    }

    /**
     * Alternative: Get the current active subject (if you only want one record per student)
     */
    public function subject()
    {
        return $this->hasOne(Subject::class);
    }
}
