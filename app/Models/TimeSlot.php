<?php

namespace App\Models;

use App\Models\Traits\Active;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TimeSlot extends Model
{
    use Active;

    protected $table='time_slots';

    protected $fillable=['date', 'start_time', 'duration', 'grade_1','grade_2','grade_3','grade_4','isactive'];


    public static function createTimeSlots($clinic_id, $date){

        $timeslots=TimeSlot::where('clinic_id', $clinic_id)->where('date', $date)->orderBy('internal_start_time', 'asc')->get();

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

            $slot=[
                'id'=>$ts->id,
                'display'=>$ts->start_time,
                'is_active'=>$ts->isactive
            ];

            if($ts->internal_start_time<'12:00:00'){
                if($ts->grade_1>0){
                    $grade_1_count=$grade_1_count+$ts->grade_1;
                    $grade_1_slots['morning'][]=$slot;
                }if($ts->grade_2>0){
                    $grade_2_count=$grade_2_count+$ts->grade_2;
                    $grade_2_slots['morning'][]=$slot;
                }if($ts->grade_3>0){
                    $grade_3_count=$grade_3_count+$ts->grade_3;
                    $grade_3_slots['morning'][]=$slot;
                }if($ts->grade_4>0){
                    $grade_4_count=$grade_4_count+$ts->grade_4;
                    $grade_4_slots['morning'][]=$slot;
                }
            }else if($ts->internal_start_time<'17:00:00'){
                if($ts->grade_1>0){
                    $grade_1_count=$grade_1_count+$ts->grade_1;
                    $grade_1_slots['afternoon'][]=$slot;
                }if($ts->grade_2>0){
                    $grade_2_count=$grade_2_count+$ts->grade_2;
                    $grade_2_slots['afternoon'][]=$slot;
                }if($ts->grade_3>0){
                    $grade_3_count=$grade_3_count+$ts->grade_3;
                    $grade_3_slots['afternoon'][]=$slot;
                }if($ts->grade_4>0){
                    $grade_4_count=$grade_4_count+$ts->grade_4;
                    $grade_4_slots['afternoon'][]=$slot;
                }
            }else{
                if($ts->grade_1>0){
                    $grade_1_count=$grade_1_count+$ts->grade_1;
                    $grade_1_slots['evening'][]=$slot;
                }if($ts->grade_2>0){
                    $grade_2_count=$grade_2_count+$ts->grade_2;
                    $grade_2_slots['evening'][]=$slot;
                }if($ts->grade_3>0){
                    $grade_3_count=$grade_3_count+$ts->grade_3;
                    $grade_3_slots['evening'][]=$slot;
                }if($ts->grade_4>0){
                    $grade_4_count=$grade_4_count+$ts->grade_4;
                    $grade_4_slots['evening'][]=$slot;
                }
            }
        }

        return compact('grade_1_count','grade_1_slots','grade_2_count', 'grade_2_slots', 'grade_3_count', 'grade_3_slots', 'grade_4_count', 'grade_4_slots');

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

}
