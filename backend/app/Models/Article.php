<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'article_id',
        'article_title',
        'article_description',
        'user_id',
        'system_id',
        'article_type_id',
        'delete_flag',
        'create_at',
        'update_at'
    ];
}
