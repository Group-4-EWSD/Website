<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $fillable = [
        'user_type_id',
        'user_type_name'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'userTypeId', 'userTypeId');
    }
}
