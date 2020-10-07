<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Models\Notification;
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

        $message='';
        if($order->details[0]->entity_type == 'App\Models\Product'){
            $message='Congratulations! Your purchase of Rs. '.$order->total_cost.' at SuzoDailyNeeds is successfull. Order Reference ID: '.$order->refid;
        }else{
            $message='Congratulations! Your therapy booking of Rs. '.$order->total_cost.' at SuzoDailyNeeds is successfull. Order Reference ID: '.$order->refid;

        }

        Notification::create([
            'user_id'=>$order->user_id,
            'title'=>'Order Confirmed',
            'description'=>$message,
            'data'=>null,
            'type'=>'individual'
        ]);
    }
}
