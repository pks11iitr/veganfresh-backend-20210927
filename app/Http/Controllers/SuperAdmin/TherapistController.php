<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Therapist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class TherapistController extends Controller
{
     public function index(Request $request){
            $therapist=Therapist::paginate(10);;
            return view('admin.therapy.view',['therapist'=>$therapist]);
              }

    public function create(Request $request){
            return view('admin.therapy.add');
               }

   public function store(Request $request){
               $request->validate([
                  			'isactive'=>'required',
                  			'name'=>'required',
                  			'description'=>'required',
                  			'price1'=>'required',
                  			'price2'=>'required',
                  			'price3'=>'required',
                  			'price4'=>'required',
                  			'image'=>'required|image'
                               ]);
                               
             //// $file=$request->image->path();
             // $name=str_replace(' ', '_', $request->image->getClientOriginalName());
             // $path='therapies/'.$name;
             // saveImage($path, 'banners');
            //  Storage::put($path, file_get_contents($file));

          if($therapy=Therapist::create([
                      'isactive'=>$request->isactive,
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'grade1_price'=>$request->price1,
                      'grade2_price'=>$request->price2,
                      'grade3_price'=>$request->price3,
                      'grade4_price'=>$request->price4,
                      'image'=>'a']))
            {
				$therapy->saveImage($request->image, 'therapies');
             return redirect()->route('therapy.list')->with('success', 'Therapy has been created');
            }
             return redirect()->back()->with('error', 'Therapy create failed');
          }
          
    public function edit(Request $request,$id){
             $therapy = Therapist::findOrFail($id);
             return view('admin.therapy.edit',['therapy'=>$therapy]);
             }

    public function update(Request $request,$id){
             $request->validate([
                            'isactive'=>'required',
                  			'name'=>'required',
                  			'description'=>'required',
                  			'price1'=>'required',
                  			'price2'=>'required',
                  			'price3'=>'required',
                  			'price4'=>'required'
                               ]);
             $therapy = Therapist::findOrFail($id);
          if($request->image){                  
			 $therapy->update([
                      'isactive'=>$request->isactive,
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'grade1_price'=>$request->price1,
                      'grade2_price'=>$request->price2,
                      'grade3_price'=>$request->price3,
                      'grade4_price'=>$request->price4,
                      'image'=>'a']);
             $therapy->saveImage($request->image, 'therapies');
        }else{
             $therapy->update([
                      'isactive'=>$request->isactive,
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'grade1_price'=>$request->price1,
                      'grade2_price'=>$request->price2,
                      'grade3_price'=>$request->price3,
                      'grade4_price'=>$request->price4 ]);
             }
          if($therapy)
             {
           return redirect()->route('therapy.list')->with('success', 'Therapy has been updated');
              }
           return redirect()->back()->with('error', 'Therapy update failed');

      }

  }
