<?php

namespace App\Http\Controllers\Rider\Api;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Membership;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\ReturnProduct;
use App\Models\Wallet;
use App\Services\Notification\FCMNotification;
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
            ->where('status','dispatched')
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
            'data'=>$lists,
            'user'=>$user->only(['name', 'mobile', 'image'])
        ];

    }

    public function passedorder(Request $request){
        //  $user=auth()->guard('riderapi')->user();
        $user=$request->user;
        //  var_dump($user->id);die;
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $orders=Order::with(['details'])
            ->where('status', 'completed')
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
            'data'=>$lists,
            'user'=>$user->only(['name', 'mobile', 'image'])
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
        $order=Order::with(['details.size', 'deliveryaddress', ])
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
                'size'=>$detail->size->size??'',
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
            'cashback_used'=>$order->points_used,
            'balance_used'=>$order->balance_used,
            'total_savings'=>$savings+$order->coupon_discount,
            'total_paid'=>$order->total_cost+$order->delivery_charge-$order->coupon_discount-$order->points_used-$order->balance_used,
        ];

        $delivery_time=$order->delivery_date.' '.($order->timeslot->name??'');
        $delivered_at=$order->delivered_at??'Not Yet Delivered';

        return [
            'status'=>'success',
            'data'=>[
                'orderdetails'=>$order->only('id', 'total_cost','refid', 'status','payment_mode', 'name', 'mobile', 'email', 'address','booking_date', 'booking_time','is_instant','status'),
                'itemdetails'=>$itemdetails,
                'show_cancel_product'=>$show_cancel_product??0,
                'deliveryaddress'=>$order->deliveryaddress??'',
                'prices'=>$prices,
                'show_delivered'=>$show_delivered,
                'show_return'=>$show_return,
                'delivery_time'=>$delivery_time,
                'delivered_at'=>$delivered_at,
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

        $total_return=0;
        //$itemids=[];
        foreach($order->details as $item){
            //if($item->order->rider_id==$user->id){
                if($request->items[$item->id]>$item->quantity){
                    return [
                        'status'=>'failed',
                        'message'=>'Invalid Request'
                    ];
                }
                $total_return=$total_return+$item->price*$request->items[$item->id];
                //$itemids[]=$item->id;
//            }else
//                return [
//                    'status'=>'failed',
//                    'message'=>'Invalid Request'
//                ];
        }

        //$order=$items[0]->order;

        if($order->coupon_applied && $order->coupon_discount){

            //$total_cost=$order->total_cost+$order->coupon_discount;
            $total_cost=$order->total_cost-$total_return;
            $coupon=Coupon::where('code', $order->coupon_applied)->first();
            $coupon_discount=$coupon->getCouponDiscount($total_cost);
        }else{
            $total_cost=$order->total_cost-$total_return;
            $coupon_discount=0;
        }

        $prices=[
            'total'=>$total_cost,
            'delivery_charge'=>($total_cost>0)?$order->delivery_charge:0,
            'coupon_discount'=>($total_cost>0)?$coupon_discount:0,
            'cashback_used'=>$order->points_used,
            'balance_used'=>$order->balance_used,
            'total_paid'=>($total_cost>0)?($total_cost+$order->delivery_charge-$coupon_discount-$order->balance_used-$order->points_used):0,
        ];

        return [
            'status'=>'success',
            'prices'=>$prices
        ];

    }

//    public function returnProduct(Request $request, $order_id){
//
//        //change
//
//        $request->validate([
//
//            'items'=>'required|array',
//            'items.*'=>'required|integer'
//
//        ]);
//
//        $user=auth()->guard('riderapi')->user();
//        if(!$user)
//            return [
//                'status'=>'failed',
//                'message'=>'Please login to continue'
//            ];
//
//
//        $itemids=[];
//        foreach($request->items as $key=>$value){
//            if(!empty($value))
//                $itemids[]=$key;
//        }
//
//        $order=Order::with(['details'=>function($details)use($itemids){
//            $details->with(['entity', 'size'])
//                ->whereIn('details.id', $itemids);
//        }])
//            ->where('status', 'dispatched')
//            ->where('rider_id', $user->id)
//            ->find($order_id);
//
//        if(!$order || empty($order->details->toArray()))
//            return [
//                'status'=>'failed',
//                'message'=>'No Such Order Found'
//            ];
//
//
//        $prev_total=$order->total_cost;
//        $prev_delivery=$order->delivery_charge;
//        $prev_cashback=$order->points_used;
//        $prev_balance=$order->balance_used;
//        $prev_discount=$order->coupon_discount;
//
//
//        $total_return=0;
//        //$itemids=[];
//        $details=[];
//        foreach($order->details as $item){
//            //if($item->order->rider_id==$user->id){
//            if($request->items[$item->id] > $item->quantity){
//                return [
//                    'status'=>'failed',
//                    'message'=>'Invalid Request'
//                ];
//            }
//            $total_return=$total_return+$item->price*$request->items[$item->id];
//            $details[]=$item;
//        }
//
//        //$order=$items[0]->order;
//        if($order->coupon_applied && $order->coupon_discount){
//
//            //$total_cost=$order->total_cost+$order->coupon_discount;
//            $total_cost=$order->total_cost-$total_return;
//            $coupon=Coupon::where('code', $order->coupon_applied)->first();
//            $coupon_discount=$coupon->getCouponDiscount($total_cost);
//
//        }else{
//            $total_cost=$order->total_cost-$total_return;
//            $coupon_discount=0;
//        }
//
//
//        foreach($details as $d){
//
//            ReturnProduct::create([
//
//                'order_id'=>$d->order_id,
//                'store_id'=>$order->store_id,
//                'rider_id'=>$order->rider_id,
//                'ref_id'=>$order->refid,
//                'entity_id'=>$d->entity_id,
//                'entity_type'=>$d->entity_type,
//                'size_id'=>$d->size_id,
//                'name'=>$d->name,
//                'image'=>$d->image,
//                'price'=>$d->price,
//                'cut_price'=>$d->cut_price,
//                'quantity'=>$request->items[$d->id],
//                'reason'=>$request->reason
//
//            ]);
//            if($d->quantity==$request->items[$d->id])
//                $d->delete();
//            else{
//                $d->quantity=$d->quantity - $request->items[$d->id];
//                $d->save();
//            }
//
//            Order::increaseItemCount($d, $request->items[$d->id]*$d->size->consumed_units);
//        }
//
//        //refund when total goes to 0
//        if($total_cost==0){
//            $order->total_cost=0;
//            $order->coupon_discount=0;
//            $order->delivery_charge=0;
//            $order->balance_used=0;
//            $order->points_used=0;
//            //$order->status='completed';
//            $order->save();
//
//            if($order->payment_mode!='COD') {
//                //Return Cashback Used
//                if ($prev_cashback) {
//                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_cashback, 'POINT', $order->id);
//                }
//
//                //Return Point+delivery charge+paid amount
//                Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_total-$prev_discount-$prev_cashback+$prev_delivery, 'CASH', $order->id);
//
//            }else{
//                //Return cashback used
//                if ($prev_cashback) {
//                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_cashback, 'POINT', $order->id);
//                }
//                //Return balance used
//                if ($prev_balance) {
//                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_balance+$prev_delivery, 'CASH', $order->id);
//                }
//            }
//
//            return [
//                'status'=>'success',
//                'message'=>'Items Has Been returned'
//            ];
//        }
//
//        $order->total_cost=$total_cost;
//        $order->coupon_discount=$coupon_discount;
//        $order->delivery_charge=$prev_delivery;
//
//        if($order->payment_mode!='COD') {
//            if($total_cost-$coupon_discount > $prev_cashback+$prev_balance){
//                $prev_paid=($prev_total-$prev_discount - $prev_cashback-$prev_balance);
//                $payble_amount=($total_cost-$coupon_discount - $prev_cashback-$prev_balance);
//                $order->balance_used=$prev_balance;
//                $order->points_used=$prev_cashback;
//                $order->save();
//                if($prev_paid-$payble_amount)
//                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',($prev_paid-$payble_amount), 'CASH', $order->id);
//
//            }else if($total_cost-$coupon_discount > $prev_cashback){
//                $prev_paid=($prev_total-$prev_discount - $prev_cashback-$prev_balance);
//                $payble_amount=0;
//                $balance_used=$total_cost-$coupon_discount-$prev_cashback;
//                $order->points_used=$prev_cashback;
//                $order->balance_used=$balance_used;
//                $order->save();
//                if($prev_paid-$payble_amount+$prev_balance-$balance_used)
//                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',($prev_paid-$payble_amount+$prev_balance-$balance_used), 'CASH', $order->id);
//
//            }else if($total_cost-$coupon_discount < $prev_cashback){
//                $prev_paid=($prev_total-$prev_discount - $prev_cashback-$prev_balance);
//                $payble_amount=0;
//                $balance_used=0;
//                $points_used=$total_cost-$prev_discount;
//                $order->points_used=$points_used;
//                $order->balance_used=0;
//                $order->save();
//                if($prev_paid-$payble_amount+$prev_balance-$balance_used)
//                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',($prev_paid-$payble_amount+$prev_balance-$balance_used), 'CASH', $order->id);
//                if($prev_cashback-$points_used)
//                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_cashback-$points_used, 'CASH', $order->id);
//            }
//        }else{
//            //Return cashback used
//            if ($prev_cashback) {
//                Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_cashback, 'POINT', $order->id);
//            }
//            //Return balance used
//            if ($prev_balance) {
//                Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_balance+$prev_delivery, 'CASH', $order->id);
//            }
//        }
//
//        //Order::deductInventory($order);
//
//        return [
//
//            'status'=>'success',
//            'message'=>'Items Has Been returned'
//
//        ];
//
//    }

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

        // if payment is COD deduct amount from wallet if any used
        if($order->payment_mode=='COD'){

            $order->payment_status='paid';

//            if($order->use_points){
//                if($order->points_used > 0)
//                    Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->points_used, 'POINT', $order->id);
//
//            }
//
//            if($order->use_balance){
//                if($order->balance_used > 0)
//                    Wallet::updatewallet($order->user_id, 'Paid For Order ID: '.$order->refid, 'DEBIT',$order->balance_used, 'CASH', $order->id);
//            }

        }

        $order->status='completed';
        $order->delivered_at=date('Y-m-d H:i:s');
        $order->save();

        OrderStatus::create([
            'order_id'=>$order->id,
            'current_status'=>$order->status
        ]);

        // add cashback to user
        $customer=Customer::find($order->user_id);
        if($customer->isMembershipActive()){

            $membership=Membership::find($customer->active_membership);

            if($membership){
                $amount=round(($order->total_cost-$order->coupon_discount-$order->points_used)*$membership->cashback/100, 2);
                $order->cashback_given=$amount;
                $order->save();

                Wallet::updatewallet($order->user_id, 'Cashback received For Order ID: '.$order->refid, 'CREDIT',$amount, 'POINT', $order->id);

                $title='Cashback Credited';
                $message="Cashback of $amount received For Order ID: ".$order->refid;

                FCMNotification::sendNotification($order->customer->notification_token, $title, $message);

            }

        }

        $title='Order Delivered';
        $message='Your Order ID: '.$order->refid.' at Hallobasket has been delivered';

        FCMNotification::sendNotification($order->customer->notification_token, $title, $message);

        return [
            'status'=>'success',
            'message'=>'Order Has Been Delivered'
        ];

    }

    public function returnorder(Request $request){
        //  $user=auth()->guard('riderapi')->user();
        $user=$request->user;
        //  var_dump($user->id);die;
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $returnproducts = ReturnProduct::with(['storename', 'size'])->where(function ($returnproducts) use ($user) {

            $returnproducts->where('rider_id',$user->id);
        });
        $returnproducts=$returnproducts->orderBy('id', 'desc')->get();
      //  $returnproducts=ReturnProduct::where('rider_id',$user->id)
                                   //   ->orderBy('id', 'desc')
        $returnd=[];                            //  ->get();
        foreach ($returnproducts as $return)
        {
            $returnd[]=array(
                "storename"=>$return->storename->name??'',
                "ref_id"=>$return->ref_id,
                "name"=>$return->name,
                "price"=>$return->price,
                "cut_price"=>$return->cut_price,
                "size"=>$return->size->size??'',
                "quantity"=>$return->quantity,
                "image"=>$return->image,
                "created_at"=>date('Y-m-d h:iA', strtotime($return->created_at)),
            );

        }
        return [
            'status'=>'success',
            'data'=>$returnd
        ];

    }

    public function returnProduct(Request $request, $order_id){

        //change

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
            $details->with(['entity', 'size']);
                //->whereIn('details.id', $itemids);
        }])
            ->where('status', 'dispatched')
            ->where('rider_id', $user->id)
            ->find($order_id);

        if(!$order || empty($order->details->toArray()))
            return [
                'status'=>'failed',
                'message'=>'No Such Order Found'
            ];


        $prev_total=$order->total_cost;
        $prev_delivery=$order->delivery_charge;
        $prev_cashback=$order->points_used;
        $prev_balance=$order->balance_used;
        $prev_discount=$order->coupon_discount;


        $total_return=0;
        //$itemids=[];
        $details=[];
        foreach($order->details as $item){
            //if($item->order->rider_id==$user->id){

            if($request->items[$item->id] > $item->quantity){
                return [
                    'status'=>'failed',
                    'message'=>'Invalid Request'
                ];
            }

            if(key_exists($item->id, $request->items)){
                $total_return=$total_return+$item->price*$request->items[$item->id];
                $details[]=$item;
            }

        }

        if($order->total_cost-$order->coupon_discount-$order->points_used){
            Wallet::updatewallet($user->id, 'Refund For Order ID: '.$order->refid, 'Credit', 'CASH', ($order->total_cost-$order->coupon_discount-$order->points_used), $order->id);
        }

        if($order->points_used){
            Wallet::updatewallet($user->id, 'Refund For Order ID: '.$order->refid, 'Credit', 'POINT', $order->points_used, $order->id);
        }



        //$order=$items[0]->order;
        if($order->coupon_applied && $order->coupon_discount){

            //$total_cost=$order->total_cost+$order->coupon_discount;
            $total_cost=$order->total_cost-$total_return;
            $coupon=Coupon::where('code', $order->coupon_applied)->first();
            $coupon_discount=$coupon->getCouponDiscount($total_cost);

        }else{
            $total_cost=$order->total_cost-$total_return;
            $coupon_discount=0;
        }


        foreach($details as $d){

            ReturnProduct::create([

                'order_id'=>$d->order_id,
                'store_id'=>$order->store_id,
                'rider_id'=>$order->rider_id,
                'ref_id'=>$order->refid,
                'entity_id'=>$d->entity_id,
                'entity_type'=>$d->entity_type,
                'size_id'=>$d->size_id,
                'name'=>$d->name,
                'image'=>$d->image,
                'price'=>$d->price,
                'cut_price'=>$d->cut_price,
                'quantity'=>$request->items[$d->id],
                'reason'=>$request->reason

            ]);
            if($d->quantity==$request->items[$d->id])
                $d->delete();
            else{
                $d->quantity=$d->quantity - $request->items[$d->id];
                $d->save();
            }

            Order::increaseItemCount($d, $request->items[$d->id]*$d->size->consumed_units);
        }

        //refund when total goes to 0
        if($total_cost==0){
            $order->total_cost=0;
            $order->coupon_discount=0;
            $order->delivery_charge=0;
            $order->balance_used=0;
            $order->points_used=0;
            //$order->status='completed';
            $order->save();

            if($order->payment_mode!='COD') {
                //Return Cashback Used
                if ($prev_cashback) {
                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_cashback, 'POINT', $order->id);
                }

                //Return Point+delivery charge+paid amount
                Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_total-$prev_discount-$prev_cashback+$prev_delivery, 'CASH', $order->id);

            }else{
                //Return cashback used
                if ($prev_cashback) {
                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_cashback, 'POINT', $order->id);
                }
                //Return balance used
                if ($prev_balance) {
                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_balance+$prev_delivery, 'CASH', $order->id);
                }
            }

            return [
                'status'=>'success',
                'message'=>'Items Has Been returned'
            ];
        }

        $order->total_cost=$total_cost;
        $order->coupon_discount=$coupon_discount;
        $order->delivery_charge=$prev_delivery;

        if($order->payment_mode!='COD') {
            if($total_cost-$coupon_discount > $prev_cashback+$prev_balance){
                $prev_paid=($prev_total-$prev_discount - $prev_cashback-$prev_balance);
                $payble_amount=($total_cost-$coupon_discount - $prev_cashback-$prev_balance);
                $order->balance_used=$prev_balance;
                $order->points_used=$prev_cashback;
                $order->save();
                if($prev_paid-$payble_amount)
                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',($prev_paid-$payble_amount), 'CASH', $order->id);

            }else if($total_cost-$coupon_discount > $prev_cashback){
                $prev_paid=($prev_total-$prev_discount - $prev_cashback-$prev_balance);
                $payble_amount=0;
                $balance_used=$total_cost-$coupon_discount-$prev_cashback;
                $order->points_used=$prev_cashback;
                $order->balance_used=$balance_used;
                $order->save();
                if($prev_paid-$payble_amount+$prev_balance-$balance_used)
                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',($prev_paid-$payble_amount+$prev_balance-$balance_used), 'CASH', $order->id);

            }else if($total_cost-$coupon_discount < $prev_cashback){
                $prev_paid=($prev_total-$prev_discount - $prev_cashback-$prev_balance);
                $payble_amount=0;
                $balance_used=0;
                $points_used=$total_cost-$prev_discount;
                $order->points_used=$points_used;
                $order->balance_used=0;
                $order->save();
                if($prev_paid-$payble_amount+$prev_balance-$balance_used)
                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',($prev_paid-$payble_amount+$prev_balance-$balance_used), 'CASH', $order->id);
                if($prev_cashback-$points_used)
                    Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_cashback-$points_used, 'CASH', $order->id);
            }
        }else{
            //Return cashback used
            if ($prev_cashback) {
                Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_cashback, 'POINT', $order->id);
            }
            //Return balance used
            if ($prev_balance) {
                Wallet::updatewallet($order->user_id, 'Refund for Order ID: '.$order->refid, 'CREDIT',$prev_balance+$prev_delivery, 'CASH', $order->id);
            }
        }

        //Order::deductInventory($order);

        return [

            'status'=>'success',
            'message'=>'Items Has Been returned'

        ];

    }

}
