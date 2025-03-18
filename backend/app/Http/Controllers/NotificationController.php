<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(){
        $notificationData = $this->notificationService->getNotificationList();
        return response()->json($notificationData);
    }

    public function setNotificationView(Request $request){
        $notificationId = $request->notificationId;
        $this->notificationService->viewNotification($notificationId);
        return response()->json(['message'=>'Notification has been set to seen.']);
    }
}
