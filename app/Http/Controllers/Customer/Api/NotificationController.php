<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index(Request $request){

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $notifications=Notification::where('user_id', $user->id)->orWhere('type', 'all')->select('title', 'description', 'created_at')->get();

        return [
            'status'=>'success',
            'data'=>compact('notifications')
        ];

    }
}
