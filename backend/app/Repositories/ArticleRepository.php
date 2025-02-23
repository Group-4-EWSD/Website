<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

//use Your Model

class ArticleRepository
{
    public function createArticle($articleId, $userId, $request)
    {
        DB::table('articles')->insert([
            'article_id' => $articleId,
            'article_title' => $request->article_title,
            'article_description' => $request->article_description,
            'article_status' => $request->article_status,
            'user_id' => $userId,
            'approver_id' => $request->approver_id ?? null,
            'article_type_id' => $request->article_type_id,
            'delete_flag' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function createArticleDetail($articleId, $filePath, $fileName, $fileType)
    {
        DB::table('article_details')->insert([
            'article_detail_id' => Str::random(10),
            'article_id' => $articleId,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
        ]);
    }

    public function getCountData(){
        $userId = Auth::id();
        return DB::table('articles')
        ->selectRaw('
            (SELECT COUNT(*) FROM activities atv JOIN articles art ON art.article_id = atv.article_id WHERE atv.Like = 1 and art.userId = ?) as reactCount,
            (SELECT COUNT(*) FROM activities atv JOIN articles art ON art.article_id = atv.article_id WHERE art.userId = ?) as totalViewCount,
            (SELECT COUNT(*) FROM articles art JOIN system_data sd ON sd.system_id = art.system_id WHERE sd.academic_year = CURRENT_YEAR() AND art.art.userId = ?) as currentYearArticleCount
        ',[$userId, $userId, $userId])
        ->first();
    }

    public function getAllArticles($request)
    {
        $articles = DB::table('articles as art')
            ->select([
                'art.article_title',
                'art.user_id',
                'u.user_name',
                'u.user_photo_path',
                'u.gender',
                'art.created_at',
                'art.updated_at',
                'ad.file_path'
            ])
            ->join('articleTypes as at', 'art.article_type_id', '=', 'at.article_type_id')
            ->join('article_details as ad', function ($join) {
                $join->on('ad.article_detail_id', '=', 'art.article_detail')
                    ->where('ad.file_type', '=', 'Word');
            })
            ->join('users as u', 'u.user_id', '=', 'art.user_id')
            ->where('art.user_id', Auth::id());

        // Apply search filter if `articleTitle` exists in the request
        if (!empty($request->articleTitle)) {
            $articles->where('art.article_title', 'LIKE', '%' . $request->articleTitle . '%');
        }

        // Ensure 'art.article_id' is selected if using GROUP BY
        $articles->groupBy('art.article_id')
            ->orderBy('ad.created_at')
            ->orderByDesc('art.created_at')
            ->offset($request->displayNumber)
            ->limit(($request->pageNumber - 1) * $request->displayNumber);

        $articles = $articles->get();

        return $articles;
    }
}
