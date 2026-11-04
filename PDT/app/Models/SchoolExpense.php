<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolExpense extends Model
{
    protected $fillable = [
        'title',
        'category',
        'amount',
        'expense_date',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];
}
