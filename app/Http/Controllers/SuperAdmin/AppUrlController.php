<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppUrlController extends Controller
{
    public function aboutus(Request $request){
        return view('admin.appurl.about');
    }

    public function privacypolicy(Request $request){
        return view('admin.appurl.privacy_policy');
    }

    public function termscondition(Request $request){
        return view('admin.appurl.terms_condition');
    }

    public function cancelationpolicy(Request $request){
        return view('admin.appurl.cancellation_policy');
    }

    public function refundpolicy(Request $request){
        return view('admin.appurl.refund_policy');
    }
}
