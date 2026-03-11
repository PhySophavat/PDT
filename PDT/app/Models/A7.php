<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class A7 extends Model
{
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function roles()
    {
        return $this->hasMany(Role::class);
    }
}
