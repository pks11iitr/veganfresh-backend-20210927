<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\FavoriteProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FavoriteProductController extends Controller
{
    public function add_favorite_product(Request $request){
      $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
      $favoriteproduct=FavoriteProduct::create([
                 'user_id'=>$user->id,
                  'product_id'=>$request->product_id,
                ]);
      if($favoriteproduct){
        return [
            'status'=>'success',
        ];
      }else{
        return [
            'status'=>'error',
        ];
      }
    }





}
