<?php

namespace App\Http\Controllers\Therapist\Api;

use App\Models\Therapist;
use App\Models\TherapistTherapy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function updateImage(Request $request){

        $user=$request->user;

        $request->validate([

            'image'=>'required'

        ]);


        $user->saveImage($request->image, 'therapist-images');

        return [
                'status'=>'success',
                'data'=>[
                    'image'=>$user->image
                ]
            ];

    }


    public function addServices(Request $request){

        $user=$request->user;



    }

    public function deleteService(Request $request, $id){
        $user=$request->user;

        $therapy=TherapistTherapy::where('therapist_id', $user->id)
        ->find($id);

        if(!$therapy)
            return [
                'status'=>'failed',
                'message'=>'No Therapy Found'
            ];

        $therapy->delete();

        return [
            'status'=>'success',
        ];


    }

    public function myProfile(Request $request){

        $user=$request->user;

        $therapiesobj=$user->therapies;

        $therapies=[];

        foreach($therapiesobj as $therapy){
               $thrapies[]=[
                   'id'=>$therapy->pivot->id
               ];
        }

        $user=$user->only('image','name','email','mobile');


        return [

            'status'=>'success',
            'data'=>compact('user', 'therapies')

        ];

    }

}
