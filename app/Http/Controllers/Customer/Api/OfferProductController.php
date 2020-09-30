<?php

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\SuperAdmin\BannerController;
use App\Models\Cart;
use App\Models\OfferCategory;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OfferProductController extends Controller
{
    public function offerproducts(Request $request){
        $user=auth()->guard('customerapi')->user();

//        if(!$user)
//            return [
//                'status'=>'failed',
//                'message'=>'Please login to continue'
//            ];
        $banner=Banner::active()->select('id','image')->get();
        $offercategory=OfferCategory::active()->select('id','name','image')->get();
          if(!empty($request->offer_cat_id)){
              $offerproduct=Product::active()->whereHas('offercategory', function($category) use($request){
                  $category->where('offer_category.id', $request->offer_cat_id);
              });
        }else{
              $offerproduct=Product::active()->has('offercategory');
        }
        $cart=Cart::getUserCart($user);
        $offerproducts=$offerproduct->with('sizeprice')->paginate(20);

        foreach($offerproducts as $product){
            foreach($product->sizeprice as $size){
                $size->quantity=$cart[$size->id]??0;
                $size->in_stock=Size::getStockStatus($product, $size);
            }

        }

        return [
            'status'=>'success',
            'banner'=>$banner,
            'offercategory'=>$offercategory,
            'data'=>$offerproducts
        ];
    }

    public function products(Request $request){

        if(!empty($request->sub_cat_id)){

            $product=Product::active()->whereHas('subcategory', function($category) use($request){
                $category->where('sub_category.id', $request->sub_cat_id);
            });
        }else{
            $product=Product::active()->whereHas('category', function($category) use($request){
                $category->where('categories.id', $request->category_id);
            });
        }
        //$product=$product->where()
        $products=$product->with('sizeprice')->paginate(20);

        return [
            'status'=>'success',
            'data'=>$products
        ];
    }
////////////////////////////////////////////////////
    public function offerproducts_withoutcategory(Request $request){
        $user=auth()->guard('customerapi')->user();

//        if(!$user)
//            return [
//                'status'=>'failed',
//                'message'=>'Please login to continue'
//            ];
        $banner=Banner::active()->select('id','image')->get();
        $category=Category::active()->select('id','name','image')->get();
        if(!empty($request->category_id)){

        $offerproduct=Product::active()->where('is_offer',true)->whereHas('category', function($category) use($request){
            $category->where('categories.id', $request->category_id);

        });
        }else {

            $offerproduct = Product::active()->where('is_offer', true);
        }

        $cart=Cart::getUserCart($user);
        $offerproducts=$offerproduct->with('sizeprice')->paginate(20);

        foreach($offerproducts as $product){
            foreach($product->sizeprice as $size)
                $size->quantity=$cart[$size->id]??0;
        }

        return [
            'status'=>'success',
            'banner'=>$banner,
            'category'=>$category,
            'data'=>$offerproducts
        ];
    }


}
