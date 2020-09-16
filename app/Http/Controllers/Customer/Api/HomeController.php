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
                $new_ban['cat_id']=$banner->parent_id;
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
            ->with('entities.sizeprice')
            ->orderBy('sequence_no', 'asc')
        ->get();

        $categories=Category::active()
            ->select('id', 'name', 'image')
            ->get();

        $sections=[];

        foreach($home_sections as $section){
            $new_sec=[];
            switch($section->type){
                case 'type1':$new_sec['type']='banner';
                    $new_sec['name']='';
                    $new_sec['banner']=$section->entities[0]??'';
                break;
                case 'type2':$new_sec['type']='product';
                    $new_sec['name']=$section->name;
                    $new_sec['products']=[];
                    foreach($section->entities as $entity){

                    }
                break;
                case 'type3':$new_sec['type']='subcategory';break;
            }

            $sections[]=$new_sec;

        }

        return compact('banners', 'categories', 'user', 'sections');

    }
}
