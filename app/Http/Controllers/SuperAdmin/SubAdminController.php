<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class SubAdminController extends Controller
{
    public function index(Request $request){
        $admins =User::where('id', '>', 1)
            ->role('subadmin')
            ->paginate(10);
        return view('admin.subadmin.view',['admins'=>$admins]);
    }

    public function create(Request $request){
        return view('admin.subadmin.add');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|unique:users',
            'mobile'=>'required|unique:users',
            'address'=>'required',
            'password'=>'required',
            'status'=>'required'
        ]);

        if($area=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'address'=>$request->address,
            'password'=> Hash::make($request->password),
            'status'=>$request->status,
        ]))

        {
            //var_dump($area->toArray());die;
            $area->assignRole('subadmin');
            return redirect()->route('stores.list')->with('success', 'store has been created');
        }
        return redirect()->back()->with('error', 'store create failed');
    }

    public function edit(Request $request,$id){
        $subadmin =User::findOrFail($id);
        return view('admin.subadmin.edit',['subadmin'=>$subadmin]);
    }

    public function update(Request $request ,$id){
        $request->validate([
            'name'=>'required',
            'email'=>'required|unique:users,email,'.$id,
            'mobile'=>'required|unique:users,mobile,'.$id,
            'address'=>'required',
            //'password'=>'required',
            'status'=>'required'
        ]);

        $subadmin =User::findOrFail($id);

        if($request->password) {
            $subadmin->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'status' => $request->status,
            ]);
        }else{
            $subadmin->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'status' => $request->status,
            ]);
        }
        if($subadmin)
        {
            return redirect()->back()->with('success', 'store has been updated');
        }
        return redirect()->back()->with('error', 'store update failed');
    }
}
