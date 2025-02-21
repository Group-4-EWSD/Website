<?php

namespace App\Repositories;

use App\Models\Activity;
use App\Models\Article;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class activityRepository.
 */
class activityRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Activity::class;
    }

    public function getUserArticlePair($request){
        $pair = $this->model()::where('article_id', $request->activityId)
            ->where('user_id', Auth::id())
            ->select('activity_id', 'react')
            ->get();
        return $pair;
    }

    public function getArticleDetail($request){
        $article = Article::select([
                'articles.article_title',
                'articles.article_description',
                'articles.created_at',
                'articles.updated_at',
                'articles.article_status',
                'users.user_name as creator_name',
                'users.creator_gender'
            ])
            ->leftJoin('users', 'users.user_id', '=', 'articles.user_id')
            ->leftJoin('activities as atv1', 'atv1.article_id', '=', 'articles.article_id')
            ->leftJoin('activities as atv2', function($join) {
                $join->on('atv2.article_id', '=', 'articles.article_id')
                    ->where('atv2.react', '=', 1);
            })
            ->leftJoin('activities as atv3', function($join) {
                $join->on('atv3.article_id', '=', 'articles.article_id')
                    ->where('atv3.user_id', '=', Auth::id());
            })
            ->leftJoin('message as msg', function($join) {
                $join->on('msg.activity_id', '=', 'atv1.activity_id')
                    ->where('msg.type', '=', 'comment');
            })
            ->where('articles.id', 1)
            ->groupBy([
                'articles.article_title',
                'articles.article_description',
                'articles.created_at',
                'articles.updated_at',
                'articles.article_status',
                'users.user_name',
                'users.creator_gender'
            ])
            ->selectRaw('COUNT(atv1.activity_id) as view_count')
            ->selectRaw('COUNT(atv2.activity_id) as like_count')
            ->selectRaw('COUNT(msg.activity_id) as comment_count')
            ->first();

        return $article;
    }

    public function increaseViewCount($request){
        $this->model()::create([
            'activity_id' => $request->activityId,
            'user_id' => Auth::id(), // Get authenticated user's ID
            'react' => 0
        ]);
    }
}
