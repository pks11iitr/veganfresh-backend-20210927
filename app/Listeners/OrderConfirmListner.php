<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Models\Notification;
use App\Models\Order;
use App\Services\Notification\FCMNotification;
use App\Services\SMS\Msg91;
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
        if($order->details[0]->entity_type == 'App\Models\Product'){
            $message='Congratulations! Your purchase of Rs. '.($order->total_cost-$order->coupon_discount+$order->delivery_charge+$order->extra_amount).' at Hallobasket is successfull. Order Reference ID: '.$order->refid;
        }else{
            $message='Congratulations! Your therapy booking of Rs. '.$order->total_cost.' at Hallobasket is successfull. Order Reference ID: '.$order->refid;

        }

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
        Msg91::send($order->customer->mobile, $message);

        //store notification
        if(!empty($order->storename->mobile))
            Msg91::send($order->storename->mobile, 'New Order '.$order->refid.' arrived. Scheduled Delivery is '.($order->delivery_date??'').' '.($order->timeslot->name??''));

    }
}
