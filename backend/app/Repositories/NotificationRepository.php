<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class NotificationRepository.
 */
class NotificationRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Notification::class;
    }

    public function getNotificationList()
    {
        return $this->model()::where('id', Auth::id())->first();
    }

    public function setNotification($type, $articleId)
    {
        $userName = User::where('id','=',Auth::id())->first()->user_name;
        $articleTitle = Article::where('article_id','=',$articleId)->first()->article_title;
        $message = "";

        if($type == '1'){//Like
            $message = $userName + " has reacted to your article " + $articleTitle;
        }elseif ($type == '2') {//Comment
            $message = $userName + " has commented to your article " + $articleTitle;
        }
        
        $this->model()::create([
            'notification_id' => Str::uuid(),
            'message' => $message,
            'user_id' => Auth::id(),
            'article_id' => $articleId,
            'seen' => 0, // Unseen
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function seenAllNotifications(){
        $this->model()::where('user_id', Auth::id())->update([
            'seen' => 1
        ]);
    }
}
