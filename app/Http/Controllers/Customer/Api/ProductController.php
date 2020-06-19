<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function home(Request $request){
        $banners=Banner::active()->get();
        $categories=Category::active()->get();
        $section1=Product::active()->where('top_deal', true)->skip(0)->take(4)->get();
        $section2=Product::active()->where('best_seller', true)->skip(0)->take(4)->get();

        return [
            'status'=>'success',
            'data'=>compact('banners','categories', 'section1', 'section2')
        ];
    }

    public function details(Request $request, $id){
        $product=Product::active()->with(['gallery','commentscount', 'avgreviews'])->find($id);

        if(!$product)
            return [
                'status'=>'failed',
                'message'=>'No such product found',
                'data'=>[]
            ];

        return [
            'status'=>'success',
            'data'=>[
                'product'=>$product
            ]
        ];

    }

    public function topdeals(Request $request){
        $products=Product::active()->where('top_deal', true)->get();

        return [
            'status'=>'success',
            'data'=>[
                'products'=>$products
            ]
        ];
    }


    public function bestseller(Request $request){
        $products=Product::active()->where('best_seller', true)->get();

        return [
            'status'=>'success',
            'data'=>[
                'products'=>$products
            ]
        ];
    }

}
