<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $fillable = [
        'userTypeId',
        'userTypeName'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'userTypeId', 'userTypeId');
    }
}
