<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Clinic;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function initiateOrder(Request $request){

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        switch($request->type){
            case 'clinic':
                return $this->initiateClinicBooking($request);
            case 'therapy':
                return $this->initiateTherapyBooking($request);
            case 'product':
                return $this->initiateProductPurchase($request);
            default:
                return [
                    'status'=>'failed',
                    'message'=>'Invalid Operation Performed'
                ];
        }
    }

    public function initiateClinicBooking(Request $request){
        $clinic=Clinic::active()->with(['therapies'=>function($therapies)use($request){
            $therapies->where('isactive', true)->where('therapies.id', $request->therapy_id);
        }])->find($request->clinic_id);

        if(!$clinic || empty($clinic->therapies)){
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];
        }

        $grade=$request->grade??1;
        $num_sessions=$request->num_sessions??1;
        switch($grade){
            case 1:$cost=($clinic->therapies[0]->pivot->grade1_price??0);
                break;
            case 2:$cost=($clinic->therapies[0]->pivot->grade2_price??0);
                break;
            case 3:$cost=($clinic->therapies[0]->pivot->grade3_price??0);
                break;
            case 4:$cost=($clinic->therapies[0]->pivot->grade4_price??0);
                break;
        }

        $refid=rand(1,9).date('y-m-d H:i:s');
        $order=Order::create([
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>$cost*$num_sessions
        ]);

        OrderDetail::create([
            'order_id'=>$order->id,
            'entity_type'=>'App\Models\Therapy',
            'entity_id'=>$clinic->therapies[0]->id,
            'clinic_id'=>$clinic->id,
            'cost'=>$cost,
            'quantity'=>$num_sessions
        ]);

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id
            ]
        ];
    }

    public function setContactInfo(Request $request, $id){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $order=Order::where('user_id', $user->id)->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];
        $request->merge(['order_details_completed'=>true]);
        if($order->update($request->only('name','email','address', 'mobile'))){
            return [
                'status'=>'success',
                'message'=>'Address has been updated'
            ];
        }

    }

    public function orderdetails(Request $request, $id){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $order=Order::with('details.entity')->where('user_id', $user->id)->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];


        return [
            'status'=>'success',
            'data'=>[
                'orderdetails'=>$order->only('total_cost','refid', 'status','payment_mode', 'name', 'mobile', 'email', 'address'),
                'itemdetails'=>$order->details
            ]
        ];
    }


    public function initiateTherapyBooking(){

    }

    public function initiateProductPurchase(){

    }


}
