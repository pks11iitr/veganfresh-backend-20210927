<?php

namespace App\Http\Controllers\Customer\Api;

use App\Events\OrderConfirmed;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Membership;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Subscription;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MembershipController extends Controller
{
    public function index(Request $request){

        $memberships=Membership::active()->get();

        return [

            'status'=>'success',
            'data'=>compact('memberships')

        ];

    }

    public function subscribe(Request $request, $id){

        $user=auth()->guard('customerapi')->user();

        $membership=Membership::active()->find($id);
        if(!$membership)
            return [
                'status'=>'success',
                'message'=>'This Plan does not exist'
            ];

        $subscription=Subscription::create([

            'user_id'=>$user,
            'plan_id'=>$membership->id,
            'refid'=>env('MACHINE_ID').time()

        ]);

        $response=$this->pay->generateorderid([
            "amount"=>($membership->price)*100,
            "currency"=>"INR",
            "receipt"=>$subscription->refid,
        ]);
        $responsearr=json_decode($response);
        //var_dump($responsearr);die;
        if(isset($responsearr->id)){
            $subscription->razorpay_order_id=$responsearr->id;
            $subscription->razorpay_order_id_response=$response;
            $subscription->save();
            return [
                'status'=>'success',
                'message'=>'success',
                'data'=>[
                    'payment_done'=>'no',
                    'razorpay_order_id'=> $subscription->razorpay_order_id,
                    'total'=>($membership->price)*100,
//                    'email'=>$order->email,
//                    'mobile'=>$order->mobile,
                    'description'=>'Membership Subscription at SuzoDaily',
                    //'name'=>$order->name,
                    'currency'=>'INR',
                    'merchantid'=>$this->pay->merchantkey,
                ],
            ];
        }else{
            return [
                'status'=>'failed',
                'message'=>'Payment cannot be initiated',
                'data'=>[
                ],
            ];
        }

    }

    public function verify(Request $request){

        $request->validate([
            'razorpay_order_id'=>'required',
            'razorpay_signature'=>'required',
            'razorpay_payment_id'=>'required'

        ]);

        $subscription=Subscription::where('razorpay_order_id', $request->razorpay_order_id)
            ->first();

        if(!$subscription)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        $paymentresult=$this->pay->verifypayment($request->all());
        if($paymentresult) {

            $user=Customer::find($subscription->user_id);
            $memberships=Membership::active()->find($subscription->plan_id);

            $subscription->is_confirmed = true;
            $subscription->razorpay_payment_id = $request->razorpay_payment_id;
            $subscription->razorpay_payment_id_response = $request->razorpay_signature;
            $subscription->save();

            $user->active_membership=$subscription->plan_id;
            $user->membership_expiry=daye('Y-m-d', strtotime('+'.$memberships->validity.' days'));
            $user->save();


            return [
                'status'=>'success',
                'message'=> 'Congratulations! Your subscription at SuzoDailyNeeds is successful',
                'data'=>[
                    'ref_id'=>$subscription->refid,
                    'order_id'=>$subscription->id
                ]
            ];
        }else{
            return [
                'status'=>'failed',
                'message'=>'We apologize, Your payment cannot be verified',
                'data'=>[

                ],
            ];
        }
    }

}
