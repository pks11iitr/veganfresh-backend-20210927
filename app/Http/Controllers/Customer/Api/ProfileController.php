<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Area;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function view(Request $request){

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $areas=Area::active()->select('id', 'name')->get();

        return [
            'status'=>'success',
                'user'=>$user->only('name','email','mobile', 'image', 'dob', 'address', 'city', 'state','pincode', 'last_name'),
                'areas'=>$areas

        ];
    }


    public function update(Request $request){

        $request->validate([
            'name'=>'required|max:60',
            //'address'=>'max:200',
            //'dob'=>'date_format:Y-m-d',
            'image'=>'image',
            'email'=>'email',
            //'pincode'=>'integer',
//            'image'=>'array',
//            'image.*'=>'image'
            'area_id'=>'required|integer',
            'last_name'=>'required'
        ]);
        //var_dump($request->all());
        //var_dump($request->image);die;
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        if($request->image){

            $user->saveImage($request->image, 'customers');

        }

        if($user->update($request->only('name','email', 'dob', 'address', 'city', 'state','pincode', 'last_name'))){
            return [
                'status'=>'success',
                'message'=>'Profile has been updated'
            ];
        }

        return [
            'status'=>'failed',
            'message'=>'Something went wrong'
        ];

    }
}
