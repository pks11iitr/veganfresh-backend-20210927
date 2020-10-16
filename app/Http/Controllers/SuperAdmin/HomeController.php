<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\SuperAdmin\BaseController;
use Illuminate\Support\Facades\DB;

class HomeController extends BaseController
{
       public function check_n_redirect(Request $request){

           if(auth()->user()->hasRole('admin'))
           {   //die('1222');
               return redirect()->route('home')->with('success', 'Login Successfull');}
           else if(auth()->user()->hasRole('store')){
               return redirect()->route('storeadmin.home')->with('success', 'Login Successfull');
           }else if(auth()->user()->hasRole('subadmin')){
               return redirect()->route('subadmin.home')->with('success', 'Login Successfull');
           }

       }
}
