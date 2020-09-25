<?php

namespace App\Http\Controllers\Rider\Api;

use App\Models\Coupon;
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
        $show_return=0;

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
                'id'=>$detail->id
                //'show_return'=>($detail->status=='dispatched'?1:0),
                //'show_cancel'=>in_array($detail->status, ['confirmed'])?1:0
            ];
            $savings=$savings+($detail->cut_price-$detail->price);

        }

        // options to be displayed
        if($order->status=='dispatched'){
            $show_delivered=1;
            $show_return=1;
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
                'show_delivered'=>$show_delivered,
                'show_return'=>$show_return
            ]
        ];
    }

    public function checkTotalAfterReturn(Request $request, $order_id){

        $request->validate([

            'items'=>'required|array',
            'items.*'=>'required|integer'

        ]);

        $user=auth()->guard('riderapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];


        $itemids=[];
        foreach($request->items as $key=>$value){
            if(!empty($value))
                $itemids[]=$key;
        }

        $order=Order::with(['details'=>function($details)use($itemids){
            $details->whereIn('details.id', $itemids);
        }])
            ->where('rider_id', $user->id)
            ->find($order_id);

        if(!$order || empty($order->details->toArray()))
            return [
                'status'=>'failed',
                'message'=>'No Such Order Found'
            ];


//        $items=OrderDetail::with('order', 'size', 'entity')
//            ->whereHas('order', function($order)use($order_id){
//                $order->where('orders.id', $order_id);
//            })
//            ->whereIn('details.id', $itemids)
//            ->get();
//
//        if(!count($items))
//            return [
//                'status'=>'failed',
//                'message'=>'No Valid Item Found'
//            ];

        $total=0;
        $itemids=[];
        foreach($order->details as $item){
            //if($item->order->rider_id==$user->id){
                if($request[$key]>$item->quantity){
                    return [
                        'status'=>'failed',
                        'message'=>'Invalid Request'
                    ];
                }
                $total=$total+$item->price*$request[$key];
                $itemids[]=$key;
//            }else
//                return [
//                    'status'=>'failed',
//                    'message'=>'Invalid Request'
//                ];
        }

        //$order=$items[0]->order;

        if($order->coupon_applied && $order->coupon_discount){

            $total_cost=$order->total_cost+$order->coupon_discount;

        }else{
            $total_cost=$order->total_cost;
        }

        if($order->coupon_applied){
            $coupon=Coupon::where('code', $order->coupon_applied)->first();

            $coupon_discount=$coupon->getCouponDiscount($total_cost);
        }else{
            $coupon_discount=0;
        }


        $prices=[
            'total'=>$total_cost,
            'delivery_charge'=>$order->delivery_charge,
            'coupon_discount'=>$coupon_discount,
            'total_paid'=>$order->total_cost+$order->delivery_charge-$coupon_discount,
        ];

        return [
            'status'=>'success',
            'prices'=>$prices
        ];

    }

    public function returnProduct(Request $request, $order_id){

        $request->validate([

            'items'=>'required|array',
            'items.*'=>'required|integer'

        ]);

        $user=auth()->guard('riderapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];


        $itemids=[];
        foreach($request->items as $key=>$value){
            if(!empty($value))
                $itemids[]=$key;
        }

        $order=Order::with(['details'=>function($details)use($itemids){
            $details->whereIn('details.id', $itemids);
        }])
            ->where('rider_id', $user->id)
            ->find($order_id);

        if(!$order || empty($order->details->toArray()))
            return [
                'status'=>'failed',
                'message'=>'No Such Order Found'
            ];


//        $items=OrderDetail::with('order', 'size', 'entity')
//            ->whereHas('order', function($order)use($order_id){
//                $order->where('orders.id', $order_id);
//            })
//            ->whereIn('details.id', $itemids)
//            ->get();
//
//        if(!count($items))
//            return [
//                'status'=>'failed',
//                'message'=>'No Valid Item Found'
//            ];

        $total=0;
        $itemids=[];
        foreach($order->details as $item){
            //if($item->order->rider_id==$user->id){
            if($request[$key]>$item->quantity){
                return [
                    'status'=>'failed',
                    'message'=>'Invalid Request'
                ];
            }
            $total=$total+$item->price*$request[$key];
            $itemids[]=$key;
//            }else
//                return [
//                    'status'=>'failed',
//                    'message'=>'Invalid Request'
//                ];
        }

        //$order=$items[0]->order;

        if($order->coupon_applied && $order->coupon_discount){

            $total_cost=$order->total_cost+$order->coupon_discount;

        }else{
            $total_cost=$order->total_cost;
        }

        $coupon=Coupon::where('code', $order->coupon_applied)->first();
        $coupon_discount=$coupon->getCouponDiscount($total_cost);

        //if()



        $order->total_cost=$total_cost;
        $order->coupon_discount=$coupon_discount;

        return [

            'status'=>'success',
            'message'=>'Items Has Been returned'

        ];

    }



    public function markDelivered(Request $request, $order_id){

        $user=auth()->guard('riderapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::find($order_id);


        if(!$order || $order->rider_id!=$user->id)
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

}
