<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'categoryId',
        'categoryName',
        'categoryDescription'
    ];

    public function users()
    {
        return $this->hasMany(Article::class, 'categoryId', 'categoryId');
    }
}
