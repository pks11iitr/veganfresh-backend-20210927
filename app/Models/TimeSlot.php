<?php

namespace App\Models;

use App\Http\Controllers\Customer\Api\ClinicController;
use App\Models\Traits\Active;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TimeSlot extends Model
{
    use Active;

    protected $table='time_slots';

    protected $fillable=['date', 'start_time', 'duration', 'grade_1','grade_2','grade_3','grade_4','isactive'];


    public static function getTimeSlots($clinic, $date){

        $tsids=[];
        $booking_data=[];

        $timeslots=TimeSlot::where('clinic_id', $clinic->id)->where('date', $date)->orderBy('internal_start_time', 'asc')->get();

        //return $timeslots;

        foreach($timeslots as $ts)
            $tsids[]=$ts->id;

        if($tsids)
            $bookings=BookingSlot::whereIn('slot_id', $tsids)
                ->where('status', 'confirmed')
                ->groupBy('slot_id','grade')
                ->select(DB::raw('count(*) as count'), 'slot_id', 'grade')
                ->get();

                //return $bookings;
            foreach($bookings as $b){
                if(!isset( $booking_data[$b->slot_id])){
                    $booking_data[$b->slot_id]=[];
                }
                $booking_data[$b->slot_id][$b->grade]=$b->count;

            }
            //return $booking_data;

        $grade_1_count=0;
        $grade_2_count=0;
        $grade_3_count=0;
        $grade_4_count=0;

        $grade_1_slots=[
            'morning'=>[],
            'afternoon'=>[],
            'evening'=>[],
        ];
        $grade_2_slots=[
            'morning'=>[],
            'afternoon'=>[],
            'evening'=>[],
        ];
        $grade_3_slots=[
            'morning'=>[],
            'afternoon'=>[],
            'evening'=>[],
        ];
        $grade_4_slots=[
            'morning'=>[],
            'afternoon'=>[],
            'evening'=>[],
        ];

        foreach($timeslots as $ts){

            if($ts->internal_start_time<'12:00:00'){
                if($ts->grade_1>0){
                    $grade_1_count=$grade_1_count+self::calculateSlotCount($ts, 1);
                    $grade_1_slots['morning'][]=self::checkSlotAvailablity($ts,$booking_data, 1);
                }if($ts->grade_2>0){
                    $grade_2_count=$grade_2_count+self::calculateSlotCount($ts, 2);
                    $grade_2_slots['morning'][]=self::checkSlotAvailablity($ts,$booking_data, 2);
                }if($ts->grade_3>0){
                    $grade_3_count=$grade_3_count+self::calculateSlotCount($ts, 3);
                    $grade_3_slots['morning'][]=self::checkSlotAvailablity($ts,$booking_data, 3);
                }if($ts->grade_4>0){
                    $grade_4_count=$grade_4_count+self::calculateSlotCount($ts, 4);
                    $grade_4_slots['morning'][]=self::checkSlotAvailablity($ts,$booking_data, 4);
                }
            }else if($ts->internal_start_time<'17:00:00'){
                if($ts->grade_1>0){
                    $grade_1_count=$grade_1_count+self::calculateSlotCount($ts, 1);
                    $grade_1_slots['afternoon'][]=self::checkSlotAvailablity($ts,$booking_data, 1);
                }if($ts->grade_2>0){
                    $grade_2_count=$grade_2_count+self::calculateSlotCount($ts, 2);
                    $grade_2_slots['afternoon'][]=self::checkSlotAvailablity($ts,$booking_data, 2);
                }if($ts->grade_3>0){
                    $grade_3_count=$grade_3_count+self::calculateSlotCount($ts, 3);
                    $grade_3_slots['afternoon'][]=self::checkSlotAvailablity($ts,$booking_data, 3);
                }if($ts->grade_4>0){
                    $grade_4_count=$grade_4_count+self::calculateSlotCount($ts, 4);
                    $grade_4_slots['afternoon'][]=self::checkSlotAvailablity($ts,$booking_data, 4);
                }
            }else{
                $grade_1_count=$grade_1_count+self::calculateSlotCount($ts, 1);
                $grade_1_slots['evening'][]=self::checkSlotAvailablity($ts,$booking_data, 1);
            }if($ts->grade_2>0){
                $grade_2_count=$grade_2_count+self::calculateSlotCount($ts, 2);
                $grade_2_slots['evening'][]=self::checkSlotAvailablity($ts,$booking_data, 2);
            }if($ts->grade_3>0){
                $grade_3_count=$grade_3_count+self::calculateSlotCount($ts, 3);
                $grade_3_slots['evening'][]=self::checkSlotAvailablity($ts,$booking_data, 3);
            }if($ts->grade_4>0){
                $grade_4_count=$grade_4_count+self::calculateSlotCount($ts, 4);
                $grade_4_slots['evening'][]=self::checkSlotAvailablity($ts,$booking_data, 4);
            }
        }

        $grade_4_slots['name']='Silver';
        $grade_4_slots['price']='Rs. '.$clinic->therapies[0]->pivot->grade4_price??0;
        $grade_4_slots['count']=$grade_4_count>0?($grade_4_count.' Slots Available'):'No Slots Available';
        $grade_3_slots['name']='Gold';
        $grade_3_slots['price']='Rs. '.$clinic->therapies[0]->pivot->grade3_price??0;
        $grade_3_slots['count']=$grade_3_count>0?($grade_3_count.' Slots Available'):'No Slots Available';
        $grade_2_slots['name']='Platinum';
        $grade_2_slots['price']='Rs. '.$clinic->therapies[0]->pivot->grade2_price??0;
        $grade_2_slots['count']=$grade_2_count>0?($grade_2_count.' Slots Available'):'No Slots Available';
        $grade_1_slots['name']='Diamond';
        $grade_1_slots['price']='Rs. '.$clinic->therapies[0]->pivot->grade1_price??0;
        $grade_1_slots['count']=$grade_1_count>0?($grade_1_count.' Slots Available'):'No Slots Available';

        return compact('grade_1_slots', 'grade_2_slots',  'grade_3_slots', 'grade_4_slots');

    }


    public static function getDatewiseSlotsCount($clinic_id, $dates){

        $timeslots=TimeSlot::active()
            ->whereIn('date', $dates)
            ->where('clinic_id', $clinic_id)
            ->groupBy('date')
            ->select(DB::raw('SUM(grade_1) as g1'),DB::raw('SUM(grade_2) as g2'),DB::raw('SUM(grade_3) as g3'), DB::raw('SUM(grade_4) as g4'), 'date')->get();

        $slots=[];
        foreach($timeslots as $ts){
            $slots[$ts->date]=$ts->g1+$ts->g2+$ts->g3+$ts->g4;
        }

        return $slots;

    }


    public static function checkSlotAvailablity($ts, $booking_data, $gradeno){

        if(!isset($booking_data[$ts->id][$gradeno])) {
            return [
                'id' => $ts->id,
                'display' => $ts->start_time,
                'is_active' => $ts->isactive ? 1 : 0
            ];
        }
        else{
            return [
                'id'=>$ts->id,
                'display'=>$ts->start_time,
                'is_active'=>(($ts->isactive?1:0) && ($booking_data[$ts->id][1]>=$ts->grade_1?0:1)) ? 1:0
            ];

        }
    }

    public static function calculateSlotCount($ts, $gradeno){
        $grade='grade_'.$gradeno;
        if(!isset($booking_data[$ts->id][$gradeno]))
            return $ts->$grade;
        else
            return ($booking_data[$ts->id][$gradeno]>=$ts->$grade?0:($ts->$grade-$booking_data[$ts->id][$gradeno]));
    }


    public function bookings(){
        $this->belongsToMany('App\Models\BookingSlot', 'slot');
    }

}
