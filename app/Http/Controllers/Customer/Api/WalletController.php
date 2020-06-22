<?php

namespace App\Http\Controllers\Customer\Api;

use App\Events\RechargeSuccess;
use App\Models\Wallet;
use App\Services\Payment\RazorPayService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    public function __construct(RazorPayService $pay){
        $this->pay=$pay;
    }

    public function history(Request $request){
        $user=auth()->user();
        if($user){
            $history=Wallet::where('user_id', $user->id)->where('iscomplete', true)->orderBy('id','desc')->get();
            $balance=Wallet::balance($user->id);
        }else{
            $history=[];
            $balance=0;
        }

        return compact('history','balance');
    }

    public function addMoney(Request $request){
        $request->validate([
            'amount'=>'required|integer|min:1'
        ]);

        $user=auth()->user();
        if($user){
            //delete all incomplete attempts
            Wallet::where('user_id', $user->id)->where('iscomplete', false)->delete();

            //start new attempt
            $wallet=Wallet::create(['refid'=>date('YmdHis'), 'type'=>'Credit', 'amount'=>$request->amount, 'description'=>'Wallet Recharge','user_id'=>$user->id]);

            $response=$this->pay->generateorderid([
                "amount"=>$wallet->amount*100,
                "currency"=>"INR",
                "receipt"=>$wallet->refid.'',
            ]);
            $responsearr=json_decode($response);
            if(isset($responsearr->id)){
                $wallet->order_id=$responsearr->id;
                $wallet->order_id_response=$response;
                $wallet->save();
                return [
                    'status'=>'success',
                    'data'=>[
                        'id'=>$wallet->id,
                        'order_id'=>$wallet->order_id,
                        'amount'=>$wallet->amount*100
                    ]
                ];
            }else{
                return response()->json([
                    'status'=>'failed',
                    'message'=>'Payment cannot be initiated',
                    'data'=>[
                    ],
                ], 200);
            }
        }

        return response()->json([
            'status'=>'failed',
            'message'=>'logout',
            'data'=>[
            ],
        ], 200);

    }

    public function verifyRecharge(Request $request){
        $user=auth()->user();
        $wallet=Wallet::where('order_id', $request->razorpay_order_id)->firstOrFail();
        $paymentresult=$this->pay->verifypayment($request->all());
        if($paymentresult){
            $wallet->payment_id=$request->razorpay_payment_id;
            $wallet->payment_id_response=$request->razorpay_signature;
            $wallet->iscomplete=true;
            $wallet->save();
            event(new RechargeSuccess($wallet));
            return response()->json([
                'status'=>'success',
                'message'=>'Payment is successfull',
                'errors'=>[

                ],
            ], 200);
        }else{
            return response()->json([
                'status'=>'failed',
                'message'=>'Payment is not successfull',
                'errors'=>[

                ],
            ], 200);
        }
    }

    public function getWalletBalance(Request $request){
        $user=auth()->user();
        if($user){
            return ['balance'=>Wallet::balance($user->id)];
        }
    }
}
