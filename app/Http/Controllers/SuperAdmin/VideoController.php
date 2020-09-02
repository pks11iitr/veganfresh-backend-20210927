<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class VideoController extends Controller
{
     public function index(Request $request){
            $videos=Video::orderBy('id','DESC')->paginate(10);
            return view('admin.video.view',['videos'=>$videos]);
              }


    public function create(Request $request){
            return view('admin.video.add');
               }

   public function store(Request $request){
               $request->validate([
                  			'isactive'=>'required',
                  			'url'=>'required',
                  			'image'=>'required|image'
                               ]);

          if($video=Video::create([
                      'isactive'=>$request->isactive,
                      'url'=>$request->url,
                      'image'=>'a']))
            {
				$video->saveImage($request->image, 'youtube');
             return redirect()->route('video.list')->with('success', 'Video has been created');
            }
             return redirect()->back()->with('error', 'Video create failed');
          }

    public function edit(Request $request,$id){
             $video = Video::findOrFail($id);
             return view('admin.video.edit',['video'=>$video]);
             }

    public function update(Request $request,$id){
             $request->validate([
                            'isactive'=>'required',
                  			'url'=>'required',
                  			'image'=>'image'
                               ]);
             $video = Video::findOrFail($id);
          if($request->image){
			 $video->update([
                      'isactive'=>$request->isactive,
                      'url'=>$request->url,
                      'image'=>'a']);
             $video->saveImage($request->image, 'youtube');
        }else{
             $video->update([
                      'isactive'=>$request->isactive,
                      'url'=>$request->url
                                  ]);
             }
          if($video)
             {
           return redirect()->route('video.list')->with('success', 'Video has been updated');
              }
           return redirect()->back()->with('error', 'Video update failed');

      }


     public function delete(Request $request, $id){
           Video::where('id', $id)->delete();
           return redirect()->back()->with('success', 'Video has been deleted');
        }
  }
