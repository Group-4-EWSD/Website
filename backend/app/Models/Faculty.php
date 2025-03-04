<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'faculty_id',
        'faculty_name',
        'faculty_description'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'faculty_id', 'faculty_id');
    }
}
