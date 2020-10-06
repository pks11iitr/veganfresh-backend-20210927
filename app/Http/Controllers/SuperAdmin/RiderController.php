<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Rider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class RiderController extends Controller
{
    public function index(Request $request){
        $riders =Rider::orderBy('id', 'DESC')->paginate(10);
        return view('admin.rider.view',['riders'=>$riders]);
    }

    public function create(Request $request){
        $stores=User::where('id', '>', 1)->get();
        return view('admin.rider.add', compact('stores'));
    }

    public function store(Request $request){
        $request->validate([
            'status'=>'required',
            'name'=>'required',
            'email'=>'required',
            'mobile'=>'required',
            'address'=>'required',
            'state'=>'required',
            'city'=>'required',
            'password'=>'required',
            'image'=>'required|image',
            'store_id'=>'required|integer'
        ]);

        if(Rider::where('mobile', $request->mobile)->first()){
            return redirect()->back()->with('error', 'Mobile Number Already Exists Registers');
        }

        if($rider=Rider::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'address'=>$request->address,
            'state'=>$request->state,
            'city'=>$request->city,
            'password'=> Hash::make($request->password),
            'status'=>$request->status,
            'image'=>'a',
            'store_id'=>$request->store_id]))
        {
            $rider->saveImage($request->image, 'rider');
            return redirect()->route('rider.list')->with('success', 'rider has been created');
        }
        return redirect()->back()->with('error', 'rider create failed');
    }

    public function edit(Request $request,$id){
        $rider = Rider::findOrFail($id);
        $stores=User::where('id', '>', 1)->get();
        return view('admin.rider.edit',['rider'=>$rider, 'stores'=>$stores]);
    }

    public function update(Request $request,$id){
        $request->validate([
            'status'=>'required',
            'name'=>'required',
            'email'=>'required',
            'mobile'=>'required',
            'address'=>'required',
            'state'=>'required',
            'city'=>'required',
            'image'=>'image',
            'store_id'=>'required|integer'
        ]);



        $rider = Rider::findOrFail($id);
        if($request->mobile!=$rider->mobile){
            if($rider1=Rider::where('mobile', $request->mobile)->first()){
                return redirect()->back()->with('error', 'Mobile Number Already Exists Registers');
            }
        }
        $rider->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'state' => $request->state,
            'city' => $request->city,
            'status' => $request->status,
            'image'=>'a',
            'store_id'=>$request->store_id,
            'password'=>!empty($request->password)?Hash::make($request->password):$rider->password
        ]);

        if($request->image ) {
            $rider->saveImage($request->image, 'rider');
        }

        if($rider)
        {
            return redirect()->back()->with('success', 'rider has been updated');
        }
        return redirect()->back()->with('error', 'rider create failed');
    }

}
