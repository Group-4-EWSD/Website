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

    public function getNotificationList()
    {
        $this->notificationRepository->seenAllNotifications();
        return $this->notificationRepository->getNotificationList();
    }

    public function setNotification($type, $articleId)
    {
        return $this->notificationRepository->setNotification($type, $articleId);
    }
}