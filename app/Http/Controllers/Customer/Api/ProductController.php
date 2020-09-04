<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Product;
use App\Models\TimeSlot;
use App\Models\Review;
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

        if(!empty($request->search))
            $product=Product::active()
            ->with('category')
            ->where('name', 'like', "%".$request->search."%");
        $products=$product->get();

        foreach($products as $i=>$r)
                  {

                  $products[$i]['category_name']=$r->category[0]->name??0;
                }
        return [
            'status'=>'success',
            'data'=>$products
        ];
    }
    public function product_detail(Request $request,$id){

        $product=Product::active()->with('sizeprice','images')->findOrFail($id);
        $timeslot=TimeSlot::active()->get();
        $review=Review::active()->with('customer')
//            ->select('reviews.rating','reviews.image1','reviews.image','reviews.description')
->get();
            $productdetails=array(
                     'id'=>$product->id,
                     'name'=>$product->name,
                     'description'=>$product->description,
                     'company'=>$product->company,
                     'image'=>$product->image,
                     'ratings'=>$product->ratings,
                     'is_offer'=>$product->is_offer,
                     'min_qty'=>$product->min_qty,
                     'max_qty'=>$product->max_qty,
                     'price'=>$product->sizeprice[0]->price,
                     'size'=>$product->sizeprice[0]->size,
                     'cut_price'=>$product->sizeprice[0]->cut_price,
                     'sizeprice'=>$product->sizeprice,
                     'images'=>$product->images,

        );
        $totalonerating=0;
        $totaltworating=0;
        $totalthreerating=0;
        $totalfourrating=0;
        $totalfiverating=0;

            foreach($review as $key=>$rv) {
                $reviews[] = array(
                    'comment' => $rv->comment??'',
                    'review' => $rv->rating,
                    'image1' => $rv->image1??'',
                    'image' => $rv->image??'',
                    'description' => $rv->description,
                    'name' => $rv->customer->name,
                );

                if($rv->rating==5){
                    $totalfiverating=$totalfiverating+1;
                }elseif($rv->rating==4){
                    $totalfourrating=$totalfourrating+1;
                }elseif($rv->rating==3){
                    $totalthreerating=$totalthreerating+1;
                }elseif ($rv->rating==2){
                    $totaltworating=$totaltworating+1;
                }elseif ($rv->rating==1){
                    $totalonerating=$totalonerating+1;
                }
            }

        return [
            'status'=>'success',
            'data'=>$productdetails,
            'fiverating'=>$totalfiverating,
            'fourrating'=>$totalfourrating,
            'threerating'=>$totalthreerating,
            'tworating'=>$totaltworating,
            'onerating'=>$totalonerating,
            'timeslot'=>$timeslot,
            'reviews'=>$reviews,

        ];
    }


}
