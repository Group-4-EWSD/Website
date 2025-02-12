<?php

namespace App\Repositories;

use App\Models\Article;
//use Your Model

class ArticleRepository
{
    public function getAll()
    {
        return Article::all();
    }

    public function getById($id)
    {
        return Article::findOrFail($id);
    }

    public function create(array $data)
    {
        return Article::create($data);
    }

    public function update($id, array $data)
    {
        $article = Article::findOrFail($id);
        $article->update($data);
        return $article;
    }

    public function delete($id)
    {
        return Article::destroy($id);
    }
}
