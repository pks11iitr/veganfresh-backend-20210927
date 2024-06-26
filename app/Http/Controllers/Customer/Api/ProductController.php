<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Banner;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\TimeSlot;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function products(Request $request){
        $user=auth()->guard('customerapi')->user();

//        if(!$user)
//            return [
//                'status'=>'failed',
//                'message'=>'Please login to continue'
//            ];
        if(!empty($request->sub_cat_id)){

          $product=Product::active()->whereHas('subcategory', function($category) use($request){
              $category->where('sub_category.id', $request->sub_cat_id)
                  ->where('sub_category.isactive',true);
            });
        }else{
          $product=Product::active()->whereHas('category', function($category) use($request){
              $category->where('categories.id', $request->category_id)
                  ->where('categories.isactive',true);
            })->whereDoesntHave('subcategory', function($category) use($request){
              $category->where('sub_category.isactive',false);
          });
        }

        if($request->prices || $request->sizes){

            $product=$product->whereHas('sizeprice', function($size) use($request){

                if($request->prices){
                    $prices=explode('-', $request->prices??'');
                    $size->where('product_prices.price', '>=', intval($prices[0]??0))
                        ->where('product_prices.price', '<=', intval($prices[1]??0))
                        ->where('product_prices.isactive',true);
                }
                if($request->sizes){
                    $sizes=explode('#', $request->sizes);
                    $size->whereIn('product_prices.size', $sizes)
                        ->where('product_prices.isactive',true);
                }
            });
        }
        if($request->brand){
            $brands=explode('#', $request->brand);
            //foreach($brands as $brand){
                $product=$product->whereIn('company', $brands);
            //}
        }


        $cart=Cart::getUserCart($user);
        $cart_total=$cart['total'];
        $cart=$cart['cart'];

        $products=$product->with(['sizeprice'=>function($size){
            $size->where('product_prices.isactive', true);
        }])->paginate(20);

        foreach($products as $product){
            foreach($product->sizeprice as $size){
                $size->quantity=$cart[$size->id]??0;
                $size->in_stocks=Size::getStockStatus($size, $product);
            }
        }

        return [
            'status'=>'success',
            'data'=>$products,
            'cart_total'=>$cart_total
        ];
    }

    public function search_products(Request $request){

        $user=auth()->guard('customerapi')->user();

//        if(!$user)
//            return [
//                'status'=>'failed',
//                'message'=>'Please login to continue'
//            ];

        if(!empty($request->search)){
            //$banner=Banner::active()->select('id','image')->get();
            //$category=Category::active()->select('id','name','image')->get();
            $products = Product::active()->where('name', 'like', "%".$request->search."%");
        }else{
            $products = Product::active();
        }

//
//            $product=Product::active()
//            ->with(['category', 'sizeprice'])
//            ->where('name', 'like', "%".$request->search."%");

        $cart=Cart::getUserCart($user);
        $cart_total=$cart['total'];
        $cart=$cart['cart'];

        $products=$products->whereDoesntHave('subcategory', function($category) {
            $category->where('sub_category.isactive', false);
        });

        $searchproducts=$products->with(['sizeprice'=>function($size){

            $size->where('product_prices.isactive', true);

        }])
            ->whereHas('sizeprice',function($size){

            $size->where('product_prices.isactive', true);

        })
            ->paginate(20);

        foreach($searchproducts as $product){
            foreach($product->sizeprice as $size){
                $size->quantity=$cart[$size->id]??0;
                $size->in_stocks=Size::getStockStatus($size, $product);
            }
        }

        return [
            'status'=>'success',
            'banner'=>$banner??null,
            'category'=>$category??null,
            'data'=>$searchproducts,
            'cart_total'=>$cart_total
        ];
    }

    public function product_detail(Request $request,$id){
        $user=auth()->guard('customerapi')->user();

//        if(!$user)
//            return [
//                'status'=>'failed',
//                'message'=>'Please login to continue'
//            ];
        $product=Product::active()
            ->with(['sizeprice'=>function($sizeprice){
                $sizeprice->with('images')->where('isactive', true);
            }])
            ->findOrFail($id);
            $timeslot=TimeSlot::active()->select('id','from_time','to_time')->get();
            $reviews=$product->reviews()->with(['customer'=>function($customer){
                $customer->select('id','name','image');
            }])->limit(4)->get();
            $avg_reviews=$product->avg_reviews()->get()[0]['rating']??0.0;
            $ratings1=$product->reviews_count()->get();
            //var_dump($ratings1->toArray());die;
           // $totalcount=$product->reviews_count()->count();
            $ratings=['one'=>0, 'two'=>0, 'three'=>0, 'four'=>0, 'five'=>0];
        $totalcount=0;
            foreach($ratings1 as $r){
                switch($r->rating){
                    case 1:$ratings['one']=$r->count;break;
                    case 2:$ratings['two']=$r->count;break;
                    case 3:$ratings['three']=$r->count;break;
                    case 4:$ratings['four']=$r->count;break;
                    case 5:$ratings['five']=$r->count;break;
                }
                $totalcount=$ratings['one']+$ratings['two']+$ratings['three']+$ratings['four']+$ratings['five'];
            }
        $cart=Cart::getUserCart($user);
        $cart_total=$cart['total'];
        $cart=$cart['cart'];
        foreach($product->sizeprice as $size)
            $size->quantity=$cart[$size->id]??0;
        $productdetails=array(
                 'id'=>$product->id,
                 'name'=>$product->name,
                 'description'=>$product->description,
                 'company'=>$product->company,
                 'ratings'=>$product->ratings,
                 'sizeprice'=>$product->sizeprice,
                 'reviews_count'=>$ratings,
                 'avg_reviews'=>$avg_reviews,
                 'totalcount'=>$totalcount,
                 'reviews'=>$reviews,
                 'timeslot'=>$timeslot,
                 'in_stocks'=>empty($size)?0:Size::getStockStatus($size, $product),
                 'min_qty'=>$size->min_qty??1,
                 'max_qty'=>$size->max_qty??50,
        );

        return [
            'status'=>'success',
            'data'=>$productdetails,
            'cart_total'=>$cart_total

        ];
    }



}
