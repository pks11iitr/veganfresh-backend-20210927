<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\OfferCategory;
use App\Models\OfferProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OfferProductController extends Controller
{
    public function offerproducts(Request $request){


        $offercategory=OfferCategory::active()->get();
          if(!empty($request->offer_cat_id)){
              $offerproduct=Product::active()->whereHas('offercategory', function($category) use($request){
                  $category->where('offer_category.id', $request->offer_cat_id);
              });
        }else{
              $offerproduct=Product::active()->has('offercategory');
        }
        //$product=$product->where()
        $offerproducts=$offerproduct->paginate(20);

        return [
            'status'=>'success',
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



}
