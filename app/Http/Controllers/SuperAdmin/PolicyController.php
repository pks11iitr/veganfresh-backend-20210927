<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class PolicyController extends Controller
{
    public function index(Request $request){

        return view('admin.contenturl.privacy_policy');
    }
    public function terms(Request $request){

        return view('admin.contenturl.termscondition');
    }
    public function about(Request $request){

        return view('admin.contenturl.aboutus');
    }



}
