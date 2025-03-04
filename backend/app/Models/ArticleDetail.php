<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'article_detail_id',
        'article_id',
        'file_path',
        'file_name',
        'file_type'
    ];
}
