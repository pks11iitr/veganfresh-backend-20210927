<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Product;
use App\Models\SaveLaterProduct;
use App\Models\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cart;

class CartController extends Controller
{
    public function store(Request $request){

        $user=auth()->guard('customerapi')->user();

        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $request->validate([
            'quantity'=>'required|integer|min:0',
            'product_id'=>'required|integer|min:1',
            'size_id'=>'required|integer|min:0'
        ]);

//        $size=Size::active()
//            ->where('product_id',$request->product_id)
//            ->find($request->size_id);

        $product=Product::active()
            ->with(['sizeprice'=>function($size) use($request){
                $size->where('product_prices.isactive', true)
                    ->where('product_prices.id', $request->size_id);
            }])->whereHas('sizeprice', function($size) use($request){
                $size->where('product_prices.isactive', true)
                    ->where('product_prices.id', $request->size_id);
            })->find($request->product_id);

        if(!$product){
            return [
                'status'=>'failed',
                'message'=>'Product is no longer available'
            ];
        }

        if($request->quantity>0 && $request->quantity < $product->sizeprice[0]->min_qty)
            return [
                'status'=>'failed',
                'message'=>'You can add minimum '.$product->sizeprice[0]->min_qty.' quantity',
            ];

        if($request->quantity > $product->sizeprice[0]->max_qty)
            return [
                'status'=>'failed',
                'message'=>'You can add maximum '.$product->sizeprice[0]->max_qty.' quantity',
            ];


        $cart = Cart::with(['product'=>function($product){
            $product->where('products.isactive',true);
        }, 'sizeprice'=>function($size){
            $size->where('product_prices.isactive', true);
        }])
            ->where('product_id',$request->product_id)
            ->where('size_id',$request->size_id)
            ->where('user_id', $user->id)
            ->first();


        if(!$cart){
            if($request->quantity>0){

//                $product=Product::active()
//                    ->with(['sizeprice'=>function($size) use($request){
//
//                        $size->where('product_prices.isactive', true)
//                            ->where('product_prices.id', $request->size_id);
//
//                    }])
//                    ->find($request->product_id);

                if($product){
                    if($product->stock_type=='quantity'){
                        if($product->stock < $request->quantity*$product->sizeprice[0]->consumed_units){
                            return [
                                'status'=>'failed',
                                'message'=>'No more quantity available',
                            ];
                        }
                    }else{
                        if($product->sizeprice[0]->stock < $request->quantity*$product->sizeprice[0]->consumed_units){
                            return [
                                'status'=>'failed',
                                'message'=>'No more quantity available',
                            ];
                        }
                    }


                    $savelaterproduct=SaveLaterProduct::where('product_id',$request->product_id)
                        ->where('size_id',$request->size_id)
                        ->where('user_id',$user->id)->first();
                    if($savelaterproduct) {
                        $savelaterproduct->delete();
                    }
                    Cart::create([
                        'product_id'=>$request->product_id,
                        'quantity'=>$request->quantity,
                        'user_id'=>$user->id,
                        'size_id'=>$request->size_id,
                    ]);
                }else{

                    return [
                        'status'=>'failed',
                        'message'=>'No more quantity available'
                    ];

                }


            }
        }else{
            if($request->quantity>0){

                if($cart->product && $cart->sizeprice){
                    if($cart->product->stock_type=='quantity'){
                        if($cart->product->stock < $request->quantity*$cart->sizeprice->consumed_units){
                            return [
                                'status'=>'failed',
                                'message'=>'No more quantity available',
                            ];
                        }
                    }else{
                        if($cart->sizeprice->stock < $request->quantity*$cart->sizeprice->consumed_units){
                            return [
                                'status'=>'failed',
                                'message'=>'No more quantity available',
                            ];
                        }
                    }
                    $cart->quantity=$request->quantity;
                    $cart->size_id=$request->size_id;
                    $cart->save();
                }else{
                    $cart->delete();
                    return [
                        'status'=>'failed',
                        'message'=>'Product is not available'
                    ];

                }
            }else{
                $cart->delete();
            }
        }
        $products=Product::active()
            ->with(['sizeprice'=>function($size){
                $size->where('product_prices.isactive', true);
            }])
            ->where('id',$request->product_id)
            ->get();

        $cart=Cart::getUserCart($user);
        $cart_total=$cart['total'];
        $price_total=$cart['price_total'];
        $cart=$cart['cart'];
        foreach($products as $product){
            foreach($product->sizeprice as $size)
                $size->quantity=$cart[$size->id]??0;

        }
        return [
            'status'=>'success',
            'message'=>'success',
            'product'=>$product,
            'cart_total'=>$cart_total,
            'price_total'=>round($price_total)
        ];

    }

    public function getCartDetails(Request $request){
        $user=auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $cartitems=Cart::with(['product'=>function($product){
            $product->where('products.isactive',true);
        }, 'sizeprice'=>function($size){
            $size->where('product_prices.isactive', true);
        }])
            ->where('user_id', $user->id)
            ->get();

        $total=0;
        $quantity=0;
        $price_total=0;
        $cartitem=array();
        $savelater=array();

        foreach($cartitems as $c){
            if(!$c->sizeprice->isactive || !$c->product->isactive){
                $c->delete();
                continue;
            }
            if($c->product->stock_type=='quantity'){
                if($c->product->stock < $c->quantity*$c->sizeprice->consumed_units){
                    $c->delete();
                    continue;
                }
            }else{
                if($c->sizeprice->stock < $c->quantity*$c->sizeprice->consumed_units){
                    $c->delete();
                    continue;
                }
            }
            if($c->quantity < $c->sizeprice->min_qty || $c->quantity > $c->sizeprice->max_qty){
                $c->delete();
                continue;

            }


            $total=$total+($c->sizeprice->price??0)*$c->quantity;
            $quantity=$quantity+$c->quantity;
            $price_total=$price_total+($c->sizeprice->price??0)*$c->quantity;
            $cartitem[]=array(
                'id'=>$c->id,
                'name'=>$c->product->name??'',
                'company'=>$c->product->company??'',
                'image'=>$c->sizeprice->image,
                'product_id'=>$c->product->id??'',
                'size_id'=>$c->sizeprice->id,
                'quantity'=>$c->quantity,
                'min_qty'=>$c->sizeprice->min_qty,
                'max_qty'=>$c->sizeprice->max_qty,
                'discount'=>$c->sizeprice->discount,
                'size'=>$c->sizeprice->size,
                'price'=>$c->sizeprice->price,
                'cut_price'=>$c->sizeprice->cut_price,
                'stock'=>$c->sizeprice->stock,
            );

        }
        $savelaters=SaveLaterProduct::with(['product'=>function($products){
            $products->where('isactive', true);
        }])->where('user_id', $user->id)->get();
        foreach($savelaters as $sl){

            $savelater[]=array(
                'id'=>$sl->id,
                'name'=>$sl->product->name??'',
                'company'=>$sl->product->company??'',
                'ratings'=>$sl->product->ratings??'',
                'image'=>$sl->sizeprice->image,
                'product_id'=>$sl->product->id??'',
                'size_id'=>$sl->sizeprice->id,
                'min_qty'=>$sl->sizeprice->min_qty,
                'max_qty'=>$sl->sizeprice->max_qty,
                'discount'=>$sl->sizeprice->discount,
                'size'=>$sl->sizeprice->size,
                'price'=>$sl->sizeprice->price,
                'cut_price'=>$sl->sizeprice->cut_price,
                'stock'=>$sl->sizeprice->stock,
            );
        }
        return [
            'cartitem'=>$cartitem,
            'total'=>$total,
            'price_total'=>$price_total,
            'quantity'=>$quantity,
            'savelater'=>$savelater,
        ];

    }

}
