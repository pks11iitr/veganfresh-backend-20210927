<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Area;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;

class AreaController extends Controller
{
    public function index(Request $request){
        $arealists =Area::get();
        return view('admin.area.view',['arealists'=>$arealists]);
    }

    public function create(Request $request){
        return view('admin.area.add');
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'isactive'=>'required'
        ]);

        if($area=Area::create([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
        ]))

        {
            return redirect()->route('area.list')->with('success', 'Area has been created');
        }
        return redirect()->back()->with('error', 'Area create failed');
    }

    public function edit(Request $request,$id){
        $arealist=Area::findOrFail($id);
        return view('admin.area.edit',['arealist'=>$arealist]);
    }

    public function update(Request $request,$id){
        $request->validate([
            'name'=>'required',
            'isactive'=>'required'
        ]);
        $arealist=Area::findOrFail($id);
        if($arealist->update([
            'name'=>$request->name,
            'isactive'=>$request->isactive,
        ]))

        {
            return redirect()->route('area.list')->with('success', 'Area has been updated');
        }
        return redirect()->back()->with('error', 'Area update failed');
    }

    public function import(Request $request){

        Excel::load($request->file('select_file'), function($reader) {
            $results = $reader->all();
            foreach ($results as $row) {
                $strtoupper =$row->name;
                $arealist=Area::where('name',$strtoupper)->get();
                if($arealist->count()<=0) {
                    $findermap = new Area();
                    $findermap->name = $row->name;
                    $findermap->save();
                }
            }
        });
        return redirect()->route('area.list')->with('success', 'Your Data imported successfully.');

        return redirect()->back()->with('error', 'Your Data import failed');
    }
}
