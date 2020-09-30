<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TimeSlot extends Model
{
    protected $table='time_slot';
    protected $fillable=['name', 'from_time','to_time','isactive'];

    protected $hidden = ['created_at','deleted_at','updated_at'];



    public static function getNextDeliverySlot(){

        $date=date('Y-m-d');
        $time=date('H:i:s');
        $text='Today';
        while(true){
            $timeslot=TimeSlot::where('from_time', '>=', $time)
                ->orderBy('from_time', 'asc')
                ->get();

            //echo "Checking For $date, $time";

            $orders=Order::where('status', 'confirmed')
                ->groupBy('delivery_slot', DB::raw('DATE(updated_at)'))
                ->where(DB::raw('DATE(updated_at)'), $date)
                ->select(DB::raw('count(delivery_slot) as available'), 'delivery_slot')
                ->get();

            $availability=[];
            foreach($orders as $o){
                $availability[$o->delivery_slot]=$o->available;
            }
            //print_r($availability);
            foreach($timeslot as $ts){
                if($ts->slot_capacity > ($availability[$ts->id]??0)){

                       return ['slot_id'=>$ts->id,  'next_slot'=>$text.' '.$ts->from_time.' - '.$ts->to_time];

                }
            }

            $date=date('Y-m-d', strtotime('+1 days', strtotime($date)));
            $time='05:00:00';
            sleep(1);
            if($text=='Today')
                $text='Tomorrow';
            else if($text=='Tomorrow')
                $text=date('d/m/Y', strtotime($date));
            else
                $text=date('d/m/Y', strtotime($date));
        }

    }


}
