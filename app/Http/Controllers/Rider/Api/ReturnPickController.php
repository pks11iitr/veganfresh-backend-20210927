<?php

namespace App\Http\Controllers\Rider\Api;

use App\Models\ReturnRequest;
use App\Services\SMS\Msg91;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReturnPickController extends Controller
{
    public function index(Request $request){

        $user=auth()->guard('riderapi')->user();

        $returnsobj=ReturnRequest::with('product', 'order.deliveryaddress', 'order.storename', 'size')
            //->where('rider_status', 'pending')
            ->whereHas('order', function($order) use($user){
                    $order->where('rider_id', $user->id);
            })
        ->where('status', 'approved')
        ->orderBy('return_requests.id', 'desc')
        ->get();

        $returns=[];
        foreach($returnsobj as $return){
            $returns[]=array(
                "id"=>$return->id,
                "storename"=>$return->order->storename->name??'',
                "ref_id"=>$return->order->refid,
                "name"=>$return->product->name??'',
                "size"=>$return->size->size??'',
                "quantity"=>$return->quantity,
                "price"=>$return->price,
                "image"=>$return->product->image??'',
                "created_at"=>date('Y-m-d h:iA', strtotime($return->created_at)),
                'status'=>$return->rider_status,
                'delivery_address'=>$return->order->deliveryaddress??null
            );
        }

        return [
            'status'=>'success',
            'data'=>compact('returns')
        ];

    }

    public function markPickup(Request $request, $id){

        $user=auth()->guard('riderapi')->user();

        $return = ReturnRequest::with('order.customer')->findOrFail($id);

        $return->rider_status='complete';
        $return->save();

        if(isset($return->order->customer->mobile))
            Msg91::send($return->order->customer->mobile, 'Return has been picked up for Order ID:'.$return->order->refid.', Product: '.($return->product->name??''), $request->reason, env('RETURN_PICKED_UP'));

        return [
            'status'=>'success',
            'message'=>'Return has been picked'
        ];

    }


}
