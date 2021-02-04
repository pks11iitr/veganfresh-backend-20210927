<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{

    public function category(Request $request){
        $categories=Category::active()->with(['subcategory'=>function($subcategory){
            $subcategory->where('sub_category.isactive', true);
        }])->get();

        if($categories){
         return [
             'status'=>'success',
             'data'=>$categories
             ];
           }else{
             return [
                  'status'=>'No Record Found',
                   'code'=>'402'
             ];
           }
    }


  public function subcategory(Request $request,$id){


        $subcatobj=SubCategory::active()->where('category_id',$id)->get();

        $subcat=[];
        $subcat[]=['id'=>0, 'name'=>'All'];
        foreach($subcatobj as $obj){
            $subcat[]=['id'=>$obj->id, 'name'=>$obj->name];
        }

        if(!empty($request->subcategory_id)){
            $pack_size=Size::active()
                ->whereHas('product', function($product) use($request){

                    $product->whereHas('subcategory', function($subcategory) use($request){
                        $subcategory->where('sub_category.isactive', true)
                            ->where('sub_category.id', $request->subcategory_id);
                    })->where('products.isactive', true);

                })
                ->select(DB::raw('distinct(size) as size'))
                ->get();
        }else{
            $pack_size=Size::active()
                ->whereHas('product', function($product) use($id){

                    $product->whereHas('category', function($category) use($id){
                        $category->where('categories.isactive', true)
                            ->where('categories.id', $id);
                    })->where('products.isactive', true);
                })
                ->select(DB::raw('distinct(size) as size'))
                ->get();
        }

      if(!empty($request->subcategory_id)){
          $brand=Product::active()
              ->whereHas('subcategory', function($subcategory) use($request){
                      $subcategory->where('sub_category.isactive', true)
                          ->where('sub_category.id', $request->subcategory_id);
                  })
              ->select(DB::raw('distinct(products.company) as brand'))
              ->get();
      }else{
          $brand=Product::active()
              ->whereHas('category', function($category) use($id){
                      $category->where('categories.isactive', true)
                          ->where('categories.id', $id);
                  })
              ->select(DB::raw('distinct(products.company) as brand'))
              ->get();
      }

      $brand=$brand->map(function($b){
          return ['name'=>$b->brand];
      });

      if(!empty($request->subcategory_id)){
          $prices=Size::active()
              ->whereHas('product', function($product) use($request){

                  $product->whereHas('subcategory', function($subcategory) use($request){
                      $subcategory->where('sub_category.isactive', true)
                          ->where('sub_category.id', $request->subcategory_id);
                  })->where('products.isactive', true);

              })
              ->select(DB::raw('max(price) as max_price'), DB::raw('max(price) as min_price'))
              ->get();
      }else{
          $prices=Size::active()
              ->whereHas('product', function($product) use($id){

                  $product->whereHas('category', function($category) use($id){
                      $category->where('categories.isactive', true)
                          ->where('categories.id', $id);
                  })->where('products.isactive', true);
              })
              ->select(DB::raw('max(price) as max_price'), DB::raw('max(price) as min_price'))
              ->get();
      }

        $min_price=$prices[0]->min_price??0;
        $max_price=$prices[0]->max_price??0;

        $prices=[];
        if($min_price>0){

            $prices[]=['name'=>'0-50'];

            if($max_price >50){
                $prices[]=['name'=>'50-100'];
            }
            if($max_price >100){
                $prices[]=['name'=>'100-500'];
            }
            if($max_price >500){
                $prices[]=['name'=>'500-1000'];
            }
            if($max_price>1000)
            {
                $prices[]=['name'=>'1000-'.$max_price];
            }
        }



        $sizes=[];
        foreach($pack_size as $ps){
            $sizes[]=['name'=>$ps->size];
        }
        // $subcat->prepend($datas);
        if(true){
         return [
             'status'=>'success',
             'code'=>'200',
             'data'=>$subcat,
             'pack_size'=>$pack_size,
             'filters'=>compact('sizes','brand', 'prices', 'min_price', 'max_price')
         ];
 }else{
     return [
          'status'=>'No Record Found',
           'code'=>'402'
     ];
}
}


}
