<?php

namespace App\Http\Controllers\Customer\Api;

use App\Events\RechargeConfirmed;
use App\Events\RechargeSuccess;
use App\Models\LogData;
use App\Models\Wallet;
use App\Services\Payment\RazorPayService;
use App\Services\Payment\Payu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
     public function __construct(Payu $pay)
     {
         $this->pay=$pay;
     }

    // public function __construct(RazorPayService $pay){
    //     $this->pay=$pay;
    // }

    public function history(Request $request){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        if($user){
            $history=Wallet::where('user_id', $user->id)
                ->where('iscomplete', true)
                ->where('amount_type', 'CASH')
                ->orderBy('id','desc')->get();
            $cashback=Wallet::where('user_id', $user->id)
                ->where('iscomplete', true)
                ->where('amount_type', 'POINT')
                ->orderBy('id','desc')->get();
            $balance=Wallet::balance($user->id);
            $points=Wallet::points($user->id);
        }else{
            $history=[];
            $balance=0;
            $points=0;
        }

        return [
            'status'=>'success',
            'data'=>compact('history','balance', 'points', 'cashback')
        ];
    }

    public function addMoney(Request $request){



        $request->validate([
            'amount'=>'required|integer|min:1'
        ]);

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        if($user){
            //delete all incomplete attempts
            Wallet::where('user_id', $user->id)->where('iscomplete', false)->delete();

            //start new attempt
            $wallet=Wallet::create(['refid'=>env('MACHINE_ID').time(), 'type'=>'Credit', 'amount_type'=>'CASH', 'amount'=>$request->amount, 'description'=>'Wallet Recharge','user_id'=>$user->id]);

            $data=[
                'refid'=>$wallet->refid,
                'amount'=>$request->amount,
                'product'=>'Wallet Recharge at Veganfresh',
                'name'=>$user->name??'',
                'email'=>$user->email??'',
                'mobile'=>$order->mobile??'',
            ];

            $response=$this->pay->generateHash($data);
           // return $response;die;
               $responsearr=json_encode($response);


            //if(isset($responsearr)){
            if(!empty($response)){
//                $wallet->order_id=$wallet->refid;
//                $wallet->order_id_response=$response;
//                $wallet->save();
//                return [
//                    'status'=>'success',
//                    'data'=>[
//                        'id'=>$wallet->id,
//                        'order_id'=>$wallet->order_id,
//                        'amount'=>$wallet->amount,
//                        'email'=>$user->email,
//                        'name'=>$user->name,
//                        'mobile'=>$user->mobile,
//                        'description'=>'Add Money',
//                        'hashdata'=>$response
//                    ]
//                ];

                return [
                    'status'=>'success',
                    'data'=>[
                        'total'=>$request->amount,
                        'email'=>$user->email??'',
                        'mobile'=>$user->mobile??'',
                        'product'=>'Wallet recharge at Veganfresh',
                        'name'=>$user->name??'',
                        'refid'=>$wallet->refid,
                        'hashdata'=>$response,
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

        $data = json_decode($request->json_data, true);
        $result= $data['result']??[];
        $status = $result['status'];
        $refid = $result['txnid'];
        $hash = $result['hash'];
        $email = $result['email'];
        $productinfo = $result['productinfo'];
        $firstname = $result['firstname'];
        $amount = $result['amount'];
        $paymentid = $result['paymentId'];


//        $user=auth()->guard('customerapi')->user();
//        if(!$user)
//            return [
//                'status'=>'failed',
//                'message'=>'Please login to continue'
//            ];
        $wallet=Wallet::where('refid', $refid)->first();
        if(!$wallet){
            return [
                'status'=>'failed',
                'message'=>'No Record found'
            ];
        }

        $data=[
            "amount"=>$amount,
            //"currency"=>"INR",
            "refid"=>$refid,
            "product"=>$productinfo,
            "email"=>$email,
            "name"=>$firstname,
            "status"=>$status
        ];

        LogData::create([
            'data'=>json_encode($data),
            'type'=>'verify'
        ]);

        //$paymentresult=$this->pay->verifypayment($request->all());
        $paymentresult=$this->pay->verifyhash($data);
        if(strtolower($paymentresult)==strtolower($hash)) {
        //if($paymentresult){
            $wallet->payment_id=$paymentid;
            //$wallet->payment_id_response=$request->razorpay_signature;
            $wallet->iscomplete=true;
            $wallet->save();

            event(new RechargeConfirmed($wallet));

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
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

//        DB::beginTransaction();
//        $balance=Wallet::balancewithlock($user->id);
//        echo date('h:i:s');
//        sleep(10);
//        echo date('h:i:s');
//        DB::commit();

        if($user){
            return [
                'balance'=>Wallet::balance($user->id),
                'points'=>Wallet::points($user->id)
            ];
        }
    }



    public function surl(){
        return view('Payment.surl');
    }

    public function furl(){
        return view('Payment.furl');
    }











}
