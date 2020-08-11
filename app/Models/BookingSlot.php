<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSlot extends Model
{
    protected $table='bookings_slots';

    protected $fillable=['order_id', 'clinic_id', 'therapy_id', 'slot_id', 'status', 'grade'];


    public static function generateSlots($slotid, $num_sessions){

        $slot=TimeSlot::active()->find($slotid);

        if($slot)
            return false;

//        while($num_sessions>0){
//            if()
//        }


    }

}
