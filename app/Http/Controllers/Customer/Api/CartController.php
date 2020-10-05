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

        $size=Size::where('product_id',$request->product_id)
            ->find($request->size_id);

        if(!$size){
            return [
                'status'=>'failed',
                'message'=>'Invalid Size'
            ];
        }

        $cart = Cart::with([
                        'product'=>function($products){
                            $products->where('isactive', true);
                        },
                        'sizeprice'=>function($size){
                            $size->where('isactive', true);
                        }])
            ->where('product_id',$request->product_id)
            ->where('size_id',$request->size_id)
            ->where('user_id', $user->id)
            ->first();


        if(!$cart){
            if($request->quantity>0){

                $product=Product::active()
                    ->with(['sizeprice'=>function($size) use($request){

                        $size->where('product_prices.isactive', true)
                            ->where('product_prices.id', $request->size_id);

                    }])
                    ->find($request->product_id);

                if($product && $product->sizeprice[0]){
                        if($product->stock_type=='quantity'){
                            if($product->stock < $request->quantity){
                                return [
                                    'status'=>'failed',
                                    'message'=>'Product is out of stock',
                                ];
                            }
                        }else{
                            if($product->sizeprice[0]->stock < $request->quantity){
                                return [
                                    'status'=>'failed',
                                    'message'=>'Product is out of stock',
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
                        'message'=>'Product is not available'
                    ];

                }


            }
        }else{
            if($request->quantity>0){
                if($cart->product && $cart->size){
                    if($cart->product->stock_type=='quantity'){
                        if($cart->product->stock < $request->quantity){
                            return [
                                'status'=>'failed',
                                'message'=>'Product is out of stock',
                            ];
                        }
                    }else{
                        if($cart->sizeprice->stock < $request->quantity){
                            return [
                                'status'=>'failed',
                                'message'=>'Product is out of stock',
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
            ->with(['sizeprice'])
            ->where('id',$request->product_id)
            ->get();

        $cart=Cart::getUserCart($user);
        $cart_total=$cart['total'];
        $cart=$cart['cart'];
        foreach($products as $product){
            foreach($product->sizeprice as $size)
                $size->quantity=$cart[$size->id]??0;

        }
        return [
            'status'=>'success',
            'message'=>'success',
            'product'=>$product,
            'cart_total'=>$cart_total
        ];

    }

public function getCartDetails(Request $request){
    $user=auth()->guard('customerapi')->user();
    if(!$user)
        return [
            'status'=>'failed',
            'message'=>'Please login to continue'
        ];
    $cartitems=Cart::with(['product'=>function($products){
        $products->where('isactive', true);
    }, 'sizeprice'])
        ->where('user_id', $user->id)
        ->get();
        $total=0;
        $quantity=0;
        $price_total=0;
        $cartitem=array();
        $savelater=array();

        foreach($cartitems as $c){

            if($c->product->stock_type=='quantity'){
                if($c->product->stock < $c->quantity){
                    $c->delete();
                    continue;
                }
            }else{
                if($c->sizeprice->stock < $c->quantity){
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
