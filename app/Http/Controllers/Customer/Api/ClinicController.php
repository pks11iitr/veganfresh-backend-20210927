<?php

namespace App\Http\Controllers\Customer\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClinicController extends Controller
{
    public function index(Request $request){
        return [
            'status'=>'succecss',
            'data'=>[
                'clinics'=>[]
            ]
        ];
    }
}
