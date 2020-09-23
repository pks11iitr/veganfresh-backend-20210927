<?php

namespace App\Listeners;

use App\Events\RiderRegistered;
use App\Models\OTPModel;
use App\Services\SMS\Msg91;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TherapistRegisterListner
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
     * @param  RiderRegistered  $event
     * @return void
     */
    public function handle(RiderRegistered $event)
    {
        $otp=OTPModel::createOTP('therapist', $event->user->id, 'register');
        $msg=str_replace('{{otp}}', $otp, config('sms-templates.register'));
        Msg91::send($event->user->mobile,$msg);
    }
}
