<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Cart;
use App\Models\FavoriteProduct;
use App\Models\Product;
use App\Models\SaveLaterProduct;
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

    public function list_favorite_product(Request $request){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $favoriteproducts=Product::active()->with('sizeprice')
            ->join('favorite_products', 'products.id', '=', 'favorite_products.product_id')
            ->where('favorite_products.user_id', $user->id)
            ->get();
        $cart=Cart::getUserCart($user);
        foreach($favoriteproducts as $c) {
            foreach($c->sizeprice as $size)
                $size->quantity=$cart[$size->id]??0;
        }

        return [
            'favoriteproduct'=>$favoriteproducts,

        ];

    }

}
