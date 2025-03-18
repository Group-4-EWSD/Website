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
                'at.article_type_id',
                'at.article_type_name',
                'articles.created_at',
                'articles.updated_at',
                'users.user_name as creator_name',
                'users.gender as creator_gender',
                DB::raw("(SELECT article_status FROM activities WHERE article_id = '$articleId' ORDER BY created_at DESC LIMIT 1) AS article_status"),
                DB::raw("(SELECT COUNT(*) FROM actions WHERE article_id = '$articleId') AS view_count"),
                DB::raw("(SELECT COUNT(*) FROM actions WHERE article_id = '$articleId' AND react = 1) AS like_count"),
                DB::raw("(SELECT EXISTS (SELECT 1 FROM actions WHERE article_id = '$articleId' AND react = 1 AND user_id = '$userId')) AS current_user_react"),
                DB::raw("(SELECT COUNT(*) FROM comments WHERE article_id = '$articleId') AS comment_count")
            ])
            ->leftJoin('users', 'users.id', '=', 'articles.user_id')
            ->leftJoin('article_types as at', 'at.article_type_id', '=', 'articles.article_type_id')
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
                    'comments.comment_id',
                    'comments.message',
                    'comments.created_at',
                    'comments.user_id',
                    DB::raw("CONCAT('https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/', u.user_photo_path) AS user_photo_path"),
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
                    DB::raw("CONCAT('https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/', u.user_photo_path) AS user_photo_path"),
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
        $reactionCount = $this->model()::where('article_id', $request->articleId)->where('react','=','1')->count();
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

    public function getNotificationList($userId)
    {
        $notifications = DB::table(DB::raw("(  
            SELECT 
                art.user_id, 
                a.action_id as action_id, 
                NULL as message, 
                a.created_at as created_at, 
                a.seen,
                '1' as action_type 
            FROM actions a 
            JOIN articles art ON art.article_id = a.article_id 
            WHERE art.user_id = ? AND a.react = 0
            UNION
            SELECT 
                c.user_id, 
                c.comment_id as action_id, 
                c.message, 
                c.created_at as created_at, 
                c.seen,
                '2' as action_type 
            FROM comments c 
            JOIN articles art ON art.article_id = c.article_id 
            WHERE art.user_id = ?
        ) AS notifications"))
        ->join('users as u', 'u.id', '=', 'notifications.user_id')
        ->join('user_types as ut', 'ut.user_type_id', '=', 'u.user_type_id')
        ->join('faculties as f', 'f.faculty_id', '=', 'u.faculty_id')
        ->selectRaw('
            notifications.action_id,
            COALESCE(notifications.message, NULL) as message,
            notifications.created_at,
            notifications.seen,
            notifications.action_type,
            u.user_name,
            CONCAT("https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/", u.user_photo_path) AS user_photo_path,
            u.gender,
            f.faculty_name,
            ut.user_type_name
        ')
        ->setBindings([$userId, $userId]) // Prevents SQL injection
        ->get();

        return $notifications;
    }

    public function setNotificationView($request)
    {
        $actionId = $request->actionId;
        $actionType = $request->actionType;
        if ($actionType == '1') {
            DB::table('actions')
                ->where('action_id', $actionId)
                ->update(['seen' => 1]);
        } elseif ($actionType == '2') {
            DB::table('comments')
                ->where('comment_id', $actionId)
                ->update(['seen' => 1]);
        }
    }

}
