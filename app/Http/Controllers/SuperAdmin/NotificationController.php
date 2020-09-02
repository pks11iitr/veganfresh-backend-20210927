<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
            
    public function create(Request $request){
            return view('admin.notification.add');
               }

   public function store(Request $request){
               $request->validate([
                  			'title'=>'required',
                  			'description'=>'required'
                               ]);

          if($notification=Notification::create([
                      'title'=>$request->title,
                      'description'=>$request->description,
                      'type'=>'all',
                      'user_id'=>'0',
                       
                      ]))
            {
				
             return redirect()->back()->with('success', 'Notification Send Successfully');
            }
             return redirect()->back()->with('error', 'Notification failed');
          }

  }
