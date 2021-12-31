<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Kodeine\Acl\Models\Eloquent\Permission;

class SubAdminController extends Controller
{
    public function index(Request $request){
        $admins = User::where('id', '>', 1)
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
            'status'=>'required',
            'permissions'=>'array'
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
            //$area->assignRole('subadmin');
            $roles=[];
            $roles[]='subadmin';
            if($request->permissions){
                foreach($request->permissions as $key=>$permission){
                    $roles[]=$key;
                }
            }
            $area->syncRoles('subadmin');
//            echo '<pre>';
//            print_r($roles);
//            die;
            return redirect()->route('subadmin.edit', ['id'=>$area->id])->with('success', 'store has been created');
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
            'status'=>'required',
            'permissions'=>'array'
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
            $roles=[];
            $roles[]='subadmin';
            if($request->permissions){
                foreach($request->permissions as $key=>$permission){
                    $roles[]=$key;
                }
            }
            $subadmin->syncRoles($roles);
        }else{
            $subadmin->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'status' => $request->status,
            ]);

            $roles=[];
            $roles[]='subadmin';
            if($request->permissions){
                foreach($request->permissions as $key=>$permission){
                    $roles[]=$key;
                }
            }
            //echo '<pre>';
            //var_dump($roles);die;
            $subadmin->syncRoles($roles);
        }

//        echo '<pre>';
//        print_r($request->all());
//        die;

        if($subadmin)
        {
            return redirect()->back()->with('success', 'store has been updated');
        }
        return redirect()->back()->with('error', 'store update failed');
    }
}
