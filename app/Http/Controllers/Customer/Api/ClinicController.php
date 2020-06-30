<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Clinic;
use App\Models\Therapy;
use App\Models\Traits\Active;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClinicController extends Controller
{

    use Active;

    public function index(Request $request){
        return [
            'status'=>'succecss',
            'data'=>[
                'clinics'=>Clinic::active()->with(['commentscount', 'avgreviews'])->get()
            ]
        ];
    }


    public function details(Request $request, $id){
        $clinic=Clinic::active()->with(['gallery', 'commentscount', 'avgreviews','therapies'=>function($therapies){
            $therapies->where('therapies.isactive', true)->where('clinic_therapies.isactive', true);
        }])->where('id', $id)->first();
        if($clinic)
            return [
                'status'=>'success',
                'data'=>[
                    'clinic'=>$clinic
                ]
            ];

        return [
            'status'=>'failed',
            'data'=>[

            ],
            'message'=>'No clinic found'
        ];
    }

    public function clinicTherapyDetails(Request $request, $clinicid, $therapyid){
        $timings=[];
        $dates=[];
        $clinic=Clinic::active()->with(['therapies'=>function($therapies) use ($therapyid){
            $therapies->where('therapies.isactive', true)->where('therapies.id', $therapyid)->where('clinic_therapies.isactive', true)->with(['commentscount', 'avgreviews','gallery']);
        }])->find($clinicid);
        if(!$clinic)
            return [
                'status'=>'failed',
                'message'=>'No clinic found'
            ];

        $date=date('Y-m-d');
        for($i=1; $i<=7;$i++){
            $dates[]=[
                'text'=>($i==1)?'Today':($i==2?'Tomorrow':date('d F', strtotime($date))),
                'text2'=>($i==1)?'':($i==2?'':date('D', strtotime($date))),
                'value'=>$date
            ];
            $date=date('Y-m-d', strtotime('+'.$i.' days', strtotime($date)));
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
                'clinic'=>$clinic,
                'dates'=>$dates,
                'timings'=>$timings
            ]
        ];
    }
}
