<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpecialProductController extends Controller
{
    public function hotdeals(Request $request){
        $user=auth()->guard('customerapi')->user();

        $banner=Banner::active()->select('id','image')->get();
        $category=Category::active()->select('id','name','image')->get();
        if(!empty($request->category_id)){

            $products=Product::active()->where('is_hotdeal',true)->whereHas('category', function($category) use($request){
                $category->where('categories.id', $request->category_id);

            });
        }else {

            $products = Product::active()->where('is_hotdeal', true);
        }

        $cart=Cart::getUserCart($user);
        $specialproducts=$products->with('sizeprice')->paginate(20);

        foreach($specialproducts as $product){
            foreach($product->sizeprice as $size)
                $size->quantity=$cart[$size->id]??0;
        }

        return [
            'status'=>'success',
            'banner'=>$banner,
            'category'=>$category,
            'data'=>$specialproducts
        ];
    }
//  new arrival
    public function newarrival(Request $request){
        $user=auth()->guard('customerapi')->user();

        $banner=Banner::active()->select('id','image')->get();
        $category=Category::active()->select('id','name','image')->get();
        if(!empty($request->category_id)){

            $products=Product::active()->where('is_newarrival',true)->whereHas('category', function($category) use($request){
                $category->where('categories.id', $request->category_id);

            });
        }else {

            $products = Product::active()->where('is_newarrival', true);
        }

        $cart=Cart::getUserCart($user);
        $specialproducts=$products->with('sizeprice')->paginate(20);

        foreach($specialproducts as $product){
            foreach($product->sizeprice as $size)
                $size->quantity=$cart[$size->id]??0;
        }

        return [
            'status'=>'success',
            'banner'=>$banner,
            'category'=>$category,
            'data'=>$specialproducts
        ];
    }
//discounted Product
    public function discountedproduct(Request $request){
        $user=auth()->guard('customerapi')->user();

        $banner=Banner::active()->select('id','image')->get();
        $category=Category::active()->select('id','name','image')->get();
        if(!empty($request->category_id)){

            $products=Product::active()->where('is_discounted',true)->whereHas('category', function($category) use($request){
                $category->where('categories.id', $request->category_id);

            });
        }else {

            $products = Product::active()->where('is_discounted', true);
        }

        $cart=Cart::getUserCart($user);
        $specialproducts=$products->with('sizeprice')->paginate(20);

        foreach($specialproducts as $product){
            foreach($product->sizeprice as $size)
                $size->quantity=$cart[$size->id]??0;
        }

        return [
            'status'=>'success',
            'banner'=>$banner,
            'category'=>$category,
            'data'=>$specialproducts
        ];
    }


}
