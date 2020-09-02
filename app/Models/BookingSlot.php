<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSlot extends Model
{
    protected $table='bookings_slots';

    protected $fillable=['order_id', 'clinic_id', 'therapy_id', 'slot_id', 'status', 'grade'];


    public static function createAutomaticSchedule($order, $grade, $slot, $num_sessions, $status){
        //var_dump($slot->toArray());die;

        $booked_slots=BookingSlot::with('timeslot')
            ->whereHas('timeslot', function($timeslots) use($slot){
                    $timeslots->where('date', '>=', $slot->date);
             })
            ->where('status','confirmed')
            ->where('grade', $grade)
            ->get();
        $bookings=[];
        foreach($booked_slots as $bs){
            if(!isset($bookings[$bs->timeslot->date]))
                $bookings[$bs->timeslot->date]=[
                    'g1'=>0,
                    'g2'=>0,
                    'g3'=>0,
                    'g4'=>0,
                ];
            $bookings[$bs->timeslot->date]['g'.$bs->grade]+=1;
        }
        //var_dump($bookings);
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
            ->limit(200)
            ->get();

        //var_dump($slots->toArray());die('3131');

        $i=0;
        $alloted=0;
        while($i<$num_sessions && isset($slots[$i])){
            if(!isset($bookings[$slots[$i]->date])){
                BookingSlot::create([
                    'order_id'=>$order->id,
                    'clinic_id'=>$order->details[0]->clinic_id,
                    'therapy_id'=>$order->details[0]->entity_id,
                    'slot_id'=>$slots[$i]->id,
                    'grade'=>$grade,
                    'status'=>$status,
                ]);
                $alloted+=1;
            }
            else{
                $grade1='grade_'.$grade;
                //var_dump($bookings[$slots[$i]->date]);die;
                if($slots[$i]->$grade1 > $bookings[$slots[$i]->date]['g'.$grade]){
                    BookingSlot::create([
                        'order_id'=>$order->id,
                        'clinic_id'=>$order->details[0]->clinic_id,
                        'therapy_id'=>$order->details[0]->entity_id,
                        'slot_id'=>$slots[$i]->id,
                        'grade'=>$grade,
                        'status'=>$status,
                    ]);
                    $alloted+=1;
                }
            }
            $i++;
        }
        //var_dump($i);die;
        return $alloted==$num_sessions;
    }

    public function timeslot(){
        return $this->belongsTo('App\Models\TimeSlot', 'slot_id');
    }

    public function order(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

}
