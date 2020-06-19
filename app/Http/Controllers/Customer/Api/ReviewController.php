<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Clinic;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index(Request $request, $type, $id){

        switch($type){
            case 'product':$entity=Product::active()->with(['comments.customer'])->find($id);
            break;
            case 'clinic':$entity=Clinic::active()->with('comments')->find($id);
            break;
            case 'therapy':$entity=Product::active()->with('comments')->find($id);
            break;
            default: $entity=null;
        }

        if(!$entity || !$entity->comments)
            return [
                'status'=>'failed',
                'message'=>'No reviews found'
            ];

        return [
            'status'=>'success',
            'data'=>[
                'reviews'=>$entity->comments
            ]
        ];

    }
}
