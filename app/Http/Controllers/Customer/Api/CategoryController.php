<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

    $datas=(object) ['id' => '0','name'=>'All','isactive'=>'1'];
     $subcat->prepend($datas);
    if(count($subcat)>0){
     return [
         'status'=>'success',
         'code'=>'200',
         'data'=>$subcat
     ];
 }else{
     return [
          'status'=>'No Record Found',
           'code'=>'402'
     ];
}
}


}
