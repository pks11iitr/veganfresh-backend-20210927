<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Cart;
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
              $category->where('sub_category.id', $request->sub_cat_id);
            });
        }else{
          $product=Product::active()->whereHas('category', function($category) use($request){
              $category->where('categories.id', $request->category_id);
            });
        }

        if($request->prices || $request->sizes){

            $product=$product->whereHas('sizeprice', function($size) use($request){

                if($request->price){
                    $prices=explode('-', $request->price??'');
                    $size->where('product_prices.price', '>=', intval($prices[0]??0))
                        ->where('product_prices.price', '<=', intval($prices[1]??0));
                }
                if($request->sizes){
                    $sizes=explode('#', $request->sizes);
                    $size->whereIn('product_prices.size', $sizes);
                }
            });
        }
        if($request->brand){
            $brands=explode('#', $request->brand);
            foreach($brands as $brand){
                $product=$product->whereIn('company_name', $brands);
            }
        }


        $cart=Cart::getUserCart($user);

        $products=$product->with(['sizeprice'])->paginate(20);

        foreach($products as $product){
            foreach($product->sizeprice as $size){
                $size->quantity=$cart[$size->id]??0;
                $size->in_stocks=Size::getStockStatus($size, $product);
            }
        }

        return [
            'status'=>'success',
            'data'=>$products,
        ];
    }

    public function search_products(Request $request){

        $user=auth()->guard('customerapi')->user();

        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        if(!empty($request->search))
            $product=Product::active()
            ->with(['category', 'sizeprice'])
            ->where('name', 'like', "%".$request->search."%");
        $products=$product->paginate(10);


        $cart=Cart::getUserCart($user);

        foreach($products as $i=>$r)
        {
            $products[$i]['category_name']=$r->category[0]->name??0;
            foreach($product[$i]->sizeprice as $size){
                $size->quantity=$cart[$size->id]??0;
                $size->in_stocks=Size::getStockStatus($size, $product);
            }
        }
        return [
            'status'=>'success',
            'data'=>$products,

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
            ->with(['sizeprice.images'])
            ->findOrFail($id);
            $timeslot=TimeSlot::active()->select('id','from_time','to_time')->get();
            $reviews=$product->reviews()->with(['customer'=>function($customer){
                $customer->select('id','name','image');
            }])->limit(4)->get();
            $avg_reviews=$product->avg_reviews()->get()[0]['rating']??0.0;
            $ratings1=$product->reviews_count()->get();
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
                     'in_stocks'=>Size::getStockStatus($size, $product),
                     'min_qty'=>$size->min_qty,
                     'max_qty'=>$size->max_qty,
        );

        return [
            'status'=>'success',
            'data'=>$productdetails,

        ];
    }



}
