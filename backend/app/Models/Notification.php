<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{    
    protected $fillable = [
        'notification_id',
        'notification_type',
        'user_id',
        'article_id',
        'status',
        'created_at',
        'updated_at'
    ];
}
