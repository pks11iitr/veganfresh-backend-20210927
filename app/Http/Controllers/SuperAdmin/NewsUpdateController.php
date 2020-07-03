<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\NewsUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class NewsUpdateController extends Controller
{
     public function index(Request $request){
            $newsupdates=NewsUpdate::orderBy('id','DESC')->paginate(10);
            return view('admin.news.view',['newsupdates'=>$newsupdates]);
              }
                     

    public function create(Request $request){
            return view('admin.news.add');
               }

   public function store(Request $request){
               $request->validate([
                  			'isactive'=>'required',
                  			'description'=>'required',
                  			'image'=>'required|image'
                               ]);

          if($newsupdate=NewsUpdate::create([
                      'isactive'=>$request->isactive,
                      'description'=>$request->description,
                      'image'=>'a']))
            {
				$newsupdate->saveImage($request->image, 'newsupdate');
             return redirect()->route('news.list')->with('success', 'News has been created');
            }
             return redirect()->back()->with('error', 'News create failed');
          }

    public function edit(Request $request,$id){
             $newsupdate = NewsUpdate::findOrFail($id);
             return view('admin.news.edit',['newsupdate'=>$newsupdate]);
             }

    public function update(Request $request,$id){
             $request->validate([
                            'isactive'=>'required',
                  			'description'=>'required'
                               ]);
             $newsupdate = NewsUpdate::findOrFail($id);
          if($request->image){
			 $newsupdate->update([
                      'isactive'=>$request->isactive,
                      'description'=>$request->description,
                      'image'=>'a']);
             $newsupdate->saveImage($request->image, 'newsupdate');
        }else{
             $newsupdate->update([
                      'isactive'=>$request->isactive,
                      'description'=>$request->description
                           ]);
             }
          if($newsupdate)
             {
           return redirect()->route('news.list')->with('success', 'News has been updated');
              }
           return redirect()->back()->with('error', 'News update failed');

      }


     public function delete(Request $request, $id){
           NewsUpdate::where('id', $id)->delete();
           return redirect()->back()->with('success', 'News has been deleted');
        }
  }
