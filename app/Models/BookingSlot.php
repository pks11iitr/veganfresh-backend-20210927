<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSlot extends Model
{
    protected $table='bookings_slots';

    protected $fillable=['order_id', 'clinic_id', 'therapy_id', 'slot_id', 'status', 'grade'];


    public static function createAutomaticSchedule($order, $grade, $slot, $num_sessions, $status){

        $slots=TimeSlot::active()->
            where('clinic_id',$slot->clinic_id)
            ->where('date', '>=',  $slot->date)
            ->where('start_time', $slot->start_time)
            ->where(function($slots) use($grade){
                switch($grade){
                    case 1:
                        $slots->where('grade_1','>', 0);
                        break;
                    case 2:
                        $slots->where('grade_2','>', 0);
                        break;
                    case 3:
                        $slots->where('grade_3','>', 0);
                        break;
                    case 4:
                        $slots->where('grade_4','>', 0);
                        break;
                }
            })
            ->orderBy('date', 'asc')
            ->limit(100)
            ->get();

        //var_dump('fd');die;

        $i=0;
        while($i<$num_sessions && isset($slots[$i])){
            BookingSlot::create([
                'order_id'=>$order->id,
                'clinic_id'=>$order->details[0]->clinic_id,
                'therapy_id'=>$order->details[0]->entity_id,
                'slot_id'=>$slots[$i]->id,
                'grade'=>$grade,
                'status'=>$status,
            ]);
            $i++;
        }
    }

    public function timeslot(){
        return $this->belongsTo('App\Models\TimeSlot', 'slot_id');
    }

}
