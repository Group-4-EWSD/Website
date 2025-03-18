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
        $notificationList = Notification::join('articles', 'notifications.article_id', '=', 'articles.article_id')
                ->where('articles.user_id', Auth::id())
                ->select('notifications.*')
                ->get();
        return $notificationList;
    }

    public function setNotification($type, $articleId)
    {
        $this->model()::create([
            'notification_id' => Str::uuid(),
            'notification_type' => $type, // 1: Like , 2: Comment
            'user_id' => Auth::id(), // Audience user id
            'article_id' => $articleId,
            'status' => 0, // Unseen
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function viewNotification($notificationId){
        $this->model()::where('user_id', Auth::id())
            ->where('notification_id', '=', $notificationId)
            ->update([
                'status' => 1
            ]); // seen
    }
}
