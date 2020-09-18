<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\SaveLaterProduct;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SavelaterProductController extends Controller
{
    public function savelater_product(Request $request){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue..'
            ];
        $cart=Cart::where('product_id',$request->product_id)
                    ->where('size_id',$request->size_id)
                    ->where('user_id',$user->id)->get();
        $savelaterproduct=SaveLaterProduct::where('product_id',$request->product_id)
            ->where('size_id',$request->size_id)
            ->where('user_id',$user->id)->get();
        if($cart->count()>0) {
            if($savelaterproduct->count()<=0) {
                $savelater = SaveLaterProduct::create([
                    'user_id' => $user->id,
                    'product_id' => $request->product_id,
                    'size_id' => $request->size_id,
                ]);
            }
            $cart[0]->delete();
        }else{
            if($savelaterproduct->count()<=0) {
                $savelater = SaveLaterProduct::create([
                    'user_id' => $user->id,
                    'product_id' => $request->product_id,
                    'size_id' => $request->size_id,
                ]);
            }
        }
            return [
                'status'=>'success',
            ];

    }





}
