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
        $categories=Category::active()->with('subcategory')->get();
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


        $subcat=SubCategory::active()->where('category_id',$id)->get();

        if(!empty($request->subcategory_id)){
            $pack_size=Size::active()
                ->whereHas('product', function($product) use($request){

                    $product->whereHas('subcategory', function($subcategory) use($request){
                        $subcategory->where('sub_category.id', $request->subcategory_id);
                    });

                })
                ->select(DB::raw('distinct(size) as size'))
                ->get();
        }else{
            $pack_size=Size::active()
                ->whereHas('product', function($product) use($id){

                    $product->whereHas('category', function($category) use($id){
                        $category->where('categories.id', $id);
                    });
                })
                ->select(DB::raw('distinct(size) as size'))
                ->get();
        }
//return $pack_size;

        $sizes=[];
        foreach($pack_size as $ps){
            $sizes[]=$ps->size;
        }
        // $subcat->prepend($datas);
        if(count($subcat)>0){
         return [
             'status'=>'success',
             'code'=>'200',
             'data'=>$subcat,
             'pack_size'=>$pack_size,
             'filters'=>compact('sizes')
         ];
 }else{
     return [
          'status'=>'No Record Found',
           'code'=>'402'
     ];
}
}


}
