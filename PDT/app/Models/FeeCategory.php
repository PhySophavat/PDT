<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    protected $fillable = [
        'name',
        'default_amount',
        'description',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
