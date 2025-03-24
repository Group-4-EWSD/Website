<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                ->join('users', 'users.id', '=', 'notifications.user_id')
                ->join('user_types', 'user_types.user_type_id', '=', 'users.user_type_id')
                ->join('faculties', 'faculties.faculty_id', '=', 'users.faculty_id')
                ->where('articles.user_id', Auth::id())
                ->select([
                    'notifications.notification_id',
                    'notifications.article_id',
                    'users.user_name',
                    'user_types.user_type_name',
                    'users.gender',
                    'faculties.faculty_name',
                    'notifications.notification_type',
                    'notifications.status as seen',
                    DB::raw('CONCAT("https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/", users.user_photo_path) AS user_photo_path'),
                    'articles.article_title',
                    'notifications.created_at'
                ])
                ->get();// notification_type = 1 (react- student), 2 (comment- student), 3 (article create- coordiantor), 4 (article update- coordinator) , 5 (article Published- manager)
        return $notificationList;
    }

    public function setNotification($type, $articleId)
    {
        $this->model()::create([
            'notification_id' => Str::uuid(),
            'notification_type' => $type, // 1: Like , 2: Comment, 3: Feedback
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
