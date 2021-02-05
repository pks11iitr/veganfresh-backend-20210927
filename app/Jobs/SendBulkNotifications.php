<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\Order;
use App\Services\Notification\FCMNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendBulkNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $title, $message, $stores;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title, $message, $stores)
    {
        $this->title=$title;
        $this->message=$message;
        $this->stores=$stores;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //var_dump($this->stores);die;
        if($this->stores)
            $user_ids=Order::whereIn('store_id', $this->stores)->select('user_id')->get();
        else
            $user_ids=Order::select('user_id')->get();

        $user_ids=$user_ids->map(function($id){
            return $id->user_id;
        });

        if(!empty($user_ids)){
            $tokens=Customer::whereIn('id', $user_ids)
                ->where('notification_token', '!=', null)
                ->select('notification_token', 'id')
                ->get();
        }else{
            $tokens=Customer::
                where('notification_token', '!=', null)
                ->select('notification_token', 'id')
                ->get();
        }
        $tokens_arr=[];
        foreach($tokens as $token){

            if(in_array($token->notification_token, $tokens_arr))
               continue;

            $message=str_replace('{{name}}', $token->name??'User', $this->message);
            $message=str_replace('{{Name}}', $token->name??'User', $message);

//            Notification::create([
//                'user_id'=>$token->id,
//                'title'=>$this->title,
//                'description'=>$message,
//                'data'=>null,
//                'type'=>'individual'
//            ]);



            FCMNotification::sendNotification($token->notification_token, $this->title, $message);

            $tokens_arr[]=$token->notification_token;

        }


    }
}
