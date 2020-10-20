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

    public function banneredit(Request $request,$id){
        $homesection =HomeSection::findOrFail($id);
        $homesectionentity =HomeSectionEntity::where('entity_type','App\Models\Banner')
            ->where('home_section_id',$id)->get();
        $banners = Banner::active()->get();
        // return  $homesectionentity;
        return view('admin.homesection.banneredit',['homesection'=>$homesection, 'banners'=>$banners,'homesectionentity'=>$homesectionentity]);
    }

    public function bannerupdate(Request $request,$id){
        $request->validate([
            'sequence_no'=>'required',
            'isactive'=>'required'
        ]);

        $homesection =HomeSection::findOrFail($id);
        $homesectionentity =HomeSectionEntity::where('entity_type','App\Models\Banner')->where('home_section_id',$id)->get();

        if(stripos($request->entity_type, 'bann_')!==false){
            $id=str_replace('bann_', '', $request->entity_type);
            $banner=Banner::find((int)$id);
            $entitytype='App\Models\Banner';
            $entitytid=$banner->id??'';

        }
        if($homesection->update([
            'sequence_no'=>$request->sequence_no,
            'isactive'=>$request->isactive,
            'type'=>$request->type,
        ]))
            //var_dump($homesection->toArray());die();
            if($homesectionentity[0]->update([
                'home_section_id'=>$homesection->id,
                'entity_type'=>$entitytype,
                'entity_id'=>$entitytid,
            ]))
                //var_dump($homesectionentity->toArray());die();
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
            {
                return redirect()->route('homesection.list')->with('success', 'homesection has been added');
            }
        return redirect()->back()->with('error', 'homesection create failed');
    }

    public function productedit(Request $request,$id){
        $homesection =HomeSection::findOrFail($id);
        $homesectionentity =HomeSectionEntity::where('entity_type','App\Models\Product')
            ->where('home_section_id',$id)->get();
        $products =Product::active()->get();
        $homeEntityNames=HomeSectionEntity::where('entity_type','App\Models\Product')->where('home_section_id',$id)->get();
        return view('admin.homesection.productedit',['homesection'=>$homesection,'products'=>$products,'homesectionentity'=>$homesectionentity,'homeEntityNames'=>$homeEntityNames]);
    }

    public function productupdate(Request $request,$id){
        $request->validate([
            'sequence_no'=>'required',
            'isactive'=>'required'
        ]);
        $homesection =HomeSection::findOrFail($id);

        if($homesection->update([
            'name'=>$request->name,
            'sequence_no'=>$request->sequence_no,
            'isactive'=>$request->isactive,
            'type'=>$request->type,
        ]))
        {
            return redirect()->back()->with('success', 'homesection has been updated');
        }
        return redirect()->back()->with('error', 'updated create failed');

    }

    public function productImage(Request $request,$id){

        $request->validate([
            'entity_type'=>'required',
        ]);

        $homesection=HomeSection::find($id);

        if(stripos($request->entity_type, 'prod_')!==false){
            $id=str_replace('prod_', '', $request->entity_type);
            $product=Product::find((int)$id);
            $entitytype='App\Models\Product';
            $entitytid=$product->id??'';

        }
        if($homesectionentity=HomeSectionEntity::create([
            'home_section_id'=>$homesection->id,
            'entity_type'=>$entitytype,
            'entity_id'=>$entitytid,
        ]))

        {
            return redirect()->back()->with('success', 'homesection has been updated');
        }
        return redirect()->back()->with('error', 'updated create failed');

    }

    public function productdelete(Request $request,$id){
        HomeSectionEntity::where('id', $id)->delete();
        return redirect()->back()->with('success', 'HomeSectionEntity has been deleted');
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
        {
            return redirect()->route('homesection.list')->with('success', 'homesection has been added');
        }
        return redirect()->back()->with('error', 'homesection create failed');
    }

    public function subcategoryedit(Request $request,$id){
        $subcategorys =SubCategory::active()->get();
        $homesection =HomeSection::findOrFail($id);
        $homesectionentity =HomeSectionEntity::where('entity_type','App\Models\SubCategory')
            ->where('home_section_id',$id)->get();
        $homeEntityImage=HomeSectionEntity::where('entity_type','App\Models\SubCategory') ->where('home_section_id',$id)->get();

        return view('admin.homesection.subcategoryedit',['homesection'=>$homesection,'subcategorys'=>$subcategorys,'homesectionentity'=>$homesectionentity,'homeEntityImage'=>$homeEntityImage]);
    }

    public function subcategoryupdate(Request $request,$id){
        $request->validate([
            'sequence_no'=>'required',
            'isactive'=>'required'
        ]);
        $homesection =HomeSection::findOrFail($id);

        if($homesection->update([
            'name'=>$request->home_section_name,
            'sequence_no'=>$request->sequence_no,
            'isactive'=>$request->isactive,
            'type'=>$request->type,
        ]))
        {
            return redirect()->back()->with('success', 'homesection has been updated');
        }
        return redirect()->back()->with('error', 'homesection updated failed');

    }

    public function subcategoryimage(Request $request,$id){
        $request->validate([
            'entity_type'=>'required',
        ]);

        $homesection=HomeSection::find($id);

        if(stripos($request->entity_type, '_subcat')!==false){
            $id=str_replace('_subcat', '', $request->entity_type);
            $subcategory=SubCategory::find((int)$id);
            $entitytype='App\Models\SubCategory';
            $entitytid=$subcategory->id??'';

        }
        if($homesectionentity=HomeSectionEntity::create([
            'home_section_id'=>$homesection->id,
            'name' => $request->name,
            'entity_type'=>$entitytype,
            'entity_id'=>$entitytid,
            'image'=>'a'
        ]));

            $homesectionentity->saveImage($request->image, 'subcategory');
        {
            return redirect()->back()->with('success', 'subcategory has been created');
        }
        return redirect()->back()->with('error', 'subcategory create failed');
    }

    public function subdelete(Request $request,$id){
        HomeSectionEntity::where('id', $id)->delete();
        return redirect()->back()->with('success', 'HomeSectionEntity has been deleted');
    }

    public function homesectiondelete(Request $request,$id){
        HomeSection::where('id', $id)->delete();
        HomeSectionEntity::where('home_section_id', $id)->delete();

        return redirect()->back()->with('success', 'homesection has been deleted');
    }


}
