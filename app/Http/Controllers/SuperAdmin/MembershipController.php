<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Membership;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MembershipController extends Controller
{
    public function index(Request $request){
        $memberships = Membership::paginate(10);
        return view('admin.membership.view',['memberships'=>$memberships]);
    }

    public function create(Request $request){
        return view('admin.membership.add');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'price'=>'required',
            'validity'=>'required',
            'cashback'=>'required',
            'isactive'=>'required'
        ]);

        if($area=Membership::create([
            'name'=>$request->name,
            'price'=>$request->price,
            'validity'=>$request->validity,
            'cashback'=>$request->cashback,
            'isactive'=>$request->isactive,
        ]))

        {
            return redirect()->route('membership.list')->with('success', 'Membership has been created');
        }
        return redirect()->back()->with('error', 'Membership create failed');
    }

    public function edit(Request $request,$id){
        $membership = Membership::findOrFail($id);
        return view('admin.membership.edit',['membership'=>$membership]);
    }

    public function update(Request $request,$id){
        $request->validate([
            'name'=>'required',
            'price'=>'required',
            'validity'=>'required',
            'cashback'=>'required',
            'isactive'=>'required'
        ]);
        $membership = Membership::findOrFail($id);

        if($membership->update([
            'name'=>$request->name,
            'price'=>$request->price,
            'validity'=>$request->validity,
            'cashback'=>$request->cashback,
            'isactive'=>$request->isactive,
        ]))

        {
            return redirect()->route('membership.list')->with('success', 'Membership has been updated');
        }
        return redirect()->back()->with('error', 'Membership update failed');
    }
}
