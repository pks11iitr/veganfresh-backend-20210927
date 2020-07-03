<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class ComplainController extends Controller
{
     public function index(Request $request){
            $complaints=Complaint::orderBy('id','DESC')->paginate(10);
            return view('admin.complain.view',['complaints'=>$complaints]);
              }
              
  
  }
