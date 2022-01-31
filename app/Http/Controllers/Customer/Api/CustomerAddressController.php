<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Area;
use App\Models\CustomerAddress;
use App\Models\TimeSlot;
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
        $customeraddress=CustomerAddress::where('user_id',$user->id)
            ->orderBy('id', 'desc')
            ->get();

       if($customeraddress->count()>0) {
           return [
               'message' => 'success',
               'data'=>$customeraddress
           ];
       }else{
           return [
               'message' => 'Please add an address'
           ];
       }

    }
    public function addcustomeraddress(Request $request){

        $request->validate([
            
            'first_name'=>'required',
            'last_name'=>'required',
            'mobile_no'=>'required',
            'email'=>'required',
            'house_no'=>'required',
            //'appertment_name'=>'required',
            //'street'=>'required',
            'area'=>'required',
            'city'=>'required',
            //'pincode'=>'required',
            //'address_type'=>'required',
            //'lat'=>'required',
            //'lang'=>'required',
            //'map_address'=>'required'
        ]);
        $user=auth()->guard('customerapi')->user();

        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $area=Area::where('name', $request->area)->first();
        if(!$area)
            return [
                'status'=>'failed',
                'message'=>'Please select area'
            ];

        $customeraddress =  CustomerAddress::create([
                    'user_id'=>$user->id,
                    'first_name'=>ucfirst($request->first_name),
                    'last_name'=>ucfirst($request->last_name),
                    'mobile_no'=>$request->mobile_no,
                    'email'=>$request->email?:'',
                    'house_no'=>$request->house_no,
                    'appertment_name'=>$request->appertment_name,
                    'street'=>$request->street,
                    'landmark'=>$request->landmark?:'',
                    'area'=>$request->area,
                    'city'=>$request->city,
                    'pincode'=>$request->pincode,
                    'address_type'=>$request->address_type??'home',
                    'other_text'=>$request->other_text?$request->other_text:'',
                    'area_id'=>$area->id,
                    'floor'=>$request->floor,
                    //'lat'=>$request->lat?$request->lat:'',
                    //'lang'=>$request->lang?$request->lang:'',
                    //'map_address'=>$request->map_address?$request->map_address:'',
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

    public function getAreaList(Request $request){
        $area=Area::active()->get();
        $user=auth()->guard('customerapi')->user();

        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $user=$user->only('name', 'last_name', 'email', 'mobile', 'area_id');

        return [
            'status'=>'success',
            'data'=>compact('area', 'user')
        ];
    }
}
