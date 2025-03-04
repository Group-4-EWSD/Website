<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'articleId',
        'articleTitle',
        'articleDescription',
        'articleStatus',
        'studentId',
        'approverId',
        'categoryId'
    ];
}
