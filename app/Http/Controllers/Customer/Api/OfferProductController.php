<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\OfferCategory;
use App\Models\OfferProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OfferProductController extends Controller
{
    public function offerproducts(Request $request){


        $offercategory=OfferCategory::active()->get();
          if(!empty($request->offer_cat_id)){
          $offerproduct=OfferProduct::active()->where('offer_cat_id',$request->offer_cat_id);
        }else{
            $offerproduct=OfferProduct::active();
        }
        //$product=$product->where()
        $offerproducts=$offerproduct->paginate(20);

        return [
            'status'=>'success',
            'offercategory'=>$offercategory,
            'data'=>$offerproducts
        ];
    }


}
