<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Models\Notification;
use App\Models\Order;
use App\Services\Notification\FCMNotification;
//use App\Services\SMS\Msg91;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmListner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderConfirmed  $event
     * @return void
     */
    public function handle(OrderConfirmed $event)
    {
        $order=$event->order;

        $this->sendNotifications($order);

    }


    public function sendNotifications($order){

        Order::setInvoiceNumber($order);

        $title='Order Confirmed';
        $message='Congratulations! Your purchase of Rs. '.($order->total_cost-$order->coupon_discount+$order->delivery_charge+$order->extra_amount).' at Vegans Fresh is successfull. Order Reference ID: '.$order->refid;


        Notification::create([
            'user_id'=>$order->user_id,
            'title'=>'Order Confirmed',
            'description'=>$message,
            'data'=>null,
            'type'=>'individual'
        ]);
        if($order->customer->notification_token??null)
            FCMNotification::sendNotification($order->customer->notification_token, $title, $message);

        //send customer notification
//        Msg91::send($order->customer->mobile, $message, env('HALLOBB_CUSTOMER_ORDER_CONFIRM'));

        //store notification
//        if(!empty($order->storename->mobile))
//            Msg91::send($order->storename->mobile, 'New Order '.$order->refid.' received at House Goods. Scheduled Delivery is '.($order->delivery_date??'').' '.($order->timeslot->name??''), env('HALLOB_STORE_ORDER_CONFIRM'));

    }
}
