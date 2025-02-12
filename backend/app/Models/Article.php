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
    
    public function catgory()
    {
        return $this->belongsTo(Category::class, 'categoryId', 'categoryId');
    }

    public function activity()
    {
        return $this->hasMany(Activity::class, 'articleId', 'articleId');
    }
}
