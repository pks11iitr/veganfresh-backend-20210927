<?php

namespace App\Http\Controllers\Rider\Api;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RiderOrderController extends Controller
{

    public function index(Request $request){
      //  $user=auth()->guard('riderapi')->user();
        $user=$request->user;
      //  var_dump($user->id);die;
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $orders=Order::with(['details'])
            ->where('status', '!=','pending')
            ->where('rider_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
//return $orders;
        $lists=[];

        foreach($orders as $order) {
            //echo $order->id.' ';
            $total = count($order->details);
            $lists[] = [
                'id' => $order->id,
                'title' => ($order->details[0]->name ?? '') . ' ' . ($total > 1 ? 'and ' . ($total - 1) . ' more' : ''),
                'booking_id' => $order->refid,
                'datetime' => date('D d M,Y', strtotime($order->created_at)),
                'total_price' => $order->total_cost,
                'image' => $order->details[0]->image ?? ''
            ];
        }
        return [
            'status'=>'success',
            'data'=>$lists
        ];

    }

    public function orderdetails(Request $request, $id){

        $show_delivered=0;

        $user=auth()->guard('riderapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $order=Order::with(['details.size', 'deliveryaddress'])
            ->where('rider_id', $user->id)
            ->where('status', '!=', 'pending')
            ->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];


        $itemdetails=[];
        $savings=0;
        foreach($order->details as $detail){

            $itemdetails[]=[
                'name'=>$detail->name??'',
                'image'=>$detail->image??'',
                'company'=>$detail->entity->company??'',
                'price'=>$detail->price,
                'cut_price'=>$detail->cut_price,
                'quantity'=>$detail->quantity,
                'size'=>$detail->size->name??'',
                'item_id'=>$detail->entity_id,
                'show_return'=>($detail->status=='dispatched'?1:0),
                //'show_cancel'=>in_array($detail->status, ['confirmed'])?1:0
            ];
            $savings=$savings+($detail->cut_price-$detail->price);

        }

        // options to be displayed
        if($order->status=='dispatched'){
            $show_delivered=1;
        }

        $prices=[
            'total'=>$order->total_cost,
            'delivery_charge'=>$order->delivery_charge,
            'coupon_discount'=>$order->coupon_discount,
            'total_savings'=>$savings+$order->coupon_discount,
            'total_paid'=>$order->total_cost+$order->delivery_charge-$order->coupon_discount,
        ];


        return [
            'status'=>'success',
            'data'=>[
                'orderdetails'=>$order->only('id', 'total_cost','refid', 'status','payment_mode', 'name', 'mobile', 'email', 'address','booking_date', 'booking_time','is_instant','status'),
                'itemdetails'=>$itemdetails,
                'show_cancel_product'=>$show_cancel_product??0,
                'deliveryaddress'=>$order->deliveryaddress??'',
                'prices'=>$prices,
            ]
        ];
    }

    public function returnProduct(Request $request){

        $request->validate([

            'item_id'=>'required|integer',
            'quantity'=>'required|integer'

        ]);

        $user=auth()->guard('riderapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $item = OrderDetail::with(['order', 'entity', 'size'])->find($request->item_id);

        if(!$item || $item->order->rider_id!=$user->id){
            return [

                'status'=>'failed',
                'message'=>'No Such Order Found'

            ];
        }

        if($request->quantity > $item->quantity){
            return [

                'status'=>'failed',
                'message'=>'You Cannot Return More Than Added Quantity'

            ];
        }

        //adjust total

        if($item->order->payment_status=='paid'){

        }else{

        }

    }



    public function markDelivered(Request $request, $order_id){

        $user=auth()->guard('riderapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::find($order_id);


        if(!$order || $order->rider_id!=$user)
            return [

                'status'=>'failed',
                'message'=>'No Such Order Found'

            ];

        if($order->status!='dispatched'){
            return [

                'status'=>'failed',
                'message'=>'This Product Cannot Be Delivered'

            ];
        }

        if($order->payment_mode=='COD'){

            $order->payment_status='paid';

            if($order->use_points){

                if($order->points_used > 0)
                    Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->points_used, 'POINT', $order->id);

                if($order->balance_used > 0)
                    Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->balance_used, 'CASH', $order->id);

            }

        }

        $order->status='completed';
        $order->save();

        OrderStatus::create([
            'order_id'=>$order->id,
            'current_status'=>$order->status
        ]);


        return [
            'status'=>'success',
            'message'=>'Order Has Been Delivered'
        ];

    }

    public function checkTotalAfterReturn(Request $request){
        
    }






}
