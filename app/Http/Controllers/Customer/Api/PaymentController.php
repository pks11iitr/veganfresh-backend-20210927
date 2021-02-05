<?php

namespace App\Http\Controllers\Customer\Api;

use App\Events\OrderConfirmed;
use App\Events\OrderSuccessfull;
use App\Events\RescheduleConfirmed;
use App\Models\BookingSlot;
use App\Models\Cart;
use App\Models\Configuration;
use App\Models\Coupon;
use App\Models\HomeBookingSlots;
use App\Models\LogData;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\RescheduleRequest;
use App\Models\Therapy;
use App\Models\TimeSlot;
use App\Models\Wallet;
use App\Services\Payment\RazorPayService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(RazorPayService $pay)
    {
        $this->pay=$pay;
    }

    public function initiatePayment(Request $request, $id){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];


        //$timeslot=TimeSlot::getNextDeliverySlot();

        $order=Order::with(['details.entity', 'details.size'])
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No record found'
            ];

        foreach($order->details as $detail){
            if(OrderDetail::removeOutOfStockItems($detail))
                return [
                    'status'=>'failed',
                    'message'=>'Some items from your cart are not available'
                ];
        }

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        // set to initial state

        if($request->time_slot){
            $timeslot=explode('**', $request->time_slot);
        }

        if(!empty($request->express_delivery)){

            $express_delivery=Configuration::where('param', 'express_delivery')->first();
            $express_delivery=[
                'text'=>$express_delivery->description??'',
                'price'=>$express_delivery->value??$order->delivery_charge
            ];

            $order->update([
                'use_balance'=>false,
                'use_points'=>false,
                'points_used'=>0,
                'balance_used'=>0,
                'coupon_applied'=>null,
                'coupon_discount'=>0,
                'delivery_slot'=>$timeslot[0]??null,
                'delivery_date'=>$timeslot[1]??null,
                'delivery_charge'=>$express_delivery['price'],
                'is_express_delivery'=>true
            ]);
        }else{
            $order->update([
                'use_balance'=>false,
                'use_points'=>false,
                'points_used'=>0,
                'balance_used'=>0,
                'coupon_applied'=>null,
                'coupon_discount'=>0,
                'delivery_slot'=>$timeslot[0]??null,
                'delivery_date'=>$timeslot[1]??null,
                'is_express_delivery'=>false

            ]);
        }

        if(!empty($request->coupon)){
            $coupon=Coupon::active()->where('code', $request->coupon)->first();
            if(!$coupon){
                return [
                    'status'=>'failed',
                    'message'=>'Invalid Coupon'
                ];
            }
            if($coupon && !$coupon->getUserEligibility($user)){
                return [
                    'status'=>'failed',
                    'message'=>'Coupon Has Been Expired'
                ];
            }

            $order->applyCoupon($coupon);
        }

        if($request->use_points==1) {
            $result=$this->payUsingPoints($order);
            if($result['status']=='success'){

                event(new OrderConfirmed($order));

                return [
                    'status'=>'success',
                    'message'=>'Congratulations! Your order at Hallobasket is successful',
                    'data'=>[
                        'payment_done'=>'yes',
                        'ref_id'=>$order->refid,
                        'refid'=>$order->refid,
                        'order_id'=>$order->id
                    ]
                ];
            }
        }

        if($request->use_balance==1) {
            $result=$this->payUsingBalance($order);
            if($result['status']=='success'){

                event(new OrderConfirmed($order));

                return [
                    'status'=>'success',
                    'message'=>'Congratulations! Your order at Hallobasket is successful',
                    'data'=>[
                        'payment_done'=>'yes',
                        'ref_id'=>$order->refid,
                        'refid'=>$order->refid,
                        'order_id'=>$order->id
                    ]
                ];
            }

        }
        if($request->type=='cod'){
//            return [
//                'status'=>'failed',
//                'message'=>'Your Account Has Been Blocked'
//            ];
            $result=$this->initiateCODPayment($order);
        }else{
            $result=$this->initiateGatewayPayment($order);
        }


        return $result;

    }

    private function payUsingPoints($order){
        //points can be used for therapy only

        $walletpoints=Wallet::points($order->user_id);
        if($walletpoints<=0)
            return [
                'status'=>'failed',
                'remaining_amount'=>$order->total_cost+$order->delivery_charge+$order->extra_amount-$order->coupon_discount
            ];

        if($walletpoints >= $order->total_cost+$order->delivery_charge+$order->extra_amount-$order->coupon_discount){
            $order->payment_status='paid';
            $order->status='confirmed';
            $order->use_points=true;
            $order->points_used=$order->total_cost+$order->delivery_charge-$order->coupon_discount+$order->extra_amount;
            $order->payment_mode='online';
            $order->save();

            //$order->changeDetailsStatus('confirmed');

            OrderStatus::create([
                'order_id'=>$order->id,
                'current_status'=>$order->status
            ]);

            Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->points_used, 'POINT', $order->id);

            Order::deductInventory($order);

            Cart::where('user_id', $order->user_id)->delete();

            return [
                'status'=>'success',
            ];
        }else{

            $order->use_points=true;
            $order->points_used=$walletpoints;
            $order->payment_mode='online';
            $order->save();

            OrderStatus::create([
                'order_id'=>$order->id,
                'current_status'=>$order->status
            ]);

            return [
                'status'=>'failed',
                'remaining_amount'=>$order->total_cost+$order->delivery_charge+$order->extra_amount-$order->coupon_discount-$order->points_used
            ];

        }


    }

    private function payUsingBalance($order){

        $walletbalance=Wallet::balance($order->user_id);
        if($walletbalance<=0)
            return [
                'status'=>'failed',
                'remaining_amount'=>$order->total_cost+$order->delivery_charge+$order->extra_amount-$order->coupon_discount-$order->points_used
            ];

        if($walletbalance >= $order->total_cost+$order->delivery_charge+$order->extra_amount-$order->coupon_discount-$order->points_used) {
            $order->payment_status='paid';
            $order->status='confirmed';
            $order->use_balance=true;
            $order->balance_used=$order->total_cost+$order->delivery_charge+$order->extra_amount-$order->coupon_discount-$order->points_used;
            $order->payment_mode='online';
            $order->save();

            $order->changeDetailsStatus('confirmed');

            OrderStatus::create([
                'order_id'=>$order->id,
                'current_status'=>$order->status
            ]);

            if($order->points_used)
                Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->points_used, 'POINT', $order->id);

            Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->balance_used, 'CASH', $order->id);

            Order::deductInventory($order);

            Cart::where('user_id', $order->user_id)->delete();

            return [
                'status'=>'success',
            ];
        }else {

            $order->use_balance=true;
            $order->balance_used=$walletbalance;
            $order->payment_mode='online';
            $order->save();

            return [
                'status'=>'failed',
                'remaining_amount'=>$order->total_cost+$order->delivery_charge+$order->extra_amount-$order->coupon_discount-$order->points_used-$order->balance_used
            ];

        }

    }

    private function initiateGatewayPayment($order){
        $data=[
            "amount"=>($order->total_cost+$order->delivery_charge+$order->extra_amount-$order->coupon_discount-$order->points_used-$order->balance_used)*100,
            "currency"=>"INR",
            "receipt"=>$order->refid,
        ];

        $response=$this->pay->generateorderid($data);

        LogData::create([
            'data'=>($response.' orderid:'.$order->id. ' '.json_encode($data)),
            'type'=>'order'
        ]);

        $responsearr=json_decode($response);
        //var_dump($responsearr);die;
        if(isset($responsearr->id)){
            $order->order_id=$responsearr->id;
            $order->order_id_response=$response;
            $order->save();
            return [
                'status'=>'success',
                'message'=>'success',
                'data'=>[
                    'payment_done'=>'no',
                    'razorpay_order_id'=> $order->order_id,
                    'total'=>($order->total_cost+$order->delivery_charge+$order->extra_amount-$order->coupon_discount-$order->points_used-$order->balance_used)*100,
                    'email'=>$order->email,
                    'mobile'=>$order->mobile,
                    'description'=>'Product Purchase at HalloBasket',
                    'name'=>$order->name,
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

    private function initiateCodPayment($order){
        $user=auth()->guard('customerapi')->user();
        if($user->status==2){
            return [
                'status'=>'failed',
                'message'=>'Your Account Has Been Blocked'
            ];
        }

        if ($order->use_points == true) {
            $walletpoints = Wallet::points($order->user_id);
            if ($walletpoints < $order->points_used) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'We apologize, Your order is not successful due to low cashback',
                    'errors' => [

                    ],
                ], 200);
            }
        }

        if ($order->use_balance == true) {
            $balance = Wallet::balance($order->user_id);
            if ($balance < $order->balance_used) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'We apologize, Your order is not successful due to low wallet balance',
                    'errors' => [

                    ],
                ], 200);
            }
        }

        $order->payment_mode='COD';
        $order->status='confirmed';
        $order->save();

        if($order->points_used > 0)
            Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->points_used, 'POINT', $order->id);

        if($order->balance_used > 0)
            Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->balance_used, 'CASH', $order->id);

        Order::deductInventory($order);

        event(new OrderConfirmed($order));

        Cart::where('user_id', $order->user_id)->delete();


        return [
            'status'=>'success',
            'message'=>'Congratulations! Your order at HalloBasket is successful',
            'data'=>[
                'payment_done'=>'yes',
                'refid'=>$order->refid
            ],
        ];
    }

    public function verifyPayment(Request $request){

        $request->validate([
           'razorpay_order_id'=>'required',
            'razorpay_signature'=>'required',
            'razorpay_payment_id'=>'required'

        ]);


        LogData::create([
            'data'=>(json_encode($request->all())??'No Payment Verify Data Found'),
            'type'=>'verify'
        ]);

        $order=Order::with('details')->where('order_id', $request->razorpay_order_id)->first();

        if(!$order || $order->status!='pending')
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        $paymentresult=$this->pay->verifypayment($request->all());
        if($paymentresult) {
            if ($order->use_points == true) {
                $walletpoints = Wallet::points($order->user_id);
                if ($walletpoints < $order->points_used) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'We apologize, Your order is not successful due to low cashback',
                        'errors' => [

                        ],
                    ], 200);
                }
            }

            if ($order->use_balance == true) {
                $balance = Wallet::balance($order->user_id);
                if ($balance < $order->balance_used) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'We apologize, Your order is not successful due to low wallet balance',
                        'errors' => [

                        ],
                    ], 200);
                }
            }
            $order->status = 'confirmed';
            $order->payment_id = $request->razorpay_payment_id;
            $order->payment_id_response = $request->razorpay_signature;
            $order->payment_status = 'paid';
            $order->payment_mode = 'online';
            $order->save();

            $order->changeDetailsStatus('confirmed');

            OrderStatus::create([
                'order_id'=>$order->id,
                'current_status'=>$order->status
            ]);

            if($order->points_used > 0)
                Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->points_used, 'POINT', $order->id);

            if($order->balance_used > 0)
                Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->balance_used, 'CASH', $order->id);

            Order::deductInventory($order);

            Cart::where('user_id', $order->user_id)->delete();

            //event(new OrderSuccessfull($order));
            event(new OrderConfirmed($order));
            return [
                'status'=>'success',
                'message'=> 'Congratulations! Your order at Hallobasket is successful',
                'data'=>[
                    'ref_id'=>$order->refid,
                    'order_id'=>$order->id,
                    'refid'=>$order->refid,
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
