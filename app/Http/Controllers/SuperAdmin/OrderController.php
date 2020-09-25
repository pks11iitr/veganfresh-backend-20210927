<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Rider;
use App\Services\Notification\FCMNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

     public function index(Request $request){
         if(isset($request->search)){
             $orders=Order::where(function($orders) use ($request){

                 $orders->where('name', 'like', "%".$request->search."%")
                     ->orWhere('email', 'like', "%".$request->search."%")
                     ->orWhere('mobile', 'like', "%".$request->search."%")
                     ->orWhereHas('customer', function($customer)use( $request){
                         $customer->where('name', 'like', "%".$request->search."%")
                             ->orWhere('email', 'like', "%".$request->search."%")
                             ->orWhere('mobile', 'like', "%".$request->search."%");
                     });
             });

         }else{
             $orders =Order::where('id', '>=', 0);
         }
            if($request->fromdate)
                $orders=$orders->where('created_at', '>=', $request->fromdate.'00:00:00');

            if($request->todate)
                $orders=$orders->where('created_at', '<=', $request->todate.'23:59:50');

            if($request->status)
                $orders=$orders->where('status', $request->status);

             if($request->payment_status)
                $orders=$orders->where('payment_status', $request->payment_status);

            if($request->ordertype)
                $orders=$orders->orderBy('created_at', $request->ordertype);

                $orders=$orders->orderBy('id', 'desc')->paginate(10);

        return view('admin.order.view',['orders'=>$orders]);

    }

    public function details(Request $request,$id){
        $order =Order::with(['details.entity'])->findOrFail($id);
        $riders =Rider::get();
        return view('admin.order.details',['order'=>$order,'riders'=>$riders]);
    }

    public function changeStatus(Request $request, $id){

        $status=$request->status;

        $order=Order::with('customer')->find($id);

        $old_status=$order->status;

        $order->status=$status;
        $order->save();

        switch($order->status){
            case 'dispatched':

                $message='Your order at Nitve Ecommerce with  ID:'.$order->refid.' has been dispatched. You will receive your order shortly';
                $title='Order Dispatched';

                break;
            case 'delivered':
                $message='Your order at Nitve Ecommerce with  ID:'.$order->refid.' has been delivered.';
                $title='Order Delivered';
                break;
            case 'cancelled':
                $message='Your order at Nitve Ecommerce with  ID:'.$order->refid.' has been cancelled.';
                $title='Order Cancelled';
                break;
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
        $rider =Order::findOrFail($id);
        $rider->rider_id=$request->riderid;
        $rider->save();
        return redirect()->back()->with('success', 'Rider Has Been change');
    }

}
