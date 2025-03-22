<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'feedback_id',
        'article_id',
        'user_id',
        'message',
        'delete_flag',
        'created_at',
        'updated_at',
    ];
}
