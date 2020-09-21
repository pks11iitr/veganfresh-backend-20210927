<?php

namespace App\Http\Controllers\Customer\Api;

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

        return [
            'status'=>'success',
                'user'=>$user->only('name','email','mobile', 'image', 'dob', 'address', 'city', 'state')

        ];
    }


    public function update(Request $request){

        $request->validate([
            'name'=>'required|max:60',
            'address'=>'max:200',
            'dob'=>'date_format:Y-m-d',
            'image'=>'image',
            'email'=>'email',
            'pincode'=>'integer',
//            'image'=>'array',
//            'image.*'=>'image'
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

        if($user->update($request->only('name','email', 'dob', 'address', 'city', 'state','pincode'))){
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
