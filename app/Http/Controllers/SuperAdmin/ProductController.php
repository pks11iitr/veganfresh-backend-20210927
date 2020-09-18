<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\CategoryProduct;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class ProductController extends Controller
{
     public function index(Request $request){

		 $products=Product::where(function($products) use($request){
                $products->where('name','LIKE','%'.$request->search.'%');
            });

            if($request->ordertype)
                $products=$products->orderBy('name', $request->ordertype);

            $products=$products->paginate(10);
            return view('admin.product.view',['products'=>$products]);
              }

    public function create(Request $request){
        $categories=Category::active()->get();
        $subcategories=SubCategory::active()->get();
            return view('admin.product.add',['categories'=>$categories,'subcategories'=>$subcategories]);
               }

   public function store(Request $request){
               $request->validate([
                  			'isactive'=>'required',
                  			'name'=>'required',
                  			'description'=>'required',
                  			'company'=>'required',
                  			'is_offer'=>'required',
//                  			'min_qty'=>'required',
//                  			'max_qty'=>'required',
//                  			'stock'=>'required',
                  			'image'=>'required|image'
                               ]);
           // var_dump($request->sub_cat_id); die;
          if($products=Product::create([
                      'name'=>$request->name,
                      'description'=>$request->description,
                      'company'=>$request->company,
                      'is_offer'=>$request->is_offer,
                     // 'min_qty'=>$request->min_qty,
                     // 'max_qty'=>$request->max_qty,
                      'ratings'=>$request->ratings,
                     // 'stock'=>$request->stock,
                      'isactive'=>$request->isactive,
                      'image'=>'a']))
              $added_categories=[];
       if(!empty($request->sub_cat_id)){
           $subcat=SubCategory::with('category')
               ->whereIn('id', $request->sub_cat_id)
               ->get();

           foreach($subcat as $subcategory) {
               CategoryProduct::create([
                   'category_id' => $subcategory->category_id,
                   'sub_cat_id' => $subcategory->id,
                   'product_id' => $products->id,

               ]);
               $added_categories[] = $subcategory->category_id;
           }
       }

       if(!empty($request->category_id)){
           $reqcat=$request->category_id;
           $remaining_ids=array_diff($reqcat,$added_categories);
           //return $remaining_ids;
           foreach($remaining_ids as $catid)
               CategoryProduct::create([
                   'category_id' => $catid,
                   'sub_cat_id' =>null,
                   'product_id' => $products->id,

               ]);
       }



       {
                if($request->image){
                    $products->saveImage($request->image, 'products');
                }


             return redirect()->route('product.list', ['id'=>$products->id])->with('success', 'Product has been created');
            }
             return redirect()->back()->with('error', 'Product create failed');
          }

    public function edit(Request $request,$id){
             $products = Product::findOrFail($id);
             $sizeprice=Size::get();
             $categories=Category::active()->get();
             $subcategories=SubCategory::active()->get();

            // $documents = $products->gallery;
             return view('admin.product.edit',['products'=>$products,'sizeprice'=>$sizeprice,'categories'=>$categories,'subcategories'=>$subcategories,]);
             }
    public function Ajaxsubcat($id)
    {
        //var_dump($id);die;
        $subcat = SubCategory::active()
            ->where("category_id",$id)
            ->pluck("name","id");

        return json_encode($subcat);
    }

    public function update(Request $request,$id){
             $request->validate([
                 'isactive'=>'required',
                 'name'=>'required',
                 'description'=>'required',
                 'company'=>'required',
                 'is_offer'=>'required',
//                 'min_qty'=>'required',
//                 'max_qty'=>'required',
//                 'stock'=>'required',
                 'image'=>'image'
                               ]);

             $product = Product::findOrFail($id);

			 $product->update([
                 'name'=>$request->name,
                 'description'=>$request->description,
                 'company'=>$request->company,
                 'is_offer'=>$request->is_offer,
//                 'min_qty'=>$request->min_qty,
//                 'max_qty'=>$request->max_qty,
                 'ratings'=>$request->ratings,
//                 'stock'=>$request->stock,
                 'isactive'=>$request->isactive,
             ]);

			 if($request->image){
                 $product->saveImage($request->image, 'products');
             }
          if($product)
             {
           return redirect()->back()->with('success', 'Product has been updated');
              }
           return redirect()->back()->with('error', 'Product update failed');

      }

      public function document(Request $request, $id){
                     $request->validate([
                               'image.*'=>'image'
                               ]);
                $product=Product::find($id);
              foreach($request->image as $file){
                $product->saveDocumentimage($file, 'sizeimage');
                  }
             if($product)  {
                   return redirect()->back()->with('success', 'Product has been created');
                     }
                   return redirect()->back()->with('error', 'Product create failed');
          }

     public function delete(Request $request, $id){
           Size::where('id', $id)->delete();
           return redirect()->back()->with('success', 'Document has been deleted');
        }

    public function sizeprice(Request $request,$id){
        $request->validate([
            'isactive'=>'required',
            'size'=>'required',
            'price'=>'required',
            'stock'=>'required',
            'min_qty'=>'required',
            'max_qty'=>'required',
            'cut_price'=>'required',
        ]);
        if($products=Size::create([
            'size'=>$request->size,
            'price'=>$request->price,
            'min_qty'=>$request->min_qty,
            'max_qty'=>$request->max_qty,
            'stock'=>$request->stock,
            'product_id'=>$id,
            'cut_price'=>$request->cut_price,
            'isactive'=>$request->isactive
        ]))
        {

            return redirect()->back()->with('success', 'Product sizeprice has been created');
        }
        return redirect()->back()->with('error', 'Product sizeprice create failed');
    }

    public function updatesizeprice(Request $request){

        $request->validate([
            'isactive'=>'required',
            'price'=>'required',
            'stock'=>'required',
            'min_qty'=>'required',
            'max_qty'=>'required',
            'cut_price'=>'required',
        ]);

        $product = Size::findOrFail($request->size_id);
        $product->update([
            'price'=>$request->price,
            'cut_price'=>$request->cut_price,
            'min_qty'=>$request->min_qty,
            'max_qty'=>$request->max_qty,
            'stock'=>$request->stock,
            'isactive'=>$request->isactive,
        ]);
        {

            return redirect()->back()->with('success', 'Product sizeprice has been created');
        }
        return redirect()->back()->with('error', 'Product sizeprice create failed');
    }


    public function productcategory(Request $request,$id){
        $request->validate([
            'category_id'=>'required',
            'sub_cat_id'=>'required',
        ]);
        if($products_with_category=CategoryProduct::create([
            'category_id'=>$request->category_id,
            'sub_cat_id'=>$request->sub_cat_id,
            'product_id'=>$id,
        ]))
        {

            return redirect()->back()->with('success', 'Product category  has been created');
        }
        return redirect()->back()->with('error', 'Product category create failed');
    }



}
