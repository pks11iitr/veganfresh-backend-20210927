<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Clinic;
use App\Models\Traits\Active;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClinicController extends Controller
{

    use Active;

    public function index(Request $request){
        return [
            'status'=>'succecss',
            'data'=>[
                'clinics'=>Clinic::active()->with(['commentscount', 'avgreviews'])->get()
            ]
        ];
    }
}
