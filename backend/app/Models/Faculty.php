<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'facultyId',
        'facultyName',
        'facultyDescription'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'facultyId', 'id');
    }
}
