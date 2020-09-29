<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
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

    public function postReview(Request $request, $id){

        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $request->validate([

            'product_id'=>'required|integer',
            'review'=>'required',
            'rating'=>'required|integer|in:1,2,3,4,5'

        ]);


        $order=Order::where('user_id', $user->id)
            ->whereHas('details', function($detail) use($request) {
                $detail->where('entity_id', $request->product_id);
            })->where('status', 'completed')
            ->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'This Request is Not Valid'
            ];

        $review=Review::create([

            "user_id"=>$user->id,
            'order_id'=>$order->id,
            'product_id'=>$request->product_id,
            'rating'=>$request->rating,
            'comment'=>$request->review

        ]);

        if($request->images){

            if(isset($request->images[0])){
                $review->saveImage($request->images[0], 'reviews');
            }

            if(isset($request->images[1])){
                $review->saveImage1($request->images[1], 'reviews');
            }

        }

        return [

            'status'=>'success',
            'message'=>'Review Has Been Submitted'
        ];

    }
}
