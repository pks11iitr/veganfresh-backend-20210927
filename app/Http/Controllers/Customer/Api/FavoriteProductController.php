<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Cart;
use App\Models\FavoriteProduct;
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


////////////////////////////////////////////////////////////////////////
///

    public function list_favorite_product(Request $request){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $favoriteproducts=FavoriteProduct::with(['product'=>function($products){
            $products->where('isactive', true);
        }])->where('user_id', $user->id)->with('sizeprice')->get();

       // var_dump($favoriteproducts);die;
        $favoriteproduct=array();
        foreach($favoriteproducts as $c){

            $favoriteproduct[]=array(
                'id'=>$c->id,
                'name'=>$c->product->name??'',
                'company'=>$c->product->company??'',
                'product_id'=>$c->product->id??'',
                'sizeprice'=>$c->product->sizeprice,
            );
        }

        return [
            'favoriteproduct'=>$favoriteproduct,

        ];

    }

}
