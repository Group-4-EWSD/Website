<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'comment_id',
        'article_id',
        'user_id',
        'message',
        'delete_flag',
        'created_at',
        'updated_at',
    ];
}
