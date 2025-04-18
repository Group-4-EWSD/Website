<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'faculty_id',
        'faculty_name',
        'remark',
        'created_at',
        'updated_at'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'facultyId', 'id');
    }
}
