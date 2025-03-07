<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{    
    protected $fillable = [
        'notification_id',
        'user_id',
        'message',
        'article_id',
        'seen',
        'created_at',
        'updated_at'
    ];
}
