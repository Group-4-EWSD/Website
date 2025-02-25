<?php

namespace App\Repositories;

use App\Models\Action;
use App\Models\Article;
use App\Models\ArticleDetail;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use Illuminate\Support\Str;
//use Your Model

/**
 * Class actionRepository.
 */
class actionRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Action::class;
    }

    public function getUserArticlePair($articleId){
        $pair = $this->model()::where('article_id', $articleId)
            ->where('user_id', 'U0003')
            ->select('action_id', 'react')
            ->get();
        return $pair;
    }

    public function getArticleDetail($articleId){
        $userId = Auth::id();
        $article = Article::select([
                'articles.article_title',
                'articles.article_description',
                'articles.created_at',
                'articles.updated_at',
                'users.user_name as creator_name',
                'users.gender as creator_gender',
                DB::raw("(SELECT status FROM activities WHERE article_id = '$articleId' ORDER BY created_at DESC LIMIT 1) AS article_status"),
                DB::raw("(SELECT COUNT(*) FROM actions WHERE article_id = '$articleId') AS view_count"),
                DB::raw("(SELECT COUNT(*) FROM actions WHERE article_id = '$articleId' AND react = 1) AS like_count"),
                DB::raw("(SELECT EXISTS (SELECT 1 FROM actions WHERE article_id = '$articleId' AND react = 1 AND user_id = 'U0003')) AS current_user_react"),
                DB::raw("(SELECT COUNT(*) FROM comments WHERE article_id = '$articleId') AS comment_count")
            ])
            ->leftJoin('users', 'users.user_id', '=', 'articles.user_id')
            ->where('articles.article_id', '=', $articleId)
            ->first();
        return $article;
    }
    
    public function getArticleDetailFiles($articleId){
        $articleDetail = ArticleDetail::where('article_id','=',$articleId)->get();
        return $articleDetail;
    }

    public function getCommentList($articleId){
        // SELECT c.message, c.created_at, c.user_id, u.user_photo_path, u.gender, u.user_name FROM comments c JOIN users u ON u.user_id = c.user_id;
        $comments = DB::table('comments as c')
                ->join('users as u', 'u.user_id', '=', 'c.user_id')
                ->select([
                    'c.message',
                    'c.created_at',
                    'c.user_id',
                    'u.user_photo_path',
                    'u.gender',
                    'u.user_name'
                ])
                ->where('c.article_id', $articleId)
                ->get();
        return $comments;
    }

    public function increaseViewCount($articleId){
        $this->model()::create([
            'action_id' => Str::uuid(),
            'article_id' => $articleId,
            'user_id' => 'U0003', // Get authenticated user's ID
            'react' => 0
        ]);
    }

    public function makeAction($request){
        $this->model()::where('article_id', $request->articleId)
                ->where('user_id', 'U0003')  // Get authenticated user's ID
                ->update(['react' => 1]);
    }

    public function createComment($request){
        Comment::create([
            'comment_id' => Str::uuid(),
            'article_id' => $request->articleId,
            'user_id' => 'U0003', // Get authenticated user's ID
            'message' => $request->message,
            'delete_flag' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
