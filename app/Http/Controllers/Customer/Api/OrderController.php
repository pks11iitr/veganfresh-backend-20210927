<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Clinic;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Wallet;
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

        $request->validate([
            'clinic_id'=>'required|integer',
            'therapy_id'=>'required|integer',
            'num_sessions'=>'required|integer',
            'grade'=>'required|integer|in:1,2,3,4',
            'time'=>'required|date_format:H:i',
            'date'=>'required|date_format:Y-m-d',
        ]);

        $clinic=Clinic::active()->with(['therapies'=>function($therapies)use($request){
            $therapies->where('therapies.isactive', true)->where('therapies.id', $request->therapy_id);
        }])->find($request->clinic_id);

        if(!$clinic || empty($clinic->therapies)){
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];
        }

        //return $clinic;
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

        $refid=env('MACHINE_ID').time();
        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>$cost*$num_sessions,
            'booking_date'=>$request->date,
            'booking_time'=>$request->time
        ]);
        OrderStatus::create([
            'order_id'=>$order->id,
            'current_status'=>$order->status
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

    public function addContactDetails(Request $request, $id){

        $request->validate([
           'name'=>'required|max:60|string',
           'email'=>'email',
           'mobile'=>'required|digits:10',
            'address'=>'string|max:100',
            'lat'=>'numeric',
            'lang'=>'numeric'
        ]);

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
        if($order->update($request->only('name','email','address', 'mobile','lat', 'lang'))){
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


        $itemdetails=[];
        foreach($order->details as $detail){
            $itemdetails[]=[
                'name'=>$detail->entity->name??'',
                'price'=>$detail->cost,
                'quantity'=>$detail->quantity,
                'image'=>$detail->entity->image??''
            ];
        }


        return [
            'status'=>'success',
            'data'=>[
                'orderdetails'=>$order->only('total_cost','refid', 'status','payment_mode', 'name', 'mobile', 'email', 'address'),
                'itemdetails'=>$itemdetails,
                'balance'=>Wallet::balance($user->id),
                'points'=>Wallet::points($user->id)
            ]
        ];
    }

    public function initiateTherapyBooking(){

    }

    public function initiateProductPurchase(){

    }


}
