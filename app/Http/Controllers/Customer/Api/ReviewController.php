<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index(Request $request,$id){

        $product=Product::active()
            ->findOrFail($id);
        $reviews=$product->reviews()->with(['customer'=>function($customer){
            $customer->select('id','name','image');
        }])->paginate(20);

        if(!$reviews)
            return [
                'status'=>'failed',
                'message'=>'No reviews found'
            ];

        return [
            'status'=>'success',
                'reviews'=>$reviews
        ];

    }
}
