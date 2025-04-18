<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'activity_id',
        'article_id',
        'user_id',
        'article_status',
        'created_at',
        'updated_at'
    ];
}
