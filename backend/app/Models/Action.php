<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'action_id',
        'article_id',
        'user_id',
        'react',
        'created_at',
        'updated_at'
    ];
}
