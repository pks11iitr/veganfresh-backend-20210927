<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\NewsUpdate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    public function index(Request $request){
        $updates=NewsUpdate::active()->orderBy('id', 'desc')->get();

        return [
            'status'=>'success',
            'data'=>compact('updates')
        ];
    }


    public function details(Request $request, $id){
        $details=NewsUpdate::active()->find($id);

        if(!$details)
            return [
                'status'=>'failed',
                'message'=>'This new is no longer exists'
            ];

        return [
            'status'=>'success',
            'data'=>compact('details')
        ];
    }
}
