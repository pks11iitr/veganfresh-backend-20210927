<?php

namespace App\Http\Controllers\Therapist\Api;

use App\Models\TherapistLocations;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function updateLocation(Request $request){

        $request->validate([
            'lat'=>'required|numeric',
            'lang'=>'required|numeric'
        ]);

        $user=$request->user;

        TherapistLocations::create([
            'lat'=>$request->lat,
            'lang'=>$request->lang,
            'therapist_id'=>$user->id
        ]);


        return [
            'status'=>'success'
        ];

    }
}
