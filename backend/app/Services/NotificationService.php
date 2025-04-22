<?php

namespace App\Services;

use App\Repositories\FileRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Http\UploadedFile;

class NotificationService
{
    protected $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function getNotificationList($userId)
    {
        $notifications = $this->notificationRepository->getNotificationList($userId);
        return $notifications;
    }

    public function setNotification($type, $articleId)
    {
        return $this->notificationRepository->setNotification($type, $articleId);
    }

    public function viewNotification($notificationId){
        return $this->notificationRepository->viewNotification($notificationId);
    }
}