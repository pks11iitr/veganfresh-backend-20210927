<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function home(Request $request){

    }

    public function details(Request $request, $id){
        $product=Product::active()->with(['gallery','commentscount', 'avgreviews'])->find($id);

        if(!$product)
            return [
                'status'=>'failed',
                'message'=>'No such product found',
                'data'=>[]
            ];

        return [
            'status'=>'success',
            'data'=>[
                'product'=>$product
            ]
        ];

    }
}
