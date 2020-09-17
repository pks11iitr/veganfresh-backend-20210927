<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Banner;
use App\Models\HomeSection;
use App\Models\HomeSectionEntity;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeSectionController extends Controller
{
    public function index(Request $request){
        $homesections =HomeSection::paginate(10);
        return view('admin.homesection.view',['homesections'=>$homesections]);
    }

    public function bannercreate(Request $request){
        $banners = Banner::active()->get();
        return view('admin.homesection.banner',['banners'=>$banners]);
    }

    public function bannerstore(Request $request){
        $request->validate([
            'sequence_no'=>'required',
            'isactive'=>'required'
        ]);

        if(stripos($request->entity_type, 'bann_')!==false){
            $id=str_replace('bann_', '', $request->entity_type);
            $banner=Banner::find((int)$id);
            $entitytype='App\Models\Banner';
            $entitytid=$banner->id??'';

        }
        if($homesection=HomeSection::create([
            'sequence_no'=>$request->sequence_no,
            'isactive'=>$request->isactive,
            'type'=>$request->type,
        ]))
            if($homesectionentity=HomeSectionEntity::create([
                'home_section_id'=>$homesection->id,
                'entity_type'=>$entitytype,
                'entity_id'=>$entitytid,
            ]))
                //var_dump($homesection);die();
            {
                return redirect()->route('homesection.list')->with('success', 'homesection has been added');
            }
        return redirect()->back()->with('error', 'homesection create failed');
    }

    public function productcreate(Request $request){
        $products =Product::active()->get();
        return view('admin.homesection.product',['products'=>$products]);
    }

    public function productstore(Request $request){
        $request->validate([
            'sequence_no'=>'required',
            'isactive'=>'required'
        ]);

        if($homesection=HomeSection::create([
            'name'=>$request->name,
            'sequence_no'=>$request->sequence_no,
            'isactive'=>$request->isactive,
            'type'=>$request->type,
        ]))
        //var_dump($request->entity_type);die();
        foreach ($request['entity_type'] as $key=>$image){

            if(stripos($image, 'prod_') !== false){
                $id = str_replace('prod_', '', $image);
                $product = Product::find((int)$id);
                $entitytype = 'App\Models\Product';
                $entitytid = $product->id ?? '';
            }

            if ($homesectionentity = HomeSectionEntity::create([
                'home_section_id' => $homesection->id,
                'entity_type' => $entitytype,
                'entity_id' => $entitytid,
            ]));
        }
            {
                return redirect()->route('homesection.list')->with('success', 'homesection has been added');
            }
        return redirect()->back()->with('error', 'homesection create failed');
    }

    public function subcategorycreate(Request $request){
        $subcategorys =SubCategory::active()->get();
        return view('admin.homesection.subcategory',['subcategorys'=>$subcategorys]);
    }

    public function subcategorystore(Request $request){
        $request->validate([
            'sequence_no'=>'required',
            'isactive'=>'required'
        ]);

        if($homesection=HomeSection::create([
            'name'=>$request->home_section_name,
            'sequence_no'=>$request->sequence_no,
            'isactive'=>$request->isactive,
            'type'=>$request->type,
        ]))

            foreach ($request['entity_type'] as $key=>$sub_cat_image){

                if(stripos($sub_cat_image, '_subcat') !== false){
                    $id = str_replace('_subcat', '', $sub_cat_image);
                    $subcategory =SubCategory::find((int)$id);
                    $entitytype = 'App\Models\SubCategory';
                    $entitytid = $subcategory->id ?? null;
                    $parentcategory=$subcategory->category_id??null;
                }

                if ($homesectionentity = HomeSectionEntity::create([
                    'home_section_id' => $homesection->id,
                    'name' => $request->name[$key],
                    'entity_type' => $entitytype,
                    'entity_id' => $entitytid,
                    'parent_category'=>$parentcategory,
                    'image'=>'a'
                ]));
                $homesectionentity->saveImage($request->image[$key], 'subcategory');
            }
        {
            return redirect()->route('homesection.list')->with('success', 'homesection has been added');
        }
        return redirect()->back()->with('error', 'homesection create failed');
    }

}
