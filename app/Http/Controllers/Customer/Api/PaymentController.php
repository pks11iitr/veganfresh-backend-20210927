<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Order;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request, $id){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $order=Order::with('details.entity')->where('user_id', $user->id)->where('status', 'pending')->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id,
                'amount'=>$order->total_cost,
                'walletbalance'=>Wallet::balance($user->id),
                'points'=>Wallet::points($user->id)
            ]
        ];
    }
}
