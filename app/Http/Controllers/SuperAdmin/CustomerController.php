<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class CustomerController extends Controller
{
     public function index(Request $request){
            $customers=Customer::paginate(10);
            return view('admin.customer.view',['customers'=>$customers]);
              }

    public function edit(Request $request,$id){
             $customers = Customer::findOrFail($id);
             return view('admin.customer.edit',['customers'=>$customers]);
             }

    public function update(Request $request,$id){
             $request->validate([
                             'isactive'=>'required',
                  			'name'=>'required',
                  			'description'=>'required',
                  			'address'=>'required',
                  			'city'=>'required',
                  			'state'=>'required',
                  			'contact'=>'required',
                  			'lat'=>'required',
                  			'lang'=>'required'
                  			]);
                      
             $customers = Customer::findOrFail($id);
          if($request->image){                  
			 $customers->update([
                      'isactive'=>$request->isactive,
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'address'=>$request->address,
                      'city'=>$request->city,
                      'state'=>$request->state,
                      'contact'=>$request->contact,
                      'lat'=>$request->lat,
                      'lang'=>$request->lang,
                      'image'=>'a']);
             $customers->saveImage($request->image, 'clinics');
        }else{
             $customers->update([
                      'isactive'=>$request->isactive,
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'address'=>$request->address,
                      'city'=>$request->city,
                      'state'=>$request->state,
                      'contact'=>$request->contact,
                      'lat'=>$request->lat,
                      'lang'=>$request->lang ]);
             }
          if($customers)
             {
           return redirect()->route('customer.list')->with('success', 'Customer has been updated');
              }
           return redirect()->back()->with('error', 'Customer update failed');

      }

  }
