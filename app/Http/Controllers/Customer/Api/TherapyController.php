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
            $date=date('Y-m-d', strtotime('+1 days', strtotime($date, strtotime($date))));
        }
        $date=date('Y-m-d h:i:s');
        for($i=9; $i<=17;$i++){
            $timings[]=[
                'text'=>date('h:i A', strtotime($date)),
                'value'=>date('H:i', strtotime($date))
            ];
            $date=date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($date)));
        }

        $display_text=[

            'automatic'=>'One session per day will be booked at selected time based on availablity',
            'cutom'=>'You can select any number of slot on any day based on availability',

        ];

        return [
            'status'=>'success',
            'data'=>[
                'therapy'=>$therapy,
                'dates'=>$dates,
                'timings'=>$timings,
                'therapist_locations'=>$therapistlocations,
                'display_text'=>$display_text
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
            'no',
            'no',
            'no',
            'no',
        ];

        foreach($therapist as $t){
            switch($t->therapies[0]->pivot->therapist_grade){
                case 1:
                    $activegrades[0]='yes';
                    break;
                case 2:
                    $activegrades[1]='yes';
                    break;
                case 3:
                    $activegrades[2]='yes';
                    break;
                case 4:
                    $activegrades[3]='yes';
                    break;
            }
            //$activegrades['grade'.$t->therapies[0]->pivot->therapist_grade]='yes';
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

}
