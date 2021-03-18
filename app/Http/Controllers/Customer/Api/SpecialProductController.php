<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpecialProductController extends Controller
{
    public function hotdeals(Request $request){
        $user=auth()->guard('customerapi')->user();

        $banner=Banner::active()->select('id','image')->get();
        $categoryobj=Category::active()->select('id','name','image')->get();
        $category=[];
        $category[]=['name'=>'All', 'id'=>0,'image'=>''];
        foreach ($categoryobj as $cat){
            $category[]=['name'=>$cat->name, 'id'=>$cat->id,'image'=>$cat->image];
        }

        if(!empty($request->category_id)){

            $products=Product::active()->where('is_hotdeal',true)->whereHas('category', function($category) use($request){
                $category->where('categories.id', $request->category_id)
                ->where('categories.isactive',true);

            });
        }else {

            $products = Product::active()->where('is_hotdeal', true);
        }

        $products=$products->whereDoesntHave('subcategory', function($category) {
            $category->where('sub_category.isactive', false);
        });


        $cart=Cart::getUserCart($user);
        $cart_total=$cart['total'];
        $cart=$cart['cart'];
        $specialproducts=$products->with(['sizeprice'=>function($size){
            $size->where('product_prices.isactive', true);
        }])->paginate(20);

        foreach($specialproducts as $product) {
            foreach ($product->sizeprice as $size) {
                $size->quantity = $cart[$size->id] ?? 0;
                $size->in_stocks = Size::getStockStatus($size, $product);
            }
        }

        return [
            'status'=>'success',
            'banner'=>$banner,
            'category'=>$category,
            'data'=>$specialproducts,
            'cart_total'=>$cart_total
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

        $products=$products->whereDoesntHave('subcategory', function($category) {
            $category->where('sub_category.isactive', false);
        });

        $cart=Cart::getUserCart($user);
        $cart_total=$cart['total'];
        $cart=$cart['cart'];
        $specialproducts=$products->with(['sizeprice'=>function($size){
            $size->where('product_prices.isactive', true);
        }])->paginate(20);

        foreach($specialproducts as $product){
            foreach($product->sizeprice as $size){
                $size->quantity=$cart[$size->id]??0;
            $size->in_stocks=Size::getStockStatus($size, $product);
        }}

        return [
            'status'=>'success',
            'banner'=>$banner,
            'category'=>$category,
            'data'=>$specialproducts,
            'cart_total'=>$cart_total
        ];
    }
//discounted Product
    public function discountedproduct(Request $request){
        $user=auth()->guard('customerapi')->user();

        $banner=Banner::active()->select('id','image')->get();
        $category=Category::active()->select('id','name','image')->get();
        if(!empty($request->category_id)){

            $products=Product::active()->where('is_discounted',true)->whereHas('category', function($category) use($request){
                $category->where('categories.id', $request->category_id)
                ->where('categories.isactive',true);

            });
        }else {

            $products = Product::active()->where('is_discounted', true);
        }

        $products=$products->whereDoesntHave('subcategory', function($category) {
            $category->where('sub_category.isactive', false);
        });

        $cart=Cart::getUserCart($user);
        $cart_total=$cart['total'];
        $cart=$cart['cart'];
        $specialproducts=$products->with(['sizeprice'=>function($size){
            $size->where('product_prices.isactive', true);
        }])->paginate(20);

        foreach($specialproducts as $product){
            foreach($product->sizeprice as $size){
                $size->quantity=$cart[$size->id]??0;
            $size->in_stocks=Size::getStockStatus($size, $product);
        }
        }

        return [
            'status'=>'success',
            'banner'=>$banner,
            'category'=>$category,
            'data'=>$specialproducts,
            'cart_total'=>$cart_total
        ];
    }


}
