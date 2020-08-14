<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBookingSlots extends Model
{
    protected $table='home_booking_slots';

    protected $fillable=['order_id', 'grade', 'date', 'time', 'display_time', 'status'];

    public static function createTimeSlots($order, $grade, $date, $time, $num_sessions, $status){

        for($i=0; $i<$num_sessions;$i++){

            HomeBookingSlots::create([
                'order_id'=>$order->id,
                'date'=>$date,
                'grade'=>$grade,
                'time'=>$time,
                'status'=>$status
            ]);

            $date=date('Y-m-d', strtotime('+1 days', strtotime($date)));

        }

    }


}
