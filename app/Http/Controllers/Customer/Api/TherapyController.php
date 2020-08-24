<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Clinic;
use App\Models\Therapist;
use App\Models\Therapy;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TherapyController extends Controller
{
    public function index(Request $request){
        $therapies=Therapy::active()->with(['commentscount', 'avgreviews'])->get();
        return [
            'status'=>'succecss',
            'data'=>[
                'therapies'=>$therapies
            ]
        ];
    }

    public function details(Request $request, $id){

        $dates=[];
        $timings=[];

        $therapy=Therapy::active()->with(['gallery', 'commentscount', 'avgreviews'])->find($id);

        if(!$therapy)
            return [
                'status'=>'failed',
                'data'=>[

                ],
                'message'=>'No Therapy found'
            ];

        $therapistlocations=Therapist::with(['locations'=>function($locations){
            $locations->first();
        }])
            ->whereHas('therapies', function($therapies) use($therapy){
            $therapies->where('therapies.id', $therapy->id);
        })
            ->get();



        $date=date('Y-m-d');
        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($date))),
                'text2'=>($i==1)?'':($i==2?'':date('D')),
                'value'=>$date
            ];
            $date=date('Y-m-d', strtotime('+'.$i.' days', strtotime($date, strtotime($date))));
        }
        $date=date('Y-m-d h:i:s');
        for($i=9; $i<=17;$i++){
            $timings[]=[
                'text'=>date('h:i A', strtotime($date)),
                'value'=>date('H:i', strtotime($date))
            ];
            $date=date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($date)));
        }
        return [
            'status'=>'success',
            'data'=>[
                'therapy'=>$therapy,
                'dates'=>$dates,
                'timings'=>$timings,
                'therapist_locations'=>$therapistlocations
            ]
        ];

    }

    public function nearbyTherapists(Request $request){
        $request->validate([
            'lat'=>'required|numeric',
            'lang'=>'required|numeric',
            'therapy_id'=>'required|integer',
        ]);


        $therapist=Therapist::active()
            ->with([
                'therapies'=>function($therapies)use($request){
                    $therapies->where('therapies.id', $request->therapy_id)->select('therapies.id');
                }
            ])
            ->whereHas(
                'therapies',function($therapies)use($request){
                    $therapies->where('therapies.id', $request->therapy_id);
                    }
            )
            //->select('last_lat', 'last_lang')
            ->get();

        $nearby=[];

        $activegrades=[
            'grade1'=>'no',
            'grade2'=>'no',
            'grade3'=>'no',
            'grade4'=>'no',
        ];

        foreach($therapist as $t){
            $activegrades['grade'.$t->therapies[0]->pivot->therapist_grade]='yes';
            $nearby[]=[
                'lat'=>$t->last_lat,
                'lang'=>$t->last_lang,
                'grade'=>$t->therapies[0]->pivot->therapist_grade
            ];
        }

        return [
            'status'=>'success',
            'data'=>compact('nearby', 'activegrades'),
        ];


    }

    public function getAvailableSlots(Request $request, $therapy_id){
        $date=$request->date??date('Y-m-d');
        $selected_date=$date;
        $today=date('Y-m-d');

        $therapy=Therapy::active()->with(['gallery', 'commentscount', 'avgreviews'])->find($therapy_id);

        if(!$therapy)
            return [
                'status'=>'failed',
                'message'=>'No Therapy found'
            ];

        $timeslots=TimeSlot::getTimeSlots($clinic, $date);

        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($today))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($today))),
                'value'=>$today,
            ];
            $today=date('Y-m-d', strtotime('+1 days', strtotime($today)));
        }

        $timeslots=[
            $timeslots['grade_1_slots'],
            $timeslots['grade_2_slots'],
            $timeslots['grade_3_slots'],
            $timeslots['grade_4_slots'],
        ];

        return [
            'status'=>'success',
            'data'=>compact('timeslots','dates', 'selected_date')
        ];
    }

}
