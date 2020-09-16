<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Banner;
use App\Models\HomeSection;
use App\Models\HomeSectionEntity;
use App\Models\Product;
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
                return redirect()->route('homesection.list')->with('success', 'detailstore has been added');
            }
        return redirect()->back()->with('error', 'detailstore create failed');
    }

    public function productcreate(Request $request){
        $products =Product::active()->get();
        return view('admin.homesection.product',['products'=>$products]);
    }

    public function subcategorycreate(Request $request){
        return view('admin.homesection.subcategory');
    }


}
