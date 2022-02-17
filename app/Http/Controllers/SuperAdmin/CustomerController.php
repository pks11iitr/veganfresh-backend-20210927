<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Exports\InventoryQuantityExport;
use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Membership;
use App\Models\Notification;
use App\Models\CustomerAddress;
use App\Services\Notification\FCMNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;
use Excel;
class CustomerController extends Controller
{
     public function index(Request $request){

            $customers=Customer::with('membership')->where(function($customers) use($request){
                $customers->where('name','LIKE','%'.$request->search.'%')
                    ->orWhere('mobile','LIKE','%'.$request->search.'%')
                    ->orWhere('email','LIKE','%'.$request->search.'%');
            });

            if($request->fromdate)
                $customers=$customers->where('created_at', '>=', $request->fromdate.'00:00:00');

            if($request->todate)
                $customers=$customers->where('created_at', '<=', $request->todate.'23:59:50');

            if(isset($request->status))
                $customers=$customers->where('status', $request->status);

            if($request->ordertype)
                $customers=$customers->orderBy('created_at', $request->ordertype);

            if($request->membership)
                $customers=$customers->where('active_membership', '>',0)->where('membership_expiry', '>', date('Y-m-d'));

            if($request->type=='export'){
                if(!auth()->user()->hasRole('admin'))
                    abort(403);
                $customers=$customers->get();
                return Excel::download(new UserExport($customers), 'customers.xlsx');
            }else{
                $customers=$customers->orderBy('id', 'desc')->paginate(10);
                return view('admin.customer.view',['customers'=>$customers]);
            }

     }

    public function edit(Request $request,$id){
            // return $caddress=CustomerAddress::where('user_id',$id);
            return $customers = Customer::with('membership')->findOrFail($id);
             $memberships=Membership::get();
                 return view('admin.customer.edit',['customers'=>$customers, 'memberships'=>$memberships, 'caddress'=>$caddress]);
             }

    public function update(Request $request,$id){
             $request->validate([
                             'status'=>'required',
                  			'name'=>'required',
                  			//'dob'=>'required',
                  			//'address'=>'required',
                  			//'city'=>'required',
                  			//'state'=>'required',
                  			//'image'=>'image'
                  			]);

             $customers = Customer::findOrFail($id);
          if($request->image){
			 $customers->update([
                      'status'=>$request->status,
                      'name'=>$request->name,
                      'dob'=>$request->dob,
                      'address'=>$request->address,
                      'city'=>$request->city,
                      'state'=>$request->state,
                 'active_membership'=>$request->active_membership,
                      'membership_expiry'=>$request->membership_expiry,
                      'image'=>'a']);
             $customers->saveImage($request->image, 'customers');
        }else{
             $customers->update([
                      'status'=>$request->status,
                      'name'=>$request->name,
                      'dob'=>$request->dob,
                      'address'=>$request->address,
                      'city'=>$request->city,
                      'state'=>$request->state,
                        'active_membership'=>$request->active_membership,
                 'membership_expiry'=>$request->membership_expiry,
                         ]);
             }
          if($customers)
             {
           return redirect()->route('customer.list')->with('success', 'Customer has been updated');
              }
           return redirect()->back()->with('error', 'Customer update failed');

      }

      function send_message(Request $request)
        {

            $customer=Customer::findOrFail($request->custid);
        $cusid=$request->cusid;
        $title=$request->title;
        $des=$request->des;
        $Notification=Notification::create([
                      'title'=>$title,
                      'description'=>$des,
                      'user_id'=>$cusid,
                      'type'=>'individual'
                      ]);
         if($Notification){
             FCMNotification::sendNotification($customer->notification_token, $title, $des);

           return response()->json(['users' => $Notification], 200);
       }else{
              return response()->json(['msg' => 'No result found!'], 404);
       }

        }

  }
