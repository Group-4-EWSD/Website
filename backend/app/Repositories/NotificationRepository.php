<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
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
}
