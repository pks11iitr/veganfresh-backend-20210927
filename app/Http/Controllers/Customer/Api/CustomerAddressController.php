<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class CustomerAddressController extends Controller
{

    public function getcustomeraddress(Request $request){

        $user=auth()->guard('customerapi')->user();

        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $customeraddress=CustomerAddress::where('user_id',$user->id)->get();

   if($customeraddress->count()>0) {
       return [
           'message' => 'success',
           'data'=>$customeraddress
       ];
   }else{
       return [
           'message' => 'error'
       ];
   }

    }
    public function addcustomeraddress(Request $request){

        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'mobile_no'=>'required',
            'house_no'=>'required',
            'appertment_name'=>'required',
            'street'=>'required',
            'area'=>'required',
            'city'=>'required',
            'pincode'=>'required',
            'address_type'=>'required',
        ]);
        $user=auth()->guard('customerapi')->user();

        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];


        $customeraddress =  CustomerAddress::create([
                    'user_id'=>$user->id,
                    'first_name'=>$request->first_name,
                    'last_name'=>$request->last_name,
                    'mobile_no'=>$request->mobile_no,
                    'email'=>$request->email?:'',
                    'house_no'=>$request->house_no,
                    'appertment_name'=>$request->appertment_name,
                    'street'=>$request->street,
                    'landmark'=>$request->landmark?:'',
                    'area'=>$request->area,
                    'city'=>$request->city,
                    'pincode'=>$request->pincode,
                    'address_type'=>$request->address_type,
                    'other_text'=>$request->other_text?:'',
                ]);

        if($customeraddress) {
            return [
                'message' => 'success',
            ];
        }else{
            return [
                'message' => 'error'
            ];
        }

    }
}
