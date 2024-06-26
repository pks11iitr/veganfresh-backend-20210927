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
        //$banner=Banner::active()->select('id','image')->get();
        $bannersobj=Banner::active()->select('entity_type', 'entity_id', 'image', 'parent_category')->get();

        $banners=[];
        foreach($bannersobj as $banner){
            $new_ban=[];
            if($banner->entity_type=='App\Models\Category'){
                $new_ban['image']=$banner->image;
                $new_ban['type']='category';
                $new_ban['cat_id']=$banner->entity_id;
                $new_ban['subcat_id']='';
            }else if($banner->entity_type=='App\Models\SubCategory'){
                $new_ban['image']=$banner->image;
                $new_ban['type']='subcategory';
                $new_ban['cat_id']=$banner->parent_category;
                $new_ban['subcat_id']=$banner->entity_id;
            }
            $banners[]=$new_ban;
        }
        $offercategory=OfferCategory::active()->select('id','name','image')->get();
          if(!empty($request->offer_cat_id)){
              $offerproduct=Product::active()
                  ->whereHas('offercategory', function($category) use($request){
                      $category->where('offer_category.id', $request->offer_cat_id)
                          ->where('offer_category.isactive',1);
                  });
        }else{
              $offerproduct=Product::active()
                  ->whereHas('offercategory',function ($category){
                  $category->where('offer_category.isactive',1);
              });
        }
        $cart=Cart::getUserCart($user);
        $cart_total=$cart['total'];
        $cart=$cart['cart'];

        $offerproducts=$offerproduct->with(['sizeprice'=>function($size){
            $size->where('product_prices.isactive', true);
        }])->paginate(20);

        foreach($offerproducts as $product){
            foreach($product->sizeprice as $size){
                $size->quantity=$cart[$size->id]??0;
                $size->in_stocks=Size::getStockStatus($size, $product);
            }

        }

        return [
            'status'=>'success',
            'banner'=>$banners,
            'offercategory'=>$offercategory,
            'data'=>$offerproducts,
            'cart_total'=>$cart_total
        ];
    }

//    public function products(Request $request){
//
//        if(!empty($request->sub_cat_id)){
//
//            $product=Product::active()->whereHas('subcategory', function($category) use($request){
//                $category->where('sub_category.id', $request->sub_cat_id);
//            });
//        }else{
//            $product=Product::active()->whereHas('category', function($category) use($request){
//                $category->where('categories.id', $request->category_id);
//            });
//        }
//        //$product=$product->where()
//        $products=$product->with('sizeprice')->paginate(20);
//
//        return [
//            'status'=>'success',
//            'data'=>$products
//        ];
//    }
////////////////////////////////////////////////////
    public function offerproducts_withoutcategory(Request $request){
        $user=auth()->guard('customerapi')->user();

        //$banner=Banner::active()->select('id','image')->get();

        $bannersobj=Banner::active()->select('entity_type', 'entity_id', 'image', 'parent_category')->get();

        $banners=[];
        foreach($bannersobj as $banner){
            $new_ban=[];
            if($banner->entity_type=='App\Models\Category'){
                $new_ban['image']=$banner->image;
                $new_ban['type']='category';
                $new_ban['cat_id']=$banner->entity_id;
                $new_ban['subcat_id']='';
            }else if($banner->entity_type=='App\Models\SubCategory'){
                $new_ban['image']=$banner->image;
                $new_ban['type']='subcategory';
                $new_ban['cat_id']=$banner->parent_category;
                $new_ban['subcat_id']=$banner->entity_id;
            }
            $banners[]=$new_ban;
        }

        $category=Category::active()->select('id','name','image')->get();
        if(!empty($request->category_id)){

        $offerproduct=Product::active()->where('is_offer',true)->whereHas('category', function($category) use($request){
            $category->where('categories.id', $request->category_id)->where('categories.isactive',true);

        });
        }else {

            $offerproduct = Product::active()->where('is_offer', true);
        }

        $cart=Cart::getUserCart($user);
        $cart_total=$cart['total'];
        $cart=$cart['cart'];

        $offerproducts=$offerproduct->with(['sizeprice'=>function($size){
            $size->where('product_prices.isactive', true);
        }])->paginate(20);

        foreach($offerproducts as $product){
            foreach($product->sizeprice as $size){
                $size->quantity=$cart[$size->id]??0;
                $size->in_stocks=Size::getStockStatus($size, $product);
            }

        }

        return [
            'status'=>'success',
            'banner'=>$banners,
            'category'=>$category,
            'data'=>$offerproducts,
            'cart_total'=>$cart_total

        ];
    }


}
