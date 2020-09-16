<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function home(Request $request){

        $user=auth()->guard('customerapi')->user();

        $bannersobj=Banner::active()->select('entity_type', 'entity_id', 'image')->get();

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

        $productsobj=Product::active()
            ->with('sizeprice')
            ->whereIn('id', $productids)
            ->get();
        //return $productsobj;
        $products=[];
        foreach($productsobj as $product)
            $products[$product->id]=$product->sizeprice;

        //return $products;

        foreach($home_sections as $section){
            $new_sec=[];
            switch($section->type){
                case 'type1':
                    $new_sec['type']='banner';
                    $new_sec['name']='';
                    $new_sec['banner']=$section->entities[0]??'';
                    $new_sec['products']=[];
                    $new_sec['subcategory']=[];
                break;
                case 'type2':
                    $new_sec['type']='product';
                    $new_sec['name']=$section->name;
                    $new_sec['banner']=[];
                    $new_sec['subcategory']=[];
                    $new_sec['products']=[];
                    foreach($section->entities as $entity){
                        $entity1=$entity->entity;
                        $entity1->sizeprice=$products[$entity->entity_id]??[];
                        $new_sec['products'][]=$entity1;
                    }
                break;
                case 'type3':
                    $new_sec['type']='subcategory';
                    $new_sec['name']=$section->name;
                    $new_sec['products']=[];
                    $new_sec['banner']=[];
                    $new_sec['subcategory']=[];
                    foreach($section->entities as $entity){
                        $new_sec['subcategory'][]=[
                            'categoryname'=>$entity->name,
                            'categoryimage'=>$entity->image,
                            'category_id'=>$entity->id,
                        ];
                    }
                break;
            }

            $sections[]=$new_sec;

        }

        return compact('banners', 'categories', 'user', 'sections');

    }
}
