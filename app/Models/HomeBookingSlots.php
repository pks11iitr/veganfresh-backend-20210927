<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBookingSlots extends Model
{
    protected $table='home_booking_slots';

    protected $fillable=['order_id', 'grade', 'date', 'time', 'display_time', 'status', 'slot_id'];


    public function timeslot(){
        return $this->belongsTo('App\Models\DailyBookingsSlots', 'slot_id');
    }

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


    public static function createAutomaticSchedule($order, $grade, $slot, $num_sessions, $status='pending'){

        $alloted=0;

        //var_dump($bookings);
        $slots=DailyBookingsSlots::where('date', '>=',  $slot->date)
            ->where('start_time', $slot->start_time)
            ->orderBy('date', 'asc')
            ->limit(200)
            ->get();

        if(count($slots) < $alloted)
            return false;

        $i=0;
        while($i<$num_sessions && isset($slots[$i])){

            HomeBookingSlots::create([
                'order_id'=>$order->id,
                'slot_id'=>$slots[$i]->id,
                'grade'=>$grade,
                'status'=>$status,
            ]);
            $alloted++;
            $i++;
        }

        return $alloted==$num_sessions;

    }


    public function therapiesorder(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

}
