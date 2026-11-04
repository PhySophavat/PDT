<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'book_category_id',
        'isbn',
        'title',
        'author',
        'publisher',
        'quantity',
        'available_copies',
    ];

    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function borrows()
    {
        return $this->hasMany(BookBorrow::class);
    }
}
