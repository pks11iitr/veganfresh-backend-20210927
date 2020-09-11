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

        $product=Product::active()
            ->with(['sizeprice','images'])
            ->findOrFail($id);
            $timeslot=TimeSlot::active()->select('id','from_time','to_time')->get();
            $reviews=$product->reviews()->with(['customer'=>function($customer){
                $customer->select('id','name','image');
            }])->limit(4)->get();
            $avg_reviews=$product->avg_reviews()->get()[0]['rating']??0.0;
            $ratings1=$product->reviews_count()->get();
            $ratings=['one'=>0, 'two'=>0, 'three'=>0, 'four'=>0, 'five'=>0];
            foreach($ratings1 as $r){
                switch($r->rating){
                    case 1:$ratings['one']=$r->count;break;
                    case 2:$ratings['two']=$r->count;break;
                    case 3:$ratings['three']=$r->count;break;
                    case 4:$ratings['four']=$r->count;break;
                    case 5:$ratings['five']=$r->count;break;
                }
            }

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
                     'price'=>$product->sizeprice[0]->price??0,
                     'size'=>$product->sizeprice[0]->size??0,
                     'cut_price'=>$product->sizeprice[0]->cut_price??0,
                'discount'=>($product->sizeprice[0]->cut_price??0)?round((($product->sizeprice[0]->cut_price??0)-($product->sizeprice[0]->price??0))/($product->sizeprice[0]->cut_price??0)*100):0,
                     'sizeprice'=>$product->sizeprice,
                     'images'=>$product->images,
                     'reviews_count'=>$ratings,
                     'avg_reviews'=>$avg_reviews,
                     'reviews'=>$reviews,
                     'timeslot'=>$timeslot,


        );
//        $totalonerating=0;
//        $totaltworating=0;
//        $totalthreerating=0;
//        $totalfourrating=0;
//        $totalfiverating=0;
//
//            foreach($review as $key=>$rv) {
//                $reviews[] = array(
//                    'comment' => $rv->comment??'',
//                    'review' => $rv->rating,
//                    'image1' => $rv->image1??'',
//                    'image' => $rv->image??'',
//                    'description' => $rv->description,
//                    'name' => $rv->customer->name,
//                );
//
//                if($rv->rating==5){
//                    $totalfiverating=$totalfiverating+1;
//                }elseif($rv->rating==4){
//                    $totalfourrating=$totalfourrating+1;
//                }elseif($rv->rating==3){
//                    $totalthreerating=$totalthreerating+1;
//                }elseif ($rv->rating==2){
//                    $totaltworating=$totaltworating+1;
//                }elseif ($rv->rating==1){
//                    $totalonerating=$totalonerating+1;
//                }
//            }

        return [
            'status'=>'success',
            'data'=>$productdetails,
//            'fiverating'=>$totalfiverating,
//            'fourrating'=>$totalfourrating,
//            'threerating'=>$totalthreerating,
//            'tworating'=>$totaltworating,
//            'onerating'=>$totalonerating,
//            'timeslot'=>$timeslot,
//            'reviews'=>$reviews,

        ];
    }


}
