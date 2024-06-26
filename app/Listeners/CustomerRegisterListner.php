<?php

namespace App\Listeners;

use App\Events\CustomerRegistered;
use App\Models\OTPModel;
use App\Services\SMS\ConnectExpress;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerRegisterListner implements ShouldQueue
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
     * @param  CustomerRegistered  $event
     * @return void
     */
    public function handle(CustomerRegistered $event)
    {
        //send OTP

        $otp=OTPModel::createOTP('customer', $event->user->id, 'register');
        $msg=str_replace('{{otp}}', $otp, config('sms-templates.register'));
        ConnectExpress::send($event->user->mobile,$msg, env('LOGIN_OTP'));
    }
}
