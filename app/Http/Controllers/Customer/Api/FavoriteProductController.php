<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Cart;
use App\Models\FavoriteProduct;
use App\Models\Product;
use App\Models\SaveLaterProduct;
use App\Models\Size;
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
        $favoriteproduct=FavoriteProduct::where('user_id',$user->id)
                                          ->where('product_id',$request->product_id)
                                          ->get();
        if($favoriteproduct->count()<=0) {
            $favoriteproduct = FavoriteProduct::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
            ]);
        }
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
    public function delete_favorite_product(Request $request){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $favoriteproduct=FavoriteProduct::where('user_id',$user->id)
            ->where('product_id',$request->product_id)
            ->get();
        if($favoriteproduct->count()>0) {
            $favoriteproduct[0]->delete();
        }
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
        //$favoriteproducts=Product::active()->with('sizeprice')
            //->join('favorite_products', 'products.id', '=', 'favorite_products.product_id')
            //->where('favorite_products.user_id', $user->id)
            //->get();

        $favoriteproducts=$user->favouriteProducts()->with(['sizeprice'=>function($size){
            $size->where('product_prices.isactive', true);
        }])->where('products.isactive', true)->get();

        $cart=Cart::getUserCart($user);
        $cart_total=$cart['total'];
        $cart=$cart['cart'];

        //return compact('favoriteproducts');
        $i=0;
        foreach($favoriteproducts as $c) {
            foreach($c->sizeprice as $size){
                $size->quantity=$cart[$size->id]??0;
                $size->in_stocks=Size::getStockStatus($size, $c);
            }


        }

        return [
            'favoriteproduct'=>$favoriteproducts,
            'cart_total'=>$cart_total
        ];

    }

}
