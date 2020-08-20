<?php

namespace App\Http\Controllers\Therapist\Api;

use App\Models\Therapist;
use App\Models\TherapistTherapy;
use App\Models\Therapy;
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



    public function getServices(Request $request){

        $user=$request->user;

        $therapies=Therapy::active()
            ->whereDoesntHave('therapists', function($therapists) use($user){
                $therapists->where('therapists.id', $user->id);
            })->get();

        return [
            'status'=>'success',
            'data'=>compact('therapies')
        ];
    }


    public function addServices(Request $request){

        $request->validate([
            'therapies'=>'array'
        ]);

        $user=$request->user;

        if(!empty($request->therapies)){
            $therapies=Therapy::active()
                ->whereIn('id', $request->therapies)
                ->whereDoesntHave('therapists', function($therapists) use($user){
                    $therapists->where('therapists.id', $user->id);
                })->get();
            //var_dump($therapies->toArray());
            foreach($therapies as $therapy){
                $user->therapies()->syncWithoutDetaching([$therapy->id=> ['isactive'=>'Applied']]);
            }
        }

        return [
            'status'=>'success',
            'message'=>'Your Request Has Been Submitted'
        ];
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


        //$therapist=Therapist::with('therapies')->find($user->id);

        $therapiesobj=$user->therapies;

        //var_dump($therapiesobj->toArray());die;

        $therapies=[];

        foreach($therapiesobj as $therapy){
               $therapies[]=[
                   'id'=>$therapy->pivot->id,
                   'name'=>$therapy->name,
                   'status'=>$therapy->pivot->isactive
               ];
        }

        $user=$user->only('image','name','email','mobile');


        return [

            'status'=>'success',
            'data'=>compact('user', 'therapies')

        ];

    }

}
