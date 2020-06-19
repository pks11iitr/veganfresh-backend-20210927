<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Clinic;
use App\Models\Product;
use App\Models\Therapist;
use App\Models\Therapy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function index(Request $request){
        if(!$request->search)
            return [
                'status'=>'failed',
                'message'=>'Please type your search'
            ];

        switch($request->type){
            case 'product':

                $product=Product::active()->with(['commentscount', 'avgreviews']);
                if($request->category_id)
                    $product=$product->whereHas('category', function($category) use($request){
                        $category->where('categories.id', $request->category_id);
                    });
                $product=$product->where('name', 'like', "%".$request->search."%")->get();
                return [
                    'status'=>'success',
                    'data'=>[
                        'products'=>$product,
                        'clinics'=>[],
                        'therapies'=>[],
                    ]
                ];

                break;
            case 'clinic':

                $clinics=Clinic::active()->with(['commentscount', 'avgreviews'])->where('name', 'like', "%".$request->search."%")->get();
                return [
                    'status'=>'success',
                    'data'=>[
                        'products'=>[],
                        'clinics'=>$clinics,
                        'therapies'=>[],
                    ]
                ];

                break;
            case 'therapy':

                $therapies=Therapy::active()->with(['commentscount', 'avgreviews'])->where('name', 'like', "%".$request->search."%")->get();
                return [
                    'status'=>'success',
                    'data'=>[
                        'products'=>[],
                        'clinics'=>[],
                        'therapies'=>$therapies,
                    ]
                ];
                break;
            default:
                return [
                    'status'=>'failed',
                    'message'=>'Search is not valid'
                ];
        }

    }
}
