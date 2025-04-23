<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(){
        $user = Auth::user();
        $notificationData = $this->notificationService->getNotificationList($user);
        return response()->json($notificationData);
    }

    public function setNotificationView(Request $request){
        $notificationId = $request->notificationId;
        if($this->notificationService->viewNotification($notificationId)){
            return response()->json(['message'=>'Notification has been set to seen.']);
        }else{
            return response()->json(['message'=>'Notification has not been set to seen.'], 500);
        }
    }
}
