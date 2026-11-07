<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'book_category_id',
        'course_subject_id',
        'isbn',
        'grade',
        'level',
        'title',
        'author',
        'publisher',
        'edition',
        'publication_year',
        'cover_image',
        'external_pdf_url',
        'quantity',
        'available_copies',
        'views_count',
        'allow_download',
        'is_broken_link',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'allow_download' => 'boolean',
        'is_broken_link' => 'boolean',
        'publication_year' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function subject()
    {
        return $this->belongsTo(CourseSubject::class, 'course_subject_id');
    }

    public function borrows()
    {
        return $this->hasMany(BookBorrow::class);
    }
}
