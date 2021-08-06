<?php

namespace App\Listeners;

use App\Events\SendOtp;
use App\Services\SMS\JaySms;
use App\Services\SMS\Msg91;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOtpListner implements ShouldQueue
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
     * @param  SendOtp  $event
     * @return void
     */
    public function handle(SendOtp $event)
    {
        JaySms::send($event->mobile,$event->message,$event->dlt_te_id);
    }
}
