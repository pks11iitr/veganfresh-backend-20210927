<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\CategoryProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
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


    public function search_products(Request $request){

      //  if(!empty($request->sub_cat_id)){

        //   $product=Product::active()->whereHas('subcategory', function($category) use($request){
        //       $category->where('sub_category.id', $request->sub_cat_id);
        //     });
        // }else{
        //   $product=Product::active()->whereHas('category', function($category) use($request){
        //       $category->where('categories.id', $request->category_id);
        //     });
        // }
        //$product=$product->where()
        if(!empty($request->search))
            $product=Product::active()
            ->with('category')
            ->where('name', 'like', "%".$request->search."%");
        $products=$product->get();
        //$product_category=CategoryProduct::getproduct();

        foreach($products as $i=>$r)
                  {
                    // $r->category_name=$r->category[0]->name??0;
                    // $r->category=null
                  $products[$i]['category_name']=$r->category[0]->name??0;
                }
        return [
            'status'=>'success',
            'data'=>$products
        ];
    }



}
