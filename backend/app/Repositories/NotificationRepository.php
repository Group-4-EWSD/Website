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

    public function getNotificationList($user)
    {
        $notificationList = Notification::join('articles', 'notifications.article_id', '=', 'articles.article_id')
            ->join('users', 'users.id', '=', 'notifications.user_id')
            ->join('user_types', 'user_types.user_type_id', '=', 'users.user_type_id')
            ->join('system_datas', 'system_datas.system_id', '=', 'articles.system_id')
            ->join('faculties', 'faculties.faculty_id', '=', 'users.faculty_id')
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
            ]);

        // Apply filters based on user type
        if ($user->user_type_id == 1) { // Student  
            $notificationList->where('articles.user_id', $user->id)
                ->whereIn('notifications.notification_type', [1, 2, 3]);
        } elseif ($user->user_type_id == 2) { // Coordinator  
            $notificationList->where('sysetm_datas.faculty_id', $user->faculty_id)
                ->whereIn('notifications.notification_type', [4, 5]);
        } elseif ($user->user_type_id == 3) { // Manager
            $notificationList->where('notifications.notification_type', 6);
        }

        // Execute the query
        $notificationList = $notificationList->get();

        // notification_type = 1 (react- student), 2 (comment- student), 3 (feedback- student) , 4 (article create- coordiantor), 5 (article update- coordinator) , 6 (article Published- manager)
        return $notificationList;
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
    }
}
