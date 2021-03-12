<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Area;
use App\Models\BookingSlot;
use App\Models\Cart;
use App\Models\Clinic;
use App\Models\Configuration;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\DailyBookingsSlots;
use App\Models\HomeBookingSlots;
use App\Models\Invoice;
use App\Models\Membership;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\RescheduleRequest;
use App\Models\ReturnRequest;
use App\Models\Therapy;
use App\Models\TimeSlot;
use App\Models\Wallet;
use App\Services\Notification\FCMNotification;
use App\Services\SMS\Msg91;
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
                'total_price' => $order->total_cost+$order->delivery_charge,
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

        //$delivery_charge=$user->isMembershipActive()?0:($delivery_charge->value??0);

        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id,
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>$total_cost,
            'delivery_charge'=>$delivery_charge->value??0,
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

        $area=Area::active()
            //->where('name', $address->area)
            ->find($address->area_id);
        $order=Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Order Found'
            ];

        $order->address_id=$address->id;
        $order->store_id=$area->store_id??null;
        $order->email=$user->email;
        $order->mobile=$user->mobile;

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
                'size'=>$detail->size->size??'',
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
//        if(!$user->isMembershipActive()){
//            $order->delivery_charge=$delivery_charge->value??0;
//        }else{
//            $order->delivery_charge=0;
//        }
        $express_delivery=Configuration::where('param', 'express_delivery')->first();
        $express_delivery=[
            'text'=>$express_delivery->description??'',
            'price'=>$express_delivery->value??0
        ];


        $order->delivery_charge=$delivery_charge->value??0;

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
            'data'=>compact('prices', 'delivery_address', 'cashback', 'wallet_balance', 'timeslot', 'itemdetails', 'timeslot_list', 'express_delivery')
        ];


    }


//    public function selectExpressDelivery(Request $request, $order_id){
//        $user= auth()->guard('customerapi')->user();
//        if(!$user)
//            return [
//                'status'=>'failed',
//                'message'=>'Please login to continue'
//            ];
//
//        $order=Order::with('details')
//        ->where('status', 'pending')
//            ->find($order_id);
//        if(!$order)
//            return [
//                'status'=>'failed',
//                'message'=>'No Such Order Found'
//            ];
//
//        $cost=0;
//        $savings=0;
//        $itemdetails=[];
//        foreach($order->details as $detail){
//            $itemdetails[]=[
//                'name'=>$detail->name??'',
//                'image'=>$detail->image??'',
//                'company'=>$detail->entity->company??'',
//                'price'=>$detail->price,
//                'cut_price'=>$detail->cut_price,
//                'quantity'=>$detail->quantity,
//                'size'=>$detail->size->name??'',
//                'item_id'=>$detail->entity_id,
//                //'show_return'=>($detail->status=='delivered'?1:0),
//                //'show_cancel'=>in_array($detail->status, ['confirmed'])?1:0,
//                'show_review'=>isset($reviews[$detail->entity_id])?0:1
//            ];
//            $cost=$cost+$detail->price*$detail->quantity;
//            $savings=$savings+($detail->cut_price-$detail->price)*$detail->quantity;
//        }
//
//        $express_delivery=Configuration::where('param', 'express_delivery')->first();
//        $express_delivery=[
//            'text'=>$express_delivery->description??'',
//            'price'=>$express_delivery->value??$order->delivery_charge
//        ];
//
//        $prices=[
//            'basket_total'=>$cost,
//            'delivery_charge'=>$express_delivery,
//            'coupon_discount'=>$order->coupon_discount,
//            'total_savings'=>$savings+$order->coupon_discount,
//            'total_payble'=>$cost+$express_delivery-$order->coupon_discount,
//        ];
//    }

    public function applyCoupon(Request $request, $order_id){

        $user= auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $coupon=Coupon::with(['categories'=>function($categories){
                $categories->select('sub_category.id');
                }])
            ->where(DB::raw('BINARY code'), $request->coupon??null)
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

        $order=Order::with(['details.entity.subcategory','details.size'])->find($order_id);
        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Such Order Found'
            ];
        $cost=0;
        $savings=0;
        //$itemdetails=[];
        foreach($order->details as $detail){
//            $itemdetails[]=[
//                'name'=>$detail->name??'',
//                'image'=>$detail->image??'',
//                'company'=>$detail->entity->company??'',
//                'price'=>$detail->price,
//                'cut_price'=>$detail->cut_price,
//                'quantity'=>$detail->quantity,
//                'size'=>$detail->size->name??'',
//                'item_id'=>$detail->entity_id,
//                //'show_return'=>($detail->status=='delivered'?1:0),
//                //'show_cancel'=>in_array($detail->status, ['confirmed'])?1:0,
//                'show_review'=>isset($reviews[$detail->entity_id])?0:1
//            ];
            $cost=$cost+$detail->price*$detail->quantity;
            $savings=$savings+($detail->cut_price-$detail->price)*$detail->quantity;
        }


        //$discount=$coupon->getCouponDiscount($cost)??0;
        $discount=$order->getCouponDiscount($coupon)??0;

        if($discount <= 0 || $discount > $order->total_cost)
        {
            return [
                'status'=>'failed',
                'message'=>'Coupon Cannot Be Applied',
            ];
        }

        $prices=[
            'basket_total'=>$cost,
            'delivery_charge'=>$order->delivery_charge,
            'coupon_discount'=>$discount,
            'total_savings'=>$savings+$discount,
            'total_payble'=>$cost+$order->delivery_charge-$discount,
        ];


        return [

            'status'=>'success',
            'message'=>'Discount of Rs. '.$discount.' Applied Successfully',
            'prices'=>$prices,
        ];


    }

    public function orderdetails(Request $request, $id){

        $show_cancel_product=0;
        $show_download_invoice=0;
        $show_repeat_order=0;
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

        if($order->status=='completed')
            $show_return=1;
        else
            $show_return=0;

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        if(in_array($order->status, ['completed','delivered', 'cancelled']))
            $show_repeat_order=1;

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
                'size'=>$detail->size->size??'',
                'item_id'=>$detail->entity_id,
                'show_return'=>$show_return,
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

        $express_delivery=Configuration::find(7);


        $time_slot=[

            'delivery_time'=>($order->is_express_delivery==true)?($express_delivery->description):($order->delivery_date.' -'. ($order->timeslot->name??'')),
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
                'time_slot'=>$time_slot,
                'show_repeat_order'=>$show_repeat_order??0,
                'show_return'=>$show_return
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
                Wallet::updatewallet($user->id, 'Cashback refunded for order cancellation. Order ID: '.$order->refid,'Credit',$order->points_used,'POINT',$order->id);
            }

            $amount=$order->total_cost-$order->coupon_discount+$order->delivery_charge-$order->points_used;
            if($amount>0){
                Wallet::updatewallet($user->id, 'Amount refunded for order cancellation. Order ID: '.$order->refid,'Credit',$amount,'CASH',$order->id);
            }

            if($order->cashback_given){
                Wallet::updatewallet($user->id, 'Cashback revoked for order cancellation. Order ID: '.$order->refid,'Debit',$order->cashback_given,'POINT',$order->id);
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

        if($order->cashback_given){
            Wallet::updatewallet($order->user_id, 'Cashback Revoked For Order ID: '.$order->refid, 'DEBIT', $order->cashback_given, 'POINT', $order->id);
        }

        $message='Your order of Rs. '.($order->total_cost-$order->coupon_discount+$order->delivery_charge+$order->extra_amount).' at Hallobasket is cancelled. Order Reference ID: '.$order->refid;

        Notification::create([
            'user_id'=>$order->user_id,
            'title'=>'Order Cancelled',
            'description'=>$message,
            'data'=>null,
            'type'=>'individual'
        ]);
        if($order->customer->notification_token??null)
            FCMNotification::sendNotification($order->customer->notification_token, 'Order Cancelled', $message);

        if(!empty($order->storename->mobile)){
            Msg91::send($order->storename->mobile, 'Order ID '.$order->refid.' has been cancelled by customer', env('CANCEL_ORDER_STORE'));
        }

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
        $invoice=Invoice::find(1);
        $pdf = PDF::loadView('admin.contenturl.newinvoice', compact('orders', 'invoice'))->setPaper('a4', 'portrait');
        return $pdf->download('invoice.pdf');
        //return view('admin.contenturl.newinvoice',['orders'=>$orders]);
    }

    public function repeatOrder(Request $request, $order_id){

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        Cart::where('user_id', $user->id)->delete();

        $order=Order::with(['details'=>function($details){
            $details->select('id','entity_id','order_id', 'size_id', 'quantity');
        }])->findOrFail($order_id)->toArray();
        //return $order;
        $pids=array_map(function($element){
            return $element['entity_id'];
        }, $order['details']);

        $sids=array_map(function($element){
            return $element['size_id'];
        }, $order['details']);

//        $quantities1=array_map(function($element){
//            return [$element['size_id']=>$element['quantity']];
//        }, $order['details']);

        $quantities=[];
        foreach($order['details'] as $d){
            $quantities[$d['size_id']]=$d['quantity'];
        }

        //return $quantities;
        //return $pids;
        if(!($sids && $pids))
            return [
                'status'=>'failed',
                'message'=>'No product available stocks'
            ];


        $products=Product::active()
            ->with(['sizeprice'=>function($sizes) use ($sids){
                $sizes->where('isactive', true)->whereIn('product_prices.id', $sids);
            }])
            ->whereIn('id', $pids)
            ->get();

        foreach($products as $product){
            if(count($product->sizeprice)){
                if($product->stock_type=='packet'){
                    if($product->sizeprice[0]->stock > $quantities[$product->sizeprice[0]->id]){
                        Cart::create([
                            'product_id'=>$product->id,
                            'quantity'=>$quantities[$product->sizeprice[0]->id],
                            'user_id'=>$user->id,
                            'size_id'=>$product->sizeprice[0]->id,
                        ]);
                    }else if($product->sizeprice[0]->stock>0){
                        Cart::create([
                            'product_id'=>$product->id,
                            'quantity'=>$product->sizeprice[0]->stock,
                            'user_id'=>$user->id,
                            'size_id'=>$product->sizeprice[0]->id,
                        ]);
                    }
            }else{
                    if($product->stock > $quantities[$product->sizeprice[0]->id]*$product->sizeprice[0]->consumed_units){
                        Cart::create([
                            'product_id'=>$product->id,
                            'quantity'=>$quantities[$product->sizeprice[0]->id],
                            'user_id'=>$user->id,
                            'size_id'=>$product->sizeprice[0]->id,
                        ]);
                    }
                }
            }
        }

        return [
            'status'=>'success',
            'message'=>'Item has been added to cart'
        ];

    }

    public function raiseReturn(Request $request, $detail_id){

        $request->validate([
            'quantity'=>'required|integer',
            'return_reason'=>'required|string'
        ]);

        $user=$request->user;
        $detail=OrderDetail::with('order')
            ->whereHas('order', function($order) use ($user){
                $order->where('user_id', $user->id)->where('status', 'completed');
            })->findOrFail($detail_id);

        if($request->quantity > $detail->quantity){
            return [
                'status'=>'failed',
                'message'=>'Max quantity '.$detail->quantity.' can be returned'
            ];
        }

        $return=ReturnRequest::where('order_id', $detail->order_id)
            ->where('details_id', $detail->id)->first();
        if($return && $return->status!='pending'){
            return [
                'status'=>'failed',
                'message'=>'This item cannot be returned now'
            ];
        }

        ReturnRequest::updateOrCreate([
            'order_id'=>$detail->order_id,
            'details_id'=>$detail->id,
            ],[
            'quantity'=>$request->quantity,
            'return_reason'=>$request->return_reason
            ]);

        return [
            'status'=>'success',
            'message'=>'Return request has been raised'
        ];

    }


}
