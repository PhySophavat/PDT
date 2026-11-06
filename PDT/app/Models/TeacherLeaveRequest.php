<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherLeaveRequest extends Model
{
    protected $fillable = [
        'teacher_id',
        'teacher_name',
        'teacher_code',
        'specialization',
        'start_date',
        'end_date',
        'days_count',
        'leave_type',
        'reason',
        'phone',
        'attachment_path',
        'status',
        'rejection_reason',
        'approved_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_count' => 'integer',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}

