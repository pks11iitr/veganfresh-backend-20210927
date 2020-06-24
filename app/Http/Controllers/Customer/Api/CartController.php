<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index(Request $request){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $items=Cart::with(['product'=>function($products){
            $products->where('isactive', true);
        }])->where('user_id', $user->id)->get();

        $cartitems=[];
        foreach($items as $item){
            $cartitems[]=[
                'name'=>$item->product->name,
                'quantity'=>$item->quantity,
                'price'=>$item->product->price??0,
                'image'=>$item->product->image??null,
                'company'=>$item->product->company??null,
                'id'=>$item->product->id??0
            ];
        }

        return [
            'status'=>'success',
            'data'=>[
                'items'=>$cartitems
            ]
        ];

    }


    public function updateCartItems(Request $request){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $request->validate([
            'product_id'=>'required|integer|min:1',
            'quantity'=>'required|integer|min:0'
        ]);

        $product=Product::active()->find($request->product_id);
        if(!$product)
            return [
                'status'=>'failed',
                'message'=>'Invalid Operation Performed'
            ];

        $cart=Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();
        if($cart){
            if($request->quantity>0){
                $cart->quantity=$request->quantity;
                $cart->save();
            }else{
                $cart->delete();
            }
        }else{
            if($request->quantity>0){
                Cart::create([
                    'product_id'=>$request->product_id,
                    'user_id'=>$user->id,
                    'quantity'=>$request->quantity
                ]);
            }
        }


        return [
            'status'=>'success',
            'message'=>'Cart has been updated'
        ];
    }
}
