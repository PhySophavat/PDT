<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class BookRequest extends Model
{
    use LogsActivity;

    protected string $activityModule = 'បណ្ណាល័យ';

    protected $fillable = [
        'user_id',
        'requested_by_name',
        'requested_by_role',
        'grade',
        'title',
        'author',
        'subject_name',
        'course_subject_id',
        'book_category_id',
        'level',
        'file_url',
        'attachment_path',
        'note',
        'status',
        'admin_feedback',
        'reviewed_by',
        'reviewed_at',
        'published_book_id',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function courseSubject()
    {
        return $this->belongsTo(CourseSubject::class, 'course_subject_id');
    }

    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function publishedBook()
    {
        return $this->belongsTo(Book::class, 'published_book_id');
    }
}

