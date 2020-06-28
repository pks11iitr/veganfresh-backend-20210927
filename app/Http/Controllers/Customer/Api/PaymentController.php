<?php

namespace App\Http\Controllers\Customer\Api;

use App\Events\OrderSuccessfull;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Therapy;
use App\Models\Wallet;
use App\Services\Payment\RazorPayService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $order=Order::with('details.entity')->where('user_id', $user->id)->where('status', 'pending')->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        // set to initial state
        $order->update([
            'use_balance'=>false,
            'use_points'=>false,
            'points_used'=>0,
            'balance_used'=>0,
        ]);



        if($request->use_points==1) {
            $result=$this->payUsingPoints($order);
            if($result['status']=='success'){
                return [
                    'status'=>'success',
                    'message'=>'Congratulations! Your order at Arogyapeeth is successful',
                    'data'=>[
                        'payment_done'=>'yes',
                        'ref_id'=>$order->refid
                    ]
                ];
            }
        }

        if($request->use_balance==1) {
            $result=$this->payUsingBalance($order);
            if($result['status']=='success'){
                return [
                    'status'=>'success',
                    'message'=>'Congratulations! Your order at Arogyapeeth is successful',
                    'data'=>[
                        'payment_done'=>'yes',
                        'ref_id'=>$order->refid
                    ]
                ];
            }

        }

        $result=$this->initiateGatewayPayment($order);

        return $result;

    }

    private function initiateGatewayPayment($order){
        $response=$this->pay->generateorderid([
            "amount"=>($order->total_cost-$order->balance_used)*100,
            "currency"=>"INR",
            "receipt"=>$order->refid,
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
                    'paymentdone'=>'no',
                    'razorpay_order_id'=> $order->order_id,
                    'total'=>($order->total-$order->balance_used)*100,
                    'email'=>$order->email,
                    'mobile'=>$order->mobile,
                    'description'=>'Order Booking at Aarogyapeeth',
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

    private function payUsingBalance($order){

            $walletbalance=Wallet::balance($order->user_id);
            if($walletbalance<=0)
                return [
                    'status'=>'failed',
                    'remaining_amount'=>$order->total_cost
                ];

            if($order->total_cost <= $walletbalance) {
                $order->payment_status='paid';
                $order->status='confirmed';
                $order->use_balance=true;
                $order->balance_used=$order->total_cost*env('POINTS_CONVERSION_RATE');
                $order->payment_mode='online';
                $order->save();

                OrderStatus::create([
                    'order_id'=>$order->id,
                    'current_status'=>$order->status
                ]);

                Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->balance_used, 'CASH', $order->id);

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
                ];
            }
    }

    private function payUsingPoints($order){
        //points can be used for therapy only

        if($order->details[0]->entity instanceof Therapy){
                $walletpoints=Wallet::points($order->user_id);
                $amount=$walletpoints/env('POINTS_CONVERSION_RATE');
                if($amount>=$order->total_cost){
                    $order->payment_status='paid';
                    $order->status='confirmed';
                    $order->use_points=true;
                    $order->points_used=$order->total_cost*env('POINTS_CONVERSION_RATE');
                    $order->payment_mode='online';
                    $order->save();

                    OrderStatus::create([
                        'order_id'=>$order->id,
                        'current_status'=>$order->status
                    ]);

                    Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->points_used, 'POINT', $order->id);

                    Cart::where('user_id', $order->user_id)->delete();

                    return [
                            'status'=>'success',
                        ];
                }
            }

        return [
            'status'=>'failed'
        ];
    }


    public function verifyPayment(Request $request){
        $order=Order::where('order_id', $request->razorpay_order_id)->first();

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        $paymentresult=$this->pay->verifypayment($request->all());
        if($paymentresult){
            if($order->use_balance==true) {
                $balance = Wallet::balance($order->user_id);
                if ($balance < $order->balance_used) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'We apologize, Your order is not successful',
                        'errors' => [

                        ],
                    ], 200);
                }
            }
            $order->status='confirmed';
            $order->payment_id=$request->razorpay_payment_id;
            $order->payment_id_response=$request->razorpay_signature;
            $order->payment_status='paid';
            $order->payment_mode='online';
            $order->save();

            OrderStatus::create([
                'order_id'=>$order->id,
                'current_status'=>$order->status
            ]);

            if($order->balance_used > 0)
                Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$balance, 'CASH', $order->id);

            Cart::where('user_id', $order->user_id)->delete();

            //event(new OrderSuccessfull($order));

            return [
                'status'=>'success',
                'message'=> 'Congratulations! Your order at Arogyapeeth is successful',
                'data'=>[
                    'ref_id'=>$order->refid,
                    'order_id'=>$order->id
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
