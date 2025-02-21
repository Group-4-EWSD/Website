<?php

namespace App\Services;

use App\Repositories\ArticleRepository;

class ArticleService
{
    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getHomePageData($request)
    {
        return [
            'countData' => $this->articleRepository->getCountData(),
            'allArticles' => $this->articleRepository->getAllArticles($request)
        ];
    }
}
