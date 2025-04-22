<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
//use Your Model

/**
 * Class NotificationRepository.
 */
class NotificationRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Notification::class;
    }

    public function getNotificationList($user)
    {
        $notificationList = Notification::join('articles', 'notifications.article_id', '=', 'articles.article_id')
            ->join('system_datas', 'system_datas.system_id', '=', 'articles.system_id')
            ->join('faculties', 'faculties.faculty_id', '=', 'system_datas.faculty_id')
            ->join('users as notify_users', 'notify_users.id', '=', 'notifications.user_id')
            ->join('user_types as notify_user_types', 'notify_user_types.user_type_id', '=', 'notify_users.user_type_id')
            ->select([
                'notifications.notification_id',
                'notifications.article_id',
                'notify_users.id',
                'notify_users.user_name',
                'notify_user_types.user_type_name AS user_type',
                DB::raw('CONCAT("https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/", notify_users.user_photo_path) AS user_photo_path'),
                'faculties.faculty_name',
                'notifications.notification_type',
                'notifications.status as seen',
                'articles.article_title',
                'notifications.created_at'
            ]);

        if ($user->user_type_id == 1) {
            $notificationList->where('articles.user_id', $user->id)
                ->whereIn('notifications.notification_type', [1, 2, 3]);
        } elseif ($user->user_type_id == 2) {
            $notificationList->where('system_datas.faculty_id', $user->faculty_id)
                ->whereIn('notifications.notification_type', [4, 5]);
        } elseif ($user->user_type_id == 3) {
            $notificationList->where('notifications.notification_type', 6);
        }
        return $notificationList->get();
    }


    public function setNotification($type, $articleId)
    {
        $this->model()::create([
            'notification_id' => Str::uuid(),
            'notification_type' => $type, // notification_type = 1 (react- student), 2 (comment- student), 3 (feedback- student) , 4 (article create- coordiantor), 5 (article update- coordinator) , 6 (article Published- manager)
            'user_id' => Auth::id(), // Audience user id
            'article_id' => $articleId,
            'status' => 0, // Unseen
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function viewNotification($notificationId)
    {
        $this->model()::where('user_id', Auth::id())
            ->where('notification_id', '=', $notificationId)
            ->update([
                'status' => 1
            ]); // seen
        return true;
    }
}
