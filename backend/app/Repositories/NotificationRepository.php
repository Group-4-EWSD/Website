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
            ->select([
                'notifications.notification_id',
                'notifications.article_id',
                'faculties.faculty_name',
                'notifications.notification_type',
                'notifications.status as seen',
                'articles.article_title',
                'notifications.created_at'
            ]);

        if ($user->user_type_id == 1) {
            if ($user->user_type_id == 1) {
                $notificationList->where('articles.user_id', $user->id)
                    ->whereIn('notifications.notification_type', [1, 2, 3])
                    ->leftJoin('actions', function ($join) {
                        $join->on('actions.article_id', '=', 'articles.article_id');
                    })
                    ->leftJoin('comments', function ($join) {
                        $join->on('comments.article_id', '=', 'articles.article_id');
                    })
                    ->leftJoin('feedbacks', function ($join) {
                        $join->on('feedbacks.article_id', '=', 'articles.article_id');
                    })
                    // Join with users table using dynamic user_id from the correct table
                    ->leftJoin('users as notify_users', function ($join) {
                        $join->on(DB::raw("
                            CASE
                                WHEN notifications.notification_type = 1 THEN actions.user_id
                                WHEN notifications.notification_type = 2 THEN comments.user_id
                                WHEN notifications.notification_type = 3 THEN feedbacks.user_id
                            END
                        "), '=', 'notify_users.id');
                    })
                    ->leftJoin('user_types as notify_user_types', 'notify_user_types.user_type_id', '=', 'notify_users.user_type_id');

                $notificationList->addSelect([
                    DB::raw('
                        CASE
                            WHEN notifications.notification_type = 1 THEN actions.user_id
                            WHEN notifications.notification_type = 2 THEN comments.user_id
                            WHEN notifications.notification_type = 3 THEN feedbacks.user_id
                            ELSE NULL
                        END AS user_id
                    '),
                    'notify_users.user_name',
                    'notify_user_types.user_type_name AS user_type',
                    DB::raw('CONCAT("https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/", notify_users.user_photo_path) AS user_photo_path')
                ]);
            }
            
        } elseif ($user->user_type_id == 2) {
            $notificationList->join('users', 'users.id', '=', 'articles.user_id')
                ->join('user_types', 'user_types.user_type_id', '=', 'users.user_type_id')
                ->addSelect([
                    'users.id as user_id',
                    'users.user_name',
                    'user_types.user_type_name as user_type',
                    DB::raw('CONCAT("https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/", users.user_photo_path) AS user_photo_path')
                ])
                ->where('system_datas.faculty_id', $user->faculty_id)
                ->whereIn('notifications.notification_type', [4, 5]);
        } elseif ($user->user_type_id == 3) {
            $notificationList->leftJoin('users as coordinators', function ($join) {
                $join->on('coordinators.faculty_id', '=', 'system_datas.faculty_id')
                    ->where('coordinators.user_type_id', '=', 2); // Coordinator type
            })
            ->leftJoin('user_types as coordinator_types', 'coordinator_types.user_type_id', '=', 'coordinators.user_type_id')
            ->addSelect([
                'coordinators.id as user_id',
                'coordinators.user_name',
                'coordinator_types.user_type_name as user_type',
                DB::raw('CONCAT("https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/", coordinators.user_photo_path) AS user_photo_path')
            ])
            ->where('notifications.notification_type', 6);
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
    }
}
