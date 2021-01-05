<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Invoice;
use App\Models\Membership;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Rider;
use App\Models\TimeSlot;
use App\Models\Wallet;
use App\Models\User;
use App\Services\Notification\FCMNotification;
use App\Services\SMS\Msg91;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

     public function index(Request $request){

         $orders=Order::where('status', '!=', 'pending');

         if(isset($request->search)){
             $orders=$orders->where(function($orders) use ($request){

                 $orders->where('name', 'like', "%".$request->search."%")
                     ->orWhere('email', 'like', "%".$request->search."%")
                     ->orWhere('mobile', 'like', "%".$request->search."%")
                     ->orWhere('refid', 'like', "%".$request->search."%")
                     ->orWhereHas('customer', function($customer)use( $request){
                         $customer->where('name', 'like', "%".$request->search."%")
                             ->orWhere('email', 'like', "%".$request->search."%")
                             ->orWhere('mobile', 'like', "%".$request->search."%");
                     });
             });

         }

         if($request->fromdate)
            $orders=$orders->where('delivery_date', '>=', $request->fromdate);


        if($request->todate)
                $orders=$orders->where('delivery_date', '<=', $request->todate);

        if($request->status)
            $orders=$orders->where('status', $request->status);

        if($request->payment_status)
                $orders=$orders->where('payment_status', $request->payment_status);

        if($request->store_id)
              $orders=$orders->where('store_id', $request->store_id);

        if($request->rider_id)
             $orders=$orders->where('rider_id', $request->rider_id);

        if($request->delivery_slot)
             $orders=$orders->where('delivery_slot', $request->delivery_slot);

        if($request->ordertype)
            $orders=$orders->orderBy('created_at', $request->ordertype);

        $orders=$orders->orderBy('id', 'DESC')->paginate(10);

        $stores=User::where('id','>', 1)->get();
        $riders=Rider::get();
        $timeslots=TimeSlot::get();
        $invoice = Invoice::first();
//var_dump($stores);die();
        return view('admin.order.view',['orders'=>$orders,'stores'=>$stores,'riders'=>$riders,'timeslots'=>$timeslots,'invoice'=>$invoice]);

    }

    public function details(Request $request,$id){
        $order =Order::with(['details.entity','details.size'])->findOrFail($id);
        $riders =Rider::active()->get();
        //var_dump($order);die();
        return view('admin.order.details',['order'=>$order,'riders'=>$riders]);
    }

    public function changeStatus(Request $request, $id){

        $status=$request->status;

        $order=Order::with(['customer', 'details.entity', 'details.size'])
            ->find($id);

        $old_status=$order->status;

        if($status=='reopen'){
            $order->status='confirmed';
            $order->payment_status='payment-wait';
            $order->paymnet_mode='COD';
            //$order->save();
        }else if($status=='cancelled') {

            $order->points_used=0;
            $order->balance_used=0;
            $order->coupon_applied=null;
            $order->coupon_discount=0;
            $order->status=$status;


            if($order->payment_status=='paid'){

                if($order->use_points && $order->points_used){
                    Wallet::updatewallet($order->user_id, 'Points added in wallet for order cancellation. Order ID: '.$order->refid,'Credit',$order->points_used,'POINT',$order->id);
                }

                //if($order->use_balance && $order->balance_used){
                $amount=$order->total_cost-$order->coupon_discount+$order->delivery_charge-$order->points_used;
                if($amount){
                    Wallet::updatewallet($order->user_id, 'Amount added in wallet for order cancellation. Order ID: '.$order->refid,'Credit',$amount,'CASH',$order->id);
                }
                //}

            }else{
                if($order->use_points && $order->points_used){
                    Wallet::updatewallet($order->user_id, 'Points added in wallet for order cancellation. Order ID: '.$order->refid,'Credit',$order->points_used,'POINT',$order->id);
                }

                if($order->use_balance && $order->balance_used){
                    Wallet::updatewallet($order->user_id, 'Amount added in wallet for order cancellation. Order ID: '.$order->refid,'Credit',$order->balance_used,'CASH',$order->id);
                }
            }

            Order::increaseInventory($order);


        }else if($status=='delivered'){
            $order->status=$status;
            $order->delivered_at=date('Y-m-d H:i:s');
        }else{
            $order->status=$status;
        }

        $order->save();

        switch($order->status){
            case 'dispatched':
                $message='Your order at Hallobasket with  ID:'.$order->refid.' has been dispatched. You will receive your order shortly';
                $title='Order Dispatched';
                break;
            case 'delivered':
                $message='Your order at Hallobasket with  ID:'.$order->refid.' has been delivered.';
                $title='Order Delivered';
                break;
            case 'cancelled':
                $message='Your order at Hallobasket with  ID:'.$order->refid.' has been reopened.';
                $title='Order Cancelled';
                break;

        }

        if($status=='reopen'){
            $message='Your order at Hallobasket with  ID:'.$order->refid.' has been cancelled.';
            $title='Order Cancelled';
        }


        //$user=Customer::find($order->user_id);

        if($old_status!='pending' && in_array($order->status, ['dispatched', 'delivered', 'cancelled'])){
            Notification::create([
                'user_id'=>$order->customer->id,
                'title'=>$title,
                'description'=>$message,
                'data'=>null,
                'type'=>'individual'
            ]);

            FCMNotification::sendNotification($order->customer->notification_token, $title, $message);
        }

        //credit cashback on completion
        if($status=='completed' && $old_status!='completed'){

            if($order->customer->isMembershipActive()){

                $membership=Membership::find($order->customer->active_membership);

                if($membership){
                    $amount=round(($order->total_cost-$order->coupon_discount-$order->points_used)*$membership->cashback/100, 2);
                    $order->cashback_given=$amount;
                    $order->save();
                    if($amount>0)
                        Wallet::updatewallet($order->user_id, 'Cashback received For Order ID: '.$order->refid, 'CREDIT',$amount, 'POINT', $order->id);

                    $title='Cashback Credited';
                    $message="Cashback of $amount received For Order ID: ".$order->refid;

                    FCMNotification::sendNotification($order->customer->notification_token, $title, $message);

                }
            }

        }


        if($old_status!='dispatched' &&  $order->status=='dispatched' && !empty($order->rider_id)){
            $rider=Rider::find($order->rider_id);
            Msg91::send($rider->mobile, 'New Order '.$order->refid.' arrived. Scheduled Delivery is '.($order->delivery_date??'').' '.($order->timeslot->name??''));
        }

        return redirect()->back()->with('success', 'Order has been updated');


    }

    public function changePaymentStatus(Request $request, $id){

        $status=$request->status;
        $order=Order::find($id);

        $order->payment_status=$status;
        $order->save();

        return redirect()->back()->with('success', 'Payment Status Has Been Updated');

    }

    public function changeRider(Request $request,$id){
        $order =Order::findOrFail($id);


        $old_rider=$order->rider_id;
        $rider=Rider::findOrFail($request->riderid);
        $order->rider_id=$request->riderid;
        $order->save();
        if($old_rider!=$order->rider_id && $order->status=='dispatched')
            Msg91::send($rider->mobile, 'New Order '.$order->refid.' arrived. Scheduled Delivery is '.($order->delivery_date??'').' '.($order->timeslot->name??''));

        return redirect()->back()->with('success', 'Rider Has Been change');
    }

    public function addCashback(Request $request, $id, $type){

        $order =Order::findOrFail($id);

        if($type=='credit'){
            if(!$order->cashback_given){

                if(!($order->status=='completed' && $order->payment_status=='paid')){
                    return redirect()->back()->with('error', 'Cashback cannot be credited for incomplete order');
                }
                $order->cashback_given=true;
                $order->save();

                Wallet::updatewallet($order->user_id,'Cashback Given For Order ID: '.$order->refid,'CREDIT', intval($order->total_cost*5/100), 'POINT',$order->id);

                return redirect()->back()->with('success', 'Cashback has been credited');
            }
        }else if($type=='debit'){
            if($order->cashback_given){
                if(!($order->status=='completed' && $order->payment_status=='paid')){
                    return redirect()->back()->with('error', 'Cashback cannot be revoked for incomplete order');
                }
                $order->cashback_given=false;
                $order->save();

                Wallet::updatewallet($order->user_id,'Cashback Revoked For Order ID: '.$order->refid,'DEBIT', intval($order->total_cost*5/100), 'POINT',$order->id);

                return redirect()->back()->with('success', 'Cashback has been revoked');
            }

        }

        return redirect()->back()->with('error', 'Invalid Request');

    }

    public function invoice_update(Request $request,$id){
        $request->validate([
            'prefix'=>'required',
            'sequence'=>'required',
        ]);

        $invoice =Invoice::findOrFail($id);

        if($invoice->update([
            'prefix'=>$request->prefix,
            'sequence'=>$request->sequence,
            'address'=>$request->address,
            'current_sequence'=>$request->current_sequence??1,
            'pan_gst'=>$request->pan_gst
        ]))
        {

            return redirect()->back()->with('success', 'Invoice has been updated');
        }
        return redirect()->back()->with('error', 'Invoice update failed');
    }

}
