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
            'approver_id' => null,
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
            (SELECT COUNT(*) FROM actions act JOIN articles art ON art.article_id = act.article_id WHERE act.react = 1 and art.user_id = ?) as reactCount,
            (SELECT COUNT(*) FROM actions act JOIN articles art ON art.article_id = act.article_id WHERE art.user_id = ?) as totalViewCount,
            (SELECT COUNT(*) FROM articles art JOIN system_datas sd ON sd.system_id = art.system_id JOIN academic_years ay ON ay.academic_year_id = sd.academic_year_id WHERE art.user_id = ?) as currentYearArticleCount
        ',[$userId, $userId, $userId]) // WHERE ay.academic_year = CURRENT_YEAR() AND 
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
                DB::raw("(SELECT ad.file_path FROM article_details ad WHERE ad.article_id = art.article_id AND ad.file_type = 'WORD' LIMIT 1) AS file_path")
            ])
            ->join('users as u', 'u.id', '=', 'art.user_id')
            ->join('article_types as at', 'at.article_type_id', '=', 'art.article_type_id');        

        // Apply search filter if `articleTitle` exists in the request
        if (!empty($request->articleTitle)) {
            $articles->where('art.article_title', 'LIKE', '%' . $request->articleTitle . '%');
        }

        // Ensure GROUP BY is valid by including `art.article_id`
        $articles->groupBy([
            'art.article_id',
            'art.article_title',
            'art.user_id',
            'u.user_name',
            'u.user_photo_path',
            'u.gender',
            'art.created_at',
            'art.updated_at'
        ]);

        // Apply ORDER BY, LIMIT, and OFFSET before executing
        $articles->orderByDesc('art.created_at');

        // Apply LIMIT only if `displayNumber` is set
        if ($request->displayNumber > 0) {
            $articles->limit($request->displayNumber);

            // Apply OFFSET only if `pageNumber` > 1
            if ($request->pageNumber > 1) {
                $offset = ($request->pageNumber - 1) * $request->displayNumber;
                $articles->offset($offset);
            }
        }
        $articles = $articles->get();

        return $articles;
    }

    public function draftArticleList(){
        $articles = DB::table('articles as a')
                ->select('a.article_title', 'a.article_description')
                ->join(
                    DB::table('activities')
                        ->selectRaw('MAX(created_at) as created_at, article_id, status')
                        ->groupBy('article_id')
                        ->where('status', 0), // Filter to only status = 0
                    'a.article_id', '=', 'atv.article_id'
                )
                ->get();
        return $articles;
    }
}
