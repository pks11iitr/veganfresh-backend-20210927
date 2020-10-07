<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\BookingSlot;
use App\Models\Cart;
use App\Models\Clinic;
use App\Models\Configuration;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\DailyBookingsSlots;
use App\Models\HomeBookingSlots;
use App\Models\Membership;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\RescheduleRequest;
use App\Models\Therapy;
use App\Models\TimeSlot;
use App\Models\Wallet;
use App\Services\Notification\FCMNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

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


        $total_cost=0;
        $remaining=[];
        foreach($cartitems as $item) {
            if(Cart::removeOutOfStockItems($item))
                continue;
            $remaining[]=$item;
            $total_cost=$total_cost+($item->sizeprice->price??0)*$item->quantity;
        }

        if(!$remaining)
            return [
                'status'=>'failed',
                'message'=>'There is no item available in your cart'
            ];

        $refid=env('MACHINE_ID').time();

        $delivery_charge=Configuration::where('param', 'delivery_charge')->first();

        $delivery_charge=$user->isMembershipActive()?0:($delivery_charge->value??0);

        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>$total_cost,
            'delivery_charge'=>$delivery_charge,
        ]);

        foreach($remaining as $item){
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

        //$membership=Membership::find($user->active_membership);

        $order=Order::with(['deliveryaddress', 'details'])
            ->where('user_id', $user->id)
        ->find($order_id);
        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Such Order Found'
            ];

        $timeslot=TimeSlot::getNextDeliverySlot();

        $timeslot_list=TimeSlot::getAvailableTimeSlotsList();

        $cost=0;
        $savings=0;
        //$remaining=[];
        $itemdetails=[];
        foreach($order->details as $detail){
            if(OrderDetail::removeOutOfStockItems($detail))
                continue;
            $itemdetails[]=[
                'name'=>$detail->name??'',
                'image'=>$detail->image??'',
                'company'=>$detail->entity->company??'',
                'price'=>$detail->price,
                'cut_price'=>$detail->cut_price,
                'quantity'=>$detail->quantity,
                'size'=>$detail->size->name??'',
                'item_id'=>$detail->entity_id,
                //'show_return'=>($detail->status=='delivered'?1:0),
                //'show_cancel'=>in_array($detail->status, ['confirmed'])?1:0,
                'show_review'=>isset($reviews[$detail->entity_id])?0:1
            ];
            $cost=$cost+$detail->price*$detail->quantity;
            $savings=$savings+($detail->cut_price-$detail->price)*$detail->quantity;
        }

        if(empty($itemdetails)){
            return [
                'status'=>'failed',
                'message'=>'There is no selected item available in stock'
            ];
        }

        $delivery_charge=Configuration::where('param', 'delivery_charge')->first();
        if(!$user->isMembershipActive()){
            $order->delivery_charge=$delivery_charge->value??0;
        }else{
            $order->delivery_charge=0;
        }

        $order->save();

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
            'data'=>compact('prices', 'delivery_address', 'cashback', 'wallet_balance', 'timeslot', 'itemdetails', 'timeslot_list')
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

        $order=Order::with('details')->find($order_id);

        $cost=0;
        $savings=0;
        $itemdetails=[];
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
                //'show_return'=>($detail->status=='delivered'?1:0),
                //'show_cancel'=>in_array($detail->status, ['confirmed'])?1:0,
                'show_review'=>isset($reviews[$detail->entity_id])?0:1
            ];
            $cost=$cost+$detail->price*$detail->quantity;
            $savings=$savings+($detail->cut_price-$detail->price)*$detail->quantity;
        }

        if($coupon->isactive==false || !$coupon->getUserEligibility($user)){
            return [
                'status'=>'failed',
                'message'=>'Coupon Has Been Expired',
            ];
        }
        $discount=$coupon->getCouponDiscount($cost)??0;

        $prices=[
            'basket_total'=>$cost,
            'delivery_charge'=>$order->delivery_charge,
            'coupon_discount'=>$discount,
            'total_savings'=>$savings+$discount,
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
            'prices'=>$prices,
        ];


    }

    public function orderdetails(Request $request, $id){

        $show_cancel_product=0;
        $show_download_invoice=0;

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $order=Order::with(['details.size', 'deliveryaddress', 'timeslot'])
            ->where('user_id', $user->id)
            ->where('status', '!=', 'pending')
            ->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        //get reviews information
        $reviews=[];
        if($order->status=='completed'){
            $reviews=$order->reviews()->get();
            foreach($reviews as $review){
                $reviews[$review->entity_id]=$review;
            }
        }


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
                //'show_return'=>($detail->status=='delivered'?1:0),
                //'show_cancel'=>in_array($detail->status, ['confirmed'])?1:0,
                'show_review'=>($order->status=='completed')?(isset($reviews[$detail->entity_id])?0:1):0
            ];
            $savings=$savings+($detail->cut_price-$detail->price);

        }

        // options to be displayed
        if(in_array($order->status, ['confirmed','processing', 'dispatched'])){
            $show_cancel_product=1;
        }
        if($order->status=='completed'){
            $show_download_invoice=1;
        }

        $prices=[
            'total'=>$order->total_cost,
            'delivery_charge'=>$order->delivery_charge,
            'coupon_discount'=>$order->coupon_discount,
            'total_savings'=>$savings+$order->coupon_discount,
            'total_paid'=>$order->total_cost+$order->delivery_charge-$order->coupon_discount,
        ];

        $time_slot=[

            'delivery_time'=>$order->delivery_date.' -'. ($order->timeslot->name??''),
            'delivered_at'=>$order->delivered_at?date('m/d/Y h:iA', strtotime($order->delivered_at)):'No Yet Delivered',
        ];

        return [
            'status'=>'success',
            'data'=>[
                'orderdetails'=>$order->only('id', 'total_cost','refid', 'status','payment_mode', 'name', 'mobile', 'email', 'address','booking_date', 'booking_time','is_instant','status'),
                'itemdetails'=>$itemdetails,
                'show_cancel_product'=>$show_cancel_product??0,
                'deliveryaddress'=>$order->deliveryaddress??'',
                'prices'=>$prices,
                'show_download_invoice'=>$show_download_invoice??0,
                'invoice_link'=>$show_download_invoice?route('download.invoice', ['id'=>$order->id]):'',
                'time_slot'=>$time_slot
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

        $order=Order::with(['details.entity', 'details.size'])
        ->where('user_id', $user->id)
            ->find($id);
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
                Wallet::updatewallet($user->id, 'Points added in wallet for order cancellation. Order ID: '.$order->refid,'Credit',$order->points_used,'POINT',$order->id);
            }

            if($order->use_balance && $order->balance_used){
                $amount=$order->total_cost-$order->coupon_discount+$order->delivery_charge-$order->points_used;
                Wallet::updatewallet($user->id, 'Amount added in wallet for order cancellation. Order ID: '.$order->refid,'Credit',$amount,'CASH',$order->id);
            }

        }else{
            if($order->use_points && $order->points_used){
                Wallet::updatewallet($user->id, 'Points added in wallet for order cancellation. Order ID: '.$order->refid,'Credit',$order->points_used,'POINT',$order->id);
            }

            if($order->use_balance && $order->balance_used){
                Wallet::updatewallet($user->id, 'Amount added in wallet for order cancellation. Order ID: '.$order->refid,'Credit',$order->balance_used,'CASH',$order->id);
            }
        }

        $order->status='cancelled';
        $order->save();

        Order::increaseInventory($order);

        $message='Congratulations! Your order of Rs. '.$order->total_cost.' at SuzoDailyNeeds is cancelled. Order Reference ID: '.$order->refid;

        Notification::create([
            'user_id'=>$order->user_id,
            'title'=>'Order Cancelled',
            'description'=>$message,
            'data'=>null,
            'type'=>'individual'
        ]);
        if($order->customer->notification_token??null)
            FCMNotification::sendNotification($order->customer->notification_token, 'Order Cancelled', $message);

        return [
            'status'=>'success',
            'message'=>'Your Order Has Been Cancelled'
        ];

    }
//    public function downloadPDF($id){
//
//        $orders = Order::with(['details'])->find($id);
//        $pdf = PDF::loadView('admin.contenturl.invoice', compact('orders'))->setPaper('a4', 'portrait');
//        return $pdf->download('invoice.pdf');
//       // return view('admin.contenturl.invoice',['orders'=>$orders]);
//    }

    public function downloadPDF($id){
        $orders = Order::with(['details'])->find($id);
        // var_dump($orders);die();
        $pdf = PDF::loadView('admin.contenturl.newinvoice', compact('orders'))->setPaper('a4', 'portrait');
        return $pdf->download('invoice.pdf');
        return view('admin.contenturl.newinvoice',['orders'=>$orders]);
    }

}
