<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\BookingSlot;
use App\Models\Cart;
use App\Models\Clinic;
use App\Models\Coupon;
use App\Models\CustomerAddress;
use App\Models\DailyBookingsSlots;
use App\Models\HomeBookingSlots;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\RescheduleRequest;
use App\Models\Therapy;
use App\Models\TimeSlot;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index(Request $request){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $orders=Order::with(['details'])
            ->where('status', '!=','pending')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

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

    public function initiateOrder(Request $request){

        $user= auth()->guard('customerapi')->user();
       // return $user->id;
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $cartitems=Cart::where('user_id', auth()->guard('customerapi')->user()->id)
            ->with(['product','sizeprice'])
            ->whereHas('product', function($product){
                $product->where('isactive', true);
            })->get();

        if(!$cartitems)
            return [
                'status'=>'failed',
                'message'=>'Cart is empty'
            ];

        $total_cost=0;
        foreach($cartitems as $item) {
            $total_cost=$total_cost+($item->sizeprice->price??0)*$item->quantity;

        }
        $refid=env('MACHINE_ID').time();

        $delivery_charge=$user->isMembershipAtive()?config('my-config')['delivery_charge']:0;

        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>$total_cost,
            'delivery_charge'=>$delivery_charge,

        ]);

        foreach($cartitems as $item){
            // var_dump($item->product_id);die();
            OrderDetail::create([
                'order_id'=>$order->id,
                'entity_id'=>$item->product_id,
                'entity_type'=>'App\Models\Product',
                'size_id'=>$item->size_id,
                'quantity'=>$item->quantity,
                'image'=>$item->sizeprice->image,
                'price'=>$item->sizeprice->price,
                'cut_price'=>$item->sizeprice->cut_price,
                'name'=>$item->product->name,
            ]);
        }

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id
            ]
        ];

    }


    public function selectAddress(Request $request, $id){

        $request->validate([
            'address_id'=>'required|integer'
        ]);

        $user= auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $address=CustomerAddress::where('user_id', $user->id)
            ->find($request->address_id);
        if(!$address)
            return [
                'status'=>'failed',
                'message'=>'No address Found'
            ];

        $order=Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Order Found'
            ];

        $order->address_id=$address->id;

        $order->save();

        return [
            'status'=>'success',
            'message'=>'Address Has Been Updated'
        ];


    }

    public function getPaymentInfo(Request $request, $order_id){

        $user= auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $order=Order::with(['deliveryaddress', 'details'])
            ->where('user_id', $user->id)
        ->find($order_id);
        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Such Order Found'
            ];
        $cost=0;
        $savings=0;
        foreach($order->details as $detail){
            $cost=$cost+$detail->price*$detail->quantity;
            $savings=$savings+($detail->cut_price-$detail->price)*$detail->quantity;
        }

        $prices=[
            'basket_total'=>$cost,
            'delivery_charge'=>$order->delivery_charge,
            'coupon_discount'=>$order->coupon_discount,
            'total_savings'=>$savings+$order->coupon_discount,
            'total_payble'=>$cost+$order->delivery_charge-$order->coupon_discount,
        ];

        $delivery_address=$order->deliveryaddress;

        $cashback=Wallet::points($user->id);
        $wallet_balance=Wallet::balance($user->id);

        return [
            'status'=>'success',
            'data'=>compact('prices', 'delivery_address', 'cashback', 'wallet_balance')
        ];


    }

    public function applyCoupon(Request $request, $order_id){

        $user= auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $coupon=Coupon::where(DB::raw('BINARY code'), $request->coupon??null)
            ->first();
        if(!$coupon){
            return [
                'status'=>'failed',
                'message'=>'Invalid Coupon Applied',
            ];
        }


        if($coupon->isactive==false || !$coupon->getUserEligibility($user)){
            return [
                'status'=>'failed',
                'message'=>'Coupon Has Been Expired',
            ];
        }

        $order=Order::with('details')->find($order_id);

        $cost=0;
        $savings=0;
        foreach($order->details as $detail){
            $cost=$cost+$detail->price*$detail->quantity;
            $savings=$savings+($detail->cut_price-$detail->price)*$detail->quantity;
        }

        $discount=$coupon->getCouponDiscount($cost);

        $prices=[
            'basket_total'=>$cost,
            'delivery_charge'=>$order->delivery_charge,
            'coupon_discount'=>$discount,
            'total_savings'=>$savings+$order->coupon_discount,
            'total_payble'=>$cost+$order->delivery_charge-$discount,
        ];


        if($discount > $order->total_cost)
        {
            return [
                'status'=>'failed',
                'message'=>'Coupon Cannot Be Applied',
            ];
        }

        return [

            'status'=>'success',
            'message'=>'Discount of Rs. '.$discount.' Applied Successfully',
            'prices'=>$prices
        ];


    }

    public function orderdetails(Request $request, $id){

        $show_cancel_product=0;

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $order=Order::with(['details.size', 'deliveryaddress'])
            ->where('user_id', $user->id)
            ->where('status', '!=', 'pending')
            ->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

//        //get reviews information
//        $reviews=[];
//        if($order->status=='completed'){
//            $reviews=$order->reviews()->where('session_id', null)->get();
//            foreach($reviews as $review){
//                $reviews[$review->entity_id]=$review;
//            }
//        }


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
                'show_return'=>($detail->status=='delivered'?1:0),
                'show_cancel'=>in_array($detail->status, ['confirmed'])?1:0
            ];
            $savings=$savings+($detail->cut_price-$detail->price);

        }

        // options to be displayed
        if(in_array($order->status, ['confirmed','processing', 'dispatched'])){
            $show_cancel_product=1;
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


    public function cancelOrder(Request $request, $id){

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
                'message'=>'No Such Order Found',
            ];

        if(!in_array($order->status, ['confirmed', 'processing', 'dispatched'])){
            return [
                'status'=>'failed',
                'message'=>'Order Cannot Be Cancelled',
            ];
        }

        //Add Amount In Customer Wallet
        if($order->payment_status=='paid'){

            if($order->use_points && $order->points_used){
                $amount=$order->total_cost-$order->points_used-$order->coupon_discount+$order->delivery_charge;
                Wallet::updatewallet($user->user_id, 'Amount added in wallet for order cancellation. Order ID: '.$order->refid,'Credit',$amount,'CASH',$order->id);
                Wallet::updatewallet($user->user_id, 'Points added in wallet for order cancellation. Order ID: '.$order->refid,'Credit',$order->points_used,'POINT',$order->id);
            }else{
                $amount=$order->total_cost-$order->coupon_discount+$order->delivery_charge;
                Wallet::updatewallet($user->user_id, 'Amount added in wallet for order cancellation. Order ID: '.$order->refid,'Credit',$amount,'CASH',$order->id);
            }

        }

        return [
            'status'=>'success',
            'message'=>'Your Order Has Been Cancelled'
        ];

    }

}
