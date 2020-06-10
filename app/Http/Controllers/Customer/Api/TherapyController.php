<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Therapy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TherapyController extends Controller
{
    public function index(Request $request){
        $thrapies=Therapy::active()->get();
        return [
            'status'=>'succecss',
            'data'=>[

            ]
        ];
    }
}
