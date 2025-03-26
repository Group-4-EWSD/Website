<?php

namespace App\Repositories;

use App\Models\Faculty;

class FacultyRepository
{
    public function getfacultyList(){
        return Faculty::select('faculties.*')
            ->selectRaw('COUNT(articles.article_id) AS articleCount')
            ->join('articles', 'faculties.faculty_id', '=', 'articles.facultyId')
            ->groupBy('faculties.faculty_id')
            ->get();
    }
}