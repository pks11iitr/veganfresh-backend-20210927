<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\TimeSlot;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function home(Request $request){

        $user=auth()->guard('customerapi')->user();


        $time=date('H:i:s');

        $timeslot=TimeSlot::getNextDeliverySlot();

//        $timeslot=TimeSlot::where('from_time', '>=', $time)->orderBy('from_time', 'asc')->first();

        if(!$timeslot){
            $next_slot='No Delivery Slot Available';
        }else{
            $next_slot=$timeslot['next_slot'];
        }



        $bannersobj=Banner::active()->select('entity_type', 'entity_id', 'image', 'parent_category')->get();

        $banners=[];
        foreach($bannersobj as $banner){
            $new_ban=[];
            if($banner->entity_type=='App\Models\Category'){
                $new_ban['image']=$banner->image;
                $new_ban['type']='category';
                $new_ban['cat_id']=$banner->entity_id;
                $new_ban['subcat_id']='';
            }else if($banner->entity_type=='App\Models\SubCategory'){
                $new_ban['image']=$banner->image;
                $new_ban['type']='subcategory';
                $new_ban['cat_id']=$banner->parent_category;
                $new_ban['subcat_id']=$banner->entity_id;
            }
            $banners[]=$new_ban;
        }

        //return $banners;

        $user=[
            'name'=>$user->name??'',
            'image'=>$user->image??'',
            'mobile'=>$user->mobile??''
        ];

        $home_sections=HomeSection::active()
            ->with('entities.entity')
            ->orderBy('sequence_no', 'asc')
        ->get();
//return $home_sections;
        $categories=Category::active()
            ->select('id', 'name', 'image')
            ->get();

        $sections=[];

        //get sizeprice of products
        $productids=[];
        foreach($home_sections as $section){
            foreach($section->entities as $e){
                if($e->entity_type=='App\Models\Product'){
                    $productids[]=$e->entity_id;
                }
            }

        }
        //return $productids;
        //DB::enableQueryLog();
        $productsobj=Product::active()
            ->with(['sizeprice','reviews_count'])
            ->whereIn('id', $productids)
            ->get();
        //return $productsobj;
        $products=[];
        foreach($productsobj as $product)
            $products[$product->id]=[
                'sizeprice'=>$product->sizeprice,
                'reviews'=>$product->reviews_count[0]->review??0,
                'ratings'=>number_format($product->reviews_count[0]->rating??0,1),
            ];

        //return $products;
        //var_dump(DB::getQueryLog());
        //die;
        foreach($home_sections as $section){
            $new_sec=[];
            switch($section->type){
                case 'banner':
                    $new_sec['type']='banner';
                    $new_sec['name']='';
                    $new_sec['bannerdata']=[
                        'image'=>$section->entities[0]->entity->image??'',
                        'category_id'=>$section->entities[0]->entity->parent_category??'',
                        'subcategory_id'=>$section->entities[0]->entity->entity_id??'',
                    ];
                    $new_sec['products']=[];
                    $new_sec['subcategory']=[];
                break;
                case 'product':
                    $new_sec['type']='product';
                    $new_sec['name']=$section->name;
                    $new_sec['bannerdata']=[
                        'image'=>'',
                        'category_id'=>'',
                        'subcategory_id'=>'',
                    ];
                    $new_sec['subcategory']=[];
                    $new_sec['products']=[];
                    foreach($section->entities as $entity){
                        $entity1=$entity->entity;
                        $entity1->sizeprice=$products[$entity->entity_id]['sizeprice']??[];
                        $entity1->ratings=number_format($products[$entity->entity_id]['ratings']??0, 1);
                        $entity1->reviews=$products[$entity->entity_id]['reviews']??0;
                        $new_sec['products'][]=$entity1;
                    }
                break;
                case 'subcategory':
                    $new_sec['type']='subcategory';
                    $new_sec['name']=$section->name;
                    $new_sec['products']=[];
                    $new_sec['bannerdata']=[
                        'image'=>'',
                        'category_id'=>'',
                        'subcategory_id'=>'',
                    ];
                    $new_sec['subcategory']=[];
                    foreach($section->entities as $entity){
                        $new_sec['subcategory'][]=[
                            'categoryname'=>$entity->name,
                            'categoryimage'=>$entity->image,
                            'subcategory_id'=>$entity->entity_id,
                            'category_id'=>$entity->parent_category,
                        ];
                    }
                break;
            }

            $sections[]=$new_sec;

        }

        return compact('banners', 'categories', 'user', 'sections', 'next_slot');

    }
}
