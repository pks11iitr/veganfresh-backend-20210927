<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\TimeSlot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TimeSlotController extends Controller
{
    public function index(Request $request){
        $timeslots =TimeSlot::get();
        return view('admin.timeslot.view',['timeslots'=>$timeslots]);
    }

    public function create(Request $request){
        return view('admin.timeslot.add');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'from_time'=>'required',
            'to_time'=>'required',
            'slot_capacity'=>'required',
            'isactive'=>'required'
        ]);

        if($timeslot=TimeSlot::create([
            'name'=>$request->name,
            'from_time'=>$request->from_time,
            'to_time'=>$request->to_time,
            'slot_capacity'=>$request->slot_capacity,
            'isactive'=>$request->isactive,
        ]))

        {
            return redirect()->route('timeslot.list')->with('success', 'TimeSlot has been created');
        }
        return redirect()->back()->with('error', 'TimeSlot create failed');
    }

    public function edit(Request $request,$id){
        $timeslot =TimeSlot::findOrFail($id);
        return view('admin.timeslot.edit',['timeslot'=>$timeslot]);
    }

    public function update(Request $request,$id){
        $request->validate([
            'name'=>'required',
            'from_time'=>'required',
            'to_time'=>'required',
            'slot_capacity'=>'required',
            'isactive'=>'required'
        ]);
        $timeslot =TimeSlot::findOrFail($id);

        if($timeslot->update([
            'name'=>$request->name,
            'from_time'=>$request->from_time,
            'to_time'=>$request->to_time,
            'slot_capacity'=>$request->slot_capacity,
            'isactive'=>$request->isactive,
        ]))

        {
            return redirect()->route('timeslot.list')->with('success', 'TimeSlot has been updated');
        }
        return redirect()->back()->with('error', 'TimeSlot update failed');
    }

}
