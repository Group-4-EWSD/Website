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
            ->where('user_id', Auth::id())
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
                DB::raw("(SELECT EXISTS (SELECT 1 FROM actions WHERE article_id = '$articleId' AND react = 1 AND user_id = '$userId')) AS current_user_react"),
                DB::raw("(SELECT COUNT(*) FROM comments WHERE article_id = '$articleId') AS comment_count")
            ])
            ->leftJoin('users', 'users.id', '=', 'articles.user_id')
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
        $comments = Comment::select([
                    'comments.message',
                    'comments.created_at',
                    'comments.user_id',
                    'u.user_photo_path',
                    'u.gender',
                    'u.user_name'
                ])
                ->join('users as u', 'u.id', '=', 'comments.user_id')
                ->where('comments.article_id', '=', $articleId)
                ->get();
        return $comments;
    }

    public function getFeedbackList($articleId){
        $feedbacks = DB::table('feedbacks as f')
                ->join('users as u', 'u.id', '=', 'f.user_id')
                ->select([
                    'f.message',
                    'f.created_at',
                    'f.user_id',
                    'u.user_photo_path',
                    'u.gender',
                    'u.user_name'
                ])
                ->where('f.article_id', $articleId)
                ->get();
        return $feedbacks;
    }

    public function increaseViewCount($articleId){
        $this->model()::create([
            'action_id' => Str::uuid(),
            'article_id' => $articleId,
            'user_id' => Auth::id(), // Get authenticated user's ID
            'react' => 0
        ]);
    }

    public function currentReaction($request){
        $reaction = $this->model()::where('article_id', $request->articleId)
                ->where('user_id', Auth::id())->first()->react;
        return $reaction;
    }
    public function makeAction($reaction, $request){
        $this->model()::where('article_id', $request->articleId)
                ->where('user_id', Auth::id())  // Get authenticated user's ID
                ->update(['react' => $reaction]);
        $reactionCount = $this->model()::where('article_id', $request->articleId)->count();
        return $reactionCount;
    }

    public function createComment($request){
        return Comment::create([
            'comment_id' => Str::uuid(),
            'article_id' => $request->articleId,
            'user_id' => Auth::id(), // Get authenticated user's ID
            'message' => $request->message,
            'delete_flag' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function deleteComment($request){
        $deleted = Comment::where('comment_id', $request->commentId)->delete();
        return $deleted > 0;
    }

    public function academicYearList(){
        $academicYearList = DB::table("academic_years")->select('academic_year_id','academic_year')->get();
        return $academicYearList;
    }
}
